<?php
/**
 * Authorization_Composite
 *
 * @package Neve_Pro\Modules\Access_Restriction\Content_Resource\Authorization\Checker
 */
namespace  Neve_Pro\Modules\Access_Restriction\Content_Resource\Authorization\Checker;

use Neve_Pro\Modules\Access_Restriction\Content_Resource\Authorization\Type\Authorization;

/**
 * Class Checker_Composite
 *
 * The composite class manages multiple authorization check classes to determine
 * if the current user/visitor is authorized to access a resource by aggregating their results.
 */
class Checker_Composite {
	/**
	 * Specifies the types of authorization checks to be performed by class.
	 *
	 * @var array
	 */
	private $authorization_types = array();

	/**
	 * Check all registered authorization types.
	 * One of the authorization methods must return true to allow access.
	 *
	 * @return bool
	 */
	public function check(): bool {
		if ( count( $this->authorization_types ) === 0 ) {
			return true;
		}

		foreach ( $this->authorization_types as $authorization_type ) {
			if ( $authorization_type->is_authorized() ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Adds a new authorization type to the list of authorization types to be checked.
	 *
	 * @param  Authorization $authorization_type instance which checks resource authorization.
	 * @return void
	 */
	public function add_authorization_type( Authorization $authorization_type ) {
		$this->authorization_types[] = $authorization_type;
	}

	/**
	 * Check if the authorization checker has a specific authorization type.
	 *
	 * @param  string $authorization_type Authorization type class name to check.
	 * @return bool
	 */
	public function has( $authorization_type ) {
		foreach ( $this->authorization_types as $registered_authorization_type ) {
			if ( $registered_authorization_type instanceof $authorization_type ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * There are not any authorization types to check?
	 *
	 * @return bool
	 */
	public function is_empty() {
		return count( $this->authorization_types ) === 0;
	}

	/**
	 * Get the list of the added authorization types.
	 *
	 * @return Authorization[]
	 */
	public function get_authorization_types() {
		return $this->authorization_types;
	}
}
