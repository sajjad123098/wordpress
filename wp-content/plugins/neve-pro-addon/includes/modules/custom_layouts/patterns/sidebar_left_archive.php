<?php
/**
 * Pattern for sidebar left.
 *
 * Author:      Bogdan Preda <friends@themeisle.com>
 * Created on:  02-12-{2022}
 *
 * @package Neve Pro Addon
 */

namespace Neve_Pro\Modules\Custom_Layouts\Patterns;

use Neve\Core\Settings\Mods;
use Neve_Pro\Modules\Custom_Layouts\Patterns\Core\Abstract_Sidebar_Left;
use Neve_Pro\Modules\Custom_Layouts\Patterns\Core\Patterns_Config;

/**
 * Class Sidebar_Left_Archive
 */
class Sidebar_Left_Archive extends Abstract_Sidebar_Left {

	/**
	 * Defines the pattern properties.
	 *
	 * @inheritDoc
	 */
	protected function define_pattern_props() {
		$this->namespace   = 'sidebar-left-archive-pattern';
		$this->title       = __( 'Sidebar Left Archive Layout', 'neve' );
		$this->description = _x( 'A layout for left sidebar archive with content. Inherits from customizer.', 'Block pattern description', 'neve' );
		$this->categories  = [ 'featured', Patterns_Config::NEVE_PATTERN_CATEGORY ];

		$this->container_style = Mods::get( 'neve_blog_archive_container_style', 'contained' );
		$this->content_width   = Mods::get( 'neve_blog_archive_content_width', false );
		if ( $this->content_width === false || $this->content_width >= 90 ) {
			$this->content_width = 75;
		}
	}

	/**
	 * Content to include before the pattern wrap.
	 *
	 * @inheritDoc
	 * @return string
	 */
	protected function before_wrap() {
		return <<<BEFORE_WRAP
<!-- wp:spacer {"height":"48px"} --><div style="height:48px" aria-hidden="true" class="wp-block-spacer"></div><!-- /wp:spacer -->
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
<!-- wp:query {"query":{"perPage":"6","pages":0,"offset":0,"postType":"post","order":"asc","orderBy":"title","author":"","search":"","exclude":[],"sticky":"","inherit":false},"displayLayout":{"type":"flex","columns":2}} -->
<div class="wp-block-query">
<!-- wp:post-template {"fontSize":"small"} -->
<!-- wp:post-featured-image {"isLink":true,"style":{"color":{"duotone":"unset"}}} /-->

<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"top","justifyContent":"space-between"}} --><div class="wp-block-group nv-author-bio-text-wrapper">
<!-- wp:post-author /-->

<!-- wp:post-date /-->
</div><!-- /wp:group -->

<!-- wp:post-title {"isLink":true,"style":{"typography":{"fontStyle":"normal","fontWeight":"900","textTransform":"uppercase"}},"fontSize":"medium"} /-->

<!-- wp:post-excerpt {"textAlign":"left","fontSize":"small"} /-->
<!-- /wp:post-template -->

<!-- wp:query-pagination {"paginationArrow":"chevron","layout":{"type":"flex","justifyContent":"space-between","orientation":"horizontal"},"fontSize":"small"} -->
<!-- wp:query-pagination-previous /-->

<!-- wp:query-pagination-numbers /-->

<!-- wp:query-pagination-next /-->
<!-- /wp:query-pagination -->

<!-- wp:query-no-results -->
<!-- wp:paragraph {"placeholder":"Add text or blocks that will display when a query returns no results."} -->
<p></p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results -->
</div><!-- /wp:query -->
CONTENT;

	}

	/**
	 * Define the pattern sidebar content.
	 *
	 * @inheritDoc
	 * @return string
	 */
	protected function sidebar_content() {
		return <<<SIDEBAR
<!-- wp:search {"label":"Search","showLabel":false,"buttonText":"Search","buttonPosition":"button-inside","buttonUseIcon":true} /-->

<!-- wp:heading {"level":3} -->
<h3>Recent Posts</h3>
<!-- /wp:heading -->

<!-- wp:latest-posts {"displayPostDate":true,"align":"left"} /-->
SIDEBAR;
	}
}
