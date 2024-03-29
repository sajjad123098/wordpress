<?php
/**
 *  Class that add shop products functionalities.
 *
 * @package Neve_Pro\Modules\Woocommerce_Booster\Views
 */

namespace Neve_Pro\Modules\Woocommerce_Booster\Views;

use Neve\Core\Settings\Mods;
use Neve_Pro\Core\Loader;
use Neve_Pro\Modules\Woocommerce_Booster\Compatibility\Yith_Brands;
use Neve_Pro\Traits\Inline_Styles;
use Neve_Pro\Traits\Shop;

/**
 * Class Shop_Product
 *
 * @package Neve_Pro\Modules\Woocommerce_Booster\Views
 */
class Shop_Product extends Abstract_Shop_Product {
	use Inline_Styles;
	use Shop;

	const SALETAG_ALIGNMENT = 'neve_sale_tag_alignment';

	/**
	 * Initialize the module
	 *
	 * @return void
	 */
	public function init() {
		if ( ! apply_filters( 'neve_pro_run_wc_view', true, self::class ) ) {
			return;
		}

		$this->woocommerce_init();
		add_action( 'woocommerce_after_shop_loop_item', array( $this, 'shop_product_content' ) );

		// Add sale tag in card wrapper
		add_action( 'neve_product_image_wrap_before', 'woocommerce_show_product_loop_sale_flash', 7 );

		// Add needed classes to set add to cart position
		add_filter( 'sparks_product_image_buttons_wrapper_classes', array( $this, 'edit_image_button_classes' ) );
		add_filter( 'sparks_product_image_overlay_classes', array( $this, 'edit_image_overlay_classes' ) );

		if ( ! Loader::has_compatibility( 'malformed_div_on_shop' ) ) {
			add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'wrapper_close_div' ), 14 );
		}

		// Add functionalities from customizer
		add_action( 'wp', array( $this, 'run' ), 12 );

		// Archive card style
		add_filter( 'product_cat_class', array( $this, 'filter_product_cat_class' ), 10, 3 );
	}

	/**
	 * Register shop product hooks
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'wp', array( $this, 'init' ), 1 ); // Run at "wp" hook (priority: 1) to able to be late enough for Elementor template checks (wait until the Elementor conditions be registered).

		add_action( 'sparks_qv_before_run_markup_changes', array( $this, 'add_sale_tag_hooks' ) ); // Add sale tag before the Quick View modal for Sparks.
	}

	/**
	 * Provides access to sale_tag method so it can be used to add the hooks before the Quick View modal.
	 *
	 * @return void
	 */
	public function add_sale_tag_hooks() {
		$this->sale_tag();
	}

	/**
	 * Edit image button classes
	 *
	 * @param  string $classes Space separated current classes.
	 * @return string
	 */
	public function edit_image_button_classes( $classes ) {
		$button_display = get_theme_mod( 'neve_add_to_cart_display', 'none' );

		if ( 'none' === $button_display ) {
			return $classes;
		}

		if ( ( ( ! in_array( $button_display, [ 'after' ] ) ) && strpos( $classes, 'sp-btn-on-image' ) === false ) ) {
			$classes .= ' sp-btn-on-image';
		}

			$classes .= ' nv-add-to-cart-' . $button_display;

			return $classes;
	}

	/**
	 * Edit image overlay classes
	 *
	 * @param  string $classes Space separated current classes.
	 * @return string
	 */
	public function edit_image_overlay_classes( $classes ) {
		$button_display = get_theme_mod( 'neve_add_to_cart_display', 'none' );

		if ( 'on_image' !== $button_display ) {
			return $classes;
		}

		$classes .= ' overlay';

		return $classes;
	}

	/**
	 * Remove Woo-Commerce Default actions
	 */
	public function woocommerce_init() {
		// Remove link that wraps the product
		remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );

		// Remove product title
		remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );

		// Remove product price
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );

		// Compatibility for YITH WOOCOMMERCE BRANDS ADD-ON hook after price.
		Yith_Brands::loop_item_hooks();

		// Remove product rating
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );

		// Remove sale tag
		remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );

		add_filter( 'sparks_neve_sale_tag_position', array( $this, 'sparks_neve_sale_tag_position' ) );
	}

	/**
	 * Sparks Neve sale tag position (inject sale tag position to Sparks via WP filter.)
	 *
	 * @param  string $default Default sale tag position of the Neve.
	 * @return string
	 */
	public function sparks_neve_sale_tag_position( string $default ):string {
		return Mods::get( self::SALETAG_ALIGNMENT, 'left' );
	}

	/**
	 * The content of a product on shop page.
	 */
	public function shop_product_content() {
		$elements_order  = $this->get_product_elements_order();
		$content_classes = apply_filters( 'neve_product_content_class', '' );
		echo '<div class="nv-product-content ' . esc_attr( $content_classes ) . '">';

		foreach ( $elements_order as $element ) {
			switch ( $element ) {
				case 'title':
					do_action( 'nv_shop_item_title_before' );
					$this->render_shop_product_title();
					do_action( 'nv_shop_item_title_after' );
					break;
				case 'price':
					$this->render_shop_product_price();
					break;
				case 'reviews':
					do_action( 'nv_shop_item_reviews_before' );
					$this->reviews_markup();
					do_action( 'nv_shop_item_reviews_after' );
					break;
				case 'short-description':
					do_action( 'nv_shop_item_description_before' );
					woocommerce_template_single_excerpt();
					do_action( 'nv_shop_item_description_after' );
					break;
				case 'category':
					do_action( 'nv_shop_item_category_before' );
					$this->render_product_category();
					do_action( 'nv_shop_item_category_after' );
					break;
				default:
					break;
			}
		}
		do_action( 'nv_shop_item_content_after' );
		echo '</div>';

	}

	/**
	 * Render the shop product title.
	 */
	private function render_shop_product_title() {

		woocommerce_template_loop_product_link_open();
		woocommerce_template_loop_product_title();
		$alignment = get_theme_mod( 'neve_product_content_alignment', 'left' );
		if ( $alignment === 'inline' ) {
			do_action( 'nv_shop_item_price_before' );
			// Apply product price on shop and shop loops.
			add_filter( 'woocommerce_get_price_html', [ $this, 'apply_shop_price_template' ], 99 );
			woocommerce_template_loop_price();
			// Remove price template after the price was rendered to avoid applying it on other elementss.
			remove_filter( 'woocommerce_get_price_html', [ $this, 'apply_shop_price_template' ], 99 );
			do_action( 'nv_shop_item_price_after' );
		}
		woocommerce_template_loop_product_link_close();
	}

	/**
	 * Render the shop product price.
	 *
	 * @return bool
	 */
	private function render_shop_product_price() {
		$alignment = get_theme_mod( 'neve_product_content_alignment', 'left' );
		if ( $alignment === 'inline' ) {
			return false;
		}


		do_action( 'nv_shop_item_price_before' );
		// Apply product price on shop and shop loops.
		add_filter( 'woocommerce_get_price_html', [ $this, 'apply_shop_price_template' ], 99 );
		woocommerce_template_loop_price();
		// Remove price template after the price was rendered to avoid applying it on other elementss.
		remove_filter( 'woocommerce_get_price_html', [ $this, 'apply_shop_price_template' ], 99 );

		do_action( 'nv_shop_item_price_after' );


		return true;
	}

	/**
	 * Function to apply the shop price template.
	 *
	 * @param string $price Current product price.
	 *
	 * @return string
	 */
	public function apply_shop_price_template( $price ) {
		return $this->render_product_price( 'shop', $price );
	}

	/**
	 * Render the shop product review.
	 */
	public function reviews_markup() {
		echo '<div class="advanced-rating-wraper">';
		woocommerce_template_loop_rating();
		$this->advanced_reviews_markup();
		echo '</div>';
	}

	/**
	 * Render advanced review.
	 */
	public function advanced_reviews_markup() {
		$advanced_reviews = get_theme_mod( 'neve_advanced_reviews' );
		if ( $advanced_reviews === false ) {
			return;
		}
		if ( get_option( 'woocommerce_enable_review_rating' ) === 'no' ) {
			return;
		}
		global $product;
		$review_count = $product->get_review_count();
		if ( $review_count === 0 ) {
			return;
		}
		$average = $product->get_average_rating();

		echo '<span class="advanced-rating">';
		echo esc_html( $average );
		if ( comments_open() ) {
			echo '<a href="' . esc_url( get_permalink() ) . '#reviews" class="woocommerce-review-link" rel="nofollow">';
			echo '<span class="count">(' . esc_html( $review_count ) . ')</span>'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '</a>';
		}
		echo '</span>';
	}

	/**
	 * Render product category.
	 */
	public function render_product_category() {
		global $product;
		echo '<div class="product_meta">';
		echo wc_get_product_category_list( $product->get_id(), ', ', '<span class="posted_in">' . _n( 'Category:', 'Categories:', count( $product->get_category_ids() ), 'neve' ) . ' ', '</span>' ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div>';
	}

	/**
	 * Run functions.
	 */
	public function run() {
		$this->add_to_cart();
		$this->list_layout();
		$this->force_image_height();
		$this->sale_tag();
		$this->product_content_alignment();
		$this->display_products_filter();
		$this->product_image_style();
	}

	/**
	 * List layout display.
	 */
	public function list_layout() {
		$view = isset( $_GET['ref'] ) ? sanitize_key( $_GET['ref'] ) : get_theme_mod( 'neve_product_card_layout', 'grid' );
		if ( ! empty( $view ) && $view === 'list' ) {
			add_filter(
				'neve_before_shop_classes',
				function ( $classes ) {
					return $classes . ' nv-list';
				}
			);
		}

	}

	/**
	 * Position the button after the content or on the image.
	 */
	private function add_to_cart() {

		$button_display = get_theme_mod( 'neve_add_to_cart_display', 'none' );
		if ( $button_display === 'none' ) {
			return false;
		}

		if ( $button_display === 'after' ) {
			add_action(
				'nv_shop_item_content_after',
				function() {
					echo '<div class="flex-break"></div>';
				},
				997
			);

			add_action( 'nv_shop_item_content_after', 'woocommerce_template_loop_add_to_cart', 998 );
		}

		if ( $button_display === 'on_image' ) {
			add_filter(
				'neve_wrapper_class',
				function ( $classes ) {
					if ( strpos( $classes, 'sp-button-on-image' ) ) {
						return $classes;
					}

					return $classes . ' sp-button-on-image';
				}
			);
			add_action( 'sparks_image_buttons', 'woocommerce_template_loop_add_to_cart', 12 );
		}

		return true;
	}

	/**
	 * If force image height is enabled, add a class on image wrapper.
	 */
	private function force_image_height() {
		$should_load = get_theme_mod( 'neve_force_same_image_height' );
		if ( $should_load === false ) {
			return;
		}
		add_filter(
			'neve_wrapper_class',
			function ( $class ) {
				return $class . ' sp-same-image-height';
			}
		);
	}

	/**
	 * The the maximum sale percentage present for a variable product.
	 *
	 * @param \WC_Product_Variable $product The product.
	 *
	 * @return mixed The highest percentage
	 */
	private function get_variable_product_sale_percentage( $product ) {

		$regular_variation_prices = $product->get_variation_prices( true )['regular_price'];
		$sale_variation_prices    = $product->get_variation_prices( true )['sale_price'];

		$percentages = array();

		foreach ( $regular_variation_prices as $id => $price ) {
			$sale_price        = $sale_variation_prices[ $id ];
			$saving_percentage = round( 100 - ( $sale_price / $price * 100 ) );
			$percentages[]     = $saving_percentage;
		}

		return max( $percentages );
	}

	/**
	 *  Sale TAG hooks.
	 */
	private function sale_tag() {
		$tag_position = get_theme_mod( 'neve_sale_tag_position', 'inside' );
		if ( $tag_position !== 'inside' ) {
			add_filter(
				'woocommerce_sale_flash',
				function ( $value ) {
					return str_replace( 'onsale', 'onsale outside', $value );
				}
			);
		}

		$tag_alignment = get_theme_mod( 'neve_sale_tag_alignment', 'left' );
		if ( $tag_alignment !== 'left' ) {
			add_filter(
				'woocommerce_sale_flash',
				function ( $value ) {
					return str_replace( 'onsale', 'onsale right', $value );
				}
			);
		}

		$text = get_theme_mod( 'neve_sale_tag_text' );
		if ( ! empty( $text ) ) {
			add_filter(
				'woocommerce_sale_flash',
				function () {
					$text          = get_theme_mod( 'neve_sale_tag_text' );
					$tag_position  = get_theme_mod( 'neve_sale_tag_position', 'inside' );
					$tag_alignment = get_theme_mod( 'neve_sale_tag_alignment', 'left' );
					return '<span class="onsale ' . esc_attr( $tag_position ) . ' ' . esc_attr( $tag_alignment ) . '">' . esc_html( $text ) . '</span>';
				}
			);
		}

		$sale_percentage = get_theme_mod( 'neve_enable_sale_percentage' );
		if ( $sale_percentage !== false ) {
			add_filter(
				'woocommerce_sale_flash',
				function ( $markup ) {
					global $product;

					$regular_price = (float) $product->get_regular_price(); // Regular price
					$sale_price    = (float) $product->get_sale_price(); // Sale price

					if ( $regular_price === (float) 0 && ! $product->is_type( 'variable' ) ) {
						return $markup;
					}

					$saving_percentage = '';
					if ( ! $product->is_type( 'variable' ) ) {
						$saving_percentage = round( 100 - ( $sale_price / $regular_price * 100 ) );
					} else {
						$saving_percentage = $this->get_variable_product_sale_percentage( $product );
					}

					if ( empty( $saving_percentage ) ) {
						return $markup;
					}

					$tag_position  = get_theme_mod( 'neve_sale_tag_position', 'inside' );
					$tag_alignment = get_theme_mod( 'neve_sale_tag_alignment', 'left' );
					$tag_format    = get_theme_mod( 'neve_sale_percentage_format', '{value}%' );
					if ( empty( $tag_format ) ) {
						$tag_format = '{value}%';
					}
					$saving_percentage = str_replace( '{value}', (string) $saving_percentage, $tag_format );

					return '<span class="onsale ' . esc_attr( $tag_position ) . ' ' . esc_attr( $tag_alignment ) . '">' . esc_html( $saving_percentage ) . '</span>';
				}
			);
		}
	}

	/**
	 * Product content alignment.
	 */
	private function product_content_alignment() {
		$content_alignment = get_theme_mod( 'neve_product_content_alignment', 'left' );
		if ( $content_alignment === 'left' ) {
			return;
		}
		add_filter(
			'neve_product_content_class',
			function () {
				return get_theme_mod( 'neve_product_content_alignment', 'left' );
			}
		);
	}

	/**
	 * Display product filter.
	 */
	public function display_products_filter() {
		$enable_filter = get_theme_mod( 'neve_enable_product_filter', true );
		if ( $enable_filter === true ) {
			return;
		}
		remove_action( 'nv_woo_header_bits', 'woocommerce_catalog_ordering', 30 );
	}

	/**
	 * Product image style ( zoom/swipe ).
	 */
	public function product_image_style() {
		$image_style = get_theme_mod( 'neve_image_hover', 'none' );
		if ( neve_is_amp() ) {
			return;
		}
		if ( $image_style === 'none' ) {
			return;
		}
		add_filter(
			'neve_wrapper_class',
			function ( $class ) use ( $image_style ) {
				return $class . ' ' . $image_style;
			}
		);

		if ( $image_style === 'swipe' || $image_style === 'next' ) {
			add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'get_second_thumbnail' ) );
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'load_image_style' ) );
	}

	/**
	 * Determines if the image effect should be loaded.
	 *
	 * @return bool
	 */
	private function has_image_effect() {
		return is_shop() || is_product_taxonomy();
	}
	/**
	 * Load image effect style.
	 */
	public function load_image_style() {
		if ( ! $this->has_image_effect() ) {
			return;
		}
		$image_style = get_theme_mod( 'neve_image_hover', 'none' );
		$css         = $this->get_thumbnail_effect_style( $image_style );
		wp_add_inline_style( 'neve-pro-addon-woo-booster', $css );
	}

	/**
	 * Get the second thumbnail for swipe effect.
	 */
	public function get_second_thumbnail() {
		if ( ! $this->has_image_effect() ) {
			return;
		}
		global $product;
		$image_size = apply_filters( 'single_product_archive_thumbnail_size', 'woocommerce_thumbnail' );
		if ( method_exists( $product, 'get_gallery_image_ids' ) ) {
			$gallery_attachment_ids = $product->get_gallery_image_ids();
			if ( ! empty( $gallery_attachment_ids[0] ) ) {
				echo wp_get_attachment_image( $gallery_attachment_ids[0], $image_size, false, 'data-secondary' ); // @phpstan-ignore-line The wordpress-stubs are not up-to-date.
			}
		}
	}

	/**
	 * Add the archive style class
	 *
	 * @param array  $classes   Array of classes.
	 * @param string $class    Class.
	 * @param string $category Category.
	 *
	 * @return array
	 */
	public function filter_product_cat_class( $classes, $class, $category ) {
		$cat_style = get_theme_mod( 'neve_category_card_layout', 'default' );

		if ( ! in_array( $cat_style, [ 'default', 'style-2', 'style-3' ] ) ) {
			$cat_style = 'default';
		}

		if ( $cat_style !== 'default' ) {
			$classes[] = 'has-style';
			$classes[] = $cat_style;
		}

		return $classes;
	}

	/**
	 * Closing tag
	 */
	public function wrapper_close_div() {
		echo '</div>';
	}
}
