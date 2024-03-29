<?php
/**
 * Conditional display trait
 *
 * Name:    Neve Pro Addon
 *
 * @package Neve_Pro
 */

namespace Neve_Pro\Traits;

use Neve_Pro\Modules\Custom_Layouts\Admin\Layouts_Metabox;

/**
 * Trait Conditional_Display
 *
 * @package Neve_Pro\Traits
 */
trait Conditional_Display {

	/**
	 * The static rules array.
	 *
	 * @var array
	 */
	private $static_rules = [];

	/**
	 * Priority Map for root ruleset.
	 *
	 * @var array
	 */
	private $priority_map = [
		'post_type'                       => 30,
		'post_taxonomy'                   => 20,
		'post_author'                     => 10,
		'post'                            => 0,
		'page_type'                       => 20,
		'page_parent'                     => 0,
		'page_ancestor'                   => 0,
		'page_template'                   => 10,
		'page'                            => 0,
		'archive_type'                    => 30,
		'archive_taxonomy'                => 20,
		'archive_term'                    => 10,
		'archive_author'                  => 0,
		'user_status'                     => 20,
		'user_role'                       => 10,
		'user'                            => 0,
		'product_category_purchase'       => 10,
		'product_purchase'                => 0,
		'product_category_added_to_cart'  => 10,
		'product_added_to_cart'           => 0,
		'lifter_student_quiz_status'      => 20,
		'lifter_membership'               => 10,
		'lifter_student_course_status'    => 0,
		'learndash_student_quiz_status'   => 20,
		'learndash_group'                 => 10,
		'learndash_student_course_status' => 0,
		'wpml_language'                   => 0,
		'pll_language'                    => 0,
	];

	/**
	 * Check which rule has the highest priority.
	 *
	 * @param array $available_layouts available layouts array [ index => post_id ].
	 * @param bool  $is_header_layout  is this a header layout we are testing.
	 *
	 * @return int|false
	 */
	public function get_greatest_priority_rule( $available_layouts, $is_header_layout = false ) {
		if ( count( $available_layouts ) === 1 ) {
			return $available_layouts[0];
		}

		$valid_layouts = [];

		foreach ( $available_layouts as $layout_index => $layout_id ) {
			$rules = json_decode( get_post_meta( $layout_id, Layouts_Metabox::META_CONDITIONAL, true ), true );
			if ( empty( $rules ) && ! $is_header_layout ) {
				return $layout_id;
			}
			// Added this check as it was failing for null.
			if ( empty( $rules ) ) {
				$rules = [];
			}

			foreach ( $rules as $index => $group ) {
				$group_state = true;
				$min_group   = 100;
				foreach ( $group as $individual_rule ) {
					if ( ! $this->evaluate_condition( $individual_rule ) ) {
						$group_state = false;
						break;
					}
					if ( $this->priority_map[ $individual_rule['root'] ] < $min_group ) {
						$min_group = $this->priority_map[ $individual_rule['root'] ];
					}
				}
				if ( $group_state === true ) {
					$valid_layouts[ $layout_id ] = isset( $valid_layouts[ $layout_id ] ) ? ( $min_group < $valid_layouts[ $layout_id ] ? $min_group : $valid_layouts[ $layout_id ] ) : $min_group;
				}
			}
		}

		if ( empty( $valid_layouts ) ) {
			return false;
		}

		return array_search( min( $valid_layouts ), $valid_layouts, true );
	}

	/**
	 * Check the display conditions.
	 *
	 * @param int $custom_layout_id the custom layout post ID.
	 *
	 * @return bool
	 */
	public function check_conditions( $custom_layout_id ) {
		$condition_groups = json_decode( get_post_meta( $custom_layout_id, Layouts_Metabox::META_CONDITIONAL, true ), true );
		return $this->check_conditions_groups( $condition_groups );
	}

	/**
	 * Check conditions groups array.
	 *
	 * @param array $condition_groups the condition groups to check.
	 *
	 * @return bool
	 */
	public function check_conditions_groups( $condition_groups ) {
		if ( ! is_array( $condition_groups ) || empty( $condition_groups ) ) {
			return true;
		}
		$evaluated_groups = array();
		foreach ( $condition_groups as $index => $conditions ) {
			$individual_rules = array();

			if ( empty( $conditions ) ) {
				continue;
			}

			foreach ( $conditions as $condition ) {
				$individual_rules[ $index ][] = $this->evaluate_condition( $condition );
			}
			$evaluated_groups[ $index ] = ! in_array( false, $individual_rules[ $index ], true );
		}

		return in_array( true, $evaluated_groups, true );
	}

	/**
	 * Setup static rules.
	 */
	private function setup_static_rules() {
		$this->static_rules = array(
			'page_type'    => array(
				'front_page' => get_option( 'show_on_front' ) === 'page' && is_front_page(),
				'not_found'  => is_404(),
				'posts_page' => is_home(),
			),
			'user_status'  => array(
				'logged_in'  => is_user_logged_in(),
				'logged_out' => ! is_user_logged_in(),
			),
			'archive_type' => array(
				'date'   => is_date(),
				'author' => is_author(),
				'search' => is_search(),
			),
		);

		$post_types = get_post_types( array( 'public' => true ) );

		foreach ( $post_types as $post_type ) {
			if ( $post_type === 'post' ) {
				$this->static_rules['archive_type'][ $post_type ] = is_home();
				continue;
			}
			$this->static_rules['archive_type'][ $post_type ] = is_post_type_archive( $post_type );
		}
	}

	/**
	 * Evaluate single condition
	 *
	 * @param array $condition condition.
	 *
	 * @return bool
	 */
	private function evaluate_condition( $condition ) {
		$this->setup_static_rules();

		$post_id = null;
		global $post;
		if ( isset( $post->ID ) && ! is_search() ) {
			$post_id = (string) $post->ID;
		}

		if ( ! is_array( $condition ) || empty( $condition ) ) {
			return true;
		}
		$evaluated = false;
		switch ( $condition['root'] ) {
			case 'post_type':
				$evaluated = is_singular( $condition['end'] );
				break;
			case 'post':
				$evaluated = is_single() && $post_id === $condition['end'];
				break;
			case 'page':
				$evaluated = is_page() && $post_id === $condition['end'];
				/**
				 * Filters the page conditional evaluation status.
				 *
				 * @since 2.1.1
				 *
				 * @param bool  $evaluated  Evaluation of the custom layout condition by page.
				 * @param string $post_id    Current post ID.
				 * @param array $condition  Condition rule details.
				 */
				$evaluated = apply_filters( 'neve_custom_layout_evaluated_condition_page', $evaluated, $post_id, $condition );
				break;
			case 'page_template':
				$evaluated = ! ( $post_id === null ) && get_page_template_slug( (int) $post_id ) === $condition['end'];
				break;
			case 'page_parent':
				$evaluated = is_page() && wp_get_post_parent_id( $post ) === (int) $condition['end'];
				break;
			case 'page_ancestor':
				$evaluated = is_page() && in_array( (int) $condition['end'], get_post_ancestors( $post ), true );
				break;
			case 'page_type':
				$evaluated = $this->static_rules['page_type'][ $condition['end'] ];
				break;
			case 'post_taxonomy':
				$parts = preg_split( '/\|/', $condition['end'] );
				if ( is_array( $parts ) && count( $parts ) === 2 ) {
					$evaluated = is_singular() && has_term( $parts[1], $parts[0], get_the_ID() );
				}
				break;
			case 'archive_term':
				$parts  = preg_split( '/\|/', $condition['end'] );
				$object = get_queried_object();
				if ( is_array( $parts ) && count( $parts ) === 2 && $object instanceof \WP_Term && isset( $object->slug ) ) {
					$evaluated = $object->slug === $parts[1] && $object->taxonomy === $parts[0];
				}
				break;
			case 'archive_taxonomy':
				$object = get_queried_object();
				if ( $object instanceof \WP_Term && isset( $object->taxonomy ) && isset( $object->slug ) ) {
					$evaluated = $object->taxonomy === $condition['end'];
				}
				break;
			case 'archive_type':
				if ( isset( $this->static_rules['archive_type'][ $condition['end'] ] ) ) {
					$evaluated = $this->static_rules['archive_type'][ $condition['end'] ];
				}
				break;
			case 'user':
				$evaluated = (string) get_current_user_id() === $condition['end'];
				break;
			case 'post_author':
				$evaluated = is_singular() && (string) $post->post_author === $condition['end'];
				break;
			case 'archive_author':
				$evaluated = is_author( $condition['end'] );
				break;
			case 'user_status':
				$evaluated = $this->static_rules['user_status'][ $condition['end'] ];
				break;
			case 'user_role':
				$user      = wp_get_current_user();
				$evaluated = in_array( $condition['end'], $user->roles, true );
				break;
			case 'product_purchase':
				$evaluated = $this->check_wc_customer_bought_product( $condition );
				break;
			case 'product_category_purchase':
				$evaluated = $this->check_wc_customer_bought_category( $condition );
				break;
			case 'product_added_to_cart':
				$evaluated = $this->check_wc_cart_items( $condition );
				break;
			case 'product_category_added_to_cart':
				$evaluated = $this->check_wc_cart_categories( $condition );
				break;
			case 'lifter_student_quiz_status':
				$evaluated = $this->lifter_check_if_student_passed_quiz( $condition );
				break;
			case 'lifter_student_course_status':
				$evaluated = $this->lifter_check_if_student_completed_course( $condition );
				break;
			case 'lifter_membership':
				$evaluated = $this->lifter_check_if_student_has_membership( $condition );
				break;
			case 'learndash_student_quiz_status':
				$evaluated = $this->learndash_check_if_student_passed_quiz( $condition );
				break;
			case 'learndash_student_course_status':
				$evaluated = $this->learndash_check_if_student_completed_course( $condition );
				break;
			case 'learndash_group':
				$evaluated = $this->learndash_check_if_student_has_membership( $condition );
				break;
			case 'wpml_language':
				$evaluated = $this->check_wpml_language( $condition );
				break;
			case 'pll_language':
				$evaluated = $this->check_pll_language( $condition );
				break;
		}
		if ( $condition['condition'] === '===' ) {
			return $evaluated;
		}

		return ! $evaluated;
	}

	/**
	 * Check if a customer has bought a product. 
	 * 
	 * @param array $condition 
	 * @return bool 
	 */
	private function check_wc_customer_bought_product( $condition ) {

		$evaluated = false;

		/* In Neve 3.4 we migrated the end condition into multiple rules. After this migration the end condition is string but we need to keep both behaviors ( string and array ) because we did the migration from conditional input. */
		if ( is_string( $condition['end'] ) ) {
			$condition['end'] = [ $condition['end'] ];
		}
		if ( ! is_array( $condition['end'] ) ) {
			return $evaluated;
		}
		if ( ! class_exists( 'WooCommerce', false ) ) {
			return $evaluated;
		}

		$user = wp_get_current_user();
		if ( ! is_object( $user ) ) {
			return $evaluated;
		}

		if ( empty( $user->ID ) ) {
			return $evaluated;
		}

		foreach ( $condition['end'] as $selected_products ) {
			$evaluated = wc_customer_bought_product( $user->user_email, $user->ID, $selected_products );
			if ( $evaluated ) {
				break;
			}
		}

		return $evaluated;
	}

	/**
	 * Check if a customer has bought a product from a particular category.
	 * 
	 * @param array $condition 
	 * @return bool 
	 */
	private function check_wc_customer_bought_category( $condition ) {

		$evaluated = false;

		if ( is_string( $condition['end'] ) ) {
			$condition['end'] = [ $condition['end'] ];
		}
		if ( ! is_array( $condition['end'] ) ) {
			return $evaluated;
		}
		if ( ! class_exists( 'WooCommerce', false ) ) {
			return $evaluated;
		}

		$customer_orders = array();
		$user_id         = get_current_user_id();

		if ( empty( $user_id ) ) {
			return $evaluated;
		}

		foreach ( wc_get_is_paid_statuses() as $paid_status ) {
			$customer_orders += wc_get_orders(
				[
					'type'        => 'shop_order',
					'limit'       => -1,
					'customer_id' => $user_id,
					'status'      => $paid_status,
				] 
			);
		}

		$purchased_product_ids = array();

		foreach ( $customer_orders as $order ) {
			$ordered_items = $order->get_items();
			if ( ! is_array( $ordered_items ) ) {
				continue;
			}
			foreach ( $ordered_items as $item_id => $item ) {
				array_push( $purchased_product_ids, $item->get_product_id() );
			}       
		}

		$purchased_product_ids = array_unique( $purchased_product_ids );
		$selected_categories   = $condition['end'];

		foreach ( $purchased_product_ids as $product_id ) {
			if ( has_term( $selected_categories, 'product_cat', $product_id ) ) {
				$evaluated = true;
				break;
			}       
		}

		return $evaluated;
	}

	/**
	 * Check WooCommerce cart items.
	 * 
	 * @param array $condition 
	 * @return bool
	 */
	private function check_wc_cart_items( $condition ) {

		$evaluated = false;

		$selected_products = $condition['end'];
		if ( ! is_array( $condition['end'] ) ) {
			$selected_products = [ $condition['end'] ];
		}
		if ( ! class_exists( 'WooCommerce', false ) ) {
			return $evaluated;
		}

		$cart = WC()->cart->get_cart();

		foreach ( $cart as $cart_item => $details ) {
			if ( in_array( $details['product_id'], $selected_products ) ) {
				$evaluated = true;
				break;
			}
		}

		return $evaluated;
	}

	/**
	 * Check categories of cart items.
	 * 
	 * @param array $condition 
	 * @return bool 
	 */
	private function check_wc_cart_categories( $condition ) {

		$evaluated = false;

		$selected_categories = $condition['end'];
		if ( ! is_array( $condition['end'] ) ) {
			$selected_categories = [ $condition['end'] ];
		}
		if ( ! class_exists( 'WooCommerce', false ) ) {
			return $evaluated;
		}

		$cart = WC()->cart->get_cart();

		foreach ( $cart as $cart_item => $details ) {

			$product_id = $details['product_id'];

			$categories_in_cart = get_the_terms( $product_id, 'product_cat' );
			$categories_in_cart = array_column( $categories_in_cart, 'slug', 'term_id' );

			foreach ( $selected_categories as $category ) {
				if ( in_array( $category, $categories_in_cart ) ) {
					$evaluated = true;
					break 2;
				}
			}       
		}

		return $evaluated;
	}

	/**
	 * Check if a user passed a quiz.
	 * 
	 * @param array $condition 
	 * @return bool 
	 */
	private function lifter_check_if_student_passed_quiz( $condition ) {

		$evaluated = false;

		if ( is_string( $condition['end'] ) ) {
			$condition['end'] = [ $condition['end'] ];
		}
		if ( ! is_array( $condition['end'] ) ) {
			return $evaluated;
		}
		if ( ! function_exists( 'llms_get_student' ) ) {
			return $evaluated;
		}

		$user_id = get_current_user_id();
		if ( empty( $user_id ) ) {
			return $evaluated;
		}

		$student_obj = llms_get_student( $user_id );
		if ( ! is_object( $student_obj ) ) {
			return $evaluated;
		}

		foreach ( $condition['end'] as $quiz_id ) {
			$student_last_quiz_attempt = $student_obj->quizzes()->get_last_attempt( $quiz_id );
			if ( ! is_object( $student_last_quiz_attempt ) ) {
				continue;
			}
			if ( $student_last_quiz_attempt->is_passing() ) {
				$evaluated = true;
				break;
			}
		}
		
		return $evaluated;
	}

	/**
	 * Check if a user completed a course.
	 * 
	 * @param array $condition 
	 * @return bool 
	 */
	private function lifter_check_if_student_completed_course( $condition ) {

		$evaluated = false;

		if ( is_string( $condition['end'] ) ) {
			$condition['end'] = [ $condition['end'] ];
		}
		if ( ! is_array( $condition['end'] ) ) {
			return $evaluated;
		}
		if ( ! function_exists( 'llms_get_student' ) ) {
			return $evaluated;
		}

		$user_id = get_current_user_id();
		if ( empty( $user_id ) ) {
			return $evaluated;
		}

		$student_obj = llms_get_student( $user_id );
		if ( ! is_object( $student_obj ) ) {
			return $evaluated;
		}

		foreach ( $condition['end'] as $course_id ) {
			$course_completed = $student_obj->is_complete( $course_id );
			if ( $course_completed ) {
				$evaluated = true;
				break;
			}
		}

		return $evaluated;
	}

	/**
	 * Check if a user has access to a membership.
	 * 
	 * @param array $condition 
	 * @return bool 
	 */
	private function lifter_check_if_student_has_membership( $condition ) {

		$evaluated = false;

		if ( is_string( $condition['end'] ) ) {
			$condition['end'] = [ $condition['end'] ];
		}
		if ( ! is_array( $condition['end'] ) ) {
			return $evaluated;
		}
		if ( ! function_exists( 'llms_get_student' ) ) {
			return $evaluated;
		}

		$user_id = get_current_user_id();
		if ( empty( $user_id ) ) {
			return $evaluated;
		}

		$student_obj = llms_get_student( $user_id );
		if ( ! is_object( $student_obj ) ) {
			return $evaluated;
		}
		$student_memberships  = $student_obj->get_membership_levels();
		$selected_memberships = $condition['end'];
		
		foreach ( $student_memberships as $student_membership ) {
			if ( in_array( $student_membership, $selected_memberships ) ) {
				$evaluated = true;
				break;
			}
		}

		return $evaluated;
	}

	/**
	 * Check if a user passed a quiz.
	 * 
	 * @param array $condition 
	 * @return bool 
	 */
	private function learndash_check_if_student_passed_quiz( $condition ) {

		$evaluated = false;

		if ( is_string( $condition['end'] ) ) {
			$condition['end'] = [ $condition['end'] ];
		}
		if ( ! is_array( $condition['end'] ) ) {
			return $evaluated;
		}
		if ( ! function_exists( 'learndash_user_quiz_has_completed' ) ) {
			return $evaluated;
		}

		$user_id = get_current_user_id();
		if ( empty( $user_id ) ) {
			return $evaluated;
		}

		foreach ( $condition['end'] as $quiz_id ) {
			$quiz_passed = learndash_user_quiz_has_completed( $user_id, $quiz_id );
			if ( is_bool( $quiz_passed ) && $quiz_passed ) {
				$evaluated = true;
				break;
			}
		}
		
		return $evaluated;
	}

	/**
	 * Check if a user completed a course.
	 * 
	 * @param array $condition 
	 * @return bool 
	 */
	private function learndash_check_if_student_completed_course( $condition ) {

		$evaluated = false;

		if ( is_string( $condition['end'] ) ) {
			$condition['end'] = [ $condition['end'] ];
		}
		if ( ! is_array( $condition['end'] ) ) {
			return $evaluated;
		}
		if ( ! function_exists( 'learndash_course_completed' ) ) {
			return $evaluated;
		}

		$user_id = get_current_user_id();
		if ( empty( $user_id ) ) {
			return $evaluated;
		}

		foreach ( $condition['end'] as $course_id ) {
			$course_completed = learndash_course_completed( $user_id, $course_id );
			if ( $course_completed ) {
				$evaluated = true;
				break;
			}
		}

		return $evaluated;
	}

	/**
	 * Check if a user is in a group.
	 * 
	 * @param array $condition 
	 * @return bool 
	 */
	private function learndash_check_if_student_has_membership( $condition ) {

		$evaluated = false;

		if ( is_string( $condition['end'] ) ) {
			$condition['end'] = [ $condition['end'] ];
		}
		if ( ! is_array( $condition['end'] ) ) {
			return $evaluated;
		}
		if ( ! function_exists( 'learndash_get_users_group_ids' ) ) {
			return $evaluated;
		}

		$user_id = get_current_user_id();
		if ( empty( $user_id ) ) {
			return $evaluated;
		}

		$student_groups = learndash_get_users_group_ids( $user_id );
		if ( empty( $student_groups ) ) {
			return $evaluated;
		}

		$selected_groups = $condition['end'];
		
		foreach ( $student_groups as $student_group ) {
			if ( in_array( $student_group, $selected_groups ) ) {
				$evaluated = true;
				break;
			}
		}

		return $evaluated;
	}

	/**
	 * Check current WPML site language.
	 * 
	 * @param array $condition 
	 * @return bool 
	 */
	private function check_wpml_language( $condition ) {

		$evaluated = false;

		if ( is_string( $condition['end'] ) ) {
			$condition['end'] = [ $condition['end'] ];
		}
		if ( ! is_array( $condition['end'] ) ) {
			return $evaluated;
		}
		if ( ! function_exists( 'icl_object_id' ) ) {
			return $evaluated;
		}
		if ( ! defined( 'ICL_LANGUAGE_CODE' ) ) {
			return $evaluated;
		}

		$selected_languages = $condition['end'];
		$current_language   = ICL_LANGUAGE_CODE;

		if ( in_array( $current_language, $selected_languages ) ) {
			$evaluated = true;
		}

		return $evaluated;
	}

	/**
	 * Check current Polylang site language.
	 * 
	 * @param array $condition 
	 * @return bool 
	 */
	private function check_pll_language( $condition ) {

		$evaluated = false;

		if ( is_string( $condition['end'] ) ) {
			$condition['end'] = [ $condition['end'] ];
		}
		if ( ! is_array( $condition['end'] ) ) {
			return $evaluated;
		}
		if ( ! function_exists( 'pll_the_languages' ) ) {
			return $evaluated;
		}
		if ( ! function_exists( 'pll_current_language' ) ) {
			return $evaluated;
		}

		$selected_languages = $condition['end'];
		$current_language   = pll_current_language();

		if ( in_array( $current_language, $selected_languages ) ) {
			$evaluated = true;
		}

		return $evaluated;
	}
	
}
