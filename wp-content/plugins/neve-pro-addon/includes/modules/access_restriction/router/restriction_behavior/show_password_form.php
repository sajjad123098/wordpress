<?php
/**
 * Show_Password_Form
 *
 * @package Neve_Pro\Modules\Access_Restriction\Router\Restriction_Behavior
 */
namespace  Neve_Pro\Modules\Access_Restriction\Router\Restriction_Behavior;

use Neve_Pro\Modules\Access_Restriction\Router\Restriction_Behavior\Restriction_Behavior;
use Neve\Core\Dynamic_Css;
use Neve_Pro\Modules\Access_Restriction\Utility\Context_Trait;

/**
 * Class Show_Password_Form
 */
class Show_Password_Form implements Restriction_Behavior {
	use Context_Trait;
	/**
	 * Some restrictions on the WP_Query instance.
	 *
	 * @return void
	 */
	public function restrict_query() {
		global $wp_query;
		$wp_query->set_404();
		status_header( 404 );
	}

	/**
	 * Load the password form template.
	 *
	 * @return void
	 */
	public function view() {
		include NEVE_PRO_PATH . 'includes/modules/access_restriction/templates/template-password-login.php';
	}

	/**
	 * Do not make any redirection since we are already on the password form.
	 *
	 * @return void
	 */
	public function redirect() {
		// no redirection for the that behavior
		exit;
	}

	/**
	 * Register hooks
	 * - Modify the page title
	 * - Remove the 404 class from the body
	 * - Add some styles for the password form
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_filter( 'pre_get_document_title', array( $this, 'modify_page_title' ) );
		add_filter( 'body_class', array( $this, 'remove_404_class' ), PHP_INT_MAX, 1 );
		add_action( 'wp_enqueue_scripts', array( $this, 'style' ) );

		$this->add_title_filter_for_context();
	}

	/**
	 * Handle removing 404 class from the body classes.
	 *
	 * @param  array $classes current classes.
	 * @return array
	 */
	public function remove_404_class( $classes ) {
		$index = array_search( 'error404', $classes, true );

		if ( $index === false ) {
			return $classes;
		}

		unset( $classes[ $index ] );
		return $classes;
	}

	/**
	 * Modify the page title.
	 *
	 * @return string
	 */
	public function modify_page_title() {
		return __( 'Password Required', 'neve' );
	}

	/**
	 * Add inline styles for the password form.
	 *
	 * @return void
	 */
	public function style() {
		$style = '
		.nv-pswd-form button, ul { margin-top:12px; margin-bottom:24px; }
		.nv-pswd-form input {width:30%}
		.nv-pswd-form button {padding: 7px 25px}
		';
		wp_add_inline_style( 'neve-style', Dynamic_Css::minify_css( $style ) );
	}
}
