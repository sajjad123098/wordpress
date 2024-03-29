<?php
/**
 * Director
 *
 * @package Neve_Pro\Modules\Access_Restriction\Content_Resource\Authorization\Checker\Builder
 */
namespace  Neve_Pro\Modules\Access_Restriction\Content_Resource\Authorization\Checker\Builder;

use Neve_Pro\Modules\Access_Restriction\Content_Resource\Authorization\Checker\Builder\Builder;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Authorization\Checker\Builder\Post_Builder;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Authorization\Checker\Builder\Term_Builder;

/**
 * Director
 */
class Director {
	/**
	 * Builder
	 *
	 * @var Post_Builder|Term_Builder Builder which will be used to build the Authorization_Checker.
	 */
	protected $builder;

	/**
	 * Constructor
	 *
	 * @param  Builder $builder Builder which will be used to build the Authorization_Checker.
	 * @throws \Exception If the builder is not supported.
	 * @return void
	 */
	public function __construct( $builder ) {
		if ( ! $builder instanceof Post_Builder && ! $builder instanceof Term_Builder ) {
			throw new \Exception( 'Invalid builder.' );
		}

		$this->builder = $builder;
	}

	/**
	 * Build Authorization_Checker for the resource.
	 *
	 * @return void
	 */
	public function build() {
		if ( $this->builder instanceof Post_Builder ) {
			$this->build_auth_checks_for_post();
		} elseif ( $this->builder instanceof Term_Builder ) {
			$this->build_auth_checks_for_term();
		}
	}

	/**
	 * Build Authorization_Checker for the post.
	 * Adds authorization checks for the main post and for the post categories (and post category ancestors as recursive).
	 * If the post has any restriction, then the post get inherit the restriction from the post categories.
	 *
	 * @return void
	 */
	protected function build_auth_checks_for_post() {
		$this->builder->build_post_checks();

		// If the post has not any restriction, authorizations checks are added for the post categories.
		if ( ! $this->builder->authorization_checker->is_empty() ) {
			return;
		}

		$this->builder->build_post_category_checks();
	}

	/**
	 * Build Authorization_Checker for the term.
	 * Adds authorization checks for the main terms and for the term ancestors.
	 * If the term has any restriction, then the term get inherit the restriction from the term ancestors.
	 *
	 * @return void
	 */
	protected function build_auth_checks_for_term() {
		$this->builder->build_term_checks();

		// If the term has not any restriction, term ancestors are checked.
		if ( ! $this->builder->authorization_checker->is_empty() ) {
			return;
		}

		$this->builder->build_term_ancestor_checks();
	}
}
