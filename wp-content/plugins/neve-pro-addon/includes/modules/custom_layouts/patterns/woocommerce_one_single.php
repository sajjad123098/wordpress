<?php
/**
 * Pattern for One column no Sidebar WooCommerce Single.
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
class Woocommerce_One_Single extends Abstract_Pattern {

	/**
	 * Defines the pattern properties.
	 *
	 * @inheritDoc
	 */
	protected function define_pattern_props() {
		$this->namespace   = 'woocommerce-one-single-pattern';
		$this->title       = __( 'WooCommerce Single One-Column Layout', 'neve' );
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
<!-- wp:themeisle-blocks/advanced-columns {"id":"wp-block-themeisle-blocks-advanced-columns-1de8ad58","columns":1,"layout":"equal","layoutMobile":"collapsedRows","padding":{"top":"80px","right":"24px","bottom":"80px","left":"24px"},"margin":{"top":"0px","bottom":"0px"},"columnsWidth":"1140px","horizontalAlign":"center","backgroundColor":"var(\u002d\u002dnv-light-bg)","align":"full"} -->
<div id="wp-block-themeisle-blocks-advanced-columns-1de8ad58" class="wp-block-themeisle-blocks-advanced-columns alignfull has-1-columns has-desktop-equal-layout has-tablet-equal-layout has-mobile-collapsedRows-layout has-vertical-unset"><div class="wp-block-themeisle-blocks-advanced-columns-overlay"></div><div class="innerblocks-wrap"><!-- wp:themeisle-blocks/advanced-column {"id":"wp-block-themeisle-blocks-advanced-column-dc5d4d2d","padding":{"top":"0px","right":"0px","bottom":"0px","left":"0px"},"paddingMobile":{"top":"16px","right":"16px","bottom":"16px","left":"16px"},"columnWidth":"100"} -->
<div id="wp-block-themeisle-blocks-advanced-column-dc5d4d2d" class="wp-block-themeisle-blocks-advanced-column"><!-- wp:query {"queryId":0,"query":{"perPage":"1","pages":0,"offset":0,"postType":"product","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":true,"parents":[]},"displayLayout":{"type":"list","columns":3},"align":"full"} -->
<div class="wp-block-query alignfull"><!-- wp:post-template {"align":"full"} -->
<!-- wp:themeisle-blocks/advanced-columns {"id":"wp-block-themeisle-blocks-advanced-columns-1780aaed","columns":2,"layout":"equal","layoutMobile":"collapsedRows","margin":{"top":"0px","bottom":"0px"},"columnsWidth":"1140px","horizontalAlign":"center","align":"full"} -->
<div id="wp-block-themeisle-blocks-advanced-columns-1780aaed" class="wp-block-themeisle-blocks-advanced-columns alignfull has-2-columns has-desktop-equal-layout has-tablet-equal-layout has-mobile-collapsedRows-layout has-vertical-unset"><div class="wp-block-themeisle-blocks-advanced-columns-overlay"></div><div class="innerblocks-wrap"><!-- wp:themeisle-blocks/advanced-column {"id":"wp-block-themeisle-blocks-advanced-column-45c3be2b","padding":{"top":"0px","right":"0px","bottom":"0px","left":"0px"},"paddingMobile":{"top":"16px","right":"16px","bottom":"16px","left":"16px"},"columnWidth":"50"} -->
<div id="wp-block-themeisle-blocks-advanced-column-45c3be2b" class="wp-block-themeisle-blocks-advanced-column"><!-- wp:woocommerce/product-image {"showProductLink":false,"isDescendentOfQueryLoop":true} /--></div>
<!-- /wp:themeisle-blocks/advanced-column -->

<!-- wp:themeisle-blocks/advanced-column {"id":"wp-block-themeisle-blocks-advanced-column-b80af0df","padding":{"top":"24px","right":"24px","bottom":"24px","left":"24px"},"paddingMobile":{"top":"16px","right":"16px","bottom":"16px","left":"16px"},"columnWidth":"50","verticalAlign":"center"} -->
<div id="wp-block-themeisle-blocks-advanced-column-b80af0df" class="wp-block-themeisle-blocks-advanced-column"><!-- wp:woocommerce/product-sku {"isDescendentOfQueryLoop":true} /-->

<!-- wp:post-title {"__woocommerceNamespace":"woocommerce/product-query/product-title"} /-->

<!-- wp:woocommerce/product-price {"isDescendentOfQueryLoop":true} /-->

<!-- wp:spacer {"height":"40px"} -->
<div style="height:40px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:post-excerpt {"moreText":"","showMoreOnNewLine":false,"fontSize":"medium","__woocommerceNamespace":"woocommerce/product-query/product-summary"} /-->

<!-- wp:woocommerce/product-button {"isDescendentOfQueryLoop":true} /--></div>
<!-- /wp:themeisle-blocks/advanced-column --></div></div>
<!-- /wp:themeisle-blocks/advanced-columns -->
<!-- /wp:post-template --></div>
<!-- /wp:query --></div>
<!-- /wp:themeisle-blocks/advanced-column --></div></div>
<!-- /wp:themeisle-blocks/advanced-columns -->

<!-- wp:themeisle-blocks/advanced-columns {"id":"wp-block-themeisle-blocks-advanced-columns-335a2f03","columns":1,"layout":"equal","padding":{"top":"80px","bottom":"80px","left":"20px","right":"20px"},"paddingTablet":{"top":"40px","bottom":"40px","left":"20px","right":"20px"},"paddingMobile":{"top":"20px","bottom":"20px","left":"20px","right":"20px"},"margin":{"top":"0px","bottom":"0px"},"marginTablet":{"top":"0px","bottom":"0px"},"marginMobile":{"top":"0px","bottom":"0px"},"columnsWidth":1170,"horizontalAlign":"center","verticalAlign":"flex-start","color":"var(\u002d\u002dnv-text-dark-bg)","backgroundColor":"var(\u002d\u002dnv-dark-bg)","backgroundPosition":{"x":"0.00","y":"0.00"},"backgroundOverlayPosition":{"x":"0.00","y":"0.00"},"border":{"top":"0px","bottom":"0px","left":"0px","right":"0px"},"borderRadius":{"top":"0px","bottom":"0px","left":"0px","right":"0px"},"dividerTopWidth":100,"dividerTopWidthTablet":100,"dividerTopWidthMobile":100,"dividerTopHeight":100,"dividerTopHeightTablet":100,"dividerTopHeightMobile":100,"dividerBottomWidth":100,"dividerBottomWidthTablet":100,"dividerBottomWidthMobile":100,"dividerBottomHeight":100,"dividerBottomHeightTablet":100,"dividerBottomHeightMobile":100,"columnsHTMLTag":"section","align":"full","className":"has-dark-bg","otterConditions":[],"hasCustomCSS":true} -->
<section id="wp-block-themeisle-blocks-advanced-columns-335a2f03" class="wp-block-themeisle-blocks-advanced-columns alignfull has-1-columns has-desktop-equal-layout has-tablet-equal-layout has-mobile-equal-layout has-vertical-flex-start has-dark-bg"><div class="wp-block-themeisle-blocks-advanced-columns-overlay"></div><div class="innerblocks-wrap"><!-- wp:themeisle-blocks/advanced-column {"id":"wp-block-themeisle-blocks-advanced-column-5d90343f","padding":{"top":"0px","bottom":"0px","left":"0px","right":"0px"},"paddingTablet":{"top":"0px","bottom":"0px","left":"0px","right":"0px"},"paddingMobile":{"top":"0px","bottom":"0px","left":"0px","right":"0px"},"margin":{"top":"0px","bottom":"0px","left":"0px","right":"0px"},"marginTablet":{"top":null,"bottom":null,"left":null,"right":null},"marginMobile":{"top":null,"bottom":null,"left":null,"right":null},"backgroundPosition":{"x":"0.00","y":"0.00"},"border":{"top":"0px","bottom":"0px","left":"0px","right":"0px"},"borderRadius":{"top":"0px","bottom":"0px","left":"0px","right":"0px"},"columnWidth":"100","otterConditions":[]} -->
<div id="wp-block-themeisle-blocks-advanced-column-5d90343f" class="wp-block-themeisle-blocks-advanced-column"><!-- wp:themeisle-blocks/advanced-columns {"id":"wp-block-themeisle-blocks-advanced-columns-8e7cc7ae","columns":3,"layout":"equal","layoutMobile":"collapsedRows","padding":{"top":"0px","bottom":"0px","left":"0px","right":"0px"},"paddingTablet":{"top":"0px","bottom":"0px","left":"0px","right":"0px"},"paddingMobile":{"top":"0px","bottom":"0px","left":"0px","right":"0px"},"margin":{"top":"0px","bottom":"0px"},"marginTablet":{"top":"0px","bottom":"0px"},"marginMobile":{"top":"0px","bottom":"0px"},"columnsWidth":1170,"horizontalAlign":"center","verticalAlign":"flex-start","backgroundPosition":{"x":"0.00","y":"0.00"},"backgroundOverlayPosition":{"x":"0.00","y":"0.00"},"border":{"top":"0px","bottom":"0px","left":"0px","right":"0px"},"borderRadius":{"top":"0px","bottom":"0px","left":"0px","right":"0px"},"dividerTopWidth":100,"dividerTopWidthTablet":100,"dividerTopWidthMobile":100,"dividerTopHeight":100,"dividerTopHeightTablet":100,"dividerTopHeightMobile":100,"dividerBottomWidth":100,"dividerBottomWidthTablet":100,"dividerBottomWidthMobile":100,"dividerBottomHeight":100,"dividerBottomHeightTablet":100,"dividerBottomHeightMobile":100,"columnsHTMLTag":"section","otterConditions":[]} -->
<section id="wp-block-themeisle-blocks-advanced-columns-8e7cc7ae" class="wp-block-themeisle-blocks-advanced-columns has-3-columns has-desktop-equal-layout has-tablet-equal-layout has-mobile-collapsedRows-layout has-vertical-flex-start"><div class="wp-block-themeisle-blocks-advanced-columns-overlay"></div><div class="innerblocks-wrap"><!-- wp:themeisle-blocks/advanced-column {"id":"wp-block-themeisle-blocks-advanced-column-725f10a7","padding":{"top":"20px","bottom":"20px","left":"20px","right":"20px"},"paddingTablet":{"top":"20px","bottom":"20px","left":"20px","right":"20px"},"paddingMobile":{"top":"0px","bottom":"0px","left":"0px","right":"0px"},"margin":{"top":"0px","bottom":"0px","left":"0px","right":"0px"},"marginTablet":{"top":null,"bottom":null,"left":null,"right":null},"marginMobile":{"top":null,"bottom":null,"left":null,"right":null},"backgroundPosition":{"x":"0.00","y":"0.00"},"border":{"top":"0px","bottom":"0px","left":"0px","right":"0px"},"borderRadius":{"top":"0px","bottom":"0px","left":"0px","right":"0px"},"columnWidth":"33.33","otterConditions":[]} -->
<div id="wp-block-themeisle-blocks-advanced-column-725f10a7" class="wp-block-themeisle-blocks-advanced-column"><!-- wp:themeisle-blocks/advanced-heading {"id":"wp-block-themeisle-blocks-advanced-heading-ea5e7576","tag":"div","align":"left","fontSize":12,"fontSizeTablet":12,"fontSizeMobile":12,"textTransform":"uppercase","marginType":"linked"} -->
<div id="wp-block-themeisle-blocks-advanced-heading-ea5e7576" class="wp-block-themeisle-blocks-advanced-heading wp-block-themeisle-blocks-advanced-heading-ea5e7576"><strong>About the Product</strong></div>
<!-- /wp:themeisle-blocks/advanced-heading --></div>
<!-- /wp:themeisle-blocks/advanced-column -->

<!-- wp:themeisle-blocks/advanced-column {"id":"wp-block-themeisle-blocks-advanced-column-3c67f726","padding":{"top":"20px","bottom":"20px","left":"20px","right":"20px"},"paddingTablet":{"top":"20px","bottom":"20px","left":"20px","right":"20px"},"paddingMobile":{"top":"0px","bottom":"0px","left":"0px","right":"0px"},"margin":{"top":"0px","bottom":"0px","left":"0px","right":"0px"},"marginTablet":{"top":null,"bottom":null,"left":null,"right":null},"marginMobile":{"top":null,"bottom":null,"left":null,"right":null},"backgroundPosition":{"x":"0.00","y":"0.00"},"border":{"top":"0px","bottom":"0px","left":"0px","right":"0px"},"borderRadius":{"top":"0px","bottom":"0px","left":"0px","right":"0px"},"columnWidth":"33.33","otterConditions":[]} -->
<div id="wp-block-themeisle-blocks-advanced-column-3c67f726" class="wp-block-themeisle-blocks-advanced-column"><!-- wp:heading {"textAlign":"left","level":3} -->
<h3 class="has-text-align-left">Product detail</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"left","textColor":"nv-text-dark-bg"} -->
<p class="has-text-align-left has-nv-text-dark-bg-color has-text-color">Synergestic actionables. Organic growth deep dive but circle back or but what's the real problem we're trying to solve here?</p>
<!-- /wp:paragraph --></div>
<!-- /wp:themeisle-blocks/advanced-column -->

<!-- wp:themeisle-blocks/advanced-column {"id":"wp-block-themeisle-blocks-advanced-column-4956dbc0","padding":{"top":"20px","bottom":"20px","left":"20px","right":"20px"},"paddingTablet":{"top":"20px","bottom":"20px","left":"20px","right":"20px"},"paddingMobile":{"top":"0px","bottom":"0px","left":"0px","right":"0px"},"margin":{"top":"0px","bottom":"0px","left":"0px","right":"0px"},"marginTablet":{"top":null,"bottom":null,"left":null,"right":null},"marginMobile":{"top":null,"bottom":null,"left":null,"right":null},"backgroundPosition":{"x":"0.00","y":"0.00"},"border":{"top":"0px","bottom":"0px","left":"0px","right":"0px"},"borderRadius":{"top":"0px","bottom":"0px","left":"0px","right":"0px"},"columnWidth":"33.33","otterConditions":[]} -->
<div id="wp-block-themeisle-blocks-advanced-column-4956dbc0" class="wp-block-themeisle-blocks-advanced-column"><!-- wp:heading {"textAlign":"left","level":3} -->
<h3 class="has-text-align-left">Product detail</h3>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"left","textColor":"nv-text-dark-bg"} -->
<p class="has-text-align-left has-nv-text-dark-bg-color has-text-color">Synergestic actionables. Organic growth deep dive but circle back or but what's the real problem we're trying to solve here?</p>
<!-- /wp:paragraph --></div>
<!-- /wp:themeisle-blocks/advanced-column --></div></section>
<!-- /wp:themeisle-blocks/advanced-columns --></div>
<!-- /wp:themeisle-blocks/advanced-column --></div></section>
<!-- /wp:themeisle-blocks/advanced-columns -->
CONTENT;

	}
}
