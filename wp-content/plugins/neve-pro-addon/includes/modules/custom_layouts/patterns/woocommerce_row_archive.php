<?php
/**
 * Pattern for WooCommerce Shop Taxonomy Row.
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
class Woocommerce_Row_Archive extends Abstract_Pattern {

	/**
	 * Defines the pattern properties.
	 *
	 * @inheritDoc
	 */
	protected function define_pattern_props() {
		$this->namespace   = 'woocommerce-row-archive-pattern';
		$this->title       = __( 'WooCommerce Archive Row Layout', 'neve' );
		$this->description = _x( 'A row layout for WooCommerce Shop archive. Inherits from customizer.', 'Block pattern description', 'neve' );
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
<!-- wp:columns {"align":"full","className":"nv-pattern-container ticss-828594e7","hasCustomCSS":true,"customCSS":".ticss-828594e7 {\n  margin-left:15px;\n}\n"} --><div class="wp-block-columns alignfull nv-pattern-container ticss-828594e7">
<!-- wp:column {"width":"","className":"ticss-82896f3f","hasCustomCSS":true,"customCSS":"\n"} --><div class="wp-block-column ticss-82896f3f">

<!-- wp:themeisle-blocks/advanced-columns {"id":"wp-block-themeisle-blocks-advanced-columns-ce13189c","columns":1,"layout":"equal","padding":{"top":"80px","right":"24px","bottom":"80px","left":"24px"},"margin":{"top":"0px","bottom":"0px"},"columnsWidth":"900px","horizontalAlign":"center","verticalAlign":"center","border":{"top":"0px","right":"0px","bottom":"0px","left":"0px"},"align":"full","className":""} --><div id="wp-block-themeisle-blocks-advanced-columns-ce13189c" class="wp-block-themeisle-blocks-advanced-columns alignfull has-1-columns has-desktop-equal-layout has-tablet-equal-layout has-mobile-equal-layout has-vertical-center">
<div class="wp-block-themeisle-blocks-advanced-columns-overlay"></div>
<div class="innerblocks-wrap">
<!-- wp:themeisle-blocks/advanced-column {"id":"wp-block-themeisle-blocks-advanced-column-3534880c","padding":{"top":"0px","right":"0px","bottom":"0px","left":"0px"},"paddingMobile":{"top":"16px","right":"16px","bottom":"16px","left":"16px"},"columnWidth":"100","verticalAlign":"center"} --><div id="wp-block-themeisle-blocks-advanced-column-3534880c" class="wp-block-themeisle-blocks-advanced-column">
<!-- wp:query-title {"type":"archive","textAlign":"center"} /-->
</div><!-- /wp:themeisle-blocks/advanced-column -->
</div>
</div><!-- /wp:themeisle-blocks/advanced-columns -->

<!-- wp:themeisle-blocks/advanced-columns {"id":"wp-block-themeisle-blocks-advanced-columns-16884f93","columns":1,"layout":"twoOne","layoutMobile":"collapsedRows","padding":{"top":"80px","right":"24px","bottom":"80px","left":"24px"},"margin":{"top":"0px","bottom":"0px"},"columnsWidth":"1140px","horizontalAlign":"center","backgroundColor":"var(\u002d\u002dnv-light-bg)","align":"full"} --><div id="wp-block-themeisle-blocks-advanced-columns-16884f93" class="wp-block-themeisle-blocks-advanced-columns alignfull has-1-columns has-desktop-twoOne-layout has-tablet-equal-layout has-mobile-collapsedRows-layout has-vertical-unset">
<div class="wp-block-themeisle-blocks-advanced-columns-overlay"></div>
<div class="innerblocks-wrap">
<!-- wp:themeisle-blocks/advanced-column {"id":"wp-block-themeisle-blocks-advanced-column-9b9f344a","padding":{},"paddingMobile":{"top":"0px","right":"0px","bottom":"0px","left":"0px"},"columnWidth":"100","verticalAlign":"center"} --><div id="wp-block-themeisle-blocks-advanced-column-9b9f344a" class="wp-block-themeisle-blocks-advanced-column">

<!-- wp:query {"queryId":2,"query":{"perPage":3,"pages":0,"offset":0,"postType":"product","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":true,"parents":[]},"displayLayout":{"type":"list"},"otterConditions":[]} -->
<div class="wp-block-query">
<!-- wp:post-template -->

<!-- wp:columns {"backgroundColor":"nv-site-bg","className":"ticss-7f501c1e","hasCustomCSS":true,"customCSS":".ticss-7f501c1e {\n  padding:0px;\n}\n"} --><div class="wp-block-columns ticss-7f501c1e has-nv-site-bg-background-color has-background">
<!-- wp:column {"verticalAlignment":"center","className":"ticss-7d3e23be","hasCustomCSS":true,"customCSS":".ticss-7d3e23be {\n  padding:0px;\n}\n.ticss-7d3e23be .wc-block-components-product-image {\nmargin:0px!important;\n}\n\n"} --><div class="wp-block-column is-vertically-aligned-center ticss-7d3e23be">
<!-- wp:woocommerce/product-image {"isDescendentOfQueryLoop":true,"className":"ticss-6caca011","hasCustomCSS":true,"customCSS":".ticss-6caca011 .wc-block-components-product-image {\nmargin:0px!important;\n}\n"} /-->
</div><!-- /wp:column -->

<!-- wp:column {"verticalAlignment":"center","className":"ticss-301bbfea","hasCustomCSS":true,"customCSS":".ticss-301bbfea {\n  padding:16px;\n}\n"} --><div class="wp-block-column is-vertically-aligned-center ticss-301bbfea">

<!-- wp:post-title /-->

<!-- wp:post-excerpt {"className":"ticss-b766edd8","hasCustomCSS":true,"customCSS":".ticss-b766edd8 {\nmargin-bottom:8px;\n}\n","__woocommerceNamespace":"woocommerce/product-query/product-summary"} /-->

<!-- wp:woocommerce/product-price {"isDescendentOfQueryLoop":true,"className":"ticss-53197206","hasCustomCSS":true,"customCSS":".ticss-53197206 {\n  margin-bottom:16px;\n}\n"} /-->

<!-- wp:spacer {"height":"24px"} -->
<div style="height:24px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:woocommerce/product-button {"isDescendentOfQueryLoop":true,"className":"ticss-406c4cd4","hasCustomCSS":true,"customCSS":".ticss-406c4cd4 {\n  margin-top:16px!important;\n}\n"} /-->
</div><!-- /wp:column -->
</div><!-- /wp:columns -->

<!-- /wp:post-template -->

<!-- wp:query-pagination {"layout":{"type":"flex","justifyContent":"space-between"}} -->
<!-- wp:query-pagination-previous /-->

<!-- wp:query-pagination-numbers /-->

<!-- wp:query-pagination-next /-->
<!-- /wp:query-pagination -->

<!-- wp:query-no-results -->

<!-- wp:paragraph {"align":"center","placeholder":"Add text or blocks that will display when a query returns no results."} -->
<p class="has-text-align-center">Unfortunately no products were found matching your selection.</p>
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
