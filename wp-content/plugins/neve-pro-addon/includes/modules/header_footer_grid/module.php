<?php
/**
 * Module Class for Header Footer Grid.
 *
 * Name:    Header Footer Grid Addon
 * Author:  Bogdan Preda <bogdan.preda@themeisle.com>
 *
 * @version 1.0.0
 * @package Neve Pro Addon
 */

namespace Neve_Pro\Modules\Header_Footer_Grid;

use Neve\Customizer\Types\Control;
use Neve_Pro\Admin\Custom_Layouts_Cpt;
use Neve_Pro\Core\Abstract_Module;
use Neve_Pro\Core\Loader;
use Neve_Pro\Modules\Header_Footer_Grid\Builder\Page_Header;
use Neve_Pro\Modules\Header_Footer_Grid\Customizer\Conditional_Headers;
use Neve_Pro\Modules\Header_Footer_Grid\Customizer\Custom_Panel;
use Neve_Pro\Modules\Header_Footer_Grid\Customizer\Sticky_Header;
use Neve_Pro\Modules\Header_Footer_Grid\Customizer\Transparent_Header;
use Neve_Pro\Modules\Header_Footer_Grid\Mega_Menu\Mega_Menu;
use Neve_Pro\Modules\Header_Footer_Grid\Submenu\Submenu;
use Neve_Pro\Modules\Header_Footer_Grid\Taxonomies\Taxonomy_Featured_Image;
use Neve_Pro\Traits\Conditional_Display;
use WP_Customize_Manager;

/**
 * Class Module
 *
 * @package Neve_Pro\Modules\Header_Footer_Grid
 */
class Module extends Abstract_Module {

	use Conditional_Display;

	const MODS_FEATURED_IMAGE_ENABLE     = 'enable_featured_image_taxonomy';
	const MODS_FEATURED_IMAGE_TAXONOMIES = 'featured_image_taxonomies';

	/**
	 * Enqueue script flag.
	 *
	 * @var bool
	 */
	private static $should_add_script = false;

	/**
	 * Allowed taxonomies to display featured image.
	 *
	 * @var array
	 */
	private $allowed_taxonomies = array();

	/**
	 * Should load mega-menu features.
	 *
	 * @var bool
	 */
	private $load_mega_menu = false;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->load_mega_menu = Loader::has_compatibility( 'mega_menu' );
		parent::__construct();
	}


	/**
	 * Define module properties.
	 *
	 * @access  public
	 * @return void
	 *
	 * @version 1.0.0
	 */
	public function define_module_properties() {
		$this->slug          = 'hfg_module';
		$this->name          = __( 'Header Booster', 'neve' );
		$this->description   = __( 'Extend your header with more components and settings, build sticky/transparent headers or display them conditionally.', 'neve' );
		$this->documentation = array(
			'url'   => 'https://docs.themeisle.com/article/1057-header-booster-documentation',
			'label' => __( 'Learn more', 'neve' ),
		);
		$this->order         = 1;

		if ( get_option( 'nv_pro_' . self::MODS_FEATURED_IMAGE_ENABLE ) ) {
			$this->allowed_taxonomies = get_option( 'nv_pro_' . self::MODS_FEATURED_IMAGE_TAXONOMIES, array( 'category' ) );
			add_action( 'init', array( $this, 'init_taxonomy_image' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'set_featured_image' ) );

			add_action(
				'wp',
				function() {
					if ( ! empty( $this->get_taxonomy_featured() ) ) {
						add_filter( 'nv_builder_row_image_url', array( $this, 'should_use_featured_image_url' ), 10, 3 );
					}
				}
			);
		}

	}

	/**
	 * Init the Taxonomy_Featured_Image instance
	 * This class registers and handles the featured image meta attribute for the allowed taxonomies.
	 */
	final public function init_taxonomy_image() {
		Taxonomy_Featured_Image::instance();
	}

	/**
	 * Add the module settings.
	 *
	 * @inheritDoc
	 */
	public function add_module_settings() {
		$options = array();

		$options[ self::MODS_FEATURED_IMAGE_ENABLE ] = array(
			'label'             => __( 'Enable Featured image for taxonomies', 'neve' ),
			'type'              => 'toggle',
			'documentation'     => [
				'url'   => 'http://rviv.ly/iOYp8K',
				'label' => __( 'Learn More', 'neve' ),
			],
			'default'           => 0,
			'show_in_rest'      => true,
			'sanitize_callback' => 'absint',
		);

		$options[ self::MODS_FEATURED_IMAGE_TAXONOMIES ] = array(
			'label'        => esc_html__( 'Allow featured image for taxonomies', 'neve' ),
			'type'         => 'multi_select',
			'depends_on'   => self::MODS_FEATURED_IMAGE_ENABLE,
			'default'      => [ 'category' ],
			'show_in_rest' => array(
				'schema' => array(
					'type'  => 'array',
					'items' => array(
						'type' => 'string',
					),
				),
			),
			'choices'      => $this->get_public_taxonomies(),
		);

		$options['enable_page_header'] = array(
			'label'             => __( 'Enable Page Header', 'neve' ),
			'type'              => 'toggle',
			'documentation'     => [
				'url'   => 'https://bit.ly/nv-hb-ph',
				'label' => __( 'Learn More', 'neve' ),
			],
			'default'           => true,
			'show_in_rest'      => true,
			'sanitize_callback' => function ( $value ) {
				return is_bool( $value ) ? $value : false;
			},
		);

		if ( $this->load_mega_menu ) {
			$options['enable_mega_menu'] = array(
				'label'             => __( 'Enable Mega Menu', 'neve' ),
				'type'              => 'toggle',
				'documentation'     => [
					'url'   => 'https://bit.ly/nv-mm-pro',
					'label' => __( 'Learn More', 'neve' ),
				],
				'default'           => false,
				'show_in_rest'      => true,
				'sanitize_callback' => function ( $value ) {
					return is_bool( $value ) ? $value : false;
				},
			);
		}

		$this->options = array(
			array(
				'label'   => __( 'Extra features', 'neve' ),
				'options' => $options,
			),
		);

		parent::add_module_settings();
	}

	/**
	 * Return registered public taxonomies
	 *
	 * @return array
	 */
	private function get_public_taxonomies() {
		$taxonomies           = [];
		$types                = get_taxonomies( [ 'public' => true ], 'objects' );
		$supported_taxonomies = apply_filters( 'neve_feature_image_taxonomy_support', array( 'category', 'post_tag' ) );

		foreach ( $types as $taxonomy => $args ) {
			if ( in_array( $taxonomy, $supported_taxonomies ) ) {
				$taxonomies[ $taxonomy ] = $args->label;
			}
		}

		return apply_filters( 'neve_feature_image_taxonomy_available', $taxonomies );
	}

	/**
	 * Get the current featured image for the taxonomy.
	 *
	 * @return string
	 */
	private function get_taxonomy_featured() {
		$current_archive         = get_queried_object();
		$taxonomy_featured_image = '';

		if ( empty( $current_archive ) || ! property_exists( $current_archive, 'taxonomy' ) ) {
			return $taxonomy_featured_image;
		}

		if ( in_array( $current_archive->taxonomy, $this->allowed_taxonomies ) ) {
			$image_id                = get_term_meta( $current_archive->term_id, 'term_image', true );
			$taxonomy_featured_image = wp_get_attachment_image_url( $image_id, 'full' );
		}
		if ( in_array( $current_archive->taxonomy, array( 'product_cat' ) ) ) {
			$image_id                = get_term_meta( $current_archive->term_id, 'thumbnail_id', true );
			$taxonomy_featured_image = wp_get_attachment_image_url( $image_id, 'full' );
		}
		return $taxonomy_featured_image;
	}

	/**
	 * Set the current featured image for archive pages.
	 * This is only used by JS inside Customizer.
	 */
	final public function set_featured_image() {
		if ( ! is_customize_preview() ) {
			return;
		}
		if ( ! is_archive() ) {
			return;
		}

		$taxonomy_featured_image = $this->get_taxonomy_featured();

		if ( empty( $taxonomy_featured_image ) ) {
			return;
		}
		wp_add_inline_script( 'neve-customizer-preview', 'neveCustomizePreview.currentFeaturedImage = "' . esc_url( $taxonomy_featured_image ) . '";' );
	}

	/**
	 * Returns the featured image if the toggle option is enabled.
	 *
	 * @param string  $image The featured image.
	 * @param boolean $use_featured If featured image should be used.
	 * @param array   $meta Additional image meta.
	 *
	 * @return string
	 */
	final public function should_use_featured_image_url( $image, $use_featured, $meta ) {
		if ( $use_featured === false ) {
			return $image;
		}

		if ( ! is_archive() ) {
			return $image;
		}

		$taxonomy_featured_image = $this->get_taxonomy_featured();

		if ( ! empty( $taxonomy_featured_image ) ) {
			return sprintf( 'url("%s")', esc_url( $taxonomy_featured_image ) );
		}

		return $image;
	}

	/**
	 * Check if module should load.
	 *
	 * @return bool
	 */
	public function should_load() {
		if ( is_customize_preview() ) {
			self::$should_add_script = true;
		}
		return $this->is_active();
	}

	/**
	 * Run Header Footer Grid Module
	 */
	public function run_module() {
		add_filter( 'hfg_template_locations', array( $this, 'add_module_template_location' ) );
		add_filter( 'hfg_after_builder_header_registered', array( $this, 'after_builder_registered' ), 10, 2 );
		add_filter( 'neve_pro_filter_customizer_modules', array( $this, 'add_customizer_modules' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ), 999 );
		add_action( 'wp_print_footer_scripts', array( $this, 'maybe_dequeue' ), 9 );

		add_filter( 'hfg_theme_support_filter', array( $this, 'add_to_theme_support' ) );
		add_filter( 'neve_register_nav_menus', array( $this, 'register_additional_nav_menus' ) );

		$sticky_header = new Sticky_Header();
		add_filter( 'hfg_header_row_classes', array( $sticky_header, 'header_row_classes' ), 10, 2 );
		add_filter( 'hfg_footer_row_classes', array( $sticky_header, 'footer_row_classes' ), 10, 2 );
		add_action( 'wp_enqueue_scripts', array( $sticky_header, 'adjust_admin_bar' ) );
		add_action( 'wp_enqueue_scripts', array( $sticky_header, 'adjust_lightbox_zindex' ) );

		$transparent_header = new Transparent_Header();
		add_filter( 'hfg_header_wrapper_class', array( $transparent_header, 'add_class_to_header_wrapper' ) );
		add_filter( 'hfg_settings_schema', array( $this, 'add_page_header_defaults' ), 100 );

		if ( Loader::has_compatibility( 'submenu_style' ) ) {
			add_action(
				'neve_after_nav_init',
				function ( $component_id ) {
					new Submenu( $component_id );
				},
				10,
				1
			);
		}

		add_action( 'wp', array( $this, 'replace_theme_mods' ) );

		if ( $this->load_mega_menu ) {
			new Mega_Menu();
		}
	}

	/**
	 * Add page header to default HFG schema.
	 *
	 * @param array $defaults HFG default schema.
	 *
	 * @return array
	 */
	public function add_page_header_defaults( $defaults ) {
		return array_merge(
			[
				'hfg_page_header_layout' => wp_json_encode(
					[
						'desktop' => [
							'top'    => [],
							'bottom' => [],
						],
						'mobile'  => [
							'top'    => [],
							'bottom' => [],
						],
					]
				),
			],
			$defaults
		);
	}


	/**
	 * Add additional navigation locations.
	 *
	 * @param array $nav_menus_to_register List of nav locations to be registered.
	 *
	 * @return mixed
	 * @since   1.0.0
	 * @access  public
	 */
	public function register_additional_nav_menus( $nav_menus_to_register ) {
		$nav_menus_to_register['page-header'] = esc_html__( 'Page Header Menu', 'neve' );

		return $nav_menus_to_register;
	}

	/**
	 * Append to the theme support builders.
	 *
	 * @param array $theme_support The theme support array.
	 *
	 * @return mixed
	 * @since   1.0.0
	 * @access  public
	 */
	public function add_to_theme_support( $theme_support ) {
		global $wp_version;

		if ( ! empty( $theme_support[0]['builders'] ) ) {

			if ( Page_Header::is_module_activated() ) {
				$theme_support[0]['builders']['Neve_Pro\Modules\Header_Footer_Grid\Builder\Page_Header'] = array(
					'Neve_Pro\Modules\Header_Footer_Grid\Components\Button',
					'Neve_Pro\Modules\Header_Footer_Grid\Components\Button',
					'Neve_Pro\Modules\Header_Footer_Grid\Components\Button',
					'Neve_Pro\Modules\Header_Footer_Grid\Components\Html_Page',
					'Neve_Pro\Modules\Header_Footer_Grid\Components\Html_Page',
					'Neve_Pro\Modules\Header_Footer_Grid\Components\Html_Page',
					'Neve_Pro\Modules\Header_Footer_Grid\Components\Nav',
					'Neve_Pro\Modules\Header_Footer_Grid\Components\Custom_Layout',
					'Neve_Pro\Modules\Header_Footer_Grid\Components\Custom_Layout',
					'Neve_Pro\Modules\Header_Footer_Grid\Components\Custom_Layout',
					'Neve_Pro\Modules\Header_Footer_Grid\Components\Divider',
					'Neve_Pro\Modules\Header_Footer_Grid\Components\Divider',
				);
			}

			$theme_support[0]['builders']['HFG\Core\Builder\Footer'] = array_merge(
				$theme_support[0]['builders']['HFG\Core\Builder\Footer'],
				array(
					'Neve_Pro\Modules\Header_Footer_Grid\Components\Social_Icons',
					'Neve_Pro\Modules\Header_Footer_Grid\Components\Payment_Icons',
					'Neve_Pro\Modules\Header_Footer_Grid\Components\Custom_Layout',
					'Neve_Pro\Modules\Header_Footer_Grid\Components\Custom_Layout',
					'Neve_Pro\Modules\Header_Footer_Grid\Components\Custom_Layout',
					'Neve_Pro\Modules\Header_Footer_Grid\Components\Copyright',
				)
			);

			$theme_support[0]['builders']['HFG\Core\Builder\Header'] = array_merge(
				$theme_support[0]['builders']['HFG\Core\Builder\Header'],
				array(
					'Neve_Pro\Modules\Header_Footer_Grid\Components\Button',
					'Neve_Pro\Modules\Header_Footer_Grid\Components\Button',
					'Neve_Pro\Modules\Header_Footer_Grid\Components\Html',
					'Neve_Pro\Modules\Header_Footer_Grid\Components\Html',
					'Neve_Pro\Modules\Header_Footer_Grid\Components\Logo',
					'Neve_Pro\Modules\Header_Footer_Grid\Components\Search',
					'Neve_Pro\Modules\Header_Footer_Grid\Components\Primary_Nav',
					'Neve_Pro\Modules\Header_Footer_Grid\Components\Primary_Nav',
					'Neve_Pro\Modules\Header_Footer_Grid\Components\Social_Icons',
					'Neve_Pro\Modules\Header_Footer_Grid\Components\Contact',
					'Neve_Pro\Modules\Header_Footer_Grid\Components\Language_Switcher',
					'Neve_Pro\Modules\Header_Footer_Grid\Components\Yoast_Breadcrumbs',
					'Neve_Pro\Modules\Header_Footer_Grid\Components\Wish_List',
					'Neve_Pro\Modules\Header_Footer_Grid\Components\Menu_Icon',
					'Neve_Pro\Modules\Header_Footer_Grid\Components\My_Account',
					'Neve_Pro\Modules\Header_Footer_Grid\Components\Custom_Layout',
					'Neve_Pro\Modules\Header_Footer_Grid\Components\Custom_Layout',
					'Neve_Pro\Modules\Header_Footer_Grid\Components\Custom_Layout',
					'Neve_Pro\Modules\Header_Footer_Grid\Components\Divider',
					'Neve_Pro\Modules\Header_Footer_Grid\Components\Divider',
				)
			);

			if ( Loader::has_compatibility( 'advanced_search_component' ) ) {
				$theme_support[0]['builders']['HFG\Core\Builder\Header'] = array_merge(
					$theme_support[0]['builders']['HFG\Core\Builder\Header'],
					array(
						'Neve_Pro\Modules\Header_Footer_Grid\Components\Advanced_Search_Icon',
						'Neve_Pro\Modules\Header_Footer_Grid\Components\Advanced_Search_Icon',
						'Neve_Pro\Modules\Header_Footer_Grid\Components\Advanced_Search_Form',
						'Neve_Pro\Modules\Header_Footer_Grid\Components\Advanced_Search_Form',
					)
				);
			}

			if ( version_compare( $wp_version, '5.8', '>=' ) ) {
				$widget_areas = array(
					'Neve_Pro\Modules\Header_Footer_Grid\Components\Widget_Area',
					'Neve_Pro\Modules\Header_Footer_Grid\Components\Widget_Area',
					'Neve_Pro\Modules\Header_Footer_Grid\Components\Widget_Area',
				);

				if ( Page_Header::is_module_activated() ) {
					$theme_support[0]['builders']['Neve_Pro\Modules\Header_Footer_Grid\Builder\Page_Header'] = array_merge( $theme_support[0]['builders']['Neve_Pro\Modules\Header_Footer_Grid\Builder\Page_Header'], $widget_areas );
				}
				$theme_support[0]['builders']['HFG\Core\Builder\Header'] = array_merge( $theme_support[0]['builders']['HFG\Core\Builder\Header'], $widget_areas );
				$theme_support[0]['builders']['HFG\Core\Builder\Footer'] = array_merge( $theme_support[0]['builders']['HFG\Core\Builder\Footer'], $widget_areas );
			}

			$theme_support[0]['builders']['HFG\Core\Builder\Header'] = apply_filters( 'neve_header_module_loader', $theme_support[0]['builders']['HFG\Core\Builder\Header'] );
		}

		return $theme_support;
	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @return bool | void
	 */
	public function enqueue() {
		$path = 'style.min.css';

		$this->rtl_enqueue_style( $this->slug, NEVE_PRO_INCLUDES_URL . 'modules/header_footer_grid/assets/' . $path, array(), NEVE_PRO_VERSION );
		if ( neve_is_amp() ) {
			return false;
		}

		wp_enqueue_script( $this->slug, NEVE_PRO_INCLUDES_URL . 'modules/header_footer_grid/assets/js/build/front-end.js', array(), NEVE_PRO_VERSION, true );
		wp_script_add_data( $this->slug, 'async', true );

		if ( is_admin_bar_showing() ) {
			wp_add_inline_style( $this->slug, '@media(max-width: 959px){ body:not(.menu_sidebar_dropdown) #header-menu-sidebar-bg {padding-top: 45px;} #header-grid.is-stuck {top:0 !important}}' );
		}
	}

	/**
	 * Maybe dequeue script if needed.
	 */
	public function maybe_dequeue() {
		if ( ! self::$should_add_script ) {
			wp_dequeue_script( $this->slug );
		}
	}

	/**
	 * Add module templates location
	 *
	 * @param array $locations the default templates locations.
	 *
	 * @return array
	 */
	public function add_module_template_location( $locations ) {
		$locations[] = NEVE_PRO_PATH . 'includes/modules/header_footer_grid/templates/';

		return $locations;
	}

	/**
	 * Hooks into customizer loader and adds additional modules to load.
	 *
	 * @param array $modules A list of modules to be loaded.
	 *
	 * @return mixed
	 * @since   1.0.0
	 * @access  public
	 */
	public function add_customizer_modules( $modules ) {
		array_push( $modules, 'Modules\Header_Footer_Grid\Customizer\Transparent_Header' );
		array_push( $modules, 'Modules\Header_Footer_Grid\Customizer\Conditional_Headers' );
		array_push( $modules, 'Modules\Header_Footer_Grid\Customizer\Sticky_Header' );

		if ( Loader::has_compatibility( 'menu_icon_svg' ) ) {
			array_push( $modules, 'Modules\Header_Footer_Grid\Customizer\Menu_Icon' );
		}
		if ( Loader::has_compatibility( 'submenu_style' ) ) {
			array_push( $modules, 'Modules\Header_Footer_Grid\Submenu\Customizer\Component_Settings' );
		}

		return $modules;
	}

	/**
	 * Alter WP_Customize_Manager object.
	 *
	 * @param WP_Customize_Manager               $wp_customize The instance of the WordPress Manager.
	 * @param \HFG\Core\Builder\Abstract_Builder $builder The builder object.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function after_builder_registered( WP_Customize_Manager $wp_customize, \HFG\Core\Builder\Abstract_Builder $builder ) {
		$title = ( isset( $builder->title ) && ! empty( $builder->title ) )
			? $builder->title
			: __( 'Header', 'neve' );

		$description = ( isset( $builder->description ) && ! empty( $builder->description ) )
			? $builder->description
			: '';

		$wp_customize->remove_panel( 'hfg_header' );
		$wp_customize->register_panel_type( 'Neve_Pro\Modules\Header_Footer_Grid\Customizer\Custom_Panel' );
		$panel = new Custom_Panel(
			$wp_customize,
			'hfg_header',
			// @phpstan-ignore-next-line theme_supports accepts string|array.
			array(
				'priority'       => 25,
				'capability'     => 'edit_theme_options',
				'theme_supports' => 'hfg_support',
				'title'          => $title,
				'description'    => $description,
			)
		);
		$wp_customize->add_panel( $panel );
		return $wp_customize;
	}

	/**
	 * Replace theme mods with the ones from the available headers.
	 */
	public function replace_theme_mods() {
		if ( get_theme_mod( 'neve_global_header', true ) ) {
			return;
		}

		$headers = Custom_Layouts_Cpt::get_conditional_headers();

		if ( empty( $headers ) || ! is_array( $headers ) ) {
			return;
		}

		if ( is_customize_preview() ) {
			$current_selection = get_theme_mod( 'neve_header_conditional_selector' );
			if ( ! isset( $current_selection['layout'] ) || $current_selection['layout'] === 'default' ) {
				return;
			}

			return;
		}

		$valid_conditions = [];

		foreach ( $headers as $id => $theme_mods ) {
			if ( ! $this->check_conditions( $id ) ) {
				continue;
			}

			$valid_conditions[] = $id;
		}

		$final_header = $this->get_greatest_priority_rule( $valid_conditions, true );
		if ( ! $final_header ) {
			return;
		}

		$theme_mods = json_decode( $headers[ $final_header ], true );
		if ( ! is_array( $theme_mods ) || empty( $theme_mods ) ) {
			return;
		}

		$theme_mods = $this->migrate_rows_skin_mode( $theme_mods, $final_header );

		foreach ( $theme_mods as $mod => $val ) {
			if ( in_array( $mod, Conditional_Headers::$theme_mods_keys, true ) ) {
				$val = wp_json_encode( $val );
			}

			add_filter(
				'theme_mod_' . $mod,
				function () use ( $val, $mod ) {
					if ( strpos( $mod, 'custom_html_' ) !== false && strpos( $mod, '_content' ) !== false ) {
						return html_entity_decode( $val );
					}
					return $val;
				}
			);
		}

		// Make sure if transparent header is on to apply it global for this header.
		add_filter( 'theme_mod_neve_transparent_only_on_home', '__return_false' );
	}

	/**
	 * Flag script for enqueue.
	 */
	public static function flag_for_enqueue() {
		self::$should_add_script = true;
	}

	/**
	 * Skins migration for conditional headers.
	 *
	 * @param array $theme_mods theme mods array from conditional header.
	 * @param int   $post_id the conditional header post id.
	 *
	 * @return array
	 */
	private function migrate_rows_skin_mode( $theme_mods, $post_id ) {
		$migration_flag = get_post_meta( $post_id, 'migrated-row-skin', true );

		if ( $migration_flag === 'yes' ) {
			return $theme_mods;
		}

		$rows = [
			'top'     => '#f0f0f0',
			'main'    => '#ffffff',
			'bottom'  => '#ffffff',
			'sidebar' => '#ffffff',
		];

		foreach ( $rows as $row => $default_dark_color ) {
			$skin_key  = 'hfg_header_layout_' . $row . '_skin';
			$color_key = 'hfg_header_layout_' . $row . '_new_text_color';
			$bg_key    = 'hfg_header_layout_' . $row . '_background';
			// If there was no skin set previously, bail.
			if ( ! isset( $theme_mods[ $skin_key ] ) ) {
				continue;
			}

			// All header rows are default light, so we'll account only for dark mode.
			if ( $theme_mods[ $skin_key ] === 'dark-mode' ) {
				// If we're on a dark background and no color is set we make sure it's white.
				if ( ! isset( $theme_mods[ $color_key ] ) ) {
					$theme_mods[ $color_key ] = '#ffffff';
				}

				// Background was never previously set OR it was but still has the default value for color and type.
				if (
					! isset( $theme_mods[ $bg_key ] ) ||
					( $theme_mods[ $bg_key ]['type'] === 'color' && $theme_mods[ $bg_key ]['colorValue'] === $default_dark_color )
				) {
					$theme_mods[ $bg_key ]['colorValue'] = '#24292e';
				}
			}
		}

		// Make sure we update the meta so the changes properly apply inside the customizer.
		update_post_meta( $post_id, 'theme-mods', wp_json_encode( $theme_mods ) );
		// Update the flag for respective header.
		update_post_meta( $post_id, 'migrated-row-skin', 'yes' );

		return $theme_mods;
	}
}
