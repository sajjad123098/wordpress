<?php
/**
 * Pattern for WooCommerce Single Once Column Variation Three.
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
class Woocommerce_Three_Single extends Abstract_Pattern {

	/**
	 * Defines the pattern properties.
	 *
	 * @inheritDoc
	 */
	protected function define_pattern_props() {
		$this->namespace   = 'woocommerce-one-single-three-pattern';
		$this->title       = __( 'WooCommerce Single Variation Three Layout', 'neve' );
		$this->description = _x( 'A layout for WooCommerce Product. Inherits from customizer.', 'Block pattern description', 'neve' );
		$this->categories  = [ 'featured', Patterns_Config::NEVE_PATTERN_CATEGORY ];

		$this->container_style = Mods::get( 'neve_single_product_container_style', 'contained' );
	}

	/**
	 * Returns the pattern string.
	 *
	 * @inheritDoc
	 * @return string
	 */
	protected function pattern_content() {
		return <<<CONTENT
<!-- wp:themeisle-blocks/advanced-columns {"id":"wp-block-themeisle-blocks-advanced-columns-ce13189c","columns":1,"layout":"equal","padding":{"top":"80px","right":"24px","bottom":"80px","left":"24px"},"margin":{"top":"0px","bottom":"0px"},"columnsWidth":"900px","horizontalAlign":"center","verticalAlign":"top","backgroundColor":"var(\u002d\u002dnv-light-bg)","border":{"top":"0px","right":"0px","bottom":"0px","left":"0px"},"align":"full","className":""} --><div id="wp-block-themeisle-blocks-advanced-columns-ce13189c" class="wp-block-themeisle-blocks-advanced-columns alignfull has-1-columns has-desktop-equal-layout has-tablet-equal-layout has-mobile-equal-layout has-vertical-top">
<div class="wp-block-themeisle-blocks-advanced-columns-overlay"></div>
<div class="innerblocks-wrap">
<!-- wp:themeisle-blocks/advanced-column {"id":"wp-block-themeisle-blocks-advanced-column-3534880c","padding":{"top":"0px","right":"0px","bottom":"0px","left":"0px"},"paddingMobile":{"top":"16px","right":"16px","bottom":"16px","left":"16px"},"columnWidth":"100","verticalAlign":"top"} --><div id="wp-block-themeisle-blocks-advanced-column-3534880c" class="wp-block-themeisle-blocks-advanced-column">
<!-- wp:query {"queryId":0,"query":{"perPage":"1","pages":0,"offset":0,"postType":"product","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":true,"parents":[]},"displayLayout":{"type":"list","columns":3},"align":"full","otterConditions":[]} -->
<div class="wp-block-query alignfull">
<!-- wp:post-template {"__woocommerceNamespace":"woocommerce/product-query/product-template"} -->
<!-- wp:post-title {"level":1,"__woocommerceNamespace":"woocommerce/product-query/product-title"} /-->
<!-- /wp:post-template -->
</div><!-- /wp:query -->
</div><!-- /wp:themeisle-blocks/advanced-column -->
</div>
</div><!-- /wp:themeisle-blocks/advanced-columns -->

<!-- wp:themeisle-blocks/advanced-columns {"id":"wp-block-themeisle-blocks-advanced-columns-1de8ad58","columns":1,"layout":"equal","layoutMobile":"collapsedRows","padding":{"top":"64px","right":"24px","bottom":"64px","left":"24px"},"margin":{"top":"0px","bottom":"0px"},"columnsWidth":"1140px","horizontalAlign":"center","align":"full","className":""} --><div id="wp-block-themeisle-blocks-advanced-columns-1de8ad58" class="wp-block-themeisle-blocks-advanced-columns alignfull has-1-columns has-desktop-equal-layout has-tablet-equal-layout has-mobile-collapsedRows-layout has-vertical-unset">
<div class="wp-block-themeisle-blocks-advanced-columns-overlay"></div>
<div class="innerblocks-wrap">
<!-- wp:themeisle-blocks/advanced-column {"id":"wp-block-themeisle-blocks-advanced-column-dc5d4d2d","padding":{"top":"0px","right":"0px","bottom":"0px","left":"0px"},"columnWidth":"100"} --><div id="wp-block-themeisle-blocks-advanced-column-dc5d4d2d" class="wp-block-themeisle-blocks-advanced-column">
<!-- wp:query {"queryId":0,"query":{"perPage":"1","pages":0,"offset":0,"postType":"product","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":true,"parents":[]},"displayLayout":{"type":"list","columns":3},"align":"full","otterConditions":[]} -->
<div class="wp-block-query alignfull">

<!-- wp:post-template {"align":"full"} -->
<!-- wp:spacer {"height":"24px"} -->
<div style="height:24px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:themeisle-blocks/advanced-columns {"id":"wp-block-themeisle-blocks-advanced-columns-1780aaed","columns":2,"layout":"equal","layoutMobile":"collapsedRows","margin":{"top":"0px","bottom":"0px"},"columnsWidth":"1140px","horizontalAlign":"center","align":"full"} --><div id="wp-block-themeisle-blocks-advanced-columns-1780aaed" class="wp-block-themeisle-blocks-advanced-columns alignfull has-2-columns has-desktop-equal-layout has-tablet-equal-layout has-mobile-collapsedRows-layout has-vertical-unset">
<div class="wp-block-themeisle-blocks-advanced-columns-overlay"></div>
<div class="innerblocks-wrap">

<!-- wp:themeisle-blocks/advanced-column {"id":"wp-block-themeisle-blocks-advanced-column-b80af0df","padding":{"top":"24px","right":"24px","bottom":"24px","left":"24px"},"paddingMobile":{"top":"0px","right":"0px","bottom":"0px","left":"0px"},"columnWidth":"50","verticalAlign":"top"} --><div id="wp-block-themeisle-blocks-advanced-column-b80af0df" class="wp-block-themeisle-blocks-advanced-column">
<!-- wp:post-excerpt {"moreText":"","showMoreOnNewLine":false,"className":"ticss-09628905","fontSize":"medium","hasCustomCSS":true,"customCSS":".ticss-09628905 {\n  margin-bottom:16px;\n}\n","__woocommerceNamespace":"woocommerce/product-query/product-summary"} /-->

<!-- wp:woocommerce/product-price {"isDescendentOfQueryLoop":true,"className":"ticss-12fedec3","hasCustomCSS":true,"customCSS":".ticss-12fedec3 .wc-block-components-product-price {\n  font-size:1.35em!important;\n}\n"} /-->

<!-- wp:spacer {"height":"24px"} -->
<div style="height:24px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:woocommerce/product-button {"isDescendentOfQueryLoop":true} /-->

<!-- wp:spacer {"height":"24px"} -->
<div style="height:24px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:woocommerce/product-sku {"isDescendentOfQueryLoop":true} /-->
</div><!-- /wp:themeisle-blocks/advanced-column -->

<!-- wp:themeisle-blocks/advanced-column {"id":"wp-block-themeisle-blocks-advanced-column-45c3be2b","padding":{"top":"0px","right":"0px","bottom":"0px","left":"0px"},"columnWidth":"50"} --><div id="wp-block-themeisle-blocks-advanced-column-45c3be2b" class="wp-block-themeisle-blocks-advanced-column">
<!-- wp:woocommerce/product-image {"showProductLink":false,"isDescendentOfQueryLoop":true} /-->
</div><!-- /wp:themeisle-blocks/advanced-column -->
</div>
</div><!-- /wp:themeisle-blocks/advanced-columns -->
<!-- /wp:post-template -->
</div><!-- /wp:query -->
</div><!-- /wp:themeisle-blocks/advanced-column -->
</div>
</div><!-- /wp:themeisle-blocks/advanced-columns -->

<!-- wp:themeisle-blocks/advanced-columns {"id":"wp-block-themeisle-blocks-advanced-columns-ff5f6c40","columns":1,"layout":"equal","padding":{"top":"64px","right":"24px","bottom":"64px","left":"24px"},"paddingTablet":{"top":"64px","right":"24px","bottom":"64px","left":"24px"},"paddingMobile":{"top":"40px","bottom":"40px"},"margin":{"top":"0px","bottom":"0px"},"columnsWidth":"1120px","horizontalAlign":"center","verticalAlign":"center","backgroundColor":"var(\u002d\u002dnv-light-bg)","align":"full","className":"ticss-c00aadba","hasCustomCSS":true,"customCSS":""} --><div id="wp-block-themeisle-blocks-advanced-columns-ff5f6c40" class="wp-block-themeisle-blocks-advanced-columns alignfull has-1-columns has-desktop-equal-layout has-tablet-equal-layout has-mobile-equal-layout has-vertical-center ticss-c00aadba">
<div class="wp-block-themeisle-blocks-advanced-columns-overlay"></div>
<div class="innerblocks-wrap">
<!-- wp:themeisle-blocks/advanced-column {"id":"wp-block-themeisle-blocks-advanced-column-70a1511f","columnWidth":"100"} --><div id="wp-block-themeisle-blocks-advanced-column-70a1511f" class="wp-block-themeisle-blocks-advanced-column">
<!-- wp:post-content /-->
</div><!-- /wp:themeisle-blocks/advanced-column -->
</div>
</div><!-- /wp:themeisle-blocks/advanced-columns -->
CONTENT;

	}
}
