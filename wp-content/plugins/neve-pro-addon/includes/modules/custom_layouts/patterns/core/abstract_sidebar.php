<?php
/**
 * Abstract sidebar base class for all Neve patterns that use sidebar
 *
 * Author:      Bogdan Preda <friends@themeisle.com>
 * Created on:  07-12-{2022}
 *
 * @package Neve Pro Addon
 */

namespace Neve_Pro\Modules\Custom_Layouts\Patterns\Core;

/**
 * Abstract Class Abstract_Sidebar
 */
abstract class Abstract_Sidebar extends Abstract_Pattern {
	/**
	 * The content width.
	 *
	 * @var int $content_width
	 */
	protected $content_width = 25;

	/**
	 * Get the container widths.
	 *
	 * @return array
	 */
	protected function get_container_widths() {

		return [
			'sidebar' => ( 100 - (int) $this->content_width ),
			'content' => (int) $this->content_width,
		];
	}

	/**
	 * Define the pattern sidebar content.
	 *
	 * @return string
	 */
	abstract protected function sidebar_content();
}
