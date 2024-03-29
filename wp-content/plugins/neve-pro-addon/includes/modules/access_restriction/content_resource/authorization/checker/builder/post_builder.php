<?php
/**
 * Post_Builder
 *
 * @package Neve_Pro\Modules\Access_Restriction\Content_Resource\Authorization\Checker\Builder
 */
namespace  Neve_Pro\Modules\Access_Restriction\Content_Resource\Authorization\Checker\Builder;

use Neve_Pro\Modules\Access_Restriction\Content_Resource\Authorization\Checker\Builder\Builder;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Authorization\Checker\Builder\Utility;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Authorization\Checker\Checker_Composite;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Resource_Factory;
use Neve_Pro\Modules\Access_Restriction\General_Settings\Storage_Adapter;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Type\Post as Post_Resource;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Type\Term as Term_Resource;
use WP_Post;
use WP_Term;

/**
 * Post_Builder
 *
 * Adds authorization checks for the post and its categories when needed.
 */
class Post_Builder implements Builder {
	use Utility;

	/**
	 * Post
	 *
	 * @var WP_Post  WP POST object, which the authorization checker will be built for.
	 */
	protected $post;

	/**
	 * Authorization Checker
	 *
	 * @var Checker_Composite Authorization Checker.
	 */
	public $authorization_checker;

	/**
	 * Constructor
	 *
	 * @param  Post_Resource|Term_Resource $resource The resource which the Authorization_Checker will be created for.
	 * @return void
	 */
	public function __construct( $resource ) {
		$this->set_resource( $resource );
		$this->authorization_checker = new Checker_Composite();
	}

	/**
	 * Setter for the Post
	 *
	 * @param  WP_Post $wp_queried_object the post object which the authorization checker will be built for.
	 * @return void
	 */
	public function set_post( WP_Post $wp_queried_object ) {
		$this->post = $wp_queried_object;
	}

	/**
	 * Get the post categories
	 *
	 * @param WP_Post $post The post object.
	 * @return WP_Term[] The post categories which supports access restriction.
	 */
	public function get_post_categories( $post ) {
		$post_type = $post->post_type;

		$taxonomy = $this->get_taxonomy( $post_type );

		if ( false === $taxonomy ) {
			return [];
		}

		$terms = get_the_terms( $post, $taxonomy );

		if ( is_wp_error( $terms ) ) {
			return [];
		}

		if ( false === $terms ) {
			return [];
		}

		return array_filter(
			$terms,
			function( $term ) {
				return $term instanceof WP_Term;
			}
		);
	}

	/**
	 * This method retrieves the taxonomy that are supported by the Access Restriction module for a given post type.
	 *
	 * @param  string $post_type The WP post type, such as post, page, etc.
	 * @return string|false
	 */
	protected function get_taxonomy( $post_type ) {
		if ( ! array_key_exists( $post_type, Storage_Adapter::POST_TYPE_TAXONOMY_MAP ) ) {
			return false;
		}

		return Storage_Adapter::POST_TYPE_TAXONOMY_MAP[ $post_type ];
	}

	/**
	 * Add authorization checks for the post
	 *
	 * @return void
	 */
	public function build_post_checks() {
		$types = $this->get_authorization_types( $this->get_resource() );

		$this->add_authorization_types( $types );
	}

	/**
	 * Add authorization checks for the post categories
	 *
	 * @return void
	 */
	public function build_post_category_checks() {
		$categories = $this->get_post_categories( $this->post );

		foreach ( $categories as $category ) {
			$cat_resource        = ( new Resource_Factory() )->get_resource( $category );
			$category_auth_types = $cat_resource->get_authorization_checker()->get_authorization_types();

			$this->add_authorization_types( $category_auth_types );
		}
	}
}
