<?php
/**
 * Post
 *
 * @package Neve_Pro\Modules\Access_Restriction\Content_Resource\Settings
 */
namespace Neve_Pro\Modules\Access_Restriction\Content_Resource\Settings;

use Neve_Pro\Modules\Access_Restriction\Content_Resource\Authorization\Type\Password as Authorization_Password;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Authorization\Type\User_ID as Authorization_User_ID;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Authorization\Type\User_Role as Authorization_User_Role;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Settings\Storage\Factory as Storage_Factory;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Type\Content_Resource;
use Neve_Pro\Modules\Access_Restriction\Utility\Sanitize;

/**
 * Resource_Settings
 */
class Resource_Settings {
	use Sanitize;

	/**
	 * Storage adapter.
	 *
	 * @var \Neve_Pro\Modules\Access_Restriction\Content_Resource\Settings\Storage\Storage
	 */
	protected $storage;

	/**
	 * Activated restrictions for the resource
	 *
	 * @var array|false
	 */
	protected $cache_activated_restrictions = false;

	const META_KEY_ALLOWED_USER_IDS     = 'nv_restrict_user_ids';
	const META_KEY_ALLOWED_USER_ROLES   = 'nv_restrict_user_roles';
	const META_KEY_RESTRICTION_PASSWORD = 'nv_restrict_password';

	const SCHEMA_RESTRICTION_TYPES = [
		'type'       => 'object',
		'properties' => [
			'user_id'   => [
				'type' => 'string',
			],
			'user_role' => [
				'type' => 'string',
			],
			'password'  => [
				'type' => 'string',
			],
		],
		'required'   => [
			'user_id',
			'user_role',
			'password',
		],
	];

	const SCHEMA_ALLOWED_USER_IDS = [
		'type'  => 'array',
		'items' => [
			'type' => 'integer',
		],
	];

	const SCHEMA_ALLOWED_USER_ROLES = [
		'type'  => 'array',
		'items' => [
			'type' => 'string',
		],
	];

	const SCHEMA_RESTRICTION_PASSWORD = [
		'type' => 'string',
	];

	const DEFAULT_RESTRICTION_TYPES = [
		'user_id'   => 'no',
		'user_role' => 'no',
		'password'  => 'no',
	];

	const DEFAULT_ALLOWED_USER_IDS = [];

	const DEFAULT_ALLOWED_USER_ROLES = [];

	const DEFAULT_RESTRICTION_PASSWORD = '';

	/**
	 * Meta stores the restriction types on the resource content (term/post) level.
	 */
	const META_KEY_RESTRICTION_TYPES = 'nv_restrict_types';

	/**
	 * Constructor
	 *
	 * @param  Content_Resource $resource The resource whose settings will be managed.
	 * @return void
	 */
	public function __construct( Content_Resource $resource ) {
		$this->storage = ( new Storage_Factory( $resource ) )->get_storage();
	}

	/**
	 * Get the list of all authorization types in the system.
	 *
	 * @return string[]
	 */
	protected function get_all_authorization_types() {
		return [
			Authorization_User_ID::get_type(),
			Authorization_User_Role::get_type(),
			Authorization_Password::get_type(),
		];
	}

	/**
	 * Get restriction types
	 *
	 * @return array|false
	 */
	public function get_restriction_types() {
		$value = $this->storage->get_meta_value( self::META_KEY_RESTRICTION_TYPES );

		if ( ! is_array( $value ) ) {
			return self::DEFAULT_RESTRICTION_TYPES;
		}

		return $value;
	}

	/**
	 * Get allowed user IDs.
	 *
	 * @return array|false
	 */
	public function get_allowed_user_ids() {
		$value = $this->storage->get_meta_value( self::META_KEY_ALLOWED_USER_IDS );

		if ( ! is_array( $value ) ) {
			return self::DEFAULT_ALLOWED_USER_IDS;
		}

		return $value;
	}

	/**
	 * Get allowed user roles
	 *
	 * @return array|false
	 */
	public function get_allowed_user_roles() {
		$value = $this->storage->get_meta_value( self::META_KEY_ALLOWED_USER_ROLES );

		if ( ! is_array( $value ) ) {
			return self::DEFAULT_ALLOWED_USER_ROLES;
		}

		return $value;
	}

	/**
	 * Get restriction password
	 *
	 * @return string|false
	 */
	public function get_restriction_password() {
		$value = $this->storage->get_meta_value( self::META_KEY_RESTRICTION_PASSWORD );

		if ( ! is_string( $value ) ) {
			return self::DEFAULT_RESTRICTION_PASSWORD;
		}

		return $value;
	}

	/**
	 * Get activated restriction types for the resource.
	 *
	 * @return string[] Array of restriction types.
	 */
	public function get_activated_restrictions() {
		if ( $this->cache_activated_restrictions !== false ) {
			return $this->cache_activated_restrictions;
		}

		$resource_restrictions = $this->storage->get_meta_value( self::META_KEY_RESTRICTION_TYPES );

		// that shows that the restriction is not activated for this resource.
		if ( $resource_restrictions === '' ) {
			return [];
		}

		// if data is malformed, enable all restriction types as safeguard.
		if ( ! is_array( $resource_restrictions ) || count( array_diff( $this->get_all_authorization_types(), array_keys( $resource_restrictions ) ) ) !== 0 ) {
			return $this->get_all_authorization_types();
		}

		$activated_types = array_filter(
			$resource_restrictions,
			function( $status ) {
				return $status === 'yes';
			}
		);

		return array_keys( $activated_types );
	}

	/**
	 * Is specific restriction activated for the resource.
	 * E.g: check if user ID restriction is activated for the resource.
	 *
	 * @param string $authorization_type Authorization type to check.
	 * @throws \Exception When Authorization type is invalid.
	 * @return bool
	 */
	public function is_restriction_activated( $authorization_type ) {
		if ( ! in_array( $authorization_type, $this->get_all_authorization_types(), true ) ) {
			throw new \Exception( __( 'Invalid authorization type', 'neve' ) );
		}

		if ( $this->cache_activated_restrictions === false ) {
			$this->cache_activated_restrictions = $this->get_activated_restrictions();
		}

		return in_array( $authorization_type, $this->cache_activated_restrictions, true );
	}

	/**
	 * Update restriction types
	 *
	 * @param  array $restriction_types Array of restriction types. Sample schema: [ 'user_id' => 'yes', 'user_role' => 'no', 'password' => 'no' ] .
	 * @return void
	 */
	public function update_restriction_types( $restriction_types ) {
		$this->storage->update_meta_value( self::META_KEY_RESTRICTION_TYPES, $this->sanitize_restriction_types( $restriction_types ) );
	}

	/**
	 * Update allowed user IDs
	 *
	 * @param  array $user_ids Array of allowed user IDs. Sample schema: [ 1, 2, 3 ] .
	 * @return void
	 */
	public function update_allowed_user_ids( $user_ids ) {
		$this->storage->update_meta_value( self::META_KEY_ALLOWED_USER_IDS, $this->sanitize_user_ids( $user_ids ) );
	}

	/**
	 * Update allowed user roles
	 *
	 * @param  array $user_roles Array of allowed user roles. Sample schema: [ 'administrator', 'editor' ] .
	 * @return void
	 */
	public function update_allowed_user_roles( $user_roles ) {
		$this->storage->update_meta_value( self::META_KEY_ALLOWED_USER_ROLES, $this->sanitize_user_roles( $user_roles ) );
	}

	/**
	 * Update restriction password
	 *
	 * @param  string $password Password to be used for password restriction.
	 * @return void
	 */
	public function update_restriction_password( $password ) {
		$this->storage->update_meta_value( self::META_KEY_RESTRICTION_PASSWORD, $this->sanitize_password_admin( $password ) );
	}
}
