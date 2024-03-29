<?php
/**
 * Authorization
 *
 * @package Neve_Pro\Modules\Access_Restriction\Content_Resource\Authorization\Type
 */
namespace Neve_Pro\Modules\Access_Restriction\Content_Resource\Authorization\Type;

/**
 * Interface Authorization
 *
 * Provides a base interface for all authorization types.
 */
interface Authorization {
	/**
	 * Get type
	 *
	 * @return string
	 */
	public static function get_type(): string;

	/**
	 * __construct
	 *
	 * @param  mixed $resource_settings Resource settings.
	 * @return void
	 */
	public function __construct( $resource_settings );

	/**
	 * Check if the current visitor/user is authorized to access the resource.
	 *
	 * @return bool
	 */
	public function is_authorized(): bool;
}
