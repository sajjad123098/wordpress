<?php
/**
 * Factory
 *
 * @package Neve_Pro\Modules\Access_Restriction\Content_Resource\Settings\Storage
 */
namespace Neve_Pro\Modules\Access_Restriction\Content_Resource\Settings\Storage;

use Neve_Pro\Modules\Access_Restriction\Content_Resource\Type\Post;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Type\Term;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Settings\Storage\WP_Post_Adapter;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Settings\Storage\WP_Term_Adapter;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Type\Content_Resource;

/**
 * Class Factory
 *
 * Creates Storage_Adapter instance for the resource.
 */
class Factory {
	/**
	 * Resource which will storage adapter be created for.
	 *
	 * @var Content_Resource
	 */
	protected $resource;

	/**
	 * Constructor
	 *
	 * @param Content_Resource $resource Resource which the Storage_Adapter will be created for.
	 * @return void
	 */
	public function __construct( Content_Resource $resource ) {
		$this->resource = $resource;
	}

	/**
	 * Get storage adapter for the resource.
	 *
	 * @return WP_Post_Adapter|WP_Term_Adapter|false
	 */
	public function get_storage() {
		if ( $this->resource instanceof Post ) {
			return new WP_Post_Adapter( $this->resource->get_post_id() );
		}

		if ( $this->resource instanceof Term ) {
			return new WP_Term_Adapter( $this->resource->get_term_id() );
		}

		return false;
	}
}
