<?php
/**
 * Custom Layouts Main Class
 *
 * @package Neve Pro Addon
 */

namespace Neve_Pro\Modules\Custom_Layouts;

use Neve_Pro\Admin\Custom_Layouts_Cpt;
use Neve_Pro\Core\Abstract_Module;
use Neve_Pro\Core\Loader as CoreLoader;
use Neve_Pro\Modules\Custom_Layouts\Admin\Builders\Loader;
use Neve_Pro\Modules\Custom_Layouts\Admin\Layouts_Metabox;
use Neve_Pro\Modules\Custom_Layouts\Elementor\Elementor_Widgets_Manager;
use Neve_Pro\Admin\Conditional_Display;
use Neve_Pro\Modules\Custom_Layouts\Patterns\Manager;

/**
 * Class Module
 *
 * @package Neve_Pro\Modules\Custom_Layouts
 */
class Module extends Abstract_Module {


	/**
	 * Holds the base module namespace
	 * Used to load submodules.
	 *
	 * @var string $module_namespace
	 */
	private $module_namespace = 'Neve_Pro\Modules\Custom_Layouts';

	/**
	 * Define module properties.
	 *
	 * @access  public
	 * @return void
	 *
	 * @version 1.0.0
	 */
	public function define_module_properties() {
		$this->slug          = 'custom_layouts';
		$this->name          = __( 'Custom Layouts', 'neve' );
		$this->description   = __( 'Easily create custom headers and footers as well as adding your own custom code or content in any location across your site and display them conditionally.', 'neve' );
		$this->documentation = array(
			'url'   => 'https://docs.themeisle.com/article/1062-custom-layouts-module',
			'label' => __( 'Learn more', 'neve' ),
		);
		$this->order         = 6;
	}

	/**
	 * Check if module should load.
	 *
	 * @return bool
	 */
	function should_load() {
		return $this->is_active();
	}

	/**
	 * Run Custom Layouts module.
	 * This function runs at init hook which is too early for public actions in Beaver Builder, so we need to stall it a bit.
	 */
	function run_module() {
		$this->do_admin_actions();
		add_action( 'init', array( $this, 'run_public' ) );
		add_filter( 'neve_custom_layouts_post_type_args', [ $this, 'change_custom_layouts_cpt' ], 11 );
		add_action( 'registered_post_type_neve_custom_layouts', array( $this, 'register_custom_meta' ) );

		$patterns_manager = Manager::get_instance();
		$patterns_manager->load_patterns();
	}

	/**
	 * Register meta for custom post type.
	 * To be updated from the React Sidebar.
	 *
	 * @return void
	 */
	final public function register_custom_meta() {

		$meta_to_register = [
			Layouts_Metabox::META_LAYOUTS        => [
				'type'    => 'string',
				'default' => 'none',
			],
			Layouts_Metabox::META_HOOKS          => [
				'type'    => 'string',
				'default' => 'none',
			],
			Layouts_Metabox::META_SIDEBAR        => [
				'type'    => 'string',
				'default' => 'blog',
			],
			Layouts_Metabox::META_SIDEBAR_ACTION => [
				'type'    => 'string',
				'default' => 'none',
			],
			Layouts_Metabox::META_HAS_EXPIRATION => [
				'type'    => 'boolean',
				'default' => false,
			],
			Layouts_Metabox::META_EXPIRATION     => [
				'type'    => 'string',
				'default' => '',
			],
			Layouts_Metabox::META_INSIDE         => [
				'type'    => 'string',
				'default' => 'none',
			],
			Layouts_Metabox::META_EVENTS_NO      => [
				'type'    => 'integer',
				'default' => 1,
			],
			Layouts_Metabox::META_PRIORITY       => [
				'type'    => 'integer',
				'default' => 10,
			],
			Layouts_Metabox::META_CONDITIONAL    => [
				'type'    => 'string',
				'default' => '',
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
					'sanitize_callback' => 'sanitize_text_field',
					'auth_callback'     => function () {
						return current_user_can( 'edit_posts' );
					},
				]
			);
		}

	}

	/**
	 * Run public actions
	 */
	public function run_public() {
		if ( $this->should_do_public_actions() !== true ) {
			return false;
		}
		$this->do_public_actions();
		return true;
	}

	/**
	 * Get show in menu parameter.
	 *
	 * @return bool|string
	 */
	private function get_show_in_menu() {
		if ( current_user_can( 'administrator' ) ) {
			return ! CoreLoader::has_compatibility( 'theme_dedicated_menu' ) ? 'themes.php' : false;
		}

		return false;
	}

	/**
	 * Make the Custom Layouts CPT public.
	 *
	 * @param array $config the CPT configuration array.
	 *
	 * @return array
	 * @hooked \Neve_Pro\Admin\Custom_Layouts_Cpt
	 */
	public function change_custom_layouts_cpt( $config ) {

		return array_merge(
			$config,
			[
				'public'            => true,
				'show_in_menu'      => $this->get_show_in_menu(),
				'show_ui'           => current_user_can( 'administrator' ),
				'show_in_admin_bar' => current_user_can( 'administrator' ),
				'supports'          => array_unique( array_merge( $config['supports'], array( 'custom-fields', 'thumbnail', 'comments', 'author', 'excerpt', 'page-attributes' ) ) ),
			]
		);
	}

	/**
	 * Do admin related actions.
	 */
	private function do_admin_actions() {
		$this->load_submodules();
		$this->run_hooks();

		return true;
	}

	/**
	 * Load admin files.
	 */
	private function load_submodules() {
		$submodules = array(
			$this->module_namespace . '\Rest\Server',
			$this->module_namespace . '\Admin\Layouts_Metabox',
			$this->module_namespace . '\Admin\PHP_Editor_Admin',
			$this->module_namespace . '\Admin\View_Hooks',
		);

		$mods = [];
		foreach ( $submodules as $index => $mod ) {
			if ( class_exists( $mod ) ) {
				$mods[ $index ] = new $mod();
				$mods[ $index ]->init();
			}
		}
	}

	/**
	 * Add hooks and filters.
	 */
	private function run_hooks() {
		/**
		 * Allow custom layouts cpt to be edited with Beaver Builder.
		 */
		if ( class_exists( 'FLBuilderModel', false ) ) {
			add_filter( 'fl_builder_post_types', array( $this, 'beaver_compatibility' ), 10, 1 );
		}

		/**
		 * Add a custom template for Custom Layouts cpt preview.
		 */
		add_filter( 'single_template', array( $this, 'custom_layouts_single_template' ) );

		/**
		 * Enqueue admin scripts and styles.
		 */
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		/**
		 * Enqueue sidebar scripts
		 */
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_assets' ) );

		/**
		 * Add support for Brizy.
		 */
		add_filter( 'brizy_supported_post_types', array( $this, 'register_brizy_support' ) );

		/** Drop page templates for custom layouts post type */
		add_filter( 'theme_neve_custom_layouts_templates', '__return_empty_array', PHP_INT_MAX );

		/**
		 * Register Elementor widget.
		 */
		$elementor_widget_manager = new Elementor_Widgets_Manager();
		$elementor_widget_manager->run();

		/**
		 * Register shortcode widget.
		 */
		add_shortcode( 'nv-custom-layout', array( $this, 'custom_layout_shortcode' ) );
	}

	/**
	 * Add support for brizy editor in custom layouts.
	 *
	 * @param array $post_types Brizy post types support.
	 *
	 * @return array
	 */
	public function register_brizy_support( $post_types ) {
		$post_types[] = 'neve_custom_layouts';

		return $post_types;
	}

	/**
	 * Check if public actions should occur.
	 *
	 * @return bool
	 */
	private function should_do_public_actions() {
		if ( $this->is_builder_preview() ) {
			return true;
		}

		$posts_array = Custom_Layouts_Cpt::get_custom_layouts();
		if ( empty( $posts_array ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Check if is builder preview.
	 *
	 * @return bool
	 */
	private function is_builder_preview() {
		if ( array_key_exists( 'preview', $_GET ) && ! empty( $_GET['preview'] ) ) {
			return true;
		}

		if ( array_key_exists( 'elementor-preview', $_GET ) && ! empty( $_GET['elementor-preview'] ) ) {
			return true;
		}

		if ( array_key_exists( 'brizy-edit', $_GET ) && ! empty( $_GET['brizy-edit'] ) ) {
			return true;
		}

		if ( class_exists( 'FLBuilderModel', false ) ) {
			if ( \FLBuilderModel::is_builder_active() === true ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Load public files.
	 */
	private function do_public_actions() {
		if ( is_admin() ) {
			return false;
		}

		$loader = new Loader( $this->module_namespace . '\Admin\Builders\\' );

		return true;
	}

	/**
	 * Add Beaver Builder Compatibility
	 *
	 * @param array $value Post types.
	 *
	 * @return array
	 */
	public function beaver_compatibility( $value ) {
		$value[] = 'neve_custom_layouts';

		return $value;
	}

	/**
	 * Set path to neve_custom_layouts template.
	 *
	 * @param string $single Path to single.php .
	 *
	 * @return string
	 */
	public function custom_layouts_single_template( $single ) {
		global $post;
		if ( $post->post_type === 'neve_custom_layouts' && file_exists( plugin_dir_path( __FILE__ ) . 'admin/template.php' ) ) {
			return plugin_dir_path( __FILE__ ) . 'admin/template.php';
		}

		return $single;
	}

	/**
	 * Enqueue Gutenberg editor sidebar script
	 */
	public function enqueue_editor_assets() {
		$screen = get_current_screen();
		if ( 'neve_custom_layouts' !== $screen->post_type ) {
			return; // disabled for other custom post types
		}

		$relative_path = 'includes/modules/custom_layouts/assets/app/';
		$dependencies  = include NEVE_PRO_PATH . $relative_path . '/build/app.asset.php';
		wp_enqueue_script(
			'neve-pro-addon-custom-layout-sidebar',
			NEVE_PRO_URL . $relative_path . 'build/app.js',
			array_merge( $dependencies['dependencies'], [ 'wp-edit-post', 'wp-element', 'wp-components', 'wp-plugins', 'wp-data' ] ),
			$dependencies['version'],
			true
		);

		wp_register_style(
			'neve-pro-addon-custom-layout-sidebar',
			NEVE_PRO_URL . $relative_path . 'build/style-app.css',
			[
				'neve-components',
				'dashicons',
			],
			$dependencies['version']
		);
		wp_style_add_data( 'neve-pro-addon-custom-layout-sidebar', 'rtl', 'replace' );
		wp_style_add_data( 'neve-pro-addon-custom-layout-sidebar', 'suffix', '.min' );
		wp_enqueue_style( 'neve-pro-addon-custom-layout-sidebar' );
	}

	/**
	 * Enqueue scripts.
	 */
	public function admin_enqueue_scripts() {
		global $pagenow;
		if ( ! in_array( $pagenow, array( 'post.php', 'post-new.php', 'edit.php' ), true ) ) {
			return;
		}

		global $post;
		$post_type = $post !== null ? $post_type = $post->post_type : '';

		if ( empty( $post_type ) && isset( $_GET['post_type'] ) ) {
			$post_type = sanitize_text_field( $_GET['post_type'] );
		}

		if ( $post_type !== 'neve_custom_layouts' ) {
			return;
		}

		if ( ! function_exists( 'wp_enqueue_code_editor' ) ) {
			return;
		}

		wp_enqueue_code_editor(
			array(
				'type'       => 'application/x-httpd-php',
				'codemirror' => array(
					'indentUnit' => 2,
					'tabSize'    => 2,
				),
			)
		);

		if ( in_array( $pagenow, array( 'edit.php' ), true ) ) {
			$relative_path = 'includes/modules/custom_layouts/assets/js/';
			$dependencies  = include NEVE_PRO_PATH . $relative_path . '/build/modal.asset.php';
			$script_handle = 'neve-pro-addon-custom-layout-modal';
			wp_enqueue_script( $script_handle, NEVE_PRO_INCLUDES_URL . 'modules/custom_layouts/assets/js/build/modal.js', array_merge( $dependencies['dependencies'], [ 'wp-edit-post', 'wp-element', 'wp-components', 'wp-plugins', 'wp-data' ] ), NEVE_PRO_VERSION, true );
			wp_localize_script(
				$script_handle,
				'neveCustomLayouts',
				array(
					'newLayoutUrl'        => esc_url( admin_url( 'post-new.php?post_type=neve_custom_layouts' ) ),
					'customLayoutOptions' => Layouts_Metabox::get_modal_select_options(),
				)
			);
			$this->rtl_enqueue_style( 'neve-pro-addon-custom-layout-modal', NEVE_PRO_INCLUDES_URL . 'modules/custom_layouts/assets/modal.min.css', array(), NEVE_PRO_VERSION );
			return;
		}

		$is_gutenberg  = get_current_screen()->is_block_editor();
		$script_handle = 'neve-pro-addon-custom-layout-sidebar';
		if ( ! $is_gutenberg ) {
			wp_enqueue_script( 'neve-pro-addon-custom-layout', NEVE_PRO_INCLUDES_URL . 'modules/custom_layouts/assets/js/build/script.js', array(), NEVE_PRO_VERSION, true );
			$script_handle = 'neve-pro-addon-custom-layout';
		}


		wp_localize_script(
			$script_handle,
			'neveCustomLayouts',
			array(
				'customEditorEndpoint' => rest_url( '/wp/v2/neve_custom_layouts/' . $post->ID ),
				'ajaxOptions'          => rest_url( NEVE_PRO_REST_NAMESPACE . '/custom-layouts/options' ),
				'nonce'                => wp_create_nonce( 'wp_rest' ),
				'phpError'             => esc_html__( 'There are some errors in your PHP code. Please fix them before saving the code.', 'neve' ),
				'magicTags'            => Layouts_Metabox::$magic_tags,
				'strings'              => array(
					'magicTagsDescription' => esc_html__( 'You can add the following tags in your template:', 'neve' ),
					'individualLayoutShd'  => Layouts_Metabox::get_shortcode_info(),
					'copiedToClipboard'    => esc_html__( 'Copied to clipboard', 'neve' ),
					'replace'              => esc_html__( 'By selecting this option, the whole sidebar will be replaced with the content of this post.', 'neve' ),
					'append'               => esc_html__( 'By selecting this option, the content of this post will be added just after the sidebar.', 'neve' ),
					'prepend'              => esc_html__( 'By selecting this option, the content of this post will be added just before the sidebar.', 'neve' ),
				),
				'conditionMap'         => Conditional_Display::create_custom_layouts_condition_text_map(),
				'sidebarOptions'       => Layouts_Metabox::get_sidebar_select_options(),
				'renderDebug'          => ( defined( 'REACT_RENDER_DEBUG' ) && REACT_RENDER_DEBUG ) ? 'true' : 'false',
			)
		);

		$this->rtl_enqueue_style( 'neve-pro-addon-custom-layouts', NEVE_PRO_INCLUDES_URL . 'modules/custom_layouts/assets/admin_style.min.css', array(), NEVE_PRO_VERSION );
	}

	/**
	 * Shortcode for custom layouts.
	 *
	 * @param array $attrs Shortcode attributes.
	 *
	 * @return false|string
	 */
	public function custom_layout_shortcode( $attrs ) {

		$attributes = shortcode_atts(
			array(
				'id' => 'none',
			),
			$attrs
		);

		$user_can_edit = current_user_can( 'editor' ) || current_user_can( 'administrator' );
		if ( (int) $attributes['id'] === get_the_ID() ) {
			if ( $user_can_edit ) {
				return esc_html__( 'You cannot have the shortcode of a custom layout in the same custom layout.', 'neve' );
			}
			return false;
		}

		if ( 'none' === $attributes['id'] ) {
			if ( $user_can_edit ) {
				return esc_html__( 'You need to add the id attribute of the custom layout you want to display in shortcode parameters. E.g, [nv-custom-layout id="123"]', 'neve' );
			}
			return false;
		}

		if ( 'neve_custom_layouts' !== get_post_type( (int) $attributes['id'] ) ) {
			if ( $user_can_edit ) {
				/* translators: %s is post id */
				return sprintf( esc_html__( 'The custom layout with id %s does not exist.', 'neve' ), $attributes['id'] );
			}
			return false;
		}

		$layout = get_post_meta( (int) $attributes['id'], 'custom-layout-options-layout', true );
		if ( 'individual' !== $layout ) {
			if ( $user_can_edit ) {
				return esc_html__( 'The layout that you\'ve selected is not of "individual" type.', 'neve' );
			}
			return false;
		}

		ob_start();
		do_action( 'neve_do_individual', $attributes['id'] );
		return ob_get_clean();
	}
}
