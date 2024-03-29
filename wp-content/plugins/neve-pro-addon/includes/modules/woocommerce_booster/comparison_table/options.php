<?php
/**
 * Class that manages options of the Comparison Table feature.
 *
 * @package Neve_Pro\Modules\Woocommerce_Booster\Comparison_Table
 */
namespace Neve_Pro\Modules\Woocommerce_Booster\Comparison_Table;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Options
 *
 * @deprecated v2.3.1
 */
class Options {
	/**
	 * That specify if the module is activated on Neve Pro extra features tab.
	 *
	 * @deprecated v2.3.1
	 * @return bool
	 */
	public static function is_module_activated() {
		if ( ! function_exists( 'sparks' ) ) {
			return false;
		}

		$ct_module = sparks()->module( 'comparison_table' );

		if ( ! $ct_module instanceof \Codeinwp\Sparks\Modules\Base_Module ) {
			return false;
		}

		return (bool) $ct_module->get_status();
	}
}
