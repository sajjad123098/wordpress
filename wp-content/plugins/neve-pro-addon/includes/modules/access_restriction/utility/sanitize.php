<?php
/**
 * Access Restriction Module Main Class
 *
 * @package Neve_Pro\Modules\Access_Restriction\Utility
 */
namespace Neve_Pro\Modules\Access_Restriction\Utility;

use Neve_Pro\Modules\Access_Restriction\Content_Resource\Authorization\Type\Password;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Settings\Resource_Settings;

/**
 * Sanitize
 */
trait Sanitize {
	/**
	 * Sanitize given restriction types.
	 * If the given restriction type is not valid, it will be removed.
	 * If the status of the given restriction type is not valid, it will be set to the "yes" (fallback) value.
	 * If a restriction type is not given, it will be set to the "yes" (fallback) value.
	 *
	 * @param  array $restriction_types Restriction types of the resource.
	 * @return array
	 */
	public function sanitize_restriction_types( $restriction_types ) {
		$valid_values           = [ 'yes', 'no' ];
		$fallback_status_value  = 'yes';
		$valid_restriction_keys = array_keys( Resource_Settings::DEFAULT_RESTRICTION_TYPES );

		foreach ( $restriction_types as $type => $value ) {
			if ( ! in_array( $type, $valid_restriction_keys, true ) ) {
				unset( $restriction_types[ $type ] );
			}

			if ( ! in_array( $value, $valid_values, true ) ) {
				$restriction_types[ $type ] = $fallback_status_value;
			}
		}

		foreach ( $valid_restriction_keys as $type ) {
			if ( ! array_key_exists( $type, $restriction_types ) ) {
				$restriction_types[ $type ] = $fallback_status_value;
			}
		}

		return $restriction_types;
	}

	/**
	 * Sanitize given user IDs.
	 *
	 * Typecast to int and remove invalid values.
	 *
	 * @param  int[] $user_ids User IDs of the resource.
	 * @return int[]
	 */
	public function sanitize_user_ids( $user_ids ) {
		if ( ! is_array( $user_ids ) ) {
			return [];
		}

		$sanitized_vals = [];

		foreach ( $user_ids as $user_id ) {
			$sanitized_user_id = absint( $user_id );

			if ( 0 === $sanitized_user_id ) {
				continue;
			}

			if ( $sanitized_user_id !== (int) $user_id ) {
				continue;
			}

			$sanitized_vals[] = $sanitized_user_id;
		}

		return $sanitized_vals;
	}

	/**
	 * Get list of valid user roles.
	 *
	 * @return string[]
	 */
	protected function get_valid_roles() {
		$roles = wp_roles();

		if ( ! $roles instanceof \WP_Roles ) {
			return [];
		}

		return array_keys( $roles->roles );
	}

	/**
	 * Sanitize given user roles.
	 *
	 * If the provided role is invalid (i.e., the sanitized role does not match the provided role), it will be removed.
	 *
	 * @param  string[] $roles User roles of the resource.
	 * @return string[]
	 */
	public function sanitize_user_roles( $roles ) {
		if ( ! is_array( $roles ) ) {
			return [];
		}

		$sanitized_roles = [];

		$valid_roles = $this->get_valid_roles();

		foreach ( $roles as $role ) {
			if ( ! is_string( $role ) ) {
				continue;
			}

			$sanitized_role = sanitize_key( $role );

			if ( $sanitized_role !== $role || ! in_array( $sanitized_role, $valid_roles, true ) ) {
				continue;
			}

			$sanitized_roles[] = $sanitized_role;
		}

		return $sanitized_roles;
	}

	/**
	 * Is the given string contains only the allowed characters?
	 *
	 * @param  string   $val Sequence of characters.
	 * @param  string[] $allowed_chars list of the allowed characters.
	 * @return bool
	 */
	protected function is_the_string_contains_excepted_chars( $val, $allowed_chars ) {
		$chars = str_split( $val );

		foreach ( $chars as $char ) {
			if ( ! in_array( $char, $allowed_chars, true ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Sanitize given password.
	 *
	 * Note: use that method only for the admin side since that throws an exception if the given password is not valid.
	 *
	 * @param  string $val Given password.
	 * @throws \Exception If the given password is not valid.
	 * @return string
	 */
	public function sanitize_password_admin( $val ) {
		$trim_value = trim( $val );
		/**
		 * To don't cause saving issue on the block editor when password is blank.
		 * if it's blank WP will not save meta value.
		 */
		if ( '' === $trim_value ) {
			return $trim_value;
		}

		$sanitized_password = sanitize_text_field( $trim_value );

		$allowed_chars = str_split( Password::ALLOWED_CHARS );

		$valid = $this->is_the_string_contains_excepted_chars( $sanitized_password, $allowed_chars );

		if ( ( ! $valid ) || ( trim( $sanitized_password ) !== $trim_value ) ) {
			throw new \Exception( 'Invalid password' );
		}

		return $trim_value;
	}
}
