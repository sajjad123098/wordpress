<?php
/**
 * Main_Query
 *
 * @package  Neve_Pro\Modules\Access_Restriction\Content_Restriction\Authorization_Layer
 */
namespace Neve_Pro\Modules\Access_Restriction\Content_Restriction\Authorization_Layer;

use Neve_Pro\Modules\Access_Restriction\Content_Resource\Resource_Factory;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Settings\Resource_Settings;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Type\Term;
use Neve_Pro\Modules\Access_Restriction\General_Settings\Module_Settings;
use Neve_Pro\Modules\Access_Restriction\General_Settings\Storage_Adapter;
use Neve_Pro\Modules\Access_Restriction\Router\Restriction_Behavior\Basic as Basic_Restriction_Behavior;
use Neve_Pro\Modules\Access_Restriction\Router\Restriction_Behavior\Show_Password_Form;
use Neve_Pro\Modules\Access_Restriction\Router\Restriction_Behavior\Redirect_WP_Login;
use Neve_Pro\Modules\Access_Restriction\Router\Restrict_Current_Page;
use Neve_Pro\Modules\Access_Restriction\Router\Restriction_Behavior\Redirect_Custom_Login;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Authorization\Type\Password as Password_Authorization_Type;
use Neve_Pro\Modules\Access_Restriction\Content_Restriction\Authorization_Layer\Layer;
use Neve_Pro\Modules\Access_Restriction\Router\Restriction_Behavior\Restriction_Behavior;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Authorization\Checker\Checker_Composite;
use Neve_Pro\Modules\Access_Restriction\Utility\Context_Trait;

/**
 * Class Main_Query
 *
 * That class is responsible for the authorization layer on the main query.
 * Maybe redirect to the login page, or show the password form or show 404.
 */
class Main_Query implements Layer {
	use Context_Trait;
	/**
	 * Init
	 */
	public function init() {
		add_action( 'template_redirect', array( $this, 'route_middleware' ) );
		$this->add_title_filter_for_context();


		$categories            = get_categories();
		$restricted_categories = array();
		foreach ( $categories as $category ) {
			$taxonomy = $category->taxonomy;
			$resource = new Term();
			$resource->set_term_id( $category->term_id );
			$resource->set_taxonomy( $taxonomy );

			$resource_settings = new Resource_Settings( $resource );
			if ( ! empty( $resource_settings->get_activated_restrictions() ) ) {
				$restricted_categories[] = $category->term_id;
			}
		}
		if ( ! empty( $restricted_categories ) ) {
			add_filter(
				'get_previous_post_excluded_terms',
				function( $excluded_terms ) use ( $restricted_categories ) {
					return $restricted_categories;
				}
			);
			add_filter(
				'get_next_post_excluded_terms',
				function( $excluded_terms ) use ( $restricted_categories ) {
					return $restricted_categories;
				}
			);
		}

		add_filter( 'allowed_previous_post', array( $this, 'allowed_previous_post' ), 10, 1 );
		add_filter( 'previous_post_link', array( $this, 'get_filtered_adjacent_post_link' ), 10, 5 );
		add_filter( 'next_post_link', array( $this, 'get_filtered_adjacent_post_link' ), 10, 5 );
	}

	/**
	 * Check if post is authorized.
	 *
	 * @param \WP_Post|\WP_Error $post The post to check.
	 *
	 * @return bool
	 */
	private function is_post_authorized( $post ) {
		if ( $post instanceof \WP_Post === false ) {
			return true;
		}

		$resource = ( new Resource_Factory() )->get_resource( $post );
		if ( ! $resource ) {
			return true;
		}

		$authorization_checker = $resource->get_authorization_checker();
		return $authorization_checker->check();
	}

	/**
	 * Filters the next and previous post links to prevent unauthorized posts from being linked to.
	 *
	 * @param string          $output The html output.
	 * @param string          $format The format passed to the function.
	 * @param string          $link The link passed to the function.
	 * @param \WP_Post|string $filter_post The post used when generating the output.
	 * @param string          $adjacent The adjacent post direction. Can be either 'previous' or 'next'.
	 *
	 * @return string
	 */
	public function get_filtered_adjacent_post_link( $output, $format, $link, $filter_post, $adjacent ) {
		if ( $this->is_post_authorized( $filter_post ) ) {
			return $output;
		}

		$title = get_the_title( $filter_post );
		$link  = get_permalink( $filter_post );

		global $post;
		$original_post = $post;

		$adjacent_post = $this->recursive_adjacent_check( $filter_post, $adjacent === 'previous' );

		if ( empty( $adjacent_post ) || is_wp_error( $adjacent_post ) ) {
			$post = $original_post; //phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			return '';
		}

		if ( $adjacent_post instanceof \WP_Post === false ) {
			$post = $original_post; //phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			return '';
		}

		$post = $original_post; //phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

		$adjacent_post_title = get_the_title( $adjacent_post );
		$adjacent_post_link  = get_permalink( $adjacent_post );

		$output = str_replace( $title, $adjacent_post_title, $output );
		return str_replace( $link, $adjacent_post_link, $output );
	}

	/**
	 * Utility method to get the adjacent post.
	 *
	 * @param bool $is_previous Whether to get the previous or next post.
	 *
	 * @return \WP_Post|null
	 */
	private function get_adjacent_post( $is_previous = true ) {
		return function_exists( 'wpcom_vip_get_adjacent_post' ) ?
			wpcom_vip_get_adjacent_post( false, array(), $is_previous ) :
			get_adjacent_post( false, '', $is_previous ); //phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.get_adjacent_post_get_adjacent_post
	}

	/**
	 * Returns first previous post that it is authorized.
	 *
	 * @param \WP_Post $previous The previous post.
	 *
	 * @return \WP_Post|null|\WP_Error
	 */
	public function allowed_previous_post( $previous ) {
		global $post;
		$original_post = $post;

		$previous = $this->recursive_adjacent_check( $previous, true );

		$post = $original_post; //phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

		return $previous;
	}

	/**
	 * Recursively check if the adjacent post is authorized.
	 *
	 * @param \WP_Post|null|\WP_Error|string $adjacent_post The adjacent post.
	 * @param bool                           $is_previous Whether to get the previous or next post.
	 *
	 * @return \WP_Post|null|\WP_Error|string
	 */
	private function recursive_adjacent_check( $adjacent_post, $is_previous = true ) {
		if ( empty( $adjacent_post ) || is_wp_error( $adjacent_post ) ) {
			return $adjacent_post;
		}

		if ( $this->is_post_authorized( $adjacent_post ) ) {
			return $adjacent_post;
		}

		global $post;
		$post          = $adjacent_post; //phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$adjacent_post = $this->get_adjacent_post( $is_previous );
		return $this->recursive_adjacent_check( $adjacent_post, $is_previous );
	}

	/**
	 * Maybe restriction the current page.
	 *
	 * On the main query, check if the current page is restricted.
	 *
	 * If the current page should be restricted, apply restriction (redirect, show password form, 404 page, etc.)
	 *
	 * @return void
	 */
	public function route_middleware() {
		global $wp_query;

		$resource = ( new Resource_Factory() )->get_resource( $wp_query->get_queried_object() );

		if ( ! $resource ) {
			return;
		}

		$authorization_checker = $resource->get_authorization_checker();
		$is_authorized         = $authorization_checker->check();

		if ( $is_authorized ) {
			return;
		}

		$context = $this->get_current_context();
		( new Restrict_Current_Page( $this->get_restriction_behavior( $authorization_checker ), $context ) )->restrict();
	}

	/**
	 * Get restriction behavior
	 *
	 * Determine the restriction behavior for the current resource.
	 *
	 * @param  Checker_Composite $authorization_checker This class manages all authorization checks to determine if the current visitor/user is authorized to access the resource.
	 * @return Restriction_Behavior
	 */
	private function get_restriction_behavior( $authorization_checker ) {
		/**
		 * If the current resource contains a enabled password restriction, redirect to the password form.
		 */
		if ( $authorization_checker->has( Password_Authorization_Type::class ) ) {
			return new Show_Password_Form();
		}

		$module_settings = new Module_Settings();

		$restriction_behavior = $module_settings->get_restriction_behavior();

		switch ( $restriction_behavior ) {
			case Storage_Adapter::RESTRICT_BEHAVIOR_404_PAGE:
				return new Basic_Restriction_Behavior();

			case Storage_Adapter::RESTRICT_BEHAVIOR_DEFAULT_WP_LOGIN:
				return new Redirect_WP_Login( new Basic_Restriction_Behavior() );

			case Storage_Adapter::RESTRICT_BEHAVIOR_CUSTOM_LOGIN_PAGE:
				return new Redirect_Custom_Login( new Basic_Restriction_Behavior() );
		}

		return new Basic_Restriction_Behavior(); // fallback
	}
}
