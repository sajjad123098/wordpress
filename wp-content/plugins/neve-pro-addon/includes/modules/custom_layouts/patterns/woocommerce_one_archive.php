<?php
/**
 * Pattern for One column no Sidebar WooCommerce Archive.
 *
 * Author:      Bogdan Preda <friends@themeisle.com>
 * Created on:  02-12-{2022}
 *
 * @package Neve Pro Addon
 */

namespace Neve_Pro\Modules\Custom_Layouts\Patterns;

use Neve\Core\Settings\Mods;
use Neve_Pro\Modules\Custom_Layouts\Patterns\Core\Abstract_Pattern;
use Neve_Pro\Modules\Custom_Layouts\Patterns\Core\Patterns_Config;

/**
 * Class Woocommerce_One_Archive
 */
class Woocommerce_One_Archive extends Abstract_Pattern {

	/**
	 * Defines the pattern properties.
	 *
	 * @inheritDoc
	 */
	protected function define_pattern_props() {
		$this->namespace   = 'woocommerce-one-archive-pattern';
		$this->title       = __( 'WooCommerce Archive Layout', 'neve' );
		$this->description = _x( 'A layout for WooCommerce Shop archive with content. Inherits from customizer.', 'Block pattern description', 'neve' );
		$this->categories  = [ 'featured', Patterns_Config::NEVE_PATTERN_CATEGORY ];

		$this->container_style = Mods::get( 'neve_shop_archive_container_style', 'contained' );
	}

	/**
	 * Returns the pattern string.
	 *
	 * @inheritDoc
	 * @return string
	 */
	protected function pattern_content() {
		return <<<CONTENT
<!-- wp:themeisle-blocks/advanced-columns {"id":"wp-block-themeisle-blocks-advanced-columns-ce13189c","columns":1,"layout":"equal","padding":{"top":"80px","right":"24px","bottom":"80px","left":"24px"},"margin":{"top":"0px","bottom":"0px"},"columnsWidth":"900px","horizontalAlign":"center","verticalAlign":"center","color":"var(\u002d\u002dnv-text-dark-bg)","backgroundColor":"var(\u002d\u002dnv-dark-bg)","align":"full","className":"has-dark-bg"} -->
<div id="wp-block-themeisle-blocks-advanced-columns-ce13189c" class="wp-block-themeisle-blocks-advanced-columns alignfull has-1-columns has-desktop-equal-layout has-tablet-equal-layout has-mobile-equal-layout has-vertical-center has-dark-bg"><div class="wp-block-themeisle-blocks-advanced-columns-overlay"></div><div class="innerblocks-wrap"><!-- wp:themeisle-blocks/advanced-column {"id":"wp-block-themeisle-blocks-advanced-column-3534880c","padding":{"top":"0px","right":"0px","bottom":"0px","left":"0px"},"paddingMobile":{"top":"16px","right":"16px","bottom":"16px","left":"16px"},"columnWidth":"100","verticalAlign":"center"} -->
<div id="wp-block-themeisle-blocks-advanced-column-3534880c" class="wp-block-themeisle-blocks-advanced-column">
<!-- wp:query-title {"type":"archive","textAlign":"center"} /-->
</div>
<!-- /wp:themeisle-blocks/advanced-column --></div></div>
<!-- /wp:themeisle-blocks/advanced-columns -->

<!-- wp:themeisle-blocks/advanced-columns {"id":"wp-block-themeisle-blocks-advanced-columns-16884f93","columns":2,"layout":"twoOne","layoutMobile":"collapsedRows","padding":{"top":"80px","right":"24px","bottom":"80px","left":"24px"},"margin":{"top":"0px","bottom":"0px"},"columnsWidth":"1140px","horizontalAlign":"center","backgroundColor":"var(\u002d\u002dnv-light-bg)","align":"full"} -->
<div id="wp-block-themeisle-blocks-advanced-columns-16884f93" class="wp-block-themeisle-blocks-advanced-columns alignfull has-2-columns has-desktop-twoOne-layout has-tablet-equal-layout has-mobile-collapsedRows-layout has-vertical-unset"><div class="wp-block-themeisle-blocks-advanced-columns-overlay"></div><div class="innerblocks-wrap"><!-- wp:themeisle-blocks/advanced-column {"id":"wp-block-themeisle-blocks-advanced-column-9b9f344a","padding":{"top":"0px","right":"0px","bottom":"0px","left":"0px"},"paddingMobile":{"top":"16px","right":"16px","bottom":"16px","left":"16px"},"columnWidth":"74.99","verticalAlign":"center"} -->
<div id="wp-block-themeisle-blocks-advanced-column-9b9f344a" class="wp-block-themeisle-blocks-advanced-column"><!-- wp:query {"queryId":74,"query":{"perPage":3,"pages":0,"offset":0,"postType":"product","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":true,"parents":[]},"displayLayout":{"type":"flex","columns":2}} -->
<div class="wp-block-query"><!-- wp:post-template -->
<!-- wp:themeisle-blocks/advanced-columns {"id":"wp-block-themeisle-blocks-advanced-columns-744845d3","columns":1,"layout":"equal","layoutMobile":"collapsedRows","padding":{"top":"24px","right":"24px","bottom":"24px","left":"24px"},"margin":{"top":"0px","bottom":"40px"},"backgroundColor":"var(\u002d\u002dnv-site-bg)","boxShadow":true,"boxShadowColorOpacity":5,"boxShadowBlur":24,"boxShadowVertical":16,"otterConditions":[]} -->
<div id="wp-block-themeisle-blocks-advanced-columns-744845d3" class="wp-block-themeisle-blocks-advanced-columns has-1-columns has-desktop-equal-layout has-tablet-equal-layout has-mobile-collapsedRows-layout has-vertical-unset"><div class="wp-block-themeisle-blocks-advanced-columns-overlay"></div><div class="innerblocks-wrap"><!-- wp:themeisle-blocks/advanced-column {"padding":{"top":"0px","right":"0px","bottom":"0px","left":"0px"},"columnWidth":"100","verticalAlign":"center"} -->
<div class="wp-block-themeisle-blocks-advanced-column"><!-- wp:woocommerce/product-image {"saleBadgeAlign":"left","isDescendentOfQueryLoop":true} /-->

<!-- wp:post-title {"level":3,"textColor":"neve-text-color","__woocommerceNamespace":"woocommerce/product-query/product-title"} /-->

<!-- wp:woocommerce/product-price {"isDescendentOfQueryLoop":true} /-->

<!-- wp:woocommerce/product-button {"isDescendentOfQueryLoop":true} /--></div>
<!-- /wp:themeisle-blocks/advanced-column --></div></div>
<!-- /wp:themeisle-blocks/advanced-columns -->
<!-- /wp:post-template -->

<!-- wp:spacer {"height":"40px"} -->
<div style="height:40px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:query-pagination {"paginationArrow":"chevron","layout":{"type":"flex","justifyContent":"space-between"}} -->
<!-- wp:query-pagination-previous /-->

<!-- wp:query-pagination-numbers /-->

<!-- wp:query-pagination-next /-->
<!-- /wp:query-pagination -->

<!-- wp:query-no-results -->
<!-- wp:paragraph {"placeholder":"Add text or blocks that will display when a query returns no results."} -->
<p>No products were found</p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results --></div>
<!-- /wp:query --></div>
<!-- /wp:themeisle-blocks/advanced-column -->

<!-- wp:themeisle-blocks/advanced-column {"id":"wp-block-themeisle-blocks-advanced-column-cdef9b45","padding":{"top":"24px","right":"24px","bottom":"24px","left":"24px"},"paddingMobile":{"top":"16px","right":"16px","bottom":"16px","left":"16px"},"columnWidth":"25.00"} -->
<div id="wp-block-themeisle-blocks-advanced-column-cdef9b45" class="wp-block-themeisle-blocks-advanced-column"><!-- wp:search {"label":"Search","showLabel":false,"placeholder":"Search products…","width":100,"widthUnit":"%","buttonText":"Search","buttonPosition":"no-button","buttonUseIcon":true,"query":{"post_type":"product"},"align":"center","backgroundColor":"neve-link-hover-color","textColor":"nv-text-dark-bg","fontSize":"medium"} /-->

<!-- wp:spacer {"height":"20px"} -->
<div style="height:20px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:woocommerce/filter-wrapper {"filterType":"price-filter","heading":"Filter by price"} -->
<div class="wp-block-woocommerce-filter-wrapper"><!-- wp:heading {"level":3,"fontSize":"medium"} -->
<h3 class="has-medium-font-size">Filter by price</h3>
<!-- /wp:heading -->

<!-- wp:woocommerce/price-filter {"showInputFields":false,"inlineInput":true,"heading":"","lock":{"remove":true}} -->
<div class="wp-block-woocommerce-price-filter is-loading" data-showinputfields="false" data-showfilterbutton="false" data-heading="" data-heading-level="3"><span aria-hidden="true" class="wc-block-product-categories__placeholder"></span></div>
<!-- /wp:woocommerce/price-filter --></div>
<!-- /wp:woocommerce/filter-wrapper -->

<!-- wp:woocommerce/filter-wrapper {"filterType":"active-filters","heading":"Active filters"} -->
<div class="wp-block-woocommerce-filter-wrapper"><!-- wp:heading {"level":3,"fontSize":"medium"} -->
<h3 class="has-medium-font-size">Active filters</h3>
<!-- /wp:heading -->

<!-- wp:woocommerce/active-filters {"heading":"","lock":{"remove":true}} -->
<div class="wp-block-woocommerce-active-filters is-loading" data-display-style="list" data-heading="" data-heading-level="3"><span aria-hidden="true" class="wc-block-active-filters__placeholder"></span></div>
<!-- /wp:woocommerce/active-filters --></div>
<!-- /wp:woocommerce/filter-wrapper -->

<!-- wp:woocommerce/filter-wrapper {"filterType":"stock-filter","heading":"Filter by stock status"} -->
<div class="wp-block-woocommerce-filter-wrapper"><!-- wp:heading {"level":3,"fontSize":"medium"} -->
<h3 class="has-medium-font-size">Filter by stock status</h3>
<!-- /wp:heading -->

<!-- wp:woocommerce/stock-filter {"displayStyle":"dropdown","heading":"","lock":{"remove":true}} -->
<div class="wp-block-woocommerce-stock-filter is-loading" data-show-counts="true" data-heading="" data-heading-level="3"><span aria-hidden="true" class="wc-block-product-stock-filter__placeholder"></span></div>
<!-- /wp:woocommerce/stock-filter -->

<!-- wp:heading {"level":3,"fontSize":"medium"} -->
<h3 class="has-medium-font-size">Product categories</h3>
<!-- /wp:heading --></div>
<!-- /wp:woocommerce/filter-wrapper -->

<!-- wp:woocommerce/product-categories {"isHierarchical":false} /--></div>
<!-- /wp:themeisle-blocks/advanced-column --></div></div>
<!-- /wp:themeisle-blocks/advanced-columns -->
CONTENT;

	}
}
