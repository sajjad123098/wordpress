<?php
/**
 * Template Layout class to use custom layouts as template.
 *
 * @package Neve_Pro\Modules\Custom_Layouts\Admin
 */

namespace Neve_Pro\Modules\Custom_Layouts\Admin;

use Neve_Pro\Modules\Custom_Layouts\Admin\Builders\Abstract_Builders;
use Neve_Pro\Traits\Conditional_Display;
use Neve_Pro\Traits\Core;

/**
 * Class Inside_Layout
 *
 * @package Neve_Pro\Modules\Custom_Layouts\Admin
 */
class Template_Layout {
	use Core;
	use Conditional_Display;

	/**
	 * Holds an instance of this class.
	 *
	 * @var null|Template_Layout
	 */
	private static $_instance = null;

	/**
	 * Return an instance of the class.
	 *
	 * @return Template_Layout;
	 */
	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Determine if is blog page
	 *
	 * @return bool
	 */
	private function is_blog_page() {
		global $post;
		$post_type = get_post_type( $post );
		return ( $post_type === 'post' ) && ( is_home() || is_archive() );
	}

	/**
	 * Init main hook.
	 */
	public function init() {
		add_action( 'template_redirect', [ $this, 'register_hooks' ] );
	}

	/**
	 * Trigger `neve_do_template_content_${layout}` hooks.
	 */
	public function register_hooks() {
		if ( is_singular( 'neve_custom_layouts' ) && is_preview() ) {
			return;
		}

		if ( is_single() ) {
			do_action( 'neve_do_template_content_single_post' );
			return;
		}

		if ( is_page() ) {
			do_action( 'neve_do_template_content_single_page' );
			return;
		}

		if ( is_archive() || $this->is_blog_page() ) {
			do_action( 'neve_do_template_content_archives' );
			return;
		}

		if ( is_search() ) {
			do_action( 'neve_do_template_content_search' );
			return;
		}
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @access public
	 * @since  3.0.5
	 */
	public function __clone() {}

	/**
	 * Un-serializing instances of this class is forbidden.
	 *
	 * @access public
	 * @since  3.0.5
	 */
	public function __wakeup() {}
}
