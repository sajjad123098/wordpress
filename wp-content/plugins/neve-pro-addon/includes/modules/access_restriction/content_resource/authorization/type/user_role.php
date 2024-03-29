<?php
/**
 * User_Role
 *
 * @package Neve_Pro\Modules\Access_Restriction\Content_Resource\Authorization\Type
 */
namespace Neve_Pro\Modules\Access_Restriction\Content_Resource\Authorization\Type;

use Neve_Pro\Modules\Access_Restriction\Content_Resource\Authorization\Type\Authorization_Type;

/**
 * Class User_Role
 *
 * Authorization check based on the user role.
 *
 * The class provides functionality to check if the current logged in user has one of the allowed roles for a specific resource, based on the resource settings.
 */
class User_Role extends Authorization_Type {
	/**
	 * Get type
	 *
	 * @return string
	 */
	public static function get_type(): string {
		return 'user_role';
	}

	/**
	 * Check if the current user is authorized to access the resource based on the user role.
	 *
	 * @return bool
	 */
	public function is_authorized(): bool {
		$allowed_roles = $this->resource_settings->get_allowed_user_roles();

		if ( 0 === count( $allowed_roles ) ) {
			return false;
		}

		$current_user = wp_get_current_user();

		$user_id = $current_user->ID;

		if ( ! ( $user_id > 0 ) ) {
			return false;
		}

		$intersect = array_intersect( $allowed_roles, $current_user->roles );

		return count( $intersect ) > 0;
	}
}
