<?php
/**
 * Description sidebar_right.php
 *
 * Author:      Bogdan Preda <friends@themeisle.com>
 * Created on:  07-12-{2022}
 *
 * @package neve/neve-pro
 */

namespace Neve_Pro\Modules\Custom_Layouts\Patterns\Core;

/**
 * Abstract Class Abstract_Sidebar_Right
 */
abstract class Abstract_Sidebar_Right extends Abstract_Sidebar {

	/**
	 * Wraps the provided content with sidebar layout data.
	 *
	 * @param string $content The content to wrap.
	 *
	 * @return string
	 */
	protected function wrap_content( $content ) {
		$sidebar_right = <<<SIDEBAR_RIGHT
<!-- wp:columns {"align":"full"} --><div class="wp-block-columns alignfull">
<!-- wp:column {"width":"{$this->get_container_widths()['content']}%","className":"nv-content"} --><div class="wp-block-column nv-content" style="flex-basis:{$this->get_container_widths()['content']}%">
{$content}
</div><!-- /wp:column -->
<!-- wp:column {"width":"{$this->get_container_widths()['sidebar']}%","className":"nv-sidebar"} --><div class="wp-block-column nv-sidebar" style="flex-basis:{$this->get_container_widths()['sidebar']}%">
{$this->sidebar_content()}
</div><!-- /wp:column -->
</div><!-- /wp:columns -->
SIDEBAR_RIGHT;

		return parent::wrap_content( $sidebar_right );
	}
}
