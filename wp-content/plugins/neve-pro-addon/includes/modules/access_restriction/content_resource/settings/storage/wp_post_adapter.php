<?php
/**
 * WP_Post_Adapter
 *
 * @package Neve_Pro\Modules\Access_Restriction\Content_Resource\Settings\Storage
 */
namespace Neve_Pro\Modules\Access_Restriction\Content_Resource\Settings\Storage;

/**
 * Class WP_Post_Adapter
 *
 * Adapter class transforms meta processes of WP_Post into a Storage interface.
 */
class WP_Post_Adapter implements Storage {
	/**
	 * Post ID
	 *
	 * @var int
	 */
	protected $post_id;

	/**
	 * Constructor
	 *
	 * @param  int $post_id Post ID.
	 * @return void
	 */
	public function __construct( $post_id ) {
		$this->post_id = $post_id;
	}

	/**
	 * Get meta value
	 *
	 * @param  string $meta_key Meta key.
	 * @return mixed
	 */
	public function get_meta_value( $meta_key ) {
		return get_post_meta( $this->post_id, $meta_key, true );
	}

	/**
	 * Update meta value
	 *
	 * @param  string $meta_key Meta key.
	 * @param  mixed  $meta_value Meta value.
	 * @return void
	 */
	public function update_meta_value( $meta_key, $meta_value ) {
		update_post_meta( $this->post_id, $meta_key, $meta_value );
	}
}
