<?php
/**
 * Shop traits, shared with other classes.
 *
 * @version 2.5.5
 * @package Neve_Pro
 */

namespace Neve_Pro\Traits;

/**
 * Trait Shop
 *
 * @package Neve_Pro\Traits
 */
trait Shop {

	/**
	 * Get the product price.
	 *
	 * @param string $context Context of the price. Default is 'shop'.
	 */
	public function render_product_price( $context = 'shop', $price = '' ) {
		if ( ! in_array( $context, array( 'shop', 'product' ), true ) ) {
			return $price;
		}

		$template_option  = 'neve_shop_price_template';
		$template_default = '{price}';

		if ( $context === 'product' ) {
			$template_option  = 'neve_product_price_template';
			$template_default = get_theme_mod( 'neve_shop_price_template', '{price}' );
		}

		$template = get_theme_mod( $template_option, $template_default );

		return str_replace( '{price}', $price, $template );
	}

	/**
	 * Get the product elements order.
	 *
	 * @param string $context Context of the order. Default is 'shop'.
	 *
	 * @return array
	 */
	public function get_product_elements_order( $context = 'shop' ) {
		$default = array( 'title', 'reviews', 'price' );

		if ( ! in_array( $context, array( 'shop', 'product' ), true ) ) {
			return $default;
		}

		$mod_name = 'neve_layout_product_elements_order';

		if ( $context === 'product' ) {
			$default  = array( 'title', 'price', 'description', 'add_to_cart', 'meta' );
			$mod_name = 'neve_single_product_elements_order';
		}

		$order = get_theme_mod( $mod_name, wp_json_encode( $default ) );

		return json_decode( $order );
	}
}
