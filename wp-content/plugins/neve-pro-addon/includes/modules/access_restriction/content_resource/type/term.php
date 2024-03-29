<?php
/**
 * Post_Category
 *
 * @package Neve_Pro\Modules\Access_Restriction\Content_Resource\Type
 */
namespace Neve_Pro\Modules\Access_Restriction\Content_Resource\Type;

use Neve_Pro\Modules\Access_Restriction\Content_Resource\Type\Utility;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Type\Content_Resource;

/**
 * Term
 */
class Term implements Content_Resource {
	const GROUP = 'taxonomy';

	use Utility;

	/**
	 * Term ID of the resource.
	 *
	 * @var int
	 */
	protected $term_id;

	/**
	 * Taxonomy of the term.
	 *
	 * @var string  category, product_cat, etc.
	 */
	protected $taxonomy;

	/**
	 * Setter for term ID of the resource.
	 *
	 * @param  int $term_id Term id of the \WP_Term instance.
	 * @return void
	 */
	public function set_term_id( $term_id ) {
		$this->term_id = $term_id;
	}

	/**
	 * Get term ID of the resource.
	 *
	 * @return int
	 */
	public function get_term_id() {
		return $this->term_id;
	}

	/**
	 * Getter for taxonomy of the term.
	 *
	 * @return string
	 */
	public function get_taxonomy() {
		return $this->taxonomy;
	}

	/**
	 * Setter for taxonomy of the term.
	 *
	 * @param  string $taxonomy Taxonomy of the term.
	 * @return void
	 */
	public function set_taxonomy( $taxonomy ) {
		$this->taxonomy = $taxonomy;
	}
}
