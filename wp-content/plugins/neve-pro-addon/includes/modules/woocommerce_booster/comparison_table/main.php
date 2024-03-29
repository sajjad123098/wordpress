<?php
/**
 * Main class of the Comparison Table.
 *
 * @package Neve_Pro\Modules\Woocommerce_Booster\Comparison_Table
 */
namespace Neve_Pro\Modules\Woocommerce_Booster\Comparison_Table;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Class Main
 *
 * @deprecated v2.3.1 - Please use built-in Sparks class (\Codeinwp\Sparks\Modules\Comparison_Table\Main)
 */
class Main {
	/**
	 * Enqueue Style and Script
	 *
	 * @deprecated v2.3.1 - Please use built-in Sparks method. (\Codeinwp\Sparks\Modules\Comparison_Table\Main::enqueue_assets)
	 * @return void
	 */
	public function enqueue_assets() {
		if ( ! function_exists( 'sparks' ) ) {
			return;
		}

		$ct_module = sparks()->module( 'comparison_table' );

		if ( ! $ct_module instanceof \Codeinwp\Sparks\Modules\Base_Module ) {
			return;
		}

		$ct_module->enqueue_assets();
	}
}
