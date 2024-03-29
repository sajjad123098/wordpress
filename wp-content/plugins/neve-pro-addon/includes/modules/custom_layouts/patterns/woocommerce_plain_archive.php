<?php
/**
 * Pattern for WooCommerce Shop Taxonomy Plain.
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
 * Class Woocommerce_One_Single
 */
class Woocommerce_Plain_Archive extends Abstract_Pattern {

	/**
	 * Defines the pattern properties.
	 *
	 * @inheritDoc
	 */
	protected function define_pattern_props() {
		$this->namespace   = 'woocommerce-plain-archive-pattern';
		$this->title       = __( 'WooCommerce Archive Plain Layout', 'neve' );
		$this->description = _x( 'A plain layout for WooCommerce Shop archive. Inherits from customizer.', 'Block pattern description', 'neve' );
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
<!-- wp:columns {"align":"full","className":"nv-pattern-container ticss-3476170b","hasCustomCSS":true,"customCSS":".ticss-3476170b {\nmargin-left:15px;\n}\n"} --><div class="wp-block-columns alignfull nv-pattern-container ticss-3476170b">

<!-- wp:column {"width":""} --><div class="wp-block-column">

<!-- wp:themeisle-blocks/advanced-columns {"id":"wp-block-themeisle-blocks-advanced-columns-ce13189c","columns":1,"layout":"equal","padding":{"top":"80px","right":"24px","bottom":"80px","left":"24px"},"margin":{"top":"0px","bottom":"0px"},"columnsWidth":"900px","horizontalAlign":"center","verticalAlign":"center","border":{"top":"1px","right":"0px","bottom":"1px","left":"0px"},"align":"full","className":""} --><div id="wp-block-themeisle-blocks-advanced-columns-ce13189c" class="wp-block-themeisle-blocks-advanced-columns alignfull has-1-columns has-desktop-equal-layout has-tablet-equal-layout has-mobile-equal-layout has-vertical-center">
<div class="wp-block-themeisle-blocks-advanced-columns-overlay"></div>
<div class="innerblocks-wrap">
<!-- wp:themeisle-blocks/advanced-column {"id":"wp-block-themeisle-blocks-advanced-column-3534880c","padding":{"top":"0px","right":"0px","bottom":"0px","left":"0px"},"paddingMobile":{"top":"16px","right":"16px","bottom":"16px","left":"16px"},"columnWidth":"100","verticalAlign":"center"} --><div id="wp-block-themeisle-blocks-advanced-column-3534880c" class="wp-block-themeisle-blocks-advanced-column">

<!-- wp:query-title {"type":"archive","textAlign":"center"} /-->

</div><!-- /wp:themeisle-blocks/advanced-column -->
</div>
</div><!-- /wp:themeisle-blocks/advanced-columns -->

<!-- wp:themeisle-blocks/advanced-columns {"id":"wp-block-themeisle-blocks-advanced-columns-16884f93","columns":1,"layout":"twoOne","layoutMobile":"collapsedRows","padding":{"top":"80px","right":"24px","bottom":"80px","left":"24px"},"margin":{"top":"0px","bottom":"0px"},"columnsWidth":"1140px","horizontalAlign":"center","align":"full"} --><div id="wp-block-themeisle-blocks-advanced-columns-16884f93" class="wp-block-themeisle-blocks-advanced-columns alignfull has-1-columns has-desktop-twoOne-layout has-tablet-equal-layout has-mobile-collapsedRows-layout has-vertical-unset">
<div class="wp-block-themeisle-blocks-advanced-columns-overlay"></div>
<div class="innerblocks-wrap">

<!-- wp:themeisle-blocks/advanced-column {"id":"wp-block-themeisle-blocks-advanced-column-9b9f344a","padding":{"top":"0px","right":"0px","bottom":"0px","left":"0px"},"columnWidth":"100","verticalAlign":"center"} --><div id="wp-block-themeisle-blocks-advanced-column-9b9f344a" class="wp-block-themeisle-blocks-advanced-column">

<!-- wp:query {"queryId":74,"query":{"perPage":3,"pages":0,"offset":0,"postType":"product","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":true,"parents":[]},"displayLayout":{"type":"flex","columns":3},"otterConditions":[]} --><div class="wp-block-query">

<!-- wp:post-template -->
<!-- wp:themeisle-blocks/advanced-columns {"id":"wp-block-themeisle-blocks-advanced-columns-744845d3","columns":1,"layout":"equal","layoutMobile":"collapsedRows","padding":{"top":"0px","right":"0px","bottom":"0px","left":"0px"},"margin":{"top":"0px","bottom":"40px"},"boxShadowColorOpacity":5,"boxShadowBlur":24,"boxShadowVertical":16,"otterConditions":[]} --><div id="wp-block-themeisle-blocks-advanced-columns-744845d3" class="wp-block-themeisle-blocks-advanced-columns has-1-columns has-desktop-equal-layout has-tablet-equal-layout has-mobile-collapsedRows-layout has-vertical-unset">
<div class="wp-block-themeisle-blocks-advanced-columns-overlay"></div>
<div class="innerblocks-wrap">
<!-- wp:themeisle-blocks/advanced-column {"padding":{"top":"0px","right":"0px","bottom":"0px","left":"0px"},"columnWidth":"100","verticalAlign":"center"} --><div class="wp-block-themeisle-blocks-advanced-column">
<!-- wp:woocommerce/product-image {"saleBadgeAlign":"left","isDescendentOfQueryLoop":true} /-->

<!-- wp:post-title {"textAlign":"left","level":3,"textColor":"neve-text-color","className":"ticss-13f1a245","hasCustomCSS":true,"customCSS":".ticss-13f1a245 {\n  margin-bottom:8px;\n}\n","__woocommerceNamespace":"woocommerce/product-query/product-title"} /-->

<!-- wp:woocommerce/product-price {"isDescendentOfQueryLoop":true,"textAlign":"left"} /-->

<!-- wp:spacer {"height":"16px"} -->
<div style="height:16px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:woocommerce/product-button {"isDescendentOfQueryLoop":true,"textAlign":"left"} /-->
</div><!-- /wp:themeisle-blocks/advanced-column -->
</div>
</div><!-- /wp:themeisle-blocks/advanced-columns -->
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
<!-- /wp:query-no-results -->
</div><!-- /wp:query -->
</div><!-- /wp:themeisle-blocks/advanced-column -->
</div>
</div><!-- /wp:themeisle-blocks/advanced-columns -->
</div><!-- /wp:column -->
</div><!-- /wp:columns -->
CONTENT;

	}
}
