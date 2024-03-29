<?php
/**
 * Restriction_Behavior
 *
 * @package Neve_Pro\Modules\Access_Restriction\Router\Restriction_Behavior
 */
namespace  Neve_Pro\Modules\Access_Restriction\Router\Restriction_Behavior;

/**
 * Interface Restriction_Behavior
 */
interface Restriction_Behavior {
	/**
	 * Redirection
	 *
	 * @return void
	 */
	public function redirect();

	/**
	 * Some restrictions on the WP_Query instance.
	 *
	 * @return void
	 */
	public function restrict_query();

	/**
	 * View (load template etc.)
	 *
	 * @return void
	 */
	public function view();

	/**
	 * Register hooks
	 */
	public function register_hooks();

	/**
	 * Set the context
	 *
	 * @param string $context The context.
	 */
	public function set_context( $context );
}
