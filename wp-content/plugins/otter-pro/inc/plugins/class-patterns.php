<?php
/**
 * Block Patterns.
 *
 * @package ThemeIsle\OtterPro\Plugins
 */

namespace ThemeIsle\OtterPro\Plugins;

use ThemeIsle\OtterPro\Plugins\License;

/**
 * Class Patterns
 */
class Patterns {
	/**
	 * Singleton.
	 *
	 * @var Patterns|null Class object.
	 */
	protected static $instance = null;

	/**
	 * Method to define hooks needed.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function init() {
		if ( License::has_active_license() ) {
			add_action( 'init', array( $this, 'register_patterns' ) );
			add_action( 'init', array( $this, 'maybe_sync_patterns' ) );
			add_action( 'otter_blocks_plugin_update', array( $this, 'sync_patterns' ) );
		}
	}

	/**
	 * Maybe Sync Patterns
	 *
	 * @access  public
	 */
	public function maybe_sync_patterns() {
		if ( ! get_transient( 'otter_pro_patterns' ) && ! get_transient( 'otter_pro_patterns_refetch' ) ) {
			$this->sync_patterns();
		}
	}

	/**
	 * Sync Patterns
	 *
	 * @access  public
	 */
	public function sync_patterns() {
		$url = add_query_arg(
			array(
				'site_url'   => get_site_url(),
				'license_id' => apply_filters( 'product_otter_license_key', 'free' ),
				'cache'      => gmdate( 'u' ),
			),
			'https://api.themeisle.com/templates-cloud/otter-patterns'
		);

		$response = '';

		if ( function_exists( 'vip_safe_wp_remote_get' ) ) {
			$response = vip_safe_wp_remote_get( esc_url_raw( $url ) );
		} else {
			$response = wp_remote_get( esc_url_raw( $url ) ); // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.wp_remote_get_wp_remote_get
		}

		$response = wp_remote_retrieve_body( $response );
		$response = json_decode( $response, true );

		if ( ! is_array( $response ) || isset( $response['message'] ) || 0 === count( $response ) ) {
			if ( ! get_transient( 'otter_pro_patterns_refetch' ) ) {
				set_transient( 'otter_pro_patterns_refetch', true, HOUR_IN_SECONDS );
			}
			return;
		}

		foreach ( $response as $pattern_block ) {
			if ( ! $this->check_pattern_structure( $pattern_block ) ) {

				if ( ! get_transient( 'otter_pro_patterns_refetch' ) ) {
					set_transient( 'otter_pro_patterns_refetch', true, HOUR_IN_SECONDS );
				}

				return;
			}
		}

		set_transient( 'otter_pro_patterns', $response, WEEK_IN_SECONDS );
	}

	/**
	 * Register Patterns
	 *
	 * @access  public
	 */
	public function register_patterns() {
		$block_patterns = get_transient( 'otter_pro_patterns' );

		if ( ! is_array( $block_patterns ) || 0 === count( $block_patterns ) ) {
			return;
		}

		// Fast check to see if we have some corrupted data.
		if ( ! $this->check_pattern_structure( $block_patterns[0] ) ) {
			delete_transient( 'otter_pro_patterns' );
			return;
		}

		foreach ( $block_patterns as $block_pattern ) {
			if ( ! version_compare( get_bloginfo( 'version' ), $block_pattern['minimum'], '>=' ) ) {
				continue;
			}

			register_block_pattern( 'otter-pro/' . $block_pattern['slug'], $block_pattern );
		}
	}

	/**
	 * Check if the given pattern block has the correct structure.
	 *
	 * @param mixed $pattern_block Pattern block to check.
	 * @return bool
	 */
	public function check_pattern_structure( $pattern_block ) {

		$valid = isset( $pattern_block['slug'] ) && is_string( $pattern_block['slug'] );
		$valid = $valid && isset( $pattern_block['title'] ) && is_string( $pattern_block['title'] );
		$valid = $valid && isset( $pattern_block['content'] ) && is_string( $pattern_block['content'] );
		$valid = $valid && isset( $pattern_block['categories'] ) && is_array( $pattern_block['categories'] );
		return $valid && isset( $pattern_block['minimum'] ) && is_numeric( $pattern_block['minimum'] );
	}

	/**
	 * Singleton method.
	 *
	 * @static
	 *
	 * @return  Patterns
	 * @since   2.0.3
	 * @access  public
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
			self::$instance->init();
		}

		return self::$instance;
	}

	/**
	 * Throw error on object clone
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @access  public
	 * @return  void
	 * @since   2.0.3
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, 'Cheatin&#8217; huh?', '1.0.0' );
	}

	/**
	 * Disable unserializing of the class
	 *
	 * @access  public
	 * @return  void
	 * @since   2.0.3
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, 'Cheatin&#8217; huh?', '1.0.0' );
	}
}
