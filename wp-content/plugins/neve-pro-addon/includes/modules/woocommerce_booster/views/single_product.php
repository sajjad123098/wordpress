<?php
/**
 * Author:          Andrei Baicus <andrei@themeisle.com>
 * Created on:      2019-02-11
 *
 * @package Neve Pro Addon
 */

namespace Neve_Pro\Modules\Woocommerce_Booster\Views;

use Neve\Views\Base_View;
use Neve_Pro\Modules\Woocommerce_Booster\Compatibility\Yith_Brands;
use Neve_Pro\Traits\Shop;

/**
 * Class Single_Product
 *
 * @package Neve_Pro\Modules\Woocommerce_Booster\Views
 */
class Single_Product extends Base_View {

	use Shop;

	/**
	 * Content ordering mapping with priority and hooked function.
	 *
	 * @var array
	 */
	protected $mapping = array(
		'title'       => array(
			'initial' => 5,
			'method'  => 'woocommerce_template_single_title',
		),
		'reviews'     => array(
			'initial' => 10,
			'method'  => 'woocommerce_template_single_rating',
		),
		'price'       => array(
			'initial' => 10,
			'method'  => 'woocommerce_template_single_price',
		),
		'description' => array(
			'initial' => 20,
			'method'  => 'woocommerce_template_single_excerpt',
		),
		'add_to_cart' => array(
			'initial' => 30,
			'method'  => 'woocommerce_template_single_add_to_cart',
		),
		'meta'        => array(
			'initial' => 40,
			'method'  => 'woocommerce_template_single_meta',
		),
	);

	/**
	 * Default content ordering.
	 *
	 * @var array
	 */
	protected $default_order = array(
		'title',
		'price',
		'description',
		'add_to_cart',
		'meta',
	);

	/**
	 * Check if submodule should be loaded.
	 *
	 * @return bool
	 */
	private function should_load() {
		if ( ! class_exists( 'Woocommerce' ) ) {
			return false;
		}

		$post_id = get_the_ID();
		if ( ! $post_id || ! in_array( get_post_type( $post_id ), array( 'product', 'product_variation' ), true ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Initialize the module.
	 *
	 * @return void
	 */
	public function init() {
		// Render the Sticky add to cart [Note: these are should be above of the "neve_pro_run_wc_view" to support sticky add to cart in the custom Elementor single product template]
		add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'sticky_add_to_cart_before' ), -100 );
		add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'sticky_add_to_cart_after' ), 100 );

		if ( ! apply_filters( 'neve_pro_run_wc_view', true, self::class ) ) {
			return;
		}

		/**
		 * In Neve we have the breadcrumbs added at 'wp' hook with the priority 11.
		 * Having this function with a lower or equal priority than 11 will make the toggle of breadcrumbs not working anymore.
		 */
		add_action( 'wp', array( $this, 'run' ), 12 );
	}

	/**
	 * Register submodule hooks
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'wp', array( $this, 'init' ), 1 ); // Run at "wp" hook (priority: 1) to able to be late enough for Elementor template checks (wait until the Elementor conditions be registered).
		add_action( 'parse_request', array( $this, 'sparks_quick_view_support' ), 9 ); // Priority is 9 because of should be earlier than attaching of the rest_api_loaded() to parse_request.
	}

	/**
	 * Run the internal "run" method for rendering of Sparks Quick View.
	 * "sparks_qv_before_run_markup_changes" hook is released from a WP Rest API callback. Therefore add_action call should be registered before the do_action call. (before rest_api_loaded() is attached)
	 *
	 * @return void
	 */
	public function sparks_quick_view_support() {
		add_action( 'sparks_qv_before_run_markup_changes', array( $this, 'run' ) );
	}

	/**
	 * Run the module.
	 */
	public function run() {
		if ( ! $this->should_load() ) {
			return;
		}

		$this->reorder_elements();
		$this->image_zoom_effect();
		$this->breadcrumbs();
		$this->tabs();
		$this->related_products();
		$this->upsells();
		$this->recently_viewed();
		$this->add_gallery_classes();
		$this->product_navigation();
		$this->product_navigation_style();
		add_filter( 'woocommerce_output_related_products_args', array( $this, 'related_products_number' ), 20 );
		add_filter( 'body_class', array( $this, 'seamless_add_to_cart' ) );
		add_filter( 'body_class', array( $this, 'related_products_column' ) );

		// Compatibility for YITH WOOCOMMERCE BRANDS ADD-ON hook after price.
		Yith_Brands::single_product_hooks();
	}

	/**
	 * Add body class to set number of related products column.
	 *
	 * @param  array $classes that current body classes.
	 * @return array
	 */
	public function related_products_column( $classes ) {
		$number_of_columns = get_theme_mod( 'neve_single_product_related_columns', 4 );

		$classes[] = 'related-products-columns-' . $number_of_columns;

		return $classes;
	}

	/**
	 * Add the recently viewed products box.
	 */
	private function recently_viewed() {
		$status = get_theme_mod( 'neve_enable_related_viewed', false );

		if ( $status !== true || neve_is_amp() ) {
			return;
		}

		remove_action( 'template_redirect', 'wc_track_product_view', 20 );
		add_action( 'template_redirect', array( $this, 'track_product_view_always' ), 20 );

		add_action(
			'woocommerce_before_single_product',
			function () {
				the_widget(
					'WC_Widget_Recently_Viewed',
					array(
						'number' => 6,
						'title'  => __( 'Recently Viewed', 'neve' ),
					),
					array(
						'before_widget' => '<div class="nv-recently-viewed expanded">',
						'before_title'  => '<span class="close"></span><h5 class="title">',
						'after_widget'  => '</div>',
						'after_title'   => '</h5>',
						'widget_id'     => 'neve_related_widget_sp',
					)
				);
			}
		);
	}

	/**
	 * Track product views. Always.
	 */
	public function track_product_view_always() {
		if ( ! is_singular( 'product' ) /* xnagyg: remove this condition to run: || ! is_active_widget( false, false, 'woocommerce_recently_viewed_products', true )*/ ) {
			return;
		}

		global $post;

		if ( empty( $_COOKIE['woocommerce_recently_viewed'] ) ) { // @codingStandardsIgnoreLine.
			$viewed_products = array();
		} else {
			$viewed_products = wp_parse_id_list( (array) explode( '|', wp_unslash( $_COOKIE['woocommerce_recently_viewed'] ) ) ); // @codingStandardsIgnoreLine.
		}

		// Unset if already in viewed products list.
		$keys = array_flip( $viewed_products );

		if ( isset( $keys[ $post->ID ] ) ) {
			unset( $viewed_products[ $keys[ $post->ID ] ] );
		}

		$viewed_products[] = $post->ID;

		if ( count( $viewed_products ) > 15 ) {
			array_shift( $viewed_products );
		}

		// Store for session only.
		wc_setcookie( 'woocommerce_recently_viewed', implode( '|', $viewed_products ) );
	}

	/**
	 * Add gallery classes
	 */
	public function add_gallery_classes() {
		if ( ! is_product() ) {
			return false;
		}

		$pid            = get_the_ID();
		$product        = wc_get_product( $pid );
		$attachment_ids = $product->get_gallery_image_ids();
		if ( empty( $attachment_ids ) ) {
			return false;
		}

		$gallery_layout = get_theme_mod( 'neve_single_product_gallery_layout', 'normal' );
		$gallery_slider = get_theme_mod( 'neve_enable_product_gallery_thumbnails_slider', false );
		$new_classes    = array();
		if ( $gallery_layout === 'left' ) {
			$new_classes[] = 'nv-left-gallery';
		}
		if ( $gallery_slider === true ) {
			$new_classes[] = 'sp-slider-gallery';
		}
		if ( $this->is_product_nav_active() ) {
			$new_classes[] = 'nv-has-product-nav';
		}

		add_filter(
			'body_class',
			function ( $classes ) use ( $new_classes ) {
				return array_merge( $classes, $new_classes );
			}
		);

		return true;
	}

	/**
	 * Product navigation control function.
	 *
	 * @return void
	 */
	private function product_navigation() {
		if ( ! $this->is_product_nav_active() ) {
			return;
		}

		remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash' );
		remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
		add_action( 'woocommerce_before_single_product_summary', array( $this, 'display_product_image' ), 20 );
		add_action( 'woocommerce_before_single_product_summary', array( $this, 'render_product_navigation' ), 10 );
	}

	/**
	 * Wrap the product image in a div and add the sale tag before the image.
	 */
	public function display_product_image() {
		echo '<div class="nv-single-image-wrapper">';
		woocommerce_show_product_sale_flash();
		woocommerce_show_product_images();
		echo '</div>';
	}

	/**
	 * Make the product navigation buttons to be customizable from secondary buttons controls
	 *
	 * @return void
	 */
	private function product_navigation_style() {
		add_filter(
			'neve_selectors_buttons_secondary_normal',
			array(
				$this,
				'add_secondary_btns_normal',
			),
			10,
			1
		);
		add_filter(
			'neve_selectors_buttons_secondary_hover',
			array(
				$this,
				'add_secondary_btns_hover',
			),
			10,
			1
		);
		add_filter(
			'neve_selectors_buttons_secondary_padding',
			array(
				$this,
				'add_secondary_btns_padding',
			),
			10,
			1
		);

	}

	/**
	 * Add secondary btn selectors for padding.
	 *
	 * @param string $selectors Current CSS selectors.
	 *
	 * @return string
	 */
	public function add_secondary_btns_padding( $selectors ) {
		return ( $selectors . ', .nv-product-nav a.nv-button' );
	}

	/**
	 * Add secondary btn selectors for hover state.
	 *
	 * @param string $selectors Current CSS selectors.
	 *
	 * @return string
	 */
	public function add_secondary_btns_hover( $selectors ) {
			return ( $selectors . ', .nv-product-nav a.nv-button:hover' );
	}

	/**
	 * Add secondary btn selectors for normal state.
	 *
	 * @param string $selectors Current CSS selectors.
	 *
	 * @return string
	 */
	public function add_secondary_btns_normal( $selectors ) {
		return ( $selectors . ', .nv-product-nav a.nv-button' );
	}

	/**
	 * Check if product nav is active.
	 *
	 * @return bool
	 */
	private function is_product_nav_active() {
		if ( function_exists( 'wpcom_vip_get_adjacent_post' ) ) {
			$next_post = wpcom_vip_get_adjacent_post( true, array(), false, 'product_cat' );
			$prev_post = wpcom_vip_get_adjacent_post( true, array(), true, 'product_cat' );
		} else {
			$next_post = get_next_post(); //phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.get_adjacent_post_get_next_post
			$prev_post = get_previous_post(); //phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.get_adjacent_post_get_previous_post
		}
		$product_navigation = get_theme_mod( 'neve_enable_product_navigation', false );
		if ( $product_navigation !== true || ( ! is_a( $next_post, 'WP_Post' ) && ! is_a( $prev_post, 'WP_Post' ) ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Render product navigation on single product page
	 */
	public function render_product_navigation() {
		if ( function_exists( 'wpcom_vip_get_adjacent_post' ) ) {
			$next_post = wpcom_vip_get_adjacent_post( true, array(), false, 'product_cat' );
			$prev_post = wpcom_vip_get_adjacent_post( true, array(), true, 'product_cat' );
		} else {
			$next_post = get_next_post(); //phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.get_adjacent_post_get_next_post
			$prev_post = get_previous_post(); //phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.get_adjacent_post_get_previous_post
		}

		// Next text
		$next_text = esc_html__( 'Previous Product', 'neve' );
		$next_text = apply_filters( 'neve_woo_nav_next_text', $next_text );

		// Prev text
		$prev_text = esc_html__( 'Next Product', 'neve' );
		$prev_text = apply_filters( 'neve_woo_nav_prev_text', $prev_text );

		echo '<div class="nv-product-nav-wrap">';
		echo '<ul class="nv-product-nav">';
		if ( is_a( $next_post, 'WP_Post' ) ) {
			echo '<li class="next-li">';
			echo '<a href="' . esc_url( get_the_permalink( $next_post->ID ) ) . '" class="nv-button nv-button-secondary next" rel="next">';
			echo '<svg width="14" height="14" viewBox="0 0 320 512" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M34.52 239.03L228.87 44.69c9.37-9.37 24.57-9.37 33.94 0l22.67 22.67c9.36 9.36 9.37 24.52.04 33.9L131.49 256l154.02 154.75c9.34 9.38 9.32 24.54-.04 33.9l-22.67 22.67c-9.37 9.37-24.57 9.37-33.94 0L34.52 272.97c-9.37-9.37-9.37-24.57 0-33.94z"/></svg>';
			echo '</a>';
			echo '<a href="' . esc_url( get_the_permalink( $next_post->ID ) ) . '" class="nv-nav-text next-text">';
			echo esc_attr( $next_text );
			echo '</a>';
			echo '</li>';
		}

		if ( is_a( $prev_post, 'WP_Post' ) ) {
			echo '<li class="prev-li">';
			echo '<a href="' . esc_url( get_the_permalink( $prev_post->ID ) ) . '" class="nv-nav-text prev-text">';
			echo esc_attr( $prev_text );
			echo '</a>';
			echo '<a href="' . esc_url( get_the_permalink( $prev_post->ID ) ) . '" class="nv-button nv-button-secondary prev" rel="next">';
			echo '<svg width="14" height="14" viewBox="0 0 320 512" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"/></svg>';
			echo '</a>';
			echo '</li>';
		}
		echo '</ul>';
		echo '</div>';
	}

	/**
	 * Remove the elements from single product.
	 */
	private function remove_elements() {
		array_walk(
			$this->mapping,
			function ( $args ) {
				remove_action( 'woocommerce_single_product_summary', $args['method'], $args['initial'] );
			}
		);
	}

	/**
	 * Reorder the elements on single product.
	 */
	private function reorder_elements() {
		$order = $this->get_product_elements_order( 'product' );

		if ( ! is_array( $order ) ) {
			return false;
		}

		$change_priorities = false;
		if ( $order !== $this->default_order ) {
			$change_priorities = true;
		}

		$this->remove_elements();

		array_walk(
			$order,
			function ( $value, $index ) use ( $change_priorities ) {
				$priority = $change_priorities ? ( $index + 1 ) * 5 : $this->mapping[ $value ]['initial'];
				add_action(
					'woocommerce_single_product_summary',
					function () use ( $value ) {
						do_action( 'nv_product_' . $value . '_before' );
						// Apply price template on single post when rendering the title.
						if ( $value === 'price' ) {
							add_filter( 'woocommerce_get_price_html', [ $this, 'apply_product_price_template' ], 99 );
						}
						call_user_func( $this->mapping[ $value ]['method'] );
						// Remove price template after product price was rendered to avoid applying it on other elements like related products.
						if ( $value === 'price' ) {
							remove_filter( 'woocommerce_get_price_html', [ $this, 'apply_product_price_template' ], 99 );
						}
						do_action( 'nv_product_' . $value . '_after' );
					},
					$priority
				);
			}
		);
	}

	/**
	 * Function to apply the product price template.
	 *
	 * @param string $price Current product price.
	 *
	 * @return string
	 */
	public function apply_product_price_template( $price ) {
		return $this->render_product_price( 'product', $price );
	}

	/**
	 * Toggle breadcrumbs
	 */
	private function breadcrumbs() {

		$enable_crumbs = get_theme_mod( 'neve_enable_product_breadcrumbs', true );
		if ( $enable_crumbs === true ) {
			return false;
		}

		add_filter( 'neve_breadcrumbs_toggle', '__return_false' );

		return true;
	}

	/**
	 * Toggle tabs
	 */
	private function tabs() {
		$enable_tabs = get_theme_mod( 'neve_enable_product_tabs', true );

		if ( $enable_tabs === true ) {
			return;
		}

		add_filter( 'woocommerce_product_tabs', '__return_empty_array', PHP_INT_MAX );
		add_filter(
			'post_class',
			function ( $classes, $class = '', $post_id = 0 ) {
				$classes[] = 'nv-tabless-product';

				return $classes;
			},
			20,
			3
		);
		add_action(
			'woocommerce_after_single_product_summary',
			function () {
				echo '<div class="nv-related-clearfix"></div>';
			},
			5
		);
	}

	/**
	 * Toggle image zoom effect
	 */
	private function image_zoom_effect() {
		$enable_zoom = get_theme_mod( 'neve_enable_product_image_zoom_effect', true );

		if ( $enable_zoom === true ) {
			return;
		}

		remove_theme_support( 'wc-product-gallery-zoom' );
	}

	/**
	 * Toggle related products
	 */
	private function related_products() {
		$enable_related_prods = get_theme_mod( 'neve_enable_product_related', true );

		if ( $enable_related_prods === true ) {
			return;
		}
		add_filter( 'woocommerce_related_products', '__return_empty_array' );
	}

	/**
	 * Toggle upsells.
	 */
	private function upsells() {
		$enable_upsells = get_theme_mod( 'neve_enable_product_upsells', true );

		if ( $enable_upsells === true ) {
			return;
		}
		remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
	}

	/**
	 * Change related products number.
	 *
	 * @param array $args query parameters for related products.
	 *
	 * @return mixed
	 */
	public function related_products_number( $args ) {
		$related_count = get_theme_mod( 'neve_single_product_related_count', 4 );
		$related_cols  = get_theme_mod( 'neve_single_product_related_columns', 4 );
		if ( empty( $related_count ) && empty( $related_cols ) ) {
			return $args;
		}

		if ( ! empty( $related_cols ) ) {
			$args['columns'] = absint( $related_cols );
		}

		if ( ! empty( $related_count ) ) {
			$args['posts_per_page'] = absint( $related_count ); //phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_posts_per_page
		}

		return $args;
	}

	/**
	 * Add the seamless add to cart class on body.
	 *
	 * @param array $classes Body classes.
	 *
	 * @return array
	 */
	public function seamless_add_to_cart( $classes ) {

		if ( ! is_product() ) {
			return $classes;
		}

		$product_id   = get_the_ID();
		$product      = wc_get_product( $product_id );
		$product_type = $product->get_type();

		if ( $product_type !== 'simple' ) {
			return $classes;
		}

		$is_seamless_add_to_cart = get_theme_mod( 'neve_enable_seamless_add_to_cart', false );
		if ( $is_seamless_add_to_cart === true ) {
			$classes[] = 'seamless-add-to-cart';
		}

		return $classes;
	}

	/**
	 * This function is wrapping the add to cart from WooCommerce.
	 */
	public function sticky_add_to_cart_before() {
		if ( ! $this->should_show_sticky_add_to_cart() ) {
			return;
		}

		$visibility = get_theme_mod( 'neve_enable_sticky_add_to_cart' );

		$media_class = '';
		if ( ! $visibility['mobile'] ) {
			$media_class = 'not-on-mobile';
		}
		if ( ! $visibility['desktop'] ) {
			$media_class = 'not-on-desktop';
		}

		$position = get_theme_mod( 'neve_sticky_add_to_cart_position', 'bottom' );

		global $product;
		echo '<div class="sticky-add-to-cart-wrapper">';
		echo '<div class="sticky-add-to-cart ' . esc_attr( 'sticky-add-to-cart-' . $position . ' ' . $media_class ) . '">';
		if ( $position === 'bottom' ) {
			$this->render_sticky_add_to_cart_product_tabs();
		}
		echo '<div class="container">';
		echo '<div class="sticky-add-to-cart__product">';
		$image_id = $product->get_image_id();
		$image    = wp_get_attachment_image_src( $image_id, 'woocommerce_gallery_thumbnail' );
		if ( $image ) {
			$image = '<img src="' . esc_url( $image[0] ) . '" class="sticky-add-to-cart-img" />';
			echo $image; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '<div class="product-title-small hide-for-small"><strong>' . esc_html( get_the_title() ) . '</strong></div>';
		if ( ! $product->is_type( 'variable' ) ) {
			add_filter( 'woocommerce_get_price_html', [ $this, 'apply_product_price_template' ], 99 );
			woocommerce_template_single_price();
			remove_filter( 'woocommerce_get_price_html', [ $this, 'apply_product_price_template' ], 99 );
		}
		echo '</div>';
	}

	/**
	 * The function renders the product tabs of the sticky add to cart.
	 *
	 * @return void
	 */
	private function render_sticky_add_to_cart_product_tabs() {
		if ( ! $this->should_show_sticky_add_to_cart_tabs() ) {
			return;
		}

		$product_tabs = apply_filters( 'woocommerce_product_tabs', array() );
		?>
		<div id="sticky-add-to-cart-tabs">
			<div class="container">
				<ul>
				<?php foreach ( $product_tabs as $key => $product_tab ) { ?>
					<li>
						<a href="#tab-<?php echo esc_attr( $key ); ?>">
							<?php echo esc_html( $product_tab['title'] ); ?>
						</a>
					</li>
				<?php } ?>
				</ul>
			</div>
		</div>
		<?php
	}

	/**
	 * This function is closing the wrapping for the add to cart from WooCommerce.
	 */
	public function sticky_add_to_cart_after() {
		if ( ! $this->should_show_sticky_add_to_cart() ) {
			return;
		}

		echo '</div>';

		$position = get_theme_mod( 'neve_sticky_add_to_cart_position', 'bottom' );
		if ( $position === 'top' ) {
			$this->render_sticky_add_to_cart_product_tabs();
		}

		echo '</div>';
		echo '</div>';
	}

	/**
	 * Decide if the tabs in the sticky add to cart should be rendered.
	 *
	 * @return bool
	 */
	private function should_show_sticky_add_to_cart_tabs() {
		return get_theme_mod( 'neve_enable_sticky_add_to_cart_tabs', true );
	}

	/**
	 * Decide if sticky add to cart should be rendered.
	 *
	 * @return bool
	 */
	private function should_show_sticky_add_to_cart() {
		if ( ! is_product() ) {
			return false;
		}

		$product = wc_get_product( get_the_ID() );
		if ( empty( $product ) ) {
			return false;
		}

		$product_type  = $product->get_type();
		$allowed_types = [ 'simple', 'grouped', 'external', 'variable' ];
		if ( ! in_array( $product_type, $allowed_types, true ) ) {
			return false;
		}

		$should_show = get_theme_mod( 'neve_enable_sticky_add_to_cart' );
		if ( empty( $should_show ) ) {
			return false;
		}

		return $should_show['mobile'] || $should_show['desktop'];
	}
}
