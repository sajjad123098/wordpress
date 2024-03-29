<?php
/**
 * Restrict_Current_Page
 *
 * @package Neve_Pro\Modules\Access_Restriction\Router
 */
namespace  Neve_Pro\Modules\Access_Restriction\Router;

use Neve_Pro\Modules\Access_Restriction\Router\Restriction_Behavior\Restriction_Behavior;

/**
 * Class Restrict_Current_Page
 */
class Restrict_Current_Page {
	/**
	 * Restriction Behavior to restrict the current page.
	 *
	 * @var Restriction_Behavior
	 */
	protected $restriction_behavior;

	/**
	 * Constructor
	 *
	 * @param  Restriction_Behavior $restriction_behavior current page is restricted based on this behavior.
	 * @return void
	 */
	public function __construct( Restriction_Behavior $restriction_behavior, $context = 'single' ) {
		$this->restriction_behavior = $restriction_behavior;
		$this->restriction_behavior->set_context( $context );
	}

	/**
	 * Restrict the current page based on the restriction behavior.
	 *
	 * @return void
	 */
	public function restrict() {
		$this->restriction_behavior->register_hooks();
		$this->restriction_behavior->restrict_query();
		$this->restriction_behavior->view();
		$this->restriction_behavior->redirect();
	}
}
