<?php
/**
 * Resource_Factory
 *
 * @package Neve_Pro\Modules\Access_Restriction\Content_Resource
 */
namespace Neve_Pro\Modules\Access_Restriction\Content_Resource;

use Neve_Pro\Modules\Access_Restriction\Content_Resource\Type\Post;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Type\Term;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Type\Content_Resource;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Authorization\Checker\Builder\Post_Builder as Post_Auth_Checker_Builder;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Authorization\Checker\Builder\Term_Builder as Term_Auth_Checker_Builder;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Authorization\Checker\Builder\Director as Auth_Checker_Director;

/**
 * Class Resource_Factory
 */
class Resource_Factory {
	/**
	 * Supress authorization checker creation.
	 * That's used prevents recursion when building authorization checker.
	 * E.g: when getting category authorization types (with category ancestors) for a post.
	 *
	 * @var bool
	 */
	protected $supress_auth_checker = false;

	/**
	 * Constructor
	 *
	 * @param  bool $supress_auth_checker Do not build authorization checker.
	 * @return void
	 */
	public function __construct( $supress_auth_checker = false ) {
		$this->supress_auth_checker = $supress_auth_checker;
	}

	/**
	 * Get resource from WP_Query object.
	 *
	 * @param  \WP_Term|\WP_Post_Type|\WP_Post|\WP_User|null $wp_queried_object Queried WP object.
	 * @return Post|Term|false
	 */
	public function get_resource( $wp_queried_object ) {
		if ( ! $wp_queried_object ) {
			return false;
		}

		switch ( get_class( $wp_queried_object ) ) {
			case 'WP_Post':
				$post_type = $wp_queried_object->post_type;

				$resource = new Post();

				$resource->set_post_type( $post_type );
				$resource->set_post_id( $wp_queried_object->ID );
				break;
			case 'WP_Term':
				$taxonomy = $wp_queried_object->taxonomy;
				$resource = new Term();
				$resource->set_term_id( $wp_queried_object->term_id );
				$resource->set_taxonomy( $taxonomy );
				break;

			default:
				return false;
		}

		if ( ! $this->supress_auth_checker ) {
			$this->set_authorization_checker( $resource, $wp_queried_object );
		}

		return $resource;
	}

	/**
	 * Set authorization checker for the resource.
	 *
	 * For the posts, add checkers for the post and its category & category ancestors if neded.
	 * For the terms, add checkers for the term and its ancestors if neded.
	 *
	 * @param  Content_Resource                              $resource Resource to set authorization checker for.
	 * @param   \WP_Term|\WP_Post_Type|\WP_Post|\WP_User|null $wp_queried_object Queried WP object.
	 * @throws \Exception When unknown resource type is passed.
	 * @return void
	 */
	protected function set_authorization_checker( $resource, $wp_queried_object ) {
		if ( $resource instanceof Post ) {
			$builder = new Post_Auth_Checker_Builder( $resource );
			$builder->set_post( $wp_queried_object );
		} elseif ( $resource instanceof Term ) {
			$builder = new Term_Auth_Checker_Builder( $resource );
			$builder->set_term( $wp_queried_object );
		} else {
			throw new \Exception( __( 'Unknown resource type.', 'neve' ) );
		}

		// Build authorization checker.
		$director = new Auth_Checker_Director( $builder );
		$director->build();

		$authorization_checker = $builder->get();

		$resource->set_authorization_checker( $authorization_checker );
	}
}
