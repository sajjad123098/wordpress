<?php
/**
 * Mega menu main class.
 *
 * @package Neve Pro
 */

namespace Neve_Pro\Modules\Header_Footer_Grid\Mega_Menu;

use Neve_Pro\Admin\Custom_Layouts_Cpt;
use Neve_Pro\Core\Loader;

/**
 * Class Mega_Menu.
 */
class Mega_Menu {

	use Menu_Icons;

	/**
	 * Options meta key.
	 *
	 * @var string
	 */
	private $options_meta_key = 'nv_mm_options';

	const RESOURCES_CDN = 'https://cdn.jsdelivr.net';

	/**
	 * Default Schema
	 *
	 * @var array
	 */
	private $default_schema = [
		'enabledMega'    => false,
		'disableLink'    => false,
		'menuWidth'      => 'contained',
		'columns'        => 3,
		'columnsLayout'  => 'equal',
		'contentType'    => 'default',
		'customLayoutId' => '-',
		'enableHeading'  => false,
	];

	/**
	 * Flag to check if the mega menu CSS was already enqueued.
	 *
	 * @var bool
	 */
	public static $mega_menu_enqueued = false;

	/**
	 * Color Controls array
	 *
	 * @var array
	 */
	private $colors_array = [];

	/**
	 * Column Options array
	 *
	 * @var array
	 */
	private $columns_options = [];

	/**
	 * Mega_Menu Constructor
	 */
	public function __construct() {
		if ( ! $this->is_active() ) {
			return;
		}

		$this->colors_array = [
			'bgColor'      => __( 'Background Color', 'neve' ),
			'color'        => __( 'Text/Link Color', 'neve' ),
			'hoverColor'   => __( 'Link Hover Color', 'neve' ),
			'headingColor' => __( 'Headings Color', 'neve' ),
			'iconColor'    => __( 'Icons Color', 'neve' ),
			'borderColor'  => __( 'Border Color', 'neve' ),
		];

		$assets_url = NEVE_PRO_URL . 'assets/apps/mega-menu/assets/img/';

		$this->columns_options = [
			1 => [
				'equal' => [
					'image' => $assets_url . '1-1.jpg',
					'value' => '1fr',
				],
			],
			2 => [
				'equal'    => [
					'image' => $assets_url . '2-1.jpg',
					'value' => '1fr 1fr',
				],
				'lg-right' => [
					'image' => $assets_url . '2-2.jpg',
					'value' => '1fr 2fr',
				],
				'lg-left'  => [
					'image' => $assets_url . '2-3.jpg',
					'value' => '2fr 1fr',
				],
			],
			3 => [
				'equal'     => [
					'image' => $assets_url . '3-1.jpg',
					'value' => '1fr 1fr 1fr',
				],
				'lg-left'   => [
					'image' => $assets_url . '3-2.jpg',
					'value' => '2fr 1fr 1fr',
				],
				'lg-right'  => [
					'image' => $assets_url . '3-3.jpg',
					'value' => '1fr 1fr 2fr',
				],
				'lg-center' => [
					'image' => $assets_url . '3-4.jpg',
					'value' => '1fr 2fr 1fr',
				],
				'xl-center' => [
					'image' => $assets_url . '3-5.jpg',
					'value' => '1fr 3fr 1fr',
				],
			],
			4 => [
				'equal'    => [
					'image' => $assets_url . '4-1.jpg',
					'value' => 'repeat(4, 1fr)',
				],
				'lg-left'  => [
					'image' => $assets_url . '4-2.jpg',
					'value' => '3fr 1fr 1fr 1fr',
				],
				'lg-right' => [
					'image' => $assets_url . '4-3.jpg',
					'value' => '1fr 1fr 1fr 3fr',
				],
			],
			5 => [
				'equal' => [
					'image' => $assets_url . '5-1.jpg',
					'value' => 'repeat(5, 1fr)',
				],
			],
		];

		// Backend
		add_action( 'admin_footer', [ $this, 'add_app_mount' ] );
		add_action( 'wp_nav_menu_item_custom_fields', [ $this, 'add_options_mount_point' ], 10 );
		add_action( 'admin_enqueue_scripts', [ $this, 'load_backend_app' ] );
		add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );
		add_filter( 'wp_edit_nav_menu_walker', [ $this, 'custom_backend_walker' ], 1 );
		add_action( 'wp_update_nav_menu_item', [ $this, 'save_menu_icons' ], 10, 3 );

		// Frontend
		add_filter( 'wp_setup_nav_menu_item', [ $this, 'add_icons' ] );
		add_filter( 'nav_menu_css_class', [ $this, 'filter_menu_item_classes' ], 10, 4 );
		add_filter( 'walker_nav_menu_start_el', [ $this, 'filter_content' ], 100, 4 );

		// Style
		add_filter( 'neve_dynamic_style_output', [ $this, 'add_menu_inline_styles' ], 10, 2 );

	}

	/**
	 * Load backend scripts and styles
	 *
	 * @return void
	 */
	public function load_backend_app() {
		if ( ! $this->is_nav_admin_screen() ) {
			return;
		}

		$relative_path = 'assets/apps/mega-menu/';

		$dependencies = include NEVE_PRO_PATH . $relative_path . '/build/app.asset.php';

		wp_register_script( 'neve-mega-menu-app', NEVE_PRO_URL . $relative_path . 'build/app.js', array_merge( $dependencies['dependencies'], [ 'nav-menu' ] ), $dependencies['version'], true );
		wp_localize_script( 'neve-mega-menu-app', 'nvMegaMenu', $this->get_localization() );
		wp_enqueue_script( 'neve-mega-menu-app' );

		wp_register_style( 'themeisle-icons', self::RESOURCES_CDN . '/npm/themeisle-icons@1.0.2/themeisle-icons.css', [], '1.0.2' );
		wp_register_style( 'nv-font-awesome', self::RESOURCES_CDN . '/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css', [], '5.15.4' );
		wp_register_style(
			'neve-mega-menu-app',
			NEVE_PRO_URL . $relative_path . 'build/style-app.css',
			[
				'neve-components',
				'themeisle-icons',
				'dashicons',
				'nv-font-awesome',
			],
			$dependencies['version']
		);
		wp_style_add_data( 'neve-mega-menu-app', 'rtl', 'replace' );
		wp_style_add_data( 'neve-mega-menu-app', 'suffix', '.min' );
		wp_enqueue_style( 'neve-mega-menu-app' );
	}

	/**
	 * Loads custom backend menu edit walker.
	 *
	 * @param \Walker_Nav_Menu_Edit $walker nav menu editor walker.
	 *
	 * @return string
	 */
	public function custom_backend_walker( $walker ) {
		return 'Neve_Pro\Modules\Header_Footer_Grid\Mega_Menu\Backend_Walker';
	}

	/**
	 * Save menu item's icon.
	 *
	 * @access  public
	 *
	 * @param int   $menu_id Nav menu ID.
	 * @param int   $item_id Menu item ID.
	 * @param array $item_args Menu item data.
	 */
	public function save_menu_icons( $menu_id, $item_id, $item_args ) {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX === true ) {
			return;
		}

		if ( ! $this->is_nav_admin_screen() ) {
			return;
		}

		check_admin_referer( 'update-nav_menu', 'update-nav-menu-nonce' );

		if ( isset( $_POST['nv-menu-item-icon'][ $item_id ] ) ) {
			$icon = sanitize_text_field( $_POST['nv-menu-item-icon'][ $item_id ] );
			update_post_meta( $item_id, 'nv_mm_icon', $icon );
		}
	}

	/**
	 * Register Rest Routes.
	 */
	public function register_rest_routes() {
		$route_args = [
			'id' => [
				'type'              => 'number',
				'sanitize_callback' => 'absint',
				'validate_callback' => function ( $param, $request, $key ) {
					return is_numeric( $param );
				},
			],
		];

		register_rest_route(
			NEVE_PRO_REST_NAMESPACE,
			'/mega-menu-item/(?P<id>\d+)',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_menu_item_data' ],
				'args'                => $route_args,
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			)
		);
		register_rest_route(
			NEVE_PRO_REST_NAMESPACE,
			'/mega-menu-item/(?P<id>\d+)',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'update_menu_item_data' ],
				'args'                => $route_args,
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			)
		);
	}

	/**
	 * Get the menu item options.
	 *
	 * @param \WP_REST_Request $request the rest request received.
	 *
	 * @return \WP_REST_Response
	 */
	public function get_menu_item_data( \WP_REST_Request $request ) {
		$meta = get_post_meta( $request['id'], $this->options_meta_key, true );

		if ( empty( $meta ) ) {
			return new \WP_REST_Response(
				[
					'success' => true,
					'data'    => $this->default_schema,
				]
			);
		}

		$meta = json_decode( $meta, true );

		return new \WP_REST_Response(
			[
				'success' => true,
				'data'    => array_merge( $this->default_schema, $meta ),
			]
		);
	}

	/**
	 * Fetch the menu item options.
	 *
	 * @param \WP_REST_Request $request the rest request received.
	 */
	public function update_menu_item_data( \WP_REST_Request $request ) {
		$status = get_post_status( $request['id'] );
		if ( ! in_array( $status, [ 'draft', 'publish' ], true ) ) {
			return new \WP_REST_Response(
				[
					'success' => false,
					'message' => __( 'Menu item does not exist.', 'neve' ),
				]
			);
		}

		$options = $request->get_body();
		$options = json_decode( $options, true );

		if ( ! is_array( $options ) ) {
			return new \WP_REST_Response(
				[
					'success' => false,
					'message' => __( 'Invalid data. Please reload the page and try again.', 'neve' ),
				]
			);
		}

		$options = sanitize_text_field( wp_json_encode( $options ) );

		update_post_meta( $request['id'], $this->options_meta_key, $options );

		if ( Loader::has_compatibility( 'track' ) ) {
			\Neve\Core\Tracker::track(
				array(
					array(
						'feature'          => 'mega-menu-pro',
						'featureComponent' => 'item-data',
						'featureValue'     => $options,
					),
				)
			);
		}

		return new \WP_REST_Response(
			[
				'success' => true,
			]
		);
	}

	/**
	 * Add main application mount point.
	 */
	public function add_app_mount() {
		if ( ! $this->is_nav_admin_screen() ) {
			return;
		}

		echo '<div id="nv-mm-app"></div>';
	}

	/**
	 * Add options mount point.
	 */
	public function add_options_mount_point() {
		echo '<div class="neve-mega-menu-hook"></div>';
	}

	/**
	 * Check if current module is active.
	 *
	 * @return bool
	 */
	private function is_active() {
		return get_option( 'nv_pro_enable_mega_menu', false );
	}

	/**
	 * Checks if we're on the nav-menus admin screen.
	 *
	 * @return bool
	 */
	private function is_nav_admin_screen() {
		if ( ! function_exists( 'get_current_screen' ) ) {
			return false;
		}

		$current_screen = get_current_screen();

		if ( ! isset( $current_screen->id ) ) {
			return false;
		}

		if ( $current_screen->id !== 'nav-menus' ) {
			return false;
		}

		return true;
	}

	/**
	 * Gets Localization for backend script.
	 *
	 * @retrun array
	 */
	private function get_localization() {
		return [
			'api'                => rest_url( NEVE_PRO_REST_NAMESPACE . '/mega-menu-item/' ),
			'nonce'              => wp_create_nonce( 'wp_rest' ),
			'customLayouts'      => $this->get_individual_custom_layouts(),
			'columnsOptions'     => $this->columns_options,
			'colorControls'      => $this->colors_array,
			'icons'              => [
				'dashicons'   => array_keys( $this->get_icon_set( 'dashicons' ) ),
				'themeisle'   => array_keys( $this->get_icon_set( 'ti' ) ),
				'fontawesome' => array_keys( $this->get_icon_set( 'fa' ) ),
			],
			'defaultSchema'      => $this->default_schema,
			'megaItems'          => $this->get_mega_menu_parent_data(),
			'globalCustomColors' => get_theme_mod( 'neve_global_custom_colors', [] ),
		];
	}

	/**
	 * Get items that have mega-menu enabled
	 *
	 * @return array
	 */
	private function get_mega_menu_parent_data() {
		if ( ! isset( $_GET['menu'] ) && ! isset( $GLOBALS['nav_menu_selected_id'] ) ) {
			return [];
		}

		// Sometimes we need to get the id from $GLOBALS.
		$id = (int) ( isset( $_GET['menu'] ) ? sanitize_text_field( $_GET['menu'] ) : $GLOBALS['nav_menu_selected_id'] );

		if ( $id === 0 ) {
			return [];
		}
		$all_items = wp_get_nav_menu_items( $id );
		$ids       = wp_list_pluck( $all_items, 'ID' );

		$mega_items = array_filter(
			$ids,
			function ( $id ) {
				$options = get_post_meta( $id, $this->options_meta_key, true );
				if ( empty( $options ) ) {
					return false;
				}
				$options = json_decode( $options, true );

				return isset( $options['enabledMega'] ) && $options['enabledMega'] === true;
			}
		);

		return array_values( $mega_items );
	}

	/**
	 * Show the menu item.
	 *
	 * @access  public
	 * @param \WP_Nav_Menu_Item $menu The menu object.
	 * @return \WP_Nav_Menu_Item $menu The menu object.
	 */
	public function add_icons( $menu ) {
		$icon = get_post_meta( $menu->ID, 'nv_mm_icon', true );
		if ( ! empty( $icon ) ) {
			$menu->icon = $icon;
			if ( ! is_admin() ) {
				$menu->title = sprintf( '<i class="nv-icon">%s</i><span>%s</span>', $this->parse_svg_json( $icon ), $menu->title );
			}
		}

		return $menu;
	}

	/**
	 * Get custom layouts.
	 *
	 * @return array
	 */
	private function get_individual_custom_layouts() {
		$layouts    = Custom_Layouts_Cpt::get_custom_layouts();
		$returnable = [ '-' => __( 'None', 'neve' ) ];

		if ( ! isset( $layouts['individual'] ) ) {
			return $returnable;
		}

		foreach ( $layouts['individual'] as $id => $priority ) {
			$title = get_the_title( $id );

			if ( empty( $title ) ) {
				/* translators: %d - Post ID (number) */
				$title = sprintf( esc_html__( 'Custom layout %d (No title)', 'neve' ), $id );
			}

			$returnable[ $id ] = $title;
		}

		return $returnable;
	}

	/**
	 * Add item classes.
	 *
	 * @param array             $classes the item classes.
	 * @param \WP_Nav_Menu_Item $item the menu item.
	 * @param \stdClass         $args menu item args.
	 * @param int               $depth the item depth.
	 *
	 * @return array
	 */
	public function filter_menu_item_classes( $classes, $item, $args, $depth ) {

		if ( $args->theme_location !== 'primary' ) {
			return $classes;
		}

		$data = $this->get_menu_item_settings( $item->ID );

		if ( $depth === 0 ) {

			// Add mega class.
			if ( isset( $data['enabledMega'] ) && $data['enabledMega'] === true ) {
				$classes[] = 'neve-mega-menu';

				$this->enqueue_frontend();
			}

			// Add width class.
			if ( isset( $data['menuWidth'] ) ) {
				$classes[] = $data['menuWidth'];
			}

			return $classes;
		}

		// Add class for columns.
		if ( (int) $depth === 1 ) {
			$parent_id      = $item->menu_item_parent;
			$parent_options = $this->get_menu_item_settings( $parent_id );

			if ( isset( $parent_options['enabledMega'] ) && $parent_options['enabledMega'] === true ) {
				$classes[] = 'neve-mm-col';
			}
		}

		// Add class for headings.
		if ( isset( $data['enableHeading'] ) && $data['enableHeading'] === true ) {
			$classes[] = 'neve-mm-heading';
		}

		if ( isset( $data['contentType'] ) ) {
			// Add a class if this is a custom layout.
			if ( $data['contentType'] === 'custom-layout' ) {
				$classes[] = 'nv-cl';
			}

			// Add a class if this is a divider.
			if ( $data['contentType'] === 'divider' ) {
				$classes[] = 'neve-mm-divider';
			}
		}

		return $classes;
	}

	/**
	 * Add item styles.
	 *
	 * @param string $css root css.
	 *
	 * @return string
	 */
	public function add_menu_inline_styles( $css, $context ) {
		if ( $context !== 'frontend' ) {
			return $css;
		}

		$locations = get_nav_menu_locations();

		if ( ! isset( $locations['primary'] ) ) {
			return $css;
		}

		$items = wp_get_nav_menu_items( $locations['primary'] );

		if ( ! is_array( $items ) ) {
			return $css;
		}

		foreach ( $items as $item ) {
			// We only add inline styles for the first level, otherwise bail.
			if ( (int) $item->menu_item_parent !== 0 ) {
				continue;
			}

			$data   = $this->get_menu_item_settings( $item->ID );
			$styles = [];

			// Add grid layout style.
			if ( isset( $data['columns'] ) && isset( $data['columnsLayout'] ) ) {
				$values = $this->columns_options[ $data['columns'] ];

				if ( isset( $values[ $data['columnsLayout'] ] ) ) {
					$value                = $values[ $data['columnsLayout'] ]['value'];
					$styles['--gridcols'] = $value;
				}
			}

			// Add all colors.
			foreach ( $this->colors_array as $slug => $nice_name ) {
				if ( isset( $data[ $slug ] ) && ! empty( $data[ $slug ] ) ) {
					$styles[ '--' . strtolower( $slug ) ] = $data[ $slug ];
				}
			}

			$styles = array_map(
				function ( $k, $v ) {
					return "$k:$v";
				},
				array_keys( $styles ),
				array_values( $styles )
			);

			$css .= sprintf( '#menu-item-%s>.sub-menu{%s}', $item->ID, join( ';', $styles ) );

			if ( isset( $data['columns'] ) ) {
				$css .= sprintf( '#menu-item-%s>.sub-menu>li:nth-child(%sn){%s}', $item->ID, $data['columns'], 'border:0!important;' );
			}
			$css .= '.layout-fullwidth .hfg-slot { position: relative }';
		}

		return $css;
	}

	/**
	 * Filter the inner content of the menu items.
	 *
	 * @param string            $item_output the item output.
	 * @param \WP_Nav_Menu_Item $item the nav menu item.
	 * @param int               $depth the depth of the item.
	 * @param \stdClass         $args the menu item args.
	 *
	 * @return string
	 */
	public function filter_content( $item_output, $item, $depth, $args ) {
		if ( $depth < 1 || $args->theme_location !== 'primary' ) {
			return $item_output;
		}

		$data = $this->get_menu_item_settings( $item->ID );


		// Hide root column links
		if ( (int) $depth === 1 ) {
			$parent_id      = $item->menu_item_parent;
			$parent_options = $this->get_menu_item_settings( $parent_id );

			if ( isset( $parent_options['enabledMega'] ) && $parent_options['enabledMega'] === true ) {
				return '';
			}
		}

		// Content Types
		if ( isset( $data['contentType'] ) ) {

			// Custom Layouts
			if ( $data['contentType'] === 'custom-layout' && isset( $data['customLayoutId'] ) ) {
				if ( $data['customLayoutId'] !== '-' ) {
					ob_start();
					do_action( 'neve_do_individual', $data['customLayoutId'] );

					return ob_get_clean();
				}
			}

			// Divider
			if ( $data['contentType'] === 'divider' ) {
				return '';
			}

			// Description only
			if ( $data['contentType'] === 'description' ) {
				// This can come from the theme if the class is already there.
				if ( strpos( $item_output, 'neve-mm-description' ) !== false ) {
					return $item_output;
				}

				return '';
			}
		}

		// Disabled Link
		if ( isset( $data['disableLink'] ) && $data['disableLink'] === true ) {

			$item_output  = $args->before;
			$item_output .= '<span class="no-link">';
			$item_output .= $item->title;
			$item_output .= '</span>';
			$item_output .= $args->after;

			return $item_output;
		}

		return $item_output;
	}

	/**
	 *
	 * Get the menu item settings by id.
	 *
	 * @param int $id menu item id.
	 *
	 * @return array
	 */
	private function get_menu_item_settings( $id ) {
		$data = get_post_meta( $id, $this->options_meta_key, true );

		$data = json_decode( $data, true );

		if ( $data === null ) {
			return $this->default_schema;
		}

		return array_merge( $this->default_schema, $data );
	}

	/**
	 * Enqueue frontend styles.
	 */
	public function enqueue_frontend() {
		if ( self::$mega_menu_enqueued ) {
			return;
		}

		wp_register_style( 'neve-pro-mega-menu', NEVE_PRO_INCLUDES_URL . '/modules/header_footer_grid/assets/mega.min.css', [ 'neve-mega-menu' ], NEVE_PRO_VERSION );
		wp_style_add_data( 'neve-pro-mega-menu', 'rtl', 'replace' );
		wp_style_add_data( 'neve-pro-mega-menu', 'suffix', '.min' );
		wp_enqueue_style( 'neve-pro-mega-menu' );

		self::$mega_menu_enqueued = true;
	}
}
