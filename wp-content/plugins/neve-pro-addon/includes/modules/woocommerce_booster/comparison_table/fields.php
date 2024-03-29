<?php
/**
 * Class provides option for comparison table module.
 *
 * @package Neve_Pro\Modules\Woocommerce_Booster\Comparison_Table
 */
namespace Neve_Pro\Modules\Woocommerce_Booster\Comparison_Table;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fields Class for Comparison Table functions.
 *
 * @deprecated v2.3.1 - Please use built-in Sparks class (\Codeinwp\Sparks\Modules\Comparison_Table\Fields)
 */
class Fields {
	/**
	 * Returns all registered field classes.
	 * Returns all field classes with non active fields.
	 *
	 * @deprecated v2.3.1 - Please use built-in Sparks method. (\Codeinwp\Sparks\Modules\Comparison_Table\Fields::get_fields)
	 *
	 * @return array
	 */
	public function get_fields() {
		if ( ! class_exists( '\Codeinwp\Sparks\Modules\Comparison_Table\Fields', false ) ) {
			return [];
		}

		return ( new \Codeinwp\Sparks\Modules\Comparison_Table\Fields() )->get_fields();
	}
}
