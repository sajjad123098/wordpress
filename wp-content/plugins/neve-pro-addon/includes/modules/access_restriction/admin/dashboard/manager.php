<?php
/**
 * Access Restriction Module Admin Dashboard Class
 *
 * @package Neve_Pro\Modules\Access_Restriction\Admin\Dashboard
 */
namespace Neve_Pro\Modules\Access_Restriction\Admin\Dashboard;

use Neve_Pro\Modules\Access_Restriction\General_Settings\Module_Settings;
use Neve_Pro\Modules\Access_Restriction\General_Settings\Storage_Adapter;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Type\Post;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Type\Term;

/**
 * Class Manager
 */
class Manager {
	const PAGE_SLUG = 'nv-access-restriction';

	/**
	 * Module Settings
	 *
	 * @var Module_Settings
	 */
	protected $module_settings;

	/**
	 * Manager constructor.
	 */
	public function __construct() {
		$this->module_settings = new Module_Settings();
	}

	/**
	 * Add dashboard menu, register REST route.
	 */
	public function init() {
		add_action( 'rest_api_init', [ $this, 'register_rest_route' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue' ] );
	}

	/**
	 * Register rest route to update settings.
	 *
	 * @return void
	 */
	public function register_rest_route() {
		register_rest_route(
			NEVE_PRO_REST_NAMESPACE,
			'/access-restriction/settings',
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'rest_callback_update_settings' ],
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			]
		);
	}

	/**
	 * REST API callback, update settings.
	 *
	 * @param  \WP_REST_Request $request Request.
	 * @return string
	 */
	public function rest_callback_update_settings( $request ) {
		$settings = json_decode( $request->get_param( 'settings' ), true );

		if ( ! is_array( $settings ) ) {
			return wp_json_encode(
				[
					'status' => 'failed',
				]
			);
		}

		$status = $this->module_settings->update( $settings );

		return wp_json_encode(
			[
				'status' => $status ? 'success' : 'error',
			]
		);
	}

	/**
	 * Get fields to show them in the dashboard.
	 *
	 * @throws \Exception If a content resource type is not supported.
	 * @return array
	 */
	private function get_ui_form_controls() {
		$content_type_settings = $this->module_settings->get_content_type_settings();

		$content_type_fields = [];
		foreach ( $content_type_settings as $content_type_key => $args ) {
			switch ( $args['group'] ) {
				case Post::GROUP:
					$post_type     = $args['post_type'];
					$post_type_obj = get_post_type_object( $post_type );

					if ( $post_type_obj instanceof \WP_Post_Type ) {
						$label = $post_type_obj->label;
					} else {
						/* translators: %s: post type */
						$label = sprintf( __( 'Deleted Post Type (%s)', 'neve' ), $post_type );
					}
					break;

				case Term::GROUP:
					$taxonomy     = $args['taxonomy'];
					$taxonomy_obj = get_taxonomy( $taxonomy );

					if ( $taxonomy_obj instanceof \WP_Taxonomy ) {
						$label = $taxonomy_obj->label;
					} else {
						/* translators: %s: taxonomy */
						$label = sprintf( __( 'Deleted Taxonomy (%s)', 'neve' ), $taxonomy );
					}
					break;
				default:
					throw new \Exception( 'Unknown content type group' );
			}

			$content_type_fields[ $content_type_key ] = [
				'label'         => esc_html( $label ),
				'default_value' => esc_html( $args['enabled'] ),
				'type'          => 'toggle',
			];
		}

		$restriction_behavior_fields = [
			Storage_Adapter::SETTING_KEY_RESTRICTION_BEHAVIOR => [
				'label'         => esc_html__( 'For logged out users:', 'neve' ),
				'default_value' => '',
				'options'       => [
					[
						'label' => __( 'Show 404 page', 'neve' ),
						'value' => Storage_Adapter::RESTRICT_BEHAVIOR_404_PAGE,
					],
					[
						'label' => __( 'Show default Wordpress login page', 'neve' ),
						'value' => Storage_Adapter::RESTRICT_BEHAVIOR_DEFAULT_WP_LOGIN,
					],
					[
						'label' => __( 'Show custom login page', 'neve' ),
						'value' => Storage_Adapter::RESTRICT_BEHAVIOR_CUSTOM_LOGIN_PAGE,
					],
				],
				'type'          => 'select',
			],
			Storage_Adapter::SETTING_KEY_CUSTOM_LOGIN_PAGE_ID => [
				'label'   => esc_html__( 'Custom login page:', 'neve' ),
				'options' => $this->get_options_custom_login_page(),
				'type'    => 'select',
				'parent'  => [
					'fieldKey'   => Storage_Adapter::SETTING_KEY_RESTRICTION_BEHAVIOR,
					'fieldValue' => Storage_Adapter::RESTRICT_BEHAVIOR_CUSTOM_LOGIN_PAGE,
				],
			],
		];

		return [
			'content_types'        => [
				'title'  => __( 'Content Types', 'neve' ),
				'icon'   => '',
				'fields' => $content_type_fields,
			],
			'restriction_behavior' => [
				'title'  => __( 'Restriction Behavior', 'neve' ),
				'icon'   => '',
				'fields' => $restriction_behavior_fields,
			],
			'sidebar'              => [
				'title'  => __( 'General', 'neve' ),
				'icon'   => '',
				'fields' => [],
			],
		];
	}

	/**
	 * Get options for custom login page.
	 *
	 * @return array
	 */
	protected function get_options_custom_login_page() {
		$pages = get_pages();

		$options = [];
		foreach ( $pages as $page ) {
			$options[] = [
				'label' => $page->post_title,
				'value' => $page->ID,
			];
		}

		return $options;
	}

	/**
	 * Enqueue scripts and styles for dashboard page.
	 *
	 * @return void
	 */
	public function enqueue() {
		$screen = get_current_screen();
		if ( ! in_array( $screen->id, [ 'appearance_page_neve-welcome', 'toplevel_page_neve-welcome' ], true ) ) {
			return;
		}

		$dependencies = include NEVE_PRO_SPL_ROOT . 'modules/access_restriction/admin/dashboard/assets/build/index.asset.php';

		wp_register_script( 'neve-access-restriction', NEVE_PRO_URL . 'includes/modules/access_restriction/admin/dashboard/assets/build/index.js', $dependencies['dependencies'], $dependencies['version'], true );
		wp_localize_script( 'neve-access-restriction', 'neveAccessRestriction', $this->get_localization() );
		wp_enqueue_script( 'neve-access-restriction' );

		wp_register_style( 'neve-access-restriction', NEVE_PRO_URL . 'includes/modules/access_restriction/admin/dashboard/assets/build/style-index.css', [ 'wp-components' ], $dependencies['version'] );
		wp_style_add_data( 'neve-access-restriction', 'rtl', 'replace' );
		wp_enqueue_style( 'neve-access-restriction' );
	}

	/**
	 * Get localization which be used in JS.
	 *
	 * @return array
	 */
	private function get_localization() {
		return [
			'assetsURL'     => esc_url( NEVE_PRO_URL . 'includes/modules/access_restriction/admin/dashboard/assets/' ),
			'fields'        => $this->get_ui_form_controls(),
			'options'       => $this->module_settings->get_all(), // current or default options
			'settingsRoute' => NEVE_PRO_REST_NAMESPACE . '/access-restriction/settings',
		];
	}
}
