<?php
/**
 * Edit_Post
 *
 * @package Neve_Pro\Modules\Access_Restriction
 */
namespace Neve_Pro\Modules\Access_Restriction\Admin\Content_Restriction;

use Neve_Pro\Modules\Access_Restriction\Content_Resource\Settings\Resource_Settings;
use Neve_Pro\Modules\Access_Restriction\Admin\Content_Restriction\Base;
use Neve_Pro\Modules\Access_Restriction\Utility\Sanitize;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Resource_Factory;

/**
 * Class Edit_Post
 */
class Edit_Post extends Base {
	use Sanitize;

	/**
	 * Sets the resource for the post on the currently visited post edit screen.
	 *
	 * @return void
	 */
	protected function set_resource() {
		$this->resource = ( new Resource_Factory() )->get_resource( get_post() );
	}

	/**
	 * A hook to register the resource settings.
	 * get_post() is not available on the init hook, so we need to register the settings later.
	 *
	 * @return string
	 */
	protected function get_resource_settings_reg_hook() {
		return 'enqueue_block_editor_assets';
	}

	/**
	 * Checks if the current screen is the post edit screen.
	 *
	 * @return bool
	 */
	protected function is_the_current_screen() {
		global $pagenow;

		return in_array( $pagenow, [ 'post.php', 'post-new.php' ], true );
	}

	/**
	 * Register the hooks for the background process.
	 * That are called even though user is not visiting the post edit screen.
	 *
	 * @return void
	 */
	protected function register_background_hooks() {
		$this->register_post_meta();
	}

	/**
	 * Register the post metas for the post edit screen.
	 *
	 * @return void
	 */
	public function register_post_meta() {
		$meta_needs_register = [
			Resource_Settings::META_KEY_RESTRICTION_TYPES  => [
				'default'           => Resource_Settings::DEFAULT_RESTRICTION_TYPES,
				'schema'            => Resource_Settings::SCHEMA_RESTRICTION_TYPES,
				'sanitize_callback' => array( $this, 'sanitize_restriction_types' ),
			],
			Resource_Settings::META_KEY_ALLOWED_USER_IDS   => [
				'default'           => Resource_Settings::DEFAULT_ALLOWED_USER_IDS,
				'schema'            => Resource_Settings::SCHEMA_ALLOWED_USER_IDS,
				'sanitize_callback' => array( $this, 'sanitize_user_ids' ),
			],
			Resource_Settings::META_KEY_ALLOWED_USER_ROLES => [
				'default'           => Resource_Settings::DEFAULT_ALLOWED_USER_ROLES,
				'schema'            => Resource_Settings::SCHEMA_ALLOWED_USER_ROLES,
				'sanitize_callback' => array( $this, 'sanitize_user_roles' ),
			],
			Resource_Settings::META_KEY_RESTRICTION_PASSWORD => [
				'default'           => Resource_Settings::DEFAULT_RESTRICTION_PASSWORD,
				'schema'            => Resource_Settings::SCHEMA_RESTRICTION_PASSWORD,
				'sanitize_callback' => array( $this, 'sanitize_password_admin' ),
			],
		];

		$enabled_post_types = $this->module_settings->get_enabled_post_types();

		foreach ( $enabled_post_types as $post_type ) {
			foreach ( $meta_needs_register as $key => $args ) {
				$schema = $args['schema'];
				register_post_meta(
					$post_type,
					$key,
					[
						'show_in_rest'      => [
							'schema' => $schema,
						],
						'type'              => $schema['type'],
						'default'           => $args['default'],
						'single'            => true,
						'sanitize_callback' => $args['sanitize_callback'],
						'auth_callback'     => [ $this, 'auth_callback_meta_edit' ],
					]
				);
			}
		}
	}

	/**
	 * Authorization callback to manage Access Restriction settings in the post edit screen.
	 *
	 * @return bool
	 */
	public function auth_callback_meta_edit() {
		return current_user_can( 'edit_posts' );
	}

	/**
	 * Enqueue the scripts for the post edit screen.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		$post_type = get_post_type_object( get_post_type() );

		$support_custom_fields = post_type_supports( get_post_type(), 'custom-fields' );
		$support_editor        = post_type_supports( get_post_type(), 'editor' );
		$support_rest          = $post_type && $post_type->show_in_rest;

		if ( ! $support_custom_fields || ! $support_editor || ! $support_rest ) {
			return;
		}

		$access_restriction_enabled = $this->module_settings->is_post_type_enabled( get_post_type() );

		if ( ! $access_restriction_enabled ) {
			return;
		}

		$dependencies = include $this->assets_path . 'edit-post/index.asset.php';

		wp_register_script( 'neve-access-restriction-pe', $this->assets_url . 'edit-post/index.js', $dependencies['dependencies'], $dependencies['version'], true );

		// should be earlier before the "wp_localize_script" call
		add_filter( 'nv_ar_edit_content_type_localize', [ $this, 'modify_localize_data' ] );
		wp_localize_script( 'neve-access-restriction-pe', 'neveAccessRestriction', $this->get_localize_data() );

		wp_enqueue_script( 'neve-access-restriction-pe' );

		wp_register_style( 'neve-access-restriction-pe', $this->assets_url . 'edit-post/style-index.css', [], $dependencies['version'] );
		wp_enqueue_style( 'neve-access-restriction-pe' );
	}

	/**
	 * Modify the localize data for the post edit screen.
	 *
	 * @param array $localize_data Localize data.
	 * @return array
	 */
	public function modify_localize_data( $localize_data ) {
		$localize_data['metaKeys'] = [
			'restrictionTypes'    => Resource_Settings::META_KEY_RESTRICTION_TYPES,
			'allowedUserIds'      => Resource_Settings::META_KEY_ALLOWED_USER_IDS,
			'allowedUserRoles'    => Resource_Settings::META_KEY_ALLOWED_USER_ROLES,
			'restrictionPassword' => Resource_Settings::META_KEY_RESTRICTION_PASSWORD,
		];

		return $localize_data;
	}
}
