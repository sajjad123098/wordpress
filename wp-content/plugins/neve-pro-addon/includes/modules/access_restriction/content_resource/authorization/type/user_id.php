<?php
/**
 * User_ID
 *
 * @package Neve_Pro\Modules\Access_Restriction\Content_Resource\Authorization\Type
 */
namespace Neve_Pro\Modules\Access_Restriction\Content_Resource\Authorization\Type;

use Neve_Pro\Modules\Access_Restriction\Content_Resource\Authorization\Type\Authorization_Type;

/**
 * Class User_ID
 *
 * Authorization check based on the user ID.
 *
 * The class provides functionality to check if the current logged in user has one of the allowed user IDs for a specific resource, based on the resource settings.
 */
class User_ID extends Authorization_Type {
	/**
	 * Get type
	 *
	 * @return string
	 */
	public static function get_type(): string {
		return 'user_id';
	}

	/**
	 * Check if the current user is authorized to access the resource based on the user ID.
	 *
	 * @return bool
	 */
	public function is_authorized(): bool {
		$allowed_user_ids = $this->resource_settings->get_allowed_user_ids();

		if ( 0 === count( $allowed_user_ids ) ) {
			return false;
		}

		$current_user = wp_get_current_user();

		$user_id = $current_user->ID;

		if ( ! ( $user_id > 0 ) ) {
			return false;
		}

		return in_array( $current_user->ID, $allowed_user_ids );
	}
}
