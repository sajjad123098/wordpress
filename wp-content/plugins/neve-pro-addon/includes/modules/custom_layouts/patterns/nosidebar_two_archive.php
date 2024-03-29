<?php
/**
 * Pattern for Two columns no Sidebar Archive.
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
 * Class Nosidebar_Two_Archive
 */
class Nosidebar_Two_Archive extends Abstract_Pattern {

	/**
	 * Defines the pattern properties.
	 *
	 * @inheritDoc
	 */
	protected function define_pattern_props() {
		$this->namespace   = 'nosidebar-two-archive-pattern';
		$this->title       = __( 'No Sidebar Two Columns Archive Layout', 'neve' );
		$this->description = _x( 'A layout for archive with content. Inherits from customizer.', 'Block pattern description', 'neve' );
		$this->categories  = [ 'featured', Patterns_Config::NEVE_PATTERN_CATEGORY ];

		$this->container_style = Mods::get( 'neve_blog_archive_container_style', 'contained' );
	}

	/**
	 * Content to include before the pattern wrap.
	 *
	 * @inheritDoc
	 * @return string
	 */
	protected function before_wrap() {
		return <<<BEFORE_WRAP
<!-- wp:cover {"useFeaturedImage":true,"dimRatio":90,"overlayColor":"neve-link-color","align":"full"} -->
<div class="wp-block-cover alignfull">
<span aria-hidden="true" class="wp-block-cover__background has-neve-link-color-background-color has-background-dim-90 has-background-dim"></span>
<div class="wp-block-cover__inner-container">
<!-- wp:heading {"textAlign":"center","level":1,"style":{"typography":{"textTransform":"capitalize","fontSize":"60px"}}} -->
<h1 class="has-text-align-center" style="font-size:60px;text-transform:capitalize"><o-dynamic data-type="postType">Post Type</o-dynamic> Archives</h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center","placeholder":"Write title…","style":{"typography":{"fontSize":"24px"}}} -->
<p class="has-text-align-center" style="font-size:24px">This is the Archive description</p>
<!-- /wp:paragraph -->
</div></div><!-- /wp:cover -->

<!-- wp:spacer {"height":"48px"} -->
<div style="height:48px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->
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
<!-- wp:query {"query":{"perPage":"4","pages":0,"offset":0,"postType":"post","order":"asc","orderBy":"title","author":"","search":"","exclude":[],"sticky":"","inherit":false},"displayLayout":{"type":"flex","columns":2}} --><div class="wp-block-query">

<!-- wp:post-template {"fontSize":"small"} -->
<!-- wp:post-featured-image {"isLink":true,"style":{"color":{"duotone":"unset"}}} /-->

<!-- wp:spacer {"height":"12px"} -->
<div style="height:12px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:post-title {"style":{"typography":{"fontStyle":"normal","fontWeight":"900","textTransform":"capitalize","fontSize":"28px"}}} /-->

<!-- wp:group {"align":"wide","layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"top","justifyContent":"left"}} --><div class="wp-block-group alignwide">
<!-- wp:paragraph {"style":{"typography":{"fontSize":"16px"}}} -->
<p style="font-size:16px">Posted on</p>
<!-- /wp:paragraph -->

<!-- wp:post-date {"format":"M j","style":{"typography":{"fontSize":"16px"}},"textColor":"neve-link-color"} /-->

<!-- wp:paragraph {"style":{"typography":{"fontSize":"16px"}}} -->
<p style="font-size:16px">/ By</p>
<!-- /wp:paragraph -->

<!-- wp:post-author {"showAvatar":false,"showBio":false,"style":{"typography":{"fontSize":"16px"}},"textColor":"neve-link-color"} /-->
</div><!-- /wp:group -->

<!-- wp:spacer {"height":"24px"} -->
<div style="height:24px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->

<!-- wp:post-excerpt {"textAlign":"left","moreText":"","showMoreOnNewLine":false,"style":{"typography":{"fontSize":"16px"}}} /-->
<!-- /wp:post-template -->

<!-- wp:query-pagination {"paginationArrow":"chevron","className":"nv-pattern-pagination","layout":{"type":"flex","justifyContent":"left","orientation":"horizontal","flexWrap":"wrap"},"fontSize":"small"} -->
<!-- wp:query-pagination-numbers {"style":{"typography":{"fontSize":"16px"}}} /-->
<!-- /wp:query-pagination -->

<!-- wp:query-no-results -->
<!-- wp:paragraph {"placeholder":"Add text or blocks that will display when a query returns no results."} -->
<p></p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results -->
</div><!-- /wp:query -->

<!-- wp:spacer {"height":"60px"} -->
<div style="height:60px" aria-hidden="true" class="wp-block-spacer"></div>
<!-- /wp:spacer -->
CONTENT;

	}
}
