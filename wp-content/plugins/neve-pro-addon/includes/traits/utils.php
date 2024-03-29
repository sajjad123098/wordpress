<?php
/**
 * Utility functions shared between modules.
 *
 * @package Neve_Pro
 */
namespace Neve_Pro\Traits;

use Neve\Customizer\Defaults\Layout;

/**
 * Trait Utils
 *
 * @package Neve_Pro\Traits
 */
trait Utils {
	use Layout;

	/**
	 * Get default meta value
	 */
	public function get_default_meta_value( $field, $default ) {
		if ( ! function_exists( 'neve_get_default_meta_value' ) ) {
			return Layout::get_meta_default_data( $field, $default );
		}

		return neve_get_default_meta_value( $field, $default );
	}

	/**
	 * Get the display type of the shop page
	 *
	 * @return false | string
	 */
	public function get_shop_display_type() {
		if ( ! class_exists( 'WooCommerce', false ) ) {
			return false;
		}

		return function_exists( 'woocommerce_get_loop_display_mode' ) ? woocommerce_get_loop_display_mode() : get_option( 'woocommerce_shop_page_display', '' );
	}
}
