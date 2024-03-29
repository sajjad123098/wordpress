<?php
/**
 * Basic
 *
 * @package Neve_Pro\Modules\Access_Restriction\Router\Restriction_Behavior
 */
namespace  Neve_Pro\Modules\Access_Restriction\Router\Restriction_Behavior;

use Neve_Pro\Modules\Access_Restriction\Router\Restriction_Behavior\Restriction_Behavior;
use Neve_Pro\Modules\Access_Restriction\Utility\Context_Trait;

/**
 * Class Basic
 *
 * Restricts the current query. That's the first grade restriction behavior
 * and that basic behavior should be applied for all restriction behaviors as firstly.
 */
class Basic implements Restriction_Behavior {
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
	 * View (load template etc.)
	 *
	 * @return void
	 */
	public function view() {
		include get_query_template( '404' );
	}

	/**
	 * Redirection
	 *
	 * @return void
	 */
	public function redirect() {
		// no redirection for the basic behavior
		exit;
	}

	/**
	 * Register hooks
	 *
	 * @return void
	 */
	public function register_hooks() {
		// no hooks for the basic behavior
	}
}
