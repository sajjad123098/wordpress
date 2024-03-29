<?php
/**
 * Password
 *
 * @package Neve_Pro\Modules\Access_Restriction\Content_Resource\Authorization\Type
 */
namespace Neve_Pro\Modules\Access_Restriction\Content_Resource\Authorization\Type;

use Neve_Pro\Modules\Access_Restriction\Content_Resource\Authorization\Type\Authorization_Type;

/**
 * Class Password
 *
 * Authorization check based on the password
 */
class Password extends Authorization_Type {
	const ALLOWED_CHARS = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!()-.?[]_`~;:!@#$%^&*+= ';
	/**
	 * Expected hashed password prefix
	 * Which is used to check if the hashed value is a valid WordPress password hash
	 */
	const HASHED_PASSWORD_PREFIX = '$P$H';

	const COOKIE_KEY_PREFIX = 'nv-restrict_';

	/**
	 * Get type
	 *
	 * @return string
	 */
	public static function get_type(): string {
		return 'password';
	}

	/**
	 * Get hashed password from cookies
	 *
	 * @return string|false
	 */
	private function provided_hashed_password() {
		$cookie_key = self::COOKIE_KEY_PREFIX . COOKIEHASH;

		if ( ! array_key_exists( $cookie_key, $_COOKIE ) ) {
			return false;
		}

		return wp_unslash( $_COOKIE[ $cookie_key ] ); // phpcs:ignore WordPressVIPMinimum.Variables.RestrictedVariables.cache_constraints___COOKIE, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	}

	/**
	 * Get PasswordHash instance
	 * Create a new PasswordHash instance with using same arguments as WordPress Core
	 *
	 * @return \PasswordHash
	 */
	private function get_hasher() {
		require_once ABSPATH . WPINC . '/class-phpass.php';
		return new \PasswordHash( 8, true );
	}

	/**
	 * Is hashed password valid
	 *
	 * @param string|false $hashed_password Hashed password.
	 * @return bool
	 */
	private function is_hashed_password_valid( $hashed_password ): bool {
		if ( false === $hashed_password ) {
			return false;
		}

		return 0 !== strpos( $hashed_password, self::HASHED_PASSWORD_PREFIX );
	}

	/**
	 * Check if the current visitor is authorized to access the resource based on the password.
	 *
	 * @return bool
	 */
	public function is_authorized(): bool {
		$expected_password = $this->resource_settings->get_restriction_password();

		// if the password set as empty, do not allow access.
		if ( empty( $expected_password ) ) {
			return false;
		}

		$hashed_password = $this->provided_hashed_password();

		if ( ! $this->is_hashed_password_valid( $hashed_password ) ) {
			return false;
		}

		return $this->get_hasher()->CheckPassword( $expected_password, $hashed_password );
	}
}
