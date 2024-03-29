<?php
/**
 * Utility
 *
 * @package Neve_Pro\Modules\Access_Restriction\Content_Resource\Type
 */
namespace Neve_Pro\Modules\Access_Restriction\Content_Resource\Type;

use Neve_Pro\Modules\Access_Restriction\Content_Resource\Authorization\Checker\Checker_Composite;

/**
 * Utility
 */
trait Utility {
	/**
	 * Authorization checker.
	 *
	 * @var Checker_Composite
	 */
	protected $authorization_checker;

	/**
	 * Set authorization checker.
	 *
	 * @param Checker_Composite $authorization_checker This class manages all authorization checks to determine if the current visitor/user is authorized to access the resource.
	 * @return void
	 */
	public function set_authorization_checker( Checker_Composite $authorization_checker ) {
		$this->authorization_checker = $authorization_checker;
	}

	/**
	 * Get authorization checker.
	 *
	 * @return Checker_Composite
	 */
	public function get_authorization_checker() {
		return $this->authorization_checker;
	}
}
