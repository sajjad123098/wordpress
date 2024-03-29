<?php
/**
 * Post
 *
 * @package Neve_Pro\Modules\Access_Restriction\Content_Resource\Type
 */
namespace Neve_Pro\Modules\Access_Restriction\Content_Resource\Type;

use Neve_Pro\Modules\Access_Restriction\Content_Resource\Type\Utility;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Type\Content_Resource;
/**
 * Post
 */
class Post implements Content_Resource {
	const GROUP = 'post_type';

	use Utility;

	/**
	 * Post ID of the resource.
	 *
	 * @var int
	 */
	protected $post_id;

	/**
	 * Post type of the post.
	 *
	 * @var string Such as post, page, product, etc.
	 */
	protected $post_type;

	/**
	 * Setter for post ID of the resource.
	 *
	 * @param  int $post_id Post id of the \WP_Post instance.
	 * @return void
	 */
	public function set_post_id( $post_id ) {
		$this->post_id = $post_id;
	}

	/**
	 * Get post ID of the resource.
	 *
	 * @return int
	 */
	public function get_post_id() {
		return $this->post_id;
	}

	/**
	 * Getter for post type of the post.
	 *
	 * @return string
	 */
	public function get_post_type() {
		return $this->post_type;
	}

	/**
	 * Setter for post type of the post.
	 *
	 * @param  string $post_type Post type of the post.
	 * @return void
	 */
	public function set_post_type( $post_type ) {
		$this->post_type = $post_type;
	}
}
