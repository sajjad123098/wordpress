<?php
/**
 * Module_Settings
 *
 * @package Neve_Pro\Modules\Access_Restriction\General_Settings
 */
namespace Neve_Pro\Modules\Access_Restriction\General_Settings;

use Neve_Pro\Modules\Access_Restriction\General_Settings\Storage_Adapter;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Type\Post as Post_Resource;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Type\Term as Term_Resource;

/**
 * Class Module_Settings
 *
 * The Module_Settings class manages and retrieves general settings
 * for the Access Restriction module.
 *
 * E.g: the ability to check the restriction mode status
 * for specific content resources such as Posts, Pages, and WooCommerce Products.
 */
class Module_Settings {
	const DISABLED_POST_TYPES = [
		'neve_custom_layouts',
		'neve_cart_notices',
		'attachment',
	];

	/**
	 * General settings data.
	 *
	 * @var array
	 */
	protected $data;

	/**
	 * Constructor.
	 *
	 * @throws \Exception If the DB module settings data is not healthy.
	 * @return void
	 */
	public function __construct() {
		$storage_adapter = new Storage_Adapter();

		$settings_data = ( new Storage_Adapter() )->get();

		if ( ! $storage_adapter->is_healthy( $settings_data ) ) {
			throw new \Exception( 'Access Restriction module settings are not healthy.' );
		}

		$this->data = $settings_data;

		add_action( 'init', array( $this, 'inject_extra_content_types' ) );
	}

	/**
	 * Adds new content types on-the-fly.
	 *
	 * For example, if a plugin registers a new custom post type,
	 * this method injects it to appear in the Access Restriction general settings dashboard.
	 *
	 * Similarly, if WooCommerce is enabled,
	 * the WooCommerce product category is injected as a content type.
	 *
	 * @return void
	 */
	public function inject_extra_content_types() {
		$this->inject_settings_for_cpts();
		$this->inject_woocommerce_content_types();
	}

	/**
	 * Get all the general settings data.
	 *
	 * @return array
	 */
	public function get_all() {
		return $this->data;
	}

	/**
	 * Get disabled post types
	 * For these post types, the Access Restriction module is disabled.
	 * (These are not shown in the general settings dashboard)
	 *
	 * @return string[]
	 */
	protected function get_disabled_post_types() {
		/**
		 * Filter the disabled post types.
		 * That is, post types that the Access Restriction module is disabled for,
		 * and not shown in the general settings dashboard.
		 *
		 * @since 3.6.0
		 *
		 * @param string[] $post_types Disabled post types.
		 */
		return apply_filters( 'nv_ar_ct_disabled_post_types', self::DISABLED_POST_TYPES );
	}

	/**
	 * Get supported post types
	 *
	 * Checks the list of registered post types and
	 * returns only the ones that are supported by the Access Restriction module.
	 *
	 * Main criteria is: the post type should support the Block Editor.
	 *
	 * @return string[]
	 */
	protected function get_supported_post_types() {
		$post_types = get_post_types(
			[
				'public'       => true,
				'show_in_rest' => true,
			],
			'names'
		);

		$allowed_post_types = array_diff( array_keys( $post_types ), $this->get_disabled_post_types() );

		$be_compatible_post_types = array_filter(
			$allowed_post_types,
			function( $post_type ) {
				return $this->check_block_editor_support( $post_type ) && post_type_supports( $post_type, 'custom-fields' );
			}
		);

		return $be_compatible_post_types;
	}

	/**
	 * Wrap the use_block_editor_for_post_type function to support older WP versions.
	 *
	 * @param string $post_type The post type.
	 *
	 * @return bool
	 */
	private function check_block_editor_support( $post_type ) {
		if ( function_exists( 'use_block_editor_for_post_type' ) ) {
			return use_block_editor_for_post_type( $post_type );
		}

		if ( ! post_type_exists( $post_type ) ) {
			return false;
		}

		if ( ! post_type_supports( $post_type, 'editor' ) ) {
			return false;
		}

		$post_type_object = get_post_type_object( $post_type );
		if ( $post_type_object && ! $post_type_object->show_in_rest ) {
			return false;
		}

		return true;
	}

	/**
	 * Adds content types for custom post types that are not registered in the Access Restriction DB settings.
	 * This mainly serves the case where a plugin registers a new custom post type and makes it appear in the Access Restriction
	 * General Settings Dashboard until the user saves the settings.
	 *
	 * @return void
	 */
	public function inject_settings_for_cpts() {
		$registered_post_types = $this->get_post_types();

		foreach ( $this->get_supported_post_types() as $custom_post_type ) {
			// if a custom post type is already registered (found in the DB settings), skip it.
			if ( in_array( $custom_post_type, $registered_post_types, true ) ) {
				continue;
			}

			$this->data[ Storage_Adapter::SETTING_KEY_CONTENT_TYPES ][] = [
				'enabled'   => 'no',
				'group'     => Post_Resource::GROUP,
				'post_type' => $custom_post_type,
			];
		}
	}

	/**
	 * Adds the content type for the WooCommerce product category
	 * on-the-fly if it's not registered in the Access Restriction DB settings.
	 *
	 * @return void
	 */
	public function inject_woocommerce_content_types() {
		if ( ! class_exists( 'woocommerce' ) ) {
			return;
		}

		$taxonomy = Storage_Adapter::POST_TYPE_TAXONOMY_MAP['product'];

		if ( in_array( $taxonomy, $this->get_taxonomies(), true ) ) {
			return;
		}

		$this->data[ Storage_Adapter::SETTING_KEY_CONTENT_TYPES ][] = [
			'enabled'  => 'no',
			'group'    => Term_Resource::GROUP,
			'taxonomy' => $taxonomy,
		];
	}

	/**
	 * Get the content type DB settings.
	 *
	 * @return array
	 */
	public function get_content_type_settings() {
		return $this->data[ Storage_Adapter::SETTING_KEY_CONTENT_TYPES ];
	}

	/**
	 * Get the post types in the Access Restriction DB settings.
	 *
	 * @return string[]
	 */
	protected function get_post_types() {
		$post_types = [];

		foreach ( $this->get_content_type_settings() as $args ) {
			if ( $args['group'] === Post_Resource::GROUP ) {
				$post_types[] = $args['post_type'];
			}
		}

		return $post_types;
	}

	/**
	 * This method returns the list of taxonomies in the Access Restriction DB settings,
	 * regardless of whether they are enabled or not.
	 *
	 * @return string[]
	 */
	protected function get_taxonomies() {
		$taxonomies = [];

		foreach ( $this->get_content_type_settings() as $args ) {
			if ( $args['group'] === Term_Resource::GROUP ) {
				$taxonomies[] = $args['taxonomy'];
			}
		}

		return $taxonomies;
	}

	/**
	 * Get the enabled content types for a given group.
	 *
	 * @param  string $group Post_Resource::GROUP or Term_Resource::GROUP etc.
	 * @return string[] sample: ['post', 'page']
	 */
	protected function get_enabled_content_types( $group ) {
		// post_type or taxonomy etc.
		$content_types = [];

		foreach ( $this->get_content_type_settings() as $args ) {
			if ( $args['group'] === $group && $args['enabled'] === 'yes' ) {
				$content_types[] = $args[ $group ];
			}
		}

		return $content_types;
	}

	/**
	 * Get enabled post types.
	 *
	 * @return string[] sample: ['post', 'page']
	 */
	public function get_enabled_post_types() {
		return $this->get_enabled_content_types( Post_Resource::GROUP );
	}

	/**
	 * Get enabled taxonomies.
	 *
	 * @return string[] sample: ['category', 'product_cat']
	 */
	public function get_enabled_taxonomies() {
		return $this->get_enabled_content_types( Term_Resource::GROUP );
	}

	/**
	 * Is the Access Restriction module enabled for a specific post type?
	 *
	 * @param  string $post_type post type name.
	 * @return bool
	 */
	public function is_post_type_enabled( $post_type ) {
		return in_array( $post_type, $this->get_enabled_post_types(), true );
	}

	/**
	 * Checks if the restriction is enabled for a specific taxonomy.
	 *
	 * @param  string $taxonomy Taxonomy name.
	 * @return bool
	 */
	public function is_taxonomy_enabled( $taxonomy ) {
		return in_array( $taxonomy, $this->get_enabled_taxonomies(), true );
	}

	/**
	 * Checks if the Access Restriction module is enabled for
	 * a specific content resource group by a given Content_Resource object.
	 *
	 * E.g: if the Access Restriction module is enabled for WooCommerce Products.
	 *
	 * @param Post_Resource|Term_Resource $resource object representing content resource to check Access Restriction module enablement.
	 * @return bool
	 */
	public function is_enabled_for_resource_type( $resource ) {
		switch ( $resource::GROUP ) {
			case Post_Resource::GROUP:
				return $this->is_post_type_enabled( $resource->get_post_type() );

			case Term_Resource::GROUP:
				return $this->is_taxonomy_enabled( $resource->get_taxonomy() );
		}

		return false;
	}

	/**
	 * Get restriction behavior.
	 *
	 * @return string Storage_Adapter::RESTRICT_BEHAVIOR_DEFAULT_WP_LOGIN|RESTRICT_BEHAVIOR_404_PAGE|RESTRICT_BEHAVIOR_CUSTOM_LOGIN_PAGE
	 */
	public function get_restriction_behavior() {
		return $this->data[ Storage_Adapter::SETTING_KEY_RESTRICTION_BEHAVIOR ];
	}

	/**
	 * Get custom WP login page ID to redirect it to.
	 *
	 * Only relevant if the restriction behavior is set to Storage_Adapter::RESTRICT_BEHAVIOR_CUSTOM_LOGIN_PAGE.
	 *
	 * @return int|false
	 */
	public function get_restriction_custom_login_page_id() {
		if ( $this->get_restriction_behavior() !== Storage_Adapter::RESTRICT_BEHAVIOR_CUSTOM_LOGIN_PAGE ) {
			return false;
		}

		return (int) $this->data[ Storage_Adapter::SETTING_KEY_CUSTOM_LOGIN_PAGE_ID ];
	}

	/**
	 * Get custom WP login page ID to redirect it to for password restriction.
	 *
	 * @return int|false
	 */
	public function get_restriction_password_page_id() {
		if ( ! array_key_exists( Storage_Adapter::SETTING_KEY_CUSTOM_PASSWORD_LOGIN_PAGE_ID, $this->data ) ) {
			return false;
		}

		$page_id = absint( $this->data[ Storage_Adapter::SETTING_KEY_CUSTOM_PASSWORD_LOGIN_PAGE_ID ] );

		if ( ! $page_id ) {
			return false;
		}

		return $page_id;
	}

	/**
	 * Update the Access Restriction DB settings.
	 *
	 * @param  array $settings Entire settings array.
	 * @return bool
	 */
	public function update( $settings ) {
		$storage_adapter = new Storage_Adapter();
		$update          = $storage_adapter->save( $settings );

		if ( false !== $update ) {
			$this->data = $storage_adapter->get();
		}

		return $update;
	}
}
