<?php
/**
 * YITH WOOCOMMERCE BRANDS ADD-ON compatibility class.
 *
 * @package Neve Pro Addon
 */

namespace Neve_Pro\Modules\Woocommerce_Booster\Compatibility;

/**
 * Class Yith_Brands
 *
 * @package Neve_Pro\Modules\Woocommerce_Booster\Compatibility
 */
class Yith_Brands {

	/**
	 * Improve compatibility by removing and re-hooking into woocomerce booster hooks.
	 * This applies to the loop items.
	 */
	public static function loop_item_hooks() {
		if ( function_exists( 'YITH_WCBR_Premium' ) ) {
			$position          = get_option( 'yith_wcbr_loop_product_brands_position', 'woocommerce_template_loop_price' );
			$yith_wcbr_premium = YITH_WCBR_Premium();
			if ( $position === 'woocommerce_template_loop_price' ) {
				remove_action( 'woocommerce_after_shop_loop_item', array( $yith_wcbr_premium, 'add_loop_brand_template' ), 5 );
				add_action( 'nv_shop_item_price_after', array( $yith_wcbr_premium, 'add_loop_brand_template' ), 5, 0 );
			}
			if ( $position === 'woocommerce_template_loop_add_to_cart' ) {
				remove_action( 'woocommerce_after_shop_loop_item', array( $yith_wcbr_premium, 'add_loop_brand_template' ), 15 );
				add_action( 'nv_shop_item_content_after', array( $yith_wcbr_premium, 'add_loop_brand_template' ), 999 );
			}
		}
	}

	/**
	 * Improve compatibility by removing and re-hooking into woocomerce booster hooks.
	 * This applies to the single product.
	 */
	public static function single_product_hooks() {
		if ( function_exists( 'YITH_WCBR_Premium' ) ) {
			$position          = get_option( 'yith_wcbr_single_product_brands_position', 'woocommerce_template_single_meta' );
			$yith_wcbr_premium = YITH_WCBR_Premium();
			if ( $position === 'woocommerce_template_single_price' ) {
				remove_action( 'woocommerce_single_product_summary', array( $yith_wcbr_premium, 'add_single_product_brand_template' ), 15 );
				add_action( 'nv_product_price_after', array( $yith_wcbr_premium, 'add_single_product_brand_template' ), 15, 0 );
			}
		}
	}
}
