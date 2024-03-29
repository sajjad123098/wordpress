<?php
/**
 * Author:          Andrei Baicus <andrei@themeisle.com>
 * Created on:      2019-01-28
 *
 * @package Neve Pro Addon
 */

namespace Neve_Pro\Admin;

use Neve_Pro\Core\Abstract_Module;
use Neve_Pro\Core\Loader;
use Neve_Pro\Traits\Core;

/**
 * Class Dashboard
 *
 * @package Neve Pro Addon
 */
class Dashboard {
	use Core;

	/**
	 * Neve Pro plugin name
	 *
	 * @var string
	 */
	private $plugin_name;

	/**
	 * The app script handle.
	 *
	 * @var string
	 */
	private $script_handle; // @phpstan-ignore-line - only written to, never read. Can be removed if not used in the future.

	/**
	 * The app endpoint.
	 *
	 * @var string
	 */
	private $rest_endpoint;

	/**
	 * Rest Routes Handler
	 *
	 * @var Rest_Server
	 */
	private $rest_server; // @phpstan-ignore-line - only written to, never read. Can be removed if not used in the future.


	/**
	 * Dashboard constructor.
	 */
	public function __construct() {
		$this->plugin_name   = apply_filters( 'ti_wl_plugin_name', NEVE_PRO_NAME );
		$this->script_handle = NEVE_PRO_NAMESPACE . '-dashboard-app';
		$this->rest_endpoint = NEVE_PRO_REST_NAMESPACE;
		$this->rest_server   = new Rest_Server( $this->rest_endpoint );
	}

	/**
	 * Initialize the module.
	 */
	public function init() {
		add_filter( 'neve_dashboard_page_data', [ $this, 'add_dashboard_data' ] );
		add_filter( 'ti_about_config_filter', [ $this, 'add_neve_pro_addons_tab_fallback' ], 20 );
		add_filter( 'ti_tpc_editor_data', [ $this, 'add_tpc_data' ], 20 );
		add_filter( 'neve_dashboard_notifications', array( $this, 'add_dashboard_notifications' ), 1 );
	}

	/**
	 * Add about page tab list item.
	 *
	 * @param array $config about page config.
	 *
	 * @return array
	 */
	public function add_neve_pro_addons_tab_fallback( $config ) {
		$config['custom_tabs']['neve_pro_addons'] = [
			'title'           => $this->plugin_name,
			'render_callback' => [ $this, 'render_fallback_tab_content' ],
		];

		return $config;
	}

	/**
	 * Renders fallback content for the old version of the theme Pro Tab Content.
	 *
	 * @see add_neve_pro_addons_tab_fallback
	 */
	public function render_fallback_tab_content() {
		$theme      = wp_get_theme();
		$theme_name = apply_filters( 'ti_wl_theme_name', $theme->__get( 'Name' ) );
		echo '<h3>';
		/* translators: s - theme name (Neve) */
		echo esc_html( sprintf( __( 'Please update %s to the latest version and then refresh this page to have access to the options.', 'neve' ), ( $theme_name ) ) );
		echo '</h3>';
	}

	/**
	 * Adds the necessary pro dashboard data.
	 *
	 * @param array $data The dashboard localization data from the theme.
	 * @return array
	 */
	public function add_dashboard_data( $data ) {
		$index = apply_filters( 'product_neve_license_plan', -1 );

		return array_merge(
			$data,
			[
				'pro'          => true,
				'proApi'       => rest_url( $this->rest_endpoint ),
				'license'      => [
					'key'         => apply_filters( 'product_neve_license_key', 'free' ),
					'valid'       => apply_filters( 'product_neve_license_status', false ),
					'expiration'  => $this->get_license_expiration_date(),
					'tier'        => $index > -1 ? $this->tier_map[ $index ] : -1,
					'supportData' => [
						'url'  => tsdk_support_link( NEVE_PRO_BASEFILE ),
						'text' => esc_html__( 'Access our Premium Support', 'neve' ),
					],
				],
				'supportURL'   => esc_url( 'https://themeisle.com/contact/' ),
				'modules'      => $this->sort_modules( $this->get_modules() ),
				'upgradeLinks' => $this->get_upgrade_links(),
			]
		);
	}

	/**
	 * Adds the necessary pro TPC data.
	 *
	 * @param array $data The TPC editor localization data.
	 * @return array
	 */
	public function add_tpc_data( $data ) {
		$index = apply_filters( 'product_neve_license_plan', -1 );

		$data['tier'] = $index > -1 ? $this->tier_map[ $index ] : -1;

		return $data;
	}

	/**
	 * Add Dashboard Notifications.
	 *
	 * @package array $notifications Notifications array.
	 *
	 * @return array
	 */
	public function add_dashboard_notifications( $notifications ) {
		$license_data = Loader::get_license_data();

		$auto_renew  = isset( $license_data->auto_renew ) ? $license_data->auto_renew : true;
		$status      = isset( $license_data->license ) ? $license_data->license : 'not_active';
		$expire_date = isset( $license_data->expires ) ? new \DateTime( $license_data->expires ) : false;

		// Bail if license status is not active
		if ( $status === 'not_active' ) {
			return $notifications;
		}

		// Bail if auto-renew is active
		if ( $auto_renew ) {
			return $notifications;
		}

		// Bail if the white label is enabled
		$is_whitelabel = apply_filters( 'neve_is_theme_whitelabeled', false ) || apply_filters( 'neve_is_plugin_whitelabeled', false );
		if ( $is_whitelabel ) {
			return $notifications;
		}

		// Bail if the expiry date is missing.
		if ( ! $expire_date ) {
			return $notifications;
		}

		// Bail if the license expires in more than 14 days
		$now = new \DateTime();
		if ( $expire_date->diff( $now )->days > 14 ) {
			return $notifications;
		}

		$license_name                         = $license_data->item_name ?? '';
		$renew_link                           = Loader::get_action_links( 'renew' );
		$notifications['license-will-expire'] = [
			'text'        => sprintf(
				/* translators: %1$s:License name, %2$s: Expiration date */
				__( 'Hi! Your %1$s subscription will expire on %2$s.', 'neve' ),
				$license_name,
				$license_data->expires
			),
			'url'         => esc_url( $renew_link ),
			'targetBlank' => true,
			'cta'         => __( 'Manage Subscription', 'neve' ),
			'type'        => 'warning',

		];

		return $notifications;
	}

	/**
	 * Get upgrade links.
	 *
	 * @return array
	 */
	private function get_upgrade_links() {
		return array(
			'1' => tsdk_utmify( 'https://themeisle.com/themes/neve/upgrade/', 'upgrade_link', 'neveprosettings' ),
			'2' => tsdk_utmify( 'https://themeisle.com/themes/neve/upgrade/', 'upgrade_link', 'neveprosettings' ),
			'3' => tsdk_utmify( 'https://themeisle.com/themes/neve/upgrade/', 'upgrade_link', 'neveprosettings' ),
		);
	}

	/**
	 * Utility method to sort modules by order key.
	 *
	 * @param array $modules The modules list.
	 *
	 * @return mixed
	 * @since   1.0.0
	 * @access  private
	 */
	private function sort_modules( $modules ) {
		uasort(
			$modules,
			function ( $item1, $item2 ) {
				if ( ! isset( $item1['order'] ) ) {
					return -1;
				}
				if ( ! isset( $item2['order'] ) ) {
					return -1;
				}
				if ( $item1['order'] === $item2['order'] ) {
					return 0;
				}

				return $item1['order'] < $item2['order'] ? -1 : 1;
			}
		);

		return $modules;
	}

	/**
	 * Get modules.
	 *
	 * For the unload option use classes from Neve_Pro\Core\Loader
	 *
	 * @return array
	 */
	private function get_modules() {

		$pluggable_modules = Loader::instance()->get_modules();
		$modules           = array();
		if ( ! empty( $pluggable_modules ) ) {
			/**
			 * Iterates over instances of Abstract_Module
			 *
			 * @var Abstract_Module $module A module instance.
			 */
			foreach ( $pluggable_modules as $module ) {
				$modules = array_merge( $modules, $module->get_module_info() );
			}
		}

		/**
		 * White label module
		 */
		$white_label_settings = get_option( 'ti_white_label_inputs' );
		$white_label_settings = json_decode( $white_label_settings, true );
		if ( isset( $white_label_settings['white_label'] ) ) {
			if ( $white_label_settings['white_label'] === true && isset( $modules['white_label'] ) ) {
				unset( $modules['white_label'] );
			}
		}

		return apply_filters( 'neve_pro_filter_dashboard_modules', $modules );
	}
}
