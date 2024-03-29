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
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Type\Post as Post_Resource;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Type\Term as Term_Resource;
use WP_Term;

/**
 * Term_Builder
 *
 * Adds authorization checks for the term and its ancestors when needed.
 */
class Term_Builder implements Builder {
	use Utility;
	/**
	 * Term
	 *
	 * @var \WP_Term WP Term object, which the authorization checker will be built for.
	 */
	protected $term;

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
	 * Setter for the Term
	 *
	 * @param  WP_Term $wp_queried_object the term object which the authorization checker will be built for.
	 * @return void
	 */
	public function set_term( WP_Term $wp_queried_object ) {
		$this->term = $wp_queried_object;
	}

	/**
	 * Add authorization checks for the term level.
	 *
	 * @return void
	 */
	public function build_term_checks() {
		$types = $this->get_authorization_types( $this->get_resource() );

		$this->add_authorization_types( $types );
	}

	/**
	 * Add authorization checks for the term ancestors.
	 *
	 * @return void
	 */
	public function build_term_ancestor_checks() {
		$ancestor_terms = get_ancestors( $this->term->term_id, $this->term->taxonomy );

		foreach ( $ancestor_terms as $term_id ) {
			$ancestor_term          = get_term( $term_id, $this->term->taxonomy );
			$ancestor_term_resource = ( new Resource_Factory( true ) )->get_resource( $ancestor_term );

			$ancestor_auth_types = $this->get_authorization_types( $ancestor_term_resource );
			$this->add_authorization_types( $ancestor_auth_types );
		}
	}
}
