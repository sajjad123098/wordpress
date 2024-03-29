<?php
/**
 * Abstract base class for all Neve patterns
 *
 * Author:      Bogdan Preda <friends@themeisle.com>
 * Created on:  07-12-{2022}
 *
 * @package Neve Pro Addon
 */

namespace Neve_Pro\Modules\Custom_Layouts\Patterns\Core;

/**
 * Abstract Class Abstract_Pattern
 */
abstract class Abstract_Pattern {
	/**
	 * The pattern namespace.
	 *
	 * @var string $namespace
	 */
	protected $namespace = 'base-pattern';
	/**
	 * The pattern title.
	 *
	 * @var string $title
	 */
	protected $title = '';
	/**
	 * The pattern description.
	 *
	 * @var string $description
	 */
	protected $description = '';
	/**
	 * A list of categories where the pattern is included.
	 *
	 * @var array $categories
	 */
	protected $categories = [];
	/**
	 * A list of post types to also allow the pattern.
	 *
	 * @var array $allowed_post_types
	 */
	protected $allowed_post_types = [];
	/**
	 * The container style to be used.
	 * Can be `contained` or  `full-width` as with the customizer options.
	 *
	 * @var string $container_style
	 */
	protected $container_style = 'contained';

	/**
	 * Base constructor.
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'register' ] );
		$this->define_pattern_props();
	}

	/**
	 * Defines the pattern properties.
	 *
	 * Available properties:
	 *      $namespace          string Default 'base-pattern'
	 *      $title              string Default ''
	 *      $description        string Default ''
	 *      $categories         array  Default []
	 *      $allowed_post_types array  Default []
	 *
	 * @return void
	 */
	abstract protected function define_pattern_props();

	/**
	 * Returns the pattern string.
	 *
	 * @return string
	 */
	abstract protected function pattern_content();

	/**
	 * Content to include before the pattern wrap.
	 *
	 * @return string
	 */
	protected function before_wrap() {
		return '';
	}

	/**
	 * Content to include after the pattern wrap.
	 *
	 * @return string
	 */
	protected function after_wrap() {
		return '';
	}

	/**
	 * Method to wrap the Pattern content with layout data.
	 *
	 * @param string $content Pattern content.
	 *
	 * @return string
	 */
	protected function wrap_content( $content ) {
		$container_class = Patterns_Config::LAYOUT_CONTAINER_CLASS;
		$align           = $this->container_style === 'contained' ? 'wide' : 'full';
		$align_class     = $this->container_style === 'contained' ? 'alignwide' : 'alignfull';
		return <<<PATTERN_WRAP_HTML
{$this->before_wrap()}
<!-- wp:columns {"align":"{$align}","className":"{$container_class}"} --><div class="wp-block-columns {$container_class} {$align_class}">
<!-- wp:column {"width":"100%"} --><div class="wp-block-column" style="flex-basis:100%">
{$content}
</div><!-- /wp:column -->
</div><!-- /wp:columns -->
{$this->after_wrap()}
PATTERN_WRAP_HTML;
	}

	/**
	 * Register the defined pattern.
	 */
	public function register() {
		register_block_pattern(
			'neve/' . $this->namespace,
			array(
				'title'       => $this->title,
				'description' => $this->description,
				'categories'  => $this->categories,
				'content'     => $this->wrap_content( $this->pattern_content() ),
				'postTypes'   => array_merge( [ 'neve_custom_layouts' ], $this->allowed_post_types ),
			)
		);
	}
}
