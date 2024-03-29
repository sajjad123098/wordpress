<?php
/**
 * Storage_Adapter
 *
 * @package Neve_Pro\Modules\Access_Restriction\General_Settings
 */
namespace Neve_Pro\Modules\Access_Restriction\General_Settings;

use Neve_Pro\Core\Loader;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Type\Post;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Type\Term;

/**
 * Storage_Adapter
 *
 * Formats and validates the Access Restriction Module's settings
 * stored in the 'wp_options' database table as ready to use.
 */
class Storage_Adapter {
	/**
	 * The 'option_name' field in the 'wp_options' database table stores the name of
	 * a centralized option that contains all settings for the Access Restriction module.
	 */
	const WP_OPTION_NAME = 'neve_access_restriction';

	/**
	 * Helps to understand option structure changes.
	 * Beneficial for future migrations.
	 *
	 * @since 3.6.0 Here since the module is released.
	 * @var string
	 */
	const STRUCTURE_VERSION = '1.0';

	const RESTRICT_BEHAVIOR_DEFAULT_WP_LOGIN  = 'redirect_wp_login';
	const RESTRICT_BEHAVIOR_404_PAGE          = 'redirect_404_page';
	const RESTRICT_BEHAVIOR_CUSTOM_LOGIN_PAGE = 'redirect_custom_page';

	const SETTING_KEY_CONTENT_TYPES                 = 'content_types';
	const SETTING_KEY_STRUCTURE_VERSION             = 'structure_version';
	const SETTING_KEY_RESTRICTION_BEHAVIOR          = 'restriction_behavior';
	const SETTING_KEY_CUSTOM_LOGIN_PAGE_ID          = 'restriction_custom_login_page_id';
	const SETTING_KEY_CUSTOM_PASSWORD_LOGIN_PAGE_ID = 'restriction_custom_password_login_page_id';

	const POST_TYPE_POST = 'post';
	const POST_TYPE_PAGE = 'page';

	/**
	 * Keys are post_type
	 * Values are taxonomy name.
	 *
	 * @var array
	 */
	const POST_TYPE_TAXONOMY_MAP = [
		'post'    => 'category',
		'product' => 'product_cat',
	];

	/**
	 * All settings data of the Access Restriction module.
	 *
	 * @var array|false
	 */
	protected $data = false;

	/**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct() {
		$this->data = get_option( self::WP_OPTION_NAME, self::get_default_value() );
	}

	/**
	 * Get the default value of the centralized option.
	 *
	 * @return array
	 */
	public static function get_default_value() {
		return [
			self::SETTING_KEY_STRUCTURE_VERSION    => self::STRUCTURE_VERSION,
			self::SETTING_KEY_CONTENT_TYPES        => [
				[
					'enabled'   => 'no',
					'group'     => Post::GROUP,
					'post_type' => self::POST_TYPE_POST,
				],
				[
					'enabled'   => 'no',
					'group'     => Post::GROUP,
					'post_type' => self::POST_TYPE_PAGE,
				],
				[
					'enabled'  => 'no',
					'group'    => Term::GROUP,
					'taxonomy' => self::POST_TYPE_TAXONOMY_MAP['post'],
				],
			],
			self::SETTING_KEY_RESTRICTION_BEHAVIOR => self::RESTRICT_BEHAVIOR_DEFAULT_WP_LOGIN,
			self::SETTING_KEY_CUSTOM_PASSWORD_LOGIN_PAGE_ID => 0,
		];
	}

	/**
	 * Get default content type settings.
	 *
	 * @return array
	 */
	public static function get_default_content_type_settings() {
		return self::get_default_value()[ self::SETTING_KEY_CONTENT_TYPES ];
	}

	/**
	 * Returns settings data.
	 *
	 * @return array|false
	 */
	public function get() {
		return $this->data;
	}

	/**
	 * Update the settings data.
	 *
	 * @param  array $settings Entire settings data.
	 * @throws \Exception If the settings are not healthy.
	 * @return bool
	 */
	public function save( $settings ) {
		if ( ! $this->is_healthy( $settings ) ) {
			throw new \Exception( __( 'Settings are not healthy.', 'neve' ) );
		}

		$updated = update_option( self::WP_OPTION_NAME, $settings );

		if ( ! $updated ) {
			return false;
		}

		if ( Loader::has_compatibility( 'track' ) ) {
			\Neve\Core\Tracker::track(
				array(
					array(
						'feature'          => 'access-restriction',
						'featureComponent' => 'module-settings',
						'featureValue'     => $settings,
					),
				)
			);
		}

		$this->data = $settings;
		return true;
	}

	/**
	 * Is the data healthy to use?
	 *
	 * @return array|false
	 */
	public static function is_healthy( $data ) {
		if ( ! is_array( $data ) ) {
			return false;
		}

		$mandatory_keys = [
			self::SETTING_KEY_STRUCTURE_VERSION,
			self::SETTING_KEY_CONTENT_TYPES,
			self::SETTING_KEY_RESTRICTION_BEHAVIOR,
			self::SETTING_KEY_CUSTOM_PASSWORD_LOGIN_PAGE_ID,
		];

		if ( count( array_diff( $mandatory_keys, array_keys( $data ) ) ) > 0 ) {
			return false;
		}

		$content_types = $data[ self::SETTING_KEY_CONTENT_TYPES ];

		foreach ( $content_types as $args ) {
			if ( ! is_array( $args ) ) {
				return false;
			}

			$mandatory_keys = [
				'enabled',
				'group',
			];

			if ( count( array_diff( $mandatory_keys, array_keys( $args ) ) ) > 0 ) {
				return false;
			}

			if ( count( array_keys( $args ) ) !== 3 ) {
				return false;
			}

			if ( ! in_array( $args['enabled'], [ 'yes', 'no' ], true ) ) {
				return false;
			}

			if ( ! in_array( $args['group'], [ Post::GROUP, Term::GROUP ], true ) ) {
				return false;
			}

			// No health check is applied for their post_type values to the content types with the post_type group, as they are dynamic.

			if ( Term::GROUP === $args['group'] ) {
				if ( ! in_array( $args['taxonomy'], [ self::POST_TYPE_TAXONOMY_MAP['post'], self::POST_TYPE_TAXONOMY_MAP['product'] ], true ) ) {
					return false;
				}
			}
		}

		if ( ! in_array( $data[ self::SETTING_KEY_RESTRICTION_BEHAVIOR ], [ self::RESTRICT_BEHAVIOR_DEFAULT_WP_LOGIN, self::RESTRICT_BEHAVIOR_404_PAGE, self::RESTRICT_BEHAVIOR_CUSTOM_LOGIN_PAGE ], true ) ) {
			return false;
		}

		if ( ! is_int( $data[ self::SETTING_KEY_CUSTOM_PASSWORD_LOGIN_PAGE_ID ] ) ) {
			return false;
		}

		return $data;
	}
}
