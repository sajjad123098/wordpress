<?php
/**
 * Redirection_Decorator
 *
 * @package Neve_Pro\Modules\Access_Restriction\Router\Restriction_Behavior
 */
namespace  Neve_Pro\Modules\Access_Restriction\Router\Restriction_Behavior;

use Neve_Pro\Modules\Access_Restriction\Router\Restriction_Behavior\Restriction_Behavior;
use Neve_Pro\Modules\Access_Restriction\Utility\Context_Trait;

/**
 * Class Redirection_Decorator
 *
 * That's a decorator class for redirection behaviors.
 */
abstract class Redirection_Decorator implements Restriction_Behavior {
	use Context_Trait;
	const WP_DEFAULT_REDIRECT_QUERY_PARAM = 'redirect_to';

	/**
	 * Restriction Behavior
	 *
	 * @var Restriction_Behavior
	 */
	protected $restriction_behavior;

	/**
	 * Constructor
	 *
	 * @param  Restriction_Behavior $behavior Restriction_Behavior instance.
	 * @return void
	 */
	public function __construct( Restriction_Behavior $behavior ) {
		$this->restriction_behavior = $behavior;
	}

	/**
	 * Restrict on the WP_Query instance.
	 *
	 * @return void
	 */
	public function restrict_query() {
		$this->restriction_behavior->restrict_query();
	}

	/**
	 * View (load template etc.)
	 *
	 * @return void
	 */
	public function view() {
		// no view for the redirection decorator
	}

	/**
	 * Redirection
	 *
	 * @return void
	 */
	abstract function redirect();

	/**
	 * Register hooks
	 *
	 * @return void
	 */
	public function register_hooks() {
		$this->restriction_behavior->register_hooks();
	}

	/**
	 * Get the current URL
	 *
	 * Note: That method can be improved in the future releases.
	 *
	 * @return string
	 */
	protected function get_current_url() {
		global $wp;
		return home_url( $wp->request );
	}

	/**
	 * Extracts the WordPress core login URL from query arguments.
	 *
	 * @return string
	 */
	protected function get_wp_core_login_url() {
		return add_query_arg( self::WP_DEFAULT_REDIRECT_QUERY_PARAM, urlencode( $this->get_current_url() ), wp_login_url() );
	}
}
