<?php
/**
 * Pattern for Three with 3 column no Sidebar WooCommerce Archive.
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
 * Class Woocommerce_Three_Archive
 */
class Woocommerce_Three_Archive extends Abstract_Pattern {

	/**
	 * Defines the pattern properties.
	 *
	 * @inheritDoc
	 */
	protected function define_pattern_props() {
		$this->namespace   = 'woocommerce-three-archive-pattern';
		$this->title       = __( 'WooCommerce Archive Three-Column Layout', 'neve' );
		$this->description = _x( 'A layout for WooCommerce Shop archive with three columns content. Inherits from customizer.', 'Block pattern description', 'neve' );
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
<!-- wp:columns {"align":"full","className":"nv-pattern-container"} --><div class="wp-block-columns alignfull nv-pattern-container">
<!-- wp:column {"width":"100%"} --><div class="wp-block-column" style="flex-basis:100%">
<!-- wp:columns {"align":"full","className":"nv-pattern-container"} --><div class="wp-block-columns alignfull nv-pattern-container">
<!-- wp:column {"width":"100%"} --><div class="wp-block-column" style="flex-basis:100%">
<!-- wp:themeisle-blocks/advanced-columns {"id":"wp-block-themeisle-blocks-advanced-columns-ce13189c","columns":1,"layout":"equal","padding":{"top":"80px","right":"24px","bottom":"80px","left":"24px"},"margin":{"top":"0px","bottom":"0px"},"columnsWidth":"900px","horizontalAlign":"center","verticalAlign":"center","color":"var(\u002d\u002dnv-text-dark-bg)","backgroundColor":"var(\u002d\u002dnv-dark-bg)","align":"full","className":"has-dark-bg"} --><div id="wp-block-themeisle-blocks-advanced-columns-ce13189c" class="wp-block-themeisle-blocks-advanced-columns alignfull has-1-columns has-desktop-equal-layout has-tablet-equal-layout has-mobile-equal-layout has-vertical-center has-dark-bg"><div class="wp-block-themeisle-blocks-advanced-columns-overlay"></div><div class="innerblocks-wrap">
<!-- wp:themeisle-blocks/advanced-column {"id":"wp-block-themeisle-blocks-advanced-column-3534880c","padding":{"top":"0px","right":"0px","bottom":"0px","left":"0px"},"paddingMobile":{"top":"16px","right":"16px","bottom":"16px","left":"16px"},"columnWidth":"100","verticalAlign":"center"} --><div id="wp-block-themeisle-blocks-advanced-column-3534880c" class="wp-block-themeisle-blocks-advanced-column">
<!-- wp:query-title {"type":"archive","textAlign":"center"} /-->
</div><!-- /wp:themeisle-blocks/advanced-column -->
</div>
</div><!-- /wp:themeisle-blocks/advanced-columns -->

<!-- wp:themeisle-blocks/advanced-columns {"id":"wp-block-themeisle-blocks-advanced-columns-a212c438","columns":1,"layout":"equal","padding":{"top":"80px","right":"24px","bottom":"80px","left":"24px"},"margin":{"top":"0px","bottom":"0px"},"columnsWidth":"1170px","horizontalAlign":"center","backgroundColor":"var(\u002d\u002dnv-light-bg)","align":"full"} --><div id="wp-block-themeisle-blocks-advanced-columns-a212c438" class="wp-block-themeisle-blocks-advanced-columns alignfull has-1-columns has-desktop-equal-layout has-tablet-equal-layout has-mobile-equal-layout has-vertical-unset"><div class="wp-block-themeisle-blocks-advanced-columns-overlay"></div><div class="innerblocks-wrap">
<!-- wp:themeisle-blocks/advanced-column {"id":"wp-block-themeisle-blocks-advanced-column-b2ee55d3","padding":{"top":"0px","right":"0px","bottom":"0px","left":"0px"},"paddingMobile":{"top":"16px","right":"16px","bottom":"16px","left":"16px"},"columnWidth":"100"} --><div id="wp-block-themeisle-blocks-advanced-column-b2ee55d3" class="wp-block-themeisle-blocks-advanced-column">

<!-- wp:query {"queryId":14,"query":{"perPage":3,"pages":0,"offset":0,"postType":"product","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":true,"parents":[]},"displayLayout":{"type":"flex","columns":3},"align":"full"} -->
<div class="wp-block-query alignfull">

<!-- wp:post-template {"align":"full"} -->

<!-- wp:themeisle-blocks/advanced-columns {"id":"wp-block-themeisle-blocks-advanced-columns-3e0e43e5","columns":1,"layout":"equal","margin":{"top":"0px","bottom":"0px"},"otterConditions":[]} --><div id="wp-block-themeisle-blocks-advanced-columns-3e0e43e5" class="wp-block-themeisle-blocks-advanced-columns has-1-columns has-desktop-equal-layout has-tablet-equal-layout has-mobile-equal-layout has-vertical-unset">
<div class="wp-block-themeisle-blocks-advanced-columns-overlay"></div>

<div class="innerblocks-wrap">

<!-- wp:themeisle-blocks/advanced-column {"id":"wp-block-themeisle-blocks-advanced-column-07dd79d6","columnWidth":"100","className":"ticss-82e3334e"} --><div id="wp-block-themeisle-blocks-advanced-column-07dd79d6" class="wp-block-themeisle-blocks-advanced-column ticss-82e3334e">

<!-- wp:woocommerce/product-image {"isDescendentOfQueryLoop":true} /-->

<!-- wp:spacer {"height":"8px"} -->
<div style="height:8px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:post-title /-->

<!-- wp:spacer {"height":"16px"} -->
<div style="height:16px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:woocommerce/product-price {"isDescendentOfQueryLoop":true,"textAlign":"center"} /-->

<!-- wp:spacer {"height":"16px"} -->
<div style="height:16px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:themeisle-blocks/button-group {"id":"wp-block-themeisle-blocks-button-group-47613917","align":{"desktop":"center"}} -->
<div id="wp-block-themeisle-blocks-button-group-47613917" class="wp-block-themeisle-blocks-button-group wp-block-buttons align-center-desktop">
<!-- wp:themeisle-blocks/button {"color":"var(\u002d\u002dnv-text-dark-bg)","background":"var(\u002d\u002dnv-secondary-accent)","hoverColor":"var(\u002d\u002dnv-text-dark-bg)","hoverBackground":"var(\u002d\u002dnv-dark-bg)"} -->
<div class="wp-block-themeisle-blocks-button wp-block-button"><a href="#otterDynamicLink?type=postURL&amp;context=query" target="_self" rel="noopener noreferrer" class="wp-block-button__link"><span>Details</span></a></div>
<!-- /wp:themeisle-blocks/button --></div>
<!-- /wp:themeisle-blocks/button-group --></div>
<!-- /wp:themeisle-blocks/advanced-column --></div></div>
<!-- /wp:themeisle-blocks/advanced-columns -->
<!-- /wp:post-template -->

<!-- wp:spacer {"height":"40px"} -->
<div style="height:40px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:query-pagination {"paginationArrow":"chevron","layout":{"type":"flex","justifyContent":"space-between","orientation":"horizontal","flexWrap":"nowrap"}} -->
<!-- wp:query-pagination-previous /-->

<!-- wp:query-pagination-numbers /-->

<!-- wp:query-pagination-next /-->
<!-- /wp:query-pagination -->

<!-- wp:query-no-results -->
<!-- wp:paragraph {"align":"center","placeholder":"Add text or blocks that will display when a query returns no results.","fontSize":"large"} -->
<p class="has-text-align-center has-large-font-size">No products were found.</p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results -->

</div><!-- /wp:query -->
</div><!-- /wp:themeisle-blocks/advanced-column -->

</div>

</div><!-- /wp:themeisle-blocks/advanced-columns -->

<!-- wp:themeisle-blocks/advanced-columns {"id":"wp-block-themeisle-blocks-advanced-columns-f5fb79f8","columns":1,"layout":"equal","padding":{"top":"80px","right":"24px","bottom":"80px","left":"24px"},"margin":{"top":"0px","bottom":"0px"},"columnsWidth":"900px","horizontalAlign":"center","backgroundColor":"var(\u002d\u002dnv-dark-bg)","backgroundOverlayOpacity":15,"backgroundOverlayImage":{"id":194,"url":"https://demosites.io/otter/wp-content/uploads/sites/664/2022/08/comingSoon_cover.png"},"backgroundOverlayAttachment":"fixed","backgroundOverlayPosition":{"x":0.15,"y":0.73},"backgroundOverlayRepeat":"no-repeat","backgroundOverlaySize":"cover","align":"full","className":"has-dark-bg"} -->
<div id="wp-block-themeisle-blocks-advanced-columns-f5fb79f8" class="wp-block-themeisle-blocks-advanced-columns alignfull has-1-columns has-desktop-equal-layout has-tablet-equal-layout has-mobile-equal-layout has-vertical-unset has-dark-bg"><div class="wp-block-themeisle-blocks-advanced-columns-overlay"></div><div class="innerblocks-wrap"><!-- wp:themeisle-blocks/advanced-column {"id":"wp-block-themeisle-blocks-advanced-column-c58d6648","padding":{"top":"0px","right":"0px","bottom":"0px","left":"0px"},"paddingMobile":{"top":"16px","right":"16px","bottom":"16px","left":"16px"},"columnWidth":"100","verticalAlign":"center"} -->
<div id="wp-block-themeisle-blocks-advanced-column-c58d6648" class="wp-block-themeisle-blocks-advanced-column"><!-- wp:heading {"textAlign":"center"} -->
<h2 class="has-text-align-center">This is just a heading at the bottom</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","textColor":"nv-text-dark-bg"} -->
<p class="has-text-align-center has-nv-text-dark-bg-color has-text-color">Lorem ipsum dolor si amet</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons">

<!-- wp:button {"textColor":"nv-text-dark-bg","className":"is-style-secondary"} -->
<div class="wp-block-button is-style-secondary"><a class="wp-block-button__link has-nv-text-dark-bg-color has-text-color wp-element-button">Learn more</a></div>
<!-- /wp:button -->

</div><!-- /wp:buttons -->
</div><!-- /wp:themeisle-blocks/advanced-column -->

</div>

</div><!-- /wp:themeisle-blocks/advanced-columns -->
</div><!-- /wp:column -->
</div><!-- /wp:columns -->
</div><!-- /wp:column -->
</div><!-- /wp:columns -->
CONTENT;

	}
}
