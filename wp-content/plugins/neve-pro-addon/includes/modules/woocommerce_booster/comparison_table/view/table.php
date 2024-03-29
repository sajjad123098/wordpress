<?php
/**
 * Comparison Table render class.
 *
 * @package Neve_Pro\Modules\Woocommerce_Booster\Comparison_Table\View
 */
namespace Neve_Pro\Modules\Woocommerce_Booster\Comparison_Table\View;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Table
 *
 * @deprecated v2.3.1 - Please use built-in Sparks class (\Codeinwp\Sparks\Modules\Comparison_Table\View\Table)
 */
class Table {
	/**
	 * Render the comparison table.
	 *
	 * @deprecated v2.3.1 - Please use built-in Sparks method. (\Codeinwp\Sparks\Modules\Comparison_Table\View\Table::render_comparison_products_table)
	 *
	 * @param bool $related render with related products.
	 * @return void
	 */
	public function render_comparison_products_table( $related = true, $block = false, $attrs = array() ) {
		if ( ! class_exists( '\Codeinwp\Sparks\Modules\Comparison_Table\View\Table', false ) ) {
			return;
		}

		( new \Codeinwp\Sparks\Modules\Comparison_Table\View\Table() )->render_comparison_products_table( $related, $block, $attrs );
	}
}
