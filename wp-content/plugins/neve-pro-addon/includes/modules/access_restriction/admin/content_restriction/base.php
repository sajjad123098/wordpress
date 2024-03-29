<?php
/**
 * Base
 *
 * @package Neve_Pro\Modules\Access_Restriction
 */
namespace Neve_Pro\Modules\Access_Restriction\Admin\Content_Restriction;

use Neve_Pro\Modules\Access_Restriction\Content_Resource\Settings\Resource_Settings;
use Neve_Pro\Modules\Access_Restriction\General_Settings\Module_Settings;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Type\Post as Post_Resource;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Type\Term as Term_Resource;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Authorization\Type\Password;

/**
 * Class Base
 */
abstract class Base {
	/**
	 * Module general settings
	 *
	 * @var Module_Settings
	 */
	protected $module_settings;

	const ASSETS_RELATIVE_PATH = 'includes/modules/access_restriction/admin/content_restriction/assets/build/feature/';

	/**
	 * Resource settings
	 *
	 * @var Resource_Settings
	 */
	protected $resource_settings;

	/**
	 * UI assets path
	 *
	 * @var string
	 */
	protected $assets_path = NEVE_PRO_PATH . self::ASSETS_RELATIVE_PATH;

	/**
	 * UI assets URL
	 *
	 * @var string
	 */
	protected $assets_url = NEVE_PRO_URL . self::ASSETS_RELATIVE_PATH;

	/**
	 * Resource which is being edited.
	 *
	 * @var Post_Resource|Term_Resource|false
	 */
	protected $resource;

	/**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct() {
		$this->module_settings = new Module_Settings();
	}

	/**
	 * Init
	 */
	public function init() {
		if ( $this->is_the_current_screen() ) {
			$this->register_hooks();
		}

		$this->register_background_hooks();
	}

	/**
	 * Sets the resource for the post or term on the currently visited admin screen.
	 *
	 * @return void
	 */
	abstract protected function set_resource();

	/**
	 * Fills $this->resource_settings with the current resource settings.
	 *
	 * @return void
	 */
	public function register_resource_settings() {
		$this->set_resource();

		if ( false === $this->resource ) {
			return;
		}

		$module_is_enabled = $this->module_settings->is_enabled_for_resource_type( $this->resource );

		// If the Access Restriction module is not enabled for that resource group, do not load the setting UI.
		if ( ! $module_is_enabled ) {
			return;
		}

		$this->resource_settings = new Resource_Settings( $this->resource );
		do_action( 'nv_ac_resource_settings_registered' );
	}

	/**
	 * Register the hooks for the background process.
	 * That are called even though user is not visiting the post edit screen.
	 *
	 * @return void
	 */
	abstract protected function register_background_hooks();

	/**
	 * A hook to register the resource settings.
	 *
	 * @return string
	 */
	abstract protected function get_resource_settings_reg_hook();

	/**
	 * Register the hooks for the render.
	 * That method is called only when the user is visiting the properly screen.
	 *
	 * @return void
	 */
	protected function register_hooks() {
		add_action(
			$this->get_resource_settings_reg_hook(),
			array( $this, 'register_resource_settings' )
		);

		// Scripts requires registered storage adapter to get current values.
		add_action( 'nv_ac_resource_settings_registered', [ $this, 'enqueue_scripts' ] );
	}

	/**
	 * Check if the current screen is the one that we want to render the UI.
	 *
	 * @return bool
	 */
	abstract protected function is_the_current_screen();

	/**
	 * Enqueue scripts
	 *
	 * @return void
	 */
	abstract protected function enqueue_scripts();

	/**
	 * Get the data needed by the JS.
	 *
	 * @return array
	 */
	protected function get_localize_data() {
		return apply_filters(
			'nv_ar_edit_content_type_localize',
			[
				'currentValues'    => [
					'allowedUserIdOptions' => $this->get_current_allowed_user_id_options(),
					'allowedUserRoles'     => $this->get_current_allowed_user_role_options(),
				],
				'availableOptions' => [
					'userRoles' => $this->get_all_roles(),
				],
				'allowedPassChars' => esc_html( Password::ALLOWED_CHARS ),
			]
		);
	}

	/**
	 * This method returns the options for currently registered allowed user roles
	 * that can be used in a select component in a React-based user interface.
	 *
	 * @return array sample: [ [ 'value' => 'administrator', 'label' => 'Administrator' ] ]
	 */
	protected function get_current_allowed_user_role_options() {
		$roles = $this->resource_settings->get_allowed_user_roles();

		if ( 0 === count( $roles ) ) {
			return [];
		}

		$roles = $this->get_all_roles( $roles );

		return $roles;
	}

	/**
	 * Get all roles in the WP
	 *
	 * @param  array|string $allowed_role_slugs Allowed role slugs.
	 * @return array sample: [ [ 'value' => 'administrator', 'label' => 'Administrator' ] ]
	 */
	protected function get_all_roles( $allowed_role_slugs = 'all' ) {
		$roles = wp_roles();

		if ( ( ! $roles instanceof \WP_Roles ) ) {
			return [];
		}

		$data = [];

		foreach ( $roles->get_names() as $slug => $name ) {
			if ( $allowed_role_slugs !== 'all' && is_array( $allowed_role_slugs ) && ! in_array( $slug, $allowed_role_slugs ) ) {
				continue;
			}

			$data[] = [
				'value' => $slug,
				'label' => esc_html( $name ),
			];
		}

		return $data;
	}

	/**
	 * This method returns the options for currently registered allowed users
	 * that can be used in a select component in a React-based user interface.
	 *
	 * @return array sample: [ [ 'value' => 1, 'label' => 'John Doe' ] ]
	 */
	protected function get_current_allowed_user_id_options() {
		$user_ids = $this->resource_settings->get_allowed_user_ids();

		if ( 0 === count( $user_ids ) ) {
			$user_ids = [];
		}

		return array_map(
			function( $user_id ) {
				$user = get_user_by( 'id', $user_id );

				if ( $user instanceof \WP_User ) {
					$username = $user->display_name;
				} else {
					/* translators: %d: user id */
					$username = sprintf( __( 'Deleted user (id: %d)', 'neve' ), $user_id ); // the user is not exist, we assume it's deleted.
				}

				return [
					'value' => (int) $user_id,
					'label' => esc_html( $username ),
				];
			},
			$user_ids
		);
	}
}
