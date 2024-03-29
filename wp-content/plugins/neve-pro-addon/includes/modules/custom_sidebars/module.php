<?php
/**
 * Custom Sidebars Main Class
 *
 * @package Neve Pro Addon
 */
namespace Neve_Pro\Modules\Custom_Sidebars;

use Neve_Pro\Admin\Conditional_Display;
use Neve_Pro\Admin\Custom_Layouts_Cpt;
use Neve_Pro\Core\Abstract_Module;
use Neve_Pro\Modules\Custom_Layouts\Admin\Layouts_Metabox;

/**
 * Class Module
 *
 * @package Neve_Pro\Modules\Custom_Sidebars
 */
class Module extends Abstract_Module {

	use \Neve_Pro\Traits\Conditional_Display;

	/**
	 * Define module properties.
	 *
	 * @access  public
	 * @return void
	 */
	public function define_module_properties() {
		$this->slug          = 'custom_sidebars';
		$this->name          = __( 'Custom Sidebars', 'neve' );
		$this->description   = __( 'Easily create different set of widgets that can be shown throughout your website. Add display conditions to make the content of your sidebar relevant.', 'neve' );
		$this->documentation = array(
			'url'   => 'https://docs.themeisle.com/article/1770-custom-sidebars-module-documentation',
			'label' => __( 'Learn more', 'neve' ),
		);
		$this->order         = 10;
	}

	/**
	 * Run Custom Sidebars module.
	 */
	public function run_module() {
		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_block_editor_assets' ], 1 );
		add_action( 'registered_post_type_neve_custom_layouts', [ $this, 'register_custom_meta' ] );
		add_action( 'widgets_init', [ $this, 'register_sidebars' ] );
		add_filter( 'neve_custom_layouts_post_type_args', [ $this, 'change_custom_layouts_cpt' ] );
		add_filter( 'neve_before_returning_sidebar_setup', [ $this, 'maybe_get_sidebar_that_matches' ] );
	}

	/**
	 * Enqueue block editor scripts.
	 */
	public function enqueue_block_editor_assets() {
		if ( get_current_screen()->base !== 'widgets' ) {
			return;
		}

		if ( ! function_exists( 'wp_use_widgets_block_editor' ) ) {
			return;
		}

		if ( ! wp_use_widgets_block_editor() ) {
			return;
		}

		$app_assets = plugin_dir_path( __FILE__ ) . 'assets/build/app.asset.php';
		if ( ! file_exists( $app_assets ) ) {
			return;
		}

		$assets = require_once $app_assets;

		wp_enqueue_script(
			'neve-custom-sidebars-admin-scripts',
			NEVE_PRO_INCLUDES_URL . 'modules/custom_sidebars/assets/build/app.js',
			$assets['dependencies'],
			$assets['version'],
			true
		);

		wp_localize_script(
			'neve-custom-sidebars-admin-scripts',
			'neveCustomLayouts',
			array(
				'conditionMap'   => Conditional_Display::create_custom_layouts_condition_text_map(),
				'sidebarOptions' => Layouts_Metabox::get_sidebar_select_options(),
				'renderDebug'    => ( defined( 'REACT_RENDER_DEBUG' ) && REACT_RENDER_DEBUG ) ? 'true' : 'false',
				'ajaxOptions'    => rest_url( NEVE_PRO_REST_NAMESPACE . '/custom-layouts/options' ),
				'nonce'          => wp_create_nonce( 'wp_rest' ),
			)
		);

		wp_register_style(
			'neve-custom-sidebars',
			NEVE_PRO_INCLUDES_URL . 'modules/custom_sidebars/assets/build/style-app.css',
			[],
			$assets['version']
		);

		wp_style_add_data( 'neve-custom-sidebars', 'rtl', 'replace' );
		wp_style_add_data( 'neve-custom-sidebars', 'suffix', '.min' );
		wp_enqueue_style( 'neve-custom-sidebars' );
	}

	/**
	 * Register meta for custom post type.
	 * To be updated from the Widgets Section.
	 *
	 * @return void
	 */
	public function register_custom_meta() {
		$meta_to_register = [
			'custom-layout-conditional-logic' => [
				'type'              => 'string',
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
			],
			'cs-layout'                       => [
				'type'              => 'boolean',
				'default'           => false,
				'sanitize_callback' => function ( $value ) {
					return (bool) $value;
				},
			],
		];

		foreach ( $meta_to_register as $meta => $props ) {
			register_post_meta(
				'neve_custom_layouts',
				$meta,
				[
					'type'              => $props['type'],
					'default'           => $props['default'],
					'single'            => true,
					'show_in_rest'      => true,
					'sanitize_callback' => $props['sanitize_callback'],
					'auth_callback'     => function () {
						return current_user_can( 'edit_posts' );
					},
				]
			);
		}
	}

	/**
	 * Register sidebar areas.
	 */
	public function register_sidebars() {
		$sidebars = Custom_Layouts_Cpt::get_custom_sidebars();
		if ( empty( $sidebars ) ) {
			return;
		}

		foreach ( $sidebars as $id => $data ) {
			register_sidebar(
				[
					'name'          => $data['title'],
					'id'            => 'nv-custom-sidebar-' . $id,
					'description'   => esc_html__( 'Neve Custom Widget Area', 'neve' ),
					'before_widget' => '<div id="%1$s" class="widget %2$s">',
					'after_widget'  => '</div>',
					'before_title'  => '<p class="widget-title">',
					'after_title'   => '</p>',
				]
			);
		}
	}

	/**
	 * Add support in custom layouts for title and custom fields.
	 * This function is required in case the Custom Layouts module is deactivated.
	 *
	 * @param array $config the CPT configuration array.
	 *
	 * @return array
	 */
	public function change_custom_layouts_cpt( $config ) {
		return array_merge(
			$config,
			[
				'supports' => array_unique( array_merge( $config['supports'], array( 'custom-fields', 'title' ) ) ),
			]
		);
	}

	/**
	 * Returns the name of the first sidebar that fulfills conditions.
	 *
	 * @return string
	 */
	public function maybe_get_sidebar_that_matches( $sidebar_setup ) {
		$all_sidebars = Custom_Layouts_Cpt::get_custom_sidebars();
		if ( empty( $all_sidebars ) ) {
			return $sidebar_setup;
		}

		foreach ( $all_sidebars as $id => $data ) {
			if ( empty( $data['conditions'] ) ) {
				continue;
			}
			if ( $this->check_conditions( $id ) ) {
				$sidebar_setup['sidebar_slug'] = 'nv-custom-sidebar-' . $id;
				return $sidebar_setup;
			}
		}

		return $sidebar_setup;
	}
}
