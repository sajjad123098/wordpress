<?php
/**
 * WP_Term_Adapter
 *
 * @package Neve_Pro\Modules\Access_Restriction\Content_Resource\Settings\Storage
 */
namespace Neve_Pro\Modules\Access_Restriction\Content_Resource\Settings\Storage;

/**
 * Class WP_Term_Adapter
 */
class WP_Term_Adapter implements Storage {
	/**
	 * Term ID
	 *
	 * @var int
	 */
	protected $term_id;

	/**
	 * Constructor
	 *
	 * @param  int $term_id Term ID.
	 * @return void
	 */
	public function __construct( $term_id ) {
		$this->term_id = $term_id;
	}

	/**
	 * Get meta value
	 *
	 * @param  string $meta_key Meta key.
	 * @return mixed
	 */
	public function get_meta_value( $meta_key ) {
		return get_term_meta( $this->term_id, $meta_key, true );
	}

	/**
	 * Update meta value
	 *
	 * @param  string $meta_key Meta key.
	 * @param  mixed  $meta_value Meta value.
	 * @return void
	 */
	public function update_meta_value( $meta_key, $meta_value ) {
		update_term_meta( $this->term_id, $meta_key, $meta_value );
	}
}
