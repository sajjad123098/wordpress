<?php
/**
 * Builder
 *
 * @package Neve_Pro\Modules\Access_Restriction\Content_Resource\Authorization\Checker\Builder
 */
namespace  Neve_Pro\Modules\Access_Restriction\Content_Resource\Authorization\Checker\Builder;

use Neve_Pro\Modules\Access_Restriction\Content_Resource\Type\Post as Post_Resource;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Type\Term as Term_Resource;

/**
 * Interface Builder
 */
interface Builder {
	/**
	 * Set resource which will be used to build the Authorization_Checker.
	 *
	 * @param  Post_Resource|Term_Resource $resource The resource which the Authorization_Checker will be created for.
	 * @return void
	 */
	public function set_resource( $resource );

	/**
	 * Getter for the resource
	 *
	 * @return Post_Resource|Term_Resource
	 */
	public function get_resource();
}
