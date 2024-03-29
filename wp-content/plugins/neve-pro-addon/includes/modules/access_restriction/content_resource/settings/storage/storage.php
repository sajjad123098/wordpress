<?php
/**
 * Storage
 *
 * @package Neve_Pro\Modules\Access_Restriction\Content_Resource\Settings\Storage
 */
namespace Neve_Pro\Modules\Access_Restriction\Content_Resource\Settings\Storage;

/**
 * Interface Storage
 *
 * Implements the storage methods for the resource settings.
 */
interface Storage {
	/**
	 * Constructor
	 *
	 * @param  int $resource_identifier Resource identifier, e.g. post_id or term_id for WP_Post and WP_Term resources.
	 * @return void
	 */
	public function __construct( $resource_identifier);

	/**
	 * Get meta value
	 *
	 * @param  string $meta_key Meta key.
	 * @return mixed
	 */
	public function get_meta_value( $meta_key);

	/**
	 * Update meta value
	 *
	 * @param  string $meta_key Meta key.
	 * @param  mixed  $meta_value Meta value.
	 * @return void
	 */
	public function update_meta_value( $meta_key, $meta_value);
}
