<?php
/**
 * Builder
 *
 * @package Neve_Pro\Modules\Access_Restriction\Content_Resource\Authorization\Checker\Builder
 */
namespace  Neve_Pro\Modules\Access_Restriction\Content_Resource\Authorization\Checker\Builder;

use Neve_Pro\Modules\Access_Restriction\General_Settings\Module_Settings;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Authorization\Type\User_Role as User_Role_Authorization;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Authorization\Type\Password as     Password_Authorization;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Authorization\Type\User_ID as User_Id_Authorization;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Authorization\Type\Authorization;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Authorization\Checker\Checker_Composite;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Settings\Resource_Settings;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Type\Post as Post_Resource;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Type\Term as Term_Resource;

/**
 * Utility
 */
trait Utility {
	/**
	 * Content_Resource
	 *
	 * @var Post_Resource|Term_Resource Content_Resource which the Authorization_Checker will be created for.
	 */
	protected $resource;

	/**
	 * Setter for the resource
	 *
	 * @param  Post_Resource|Term_Resource $resource Set the resource which the Authorization_Checker will be created for.
	 * @return void
	 */
	public function set_resource( $resource ) {
		$this->resource = $resource;
	}

	/**
	 * Getter for the resource
	 *
	 * @return Post_Resource|Term_Resource
	 */
	public function get_resource() {
		return $this->resource;
	}

	/**
	 * Get authorization types for the given resource.
	 *
	 * @param  Post_Resource|Term_Resource $resource Resource which the Authorization_Checker is created for.
	 * @return Authorization[]
	 */
	private function get_authorization_types( $resource ) {
		/**
		 * If Access Restriction module is not enabled for the group of the resource
		 * return empty Authorization_Checker.
		 */
		if ( ! ( new Module_Settings() )->is_enabled_for_resource_type( $resource ) ) {
			return [];
		}

		$settings = new Resource_Settings( $resource );

		$types = [];

		if ( $settings->is_restriction_activated( User_Role_Authorization::get_type() ) ) {
			$types[] = ( new User_Role_Authorization( $settings ) );
		}

		if ( $settings->is_restriction_activated( User_Id_Authorization::get_type() ) ) {
			$types[] = new User_Id_Authorization( $settings );
		}

		if ( $settings->is_restriction_activated( Password_Authorization::get_type() ) ) {
			$types[] = new Password_Authorization( $settings );
		}

		return $types;
	}

	/**
	 * Get the authorization checker instance built for the given resource.
	 * That authorization checker has all the authorization types added to it.
	 *
	 * @return Checker_Composite
	 */
	public function get() {
		return $this->authorization_checker;
	}

	/**
	 * Add authorization types to the authorization checker.
	 *
	 * @param  Authorization[] $types Types to be added to the authorization checker.
	 * @return void
	 */
	protected function add_authorization_types( $types ) {
		foreach ( $types as $type ) {
			$this->authorization_checker->add_authorization_type( $type );
		}
	}
}
