<?php
/**
 * Access Restriction Context Trait
 *
 * Author:      Bogdan Preda <bogdan.preda@themeisle.com>
 * Created on:  18-05-{2023}
 *
 * @package neve
 */
namespace Neve_Pro\Modules\Access_Restriction\Utility;

trait Context_Trait {
	/**
	 * Context of the restriction.
	 *
	 * @var string
	 */
	protected $restrict_context;

	/**
	 * Set the context of the restriction.
	 *
	 * @param string $context Context of the restriction.
	 */
	public function set_context( $context ) {
		$this->restrict_context = $context;
	}

	/**
	 * Get the context of the restriction.
	 *
	 * @return string
	 */
	public function get_context() {
		return $this->restrict_context;
	}

	/**
	 * Get the current context.
	 *
	 * @return string
	 */
	protected function get_current_context() {
		$default = 'single';
		if ( function_exists( 'is_product_category' ) && is_product_category() ) {
			return 'product_category';
		}

		if ( function_exists( 'is_product' ) && is_product() ) {
			return 'product';
		}

		if ( function_exists( 'is_shop' ) && is_shop() ) {
			return 'shop';
		}

		if ( is_archive() ) {
			return 'archive';
		}

		return $default;
	}

	/**
	 * Add title filter for the passed context.
	 */
	protected function add_title_filter_for_context() {
		add_filter(
			'neve_restricted_title',
			function( $title ) {
				$restricted_suffix = __( 'Restricted', 'neve' ) . ': ';
				if ( strpos( $title, $restricted_suffix ) !== false ) {
					return $title;
				}

				return $restricted_suffix . $title;

			},
			10,
			1
		);
	}
}
