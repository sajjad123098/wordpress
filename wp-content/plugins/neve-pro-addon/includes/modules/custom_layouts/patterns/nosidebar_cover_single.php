<?php
/**
 * Pattern for no sidebar cover post.
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
 * Class Nosidebar_Cover_Single
 */
class Nosidebar_Cover_Single extends Abstract_Pattern {

	/**
	 * Defines the pattern properties.
	 *
	 * @inheritDoc
	 */
	protected function define_pattern_props() {
		$this->namespace   = 'nosidebar-cover-single-pattern';
		$this->title       = __( 'Cover Single Layout', 'neve' );
		$this->description = _x( 'A layout for single post with cover. Inherits from customizer.', 'Block pattern description', 'neve' );
		$this->categories  = [ 'featured', Patterns_Config::NEVE_PATTERN_CATEGORY ];

		$this->container_style = 'full-width';
	}

	/**
	 * Content to include before the pattern wrap.
	 *
	 * @inheritDoc
	 * @return string
	 */
	protected function before_wrap() {
		$home_url = get_home_url();
		return <<<BEFORE_WRAP
<!-- wp:query {"query":{"perPage":1,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":true}} -->
<div class="wp-block-query"><!-- wp:post-template -->
<!-- wp:cover {"useFeaturedImage":true,"dimRatio":80,"overlayColor":"neve-link-color","align":"full"} -->
<div class="wp-block-cover alignfull"><span aria-hidden="true" class="wp-block-cover__background has-neve-link-color-background-color has-background-dim-80 has-background-dim"></span><div class="wp-block-cover__inner-container"><!-- wp:columns {"align":"wide","className":"nv-pattern-container"} -->
<div class="wp-block-columns alignwide nv-pattern-container"><!-- wp:column {"width":"75%"} -->
<div class="wp-block-column" style="flex-basis:75%"><!-- wp:spacer {"height":"90px"} -->
<div style="height:90px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:heading {"textAlign":"center","level":1,"style":{"typography":{"fontSize":"60px","textTransform":"capitalize"}}} -->
<h1 class="has-text-align-center" style="font-size:60px;text-transform:capitalize"><o-dynamic data-type="postTitle">Post Title</o-dynamic></h1>
<!-- /wp:heading -->

<!-- wp:group {"layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
<div class="wp-block-group"><!-- wp:spacer {"height":"12px"} -->
<div style="height:12px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:image {"id":1,"sizeSlug":"full","linkDestination":"none","className":"is-style-rounded"} -->
<figure class="wp-block-image size-full is-style-rounded"><img src="{$home_url}/wp-json/otter/v1/dynamic/?type=author&amp;context=query&amp;uid=1" alt=""/></figure>
<!-- /wp:image -->

<!-- wp:spacer {"height":"12px"} -->
<div style="height:12px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group"><!-- wp:paragraph {"style":{"typography":{"fontSize":"16px"}}} -->
<p style="font-size:16px">Posted on</p>
<!-- /wp:paragraph -->

<!-- wp:post-date {"format":"M j, Y","style":{"typography":{"fontSize":"16px"}}} /-->

<!-- wp:paragraph {"style":{"typography":{"fontSize":"16px"}}} -->
<p style="font-size:16px">by <o-dynamic data-type="authorName">Author Name</o-dynamic></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:spacer {"height":"75px"} -->
<div style="height:75px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div></div>
<!-- /wp:cover -->
<!-- /wp:post-template --></div>
<!-- /wp:query -->
BEFORE_WRAP;

	}

	/**
	 * Returns the pattern string.
	 *
	 * @inheritDoc
	 * @return string
	 */
	protected function pattern_content() {
		return <<<CONTENT
<!-- wp:cover {"overlayColor":"nv-light-bg"} --><div class="wp-block-cover">
<span aria-hidden="true" class="wp-block-cover__background has-nv-light-bg-background-color has-background-dim-100 has-background-dim"></span>
<div class="wp-block-cover__inner-container">

<!-- wp:spacer {"height":"60px"} -->
<div style="height:60px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:columns {"verticalAlignment":"top","isStackedOnMobile":false,"align":"wide","className":"nv-pattern-container"} --><div class="wp-block-columns alignwide are-vertically-aligned-top is-not-stacked-on-mobile nv-pattern-container">
<!-- wp:column {"verticalAlignment":"top","width":"75%"} --><div class="wp-block-column is-vertically-aligned-top" style="flex-basis:75%">
<!-- wp:columns {"align":"full","backgroundColor":"nv-site-bg","className":"nv-pattern-container"} --><div class="wp-block-columns alignfull nv-pattern-container has-nv-site-bg-background-color has-background">
<!-- wp:column {"width":"75%"} --><div class="wp-block-column" style="flex-basis:75%">

<!-- wp:spacer {"height":"80px"} -->
<div style="height:80px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:query {"query":{"perPage":"1","pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":true},"displayLayout":{"type":"list"},"textColor":"nv-dark-bg"} --><div class="wp-block-query has-nv-dark-bg-color has-text-color">

<!-- wp:post-template -->
<!-- wp:post-content /-->
<!-- /wp:post-template -->

<!-- wp:query-no-results -->
<!-- wp:paragraph {"placeholder":"Add text or blocks that will display when a query returns no results."} -->
<p></p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results -->
</div><!-- /wp:query -->

<!-- wp:spacer {"height":"80px"} -->
<div style="height:80px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->
</div><!-- /wp:column -->
</div><!-- /wp:columns -->
</div><!-- /wp:column -->
</div><!-- /wp:columns -->

<!-- wp:spacer {"height":"60px"} -->
<div style="height:60px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->
</div></div><!-- /wp:cover -->

<!-- wp:cover {"overlayColor":"nv-site-bg","align":"full"} --><div class="wp-block-cover alignfull">
<span aria-hidden="true" class="wp-block-cover__background has-nv-site-bg-background-color has-background-dim-100 has-background-dim"></span>
<div class="wp-block-cover__inner-container">
<!-- wp:columns {"align":"wide","textColor":"neve-text-color","className":"nv-pattern-container"} --><div class="wp-block-columns alignwide nv-pattern-container has-neve-text-color-color has-text-color">
<!-- wp:column {"width":"50%"} --><div class="wp-block-column" style="flex-basis:50%">

<!-- wp:heading {"textAlign":"center","level":6,"style":{"typography":{"textTransform":"uppercase","fontSize":"16px"}}} -->
<h6 class="has-text-align-center" style="font-size:16px;text-transform:uppercase"><strong>Author</strong></h6>
<!-- /wp:heading -->

<!-- wp:heading {"textAlign":"center","level":6,"style":{"typography":{"textTransform":"capitalize","fontSize":"28px"}}} -->
<h6 class="has-text-align-center" style="font-size:28px;text-transform:capitalize"><o-dynamic data-type="authorName">Author Name</o-dynamic></h6>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center"><o-dynamic data-type="authorDescription">Author Description</o-dynamic></p>
<!-- /wp:paragraph -->
</div><!-- /wp:column -->
</div><!-- /wp:columns -->
</div></div><!-- /wp:cover -->

<!-- wp:cover {"overlayColor":"neve-link-color","align":"full"} --><div class="wp-block-cover alignfull">
<span aria-hidden="true" class="wp-block-cover__background has-neve-link-color-background-color has-background-dim-100 has-background-dim"></span>
<div class="wp-block-cover__inner-container">
<!-- wp:columns {"align":"wide","textColor":"nv-site-bg","className":"nv-pattern-container"} --><div class="wp-block-columns alignwide nv-pattern-container has-nv-site-bg-color has-text-color">
<!-- wp:column {"width":"75%"} --><div class="wp-block-column" style="flex-basis:75%">

<!-- wp:heading {"textAlign":"center","level":6,"style":{"typography":{"textTransform":"uppercase"}},"fontSize":"small"} -->
<h6 class="has-text-align-center has-small-font-size" style="text-transform:uppercase"><strong>Share on social</strong></h6>
<!-- /wp:heading -->

<!-- wp:social-links {"iconColor":"neve-link-color","iconColorValue":"var(\u002d\u002dnv-primary-accent)","iconBackgroundColor":"nv-site-bg","iconBackgroundColorValue":"var(\u002d\u002dnv-site-bg)","size":"has-normal-icon-size","className":"is-style-default","layout":{"type":"flex","justifyContent":"center"}} -->
<ul class="wp-block-social-links has-normal-icon-size has-icon-color has-icon-background-color is-style-default">
<!-- wp:social-link {"url":"facebook.com","service":"facebook","label":"Facebook"} /-->
<!-- wp:social-link {"url":"twitter.com","service":"twitter"} /-->
<!-- wp:social-link {"url":"tiktok.com","service":"tiktok"} /-->
<!-- wp:social-link {"url":"reddit.com","service":"reddit"} /-->
<!-- wp:social-link {"url":"linkedin.com","service":"linkedin"} /-->
<!-- wp:social-link {"url":"instagram.com","service":"instagram"} /-->
</ul><!-- /wp:social-links -->

</div><!-- /wp:column -->
</div><!-- /wp:columns -->

</div></div><!-- /wp:cover -->
CONTENT;

	}
}
