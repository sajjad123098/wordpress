<?php
/**
 * Author:          Andrei Baicus <andrei@themeisle.com>
 * Created on:      2019-12-19
 *
 * @package Neve Pro
 */

namespace Neve_Pro\Admin;

/**
 * Class Conditional_Display
 *
 * @package Neve_Pro\Admin
 */
class Conditional_Display {

	const RESULTS_POST_LIMIT = 99;

	const USERS_CACHE_KEY = '_nv_pro_cl_users_cache';

	/**
	 * List of end rules that should have multiselect inputs.
	 * 
	 * @var array 
	 */
	const MULTISELECT_RULES = [
		'product_purchase',
		'product_category_purchase',
		'product_added_to_cart',
		'product_category_added_to_cart',
		'lifter_student_quiz_status',
		'lifter_student_course_status',
		'lifter_membership',
		'learndash_student_quiz_status',
		'learndash_student_course_status',
		'learndash_group',
		'wpml_language',
		'pll_language',
	];

	/**
	 * Root Ruleset
	 *
	 * @var array
	 */
	private $root_ruleset;
	/**
	 * End Ruleset
	 *
	 * @var array
	 */
	private $end_ruleset = [
		'post_types'     => [],
		'posts'          => [],
		'page_templates' => [],
		'pages'          => [],
		'page_type'      => [],
		'page_parent'    => [],
		'page_ancestor'  => [],
		'terms'          => [],
		'taxonomies'     => [],
		'archive_types'  => [],
		'users'          => [],
		'user_status'    => [],
		'user_roles'     => [],
	];
	/**
	 * Ruleset Map.
	 *
	 * @var array
	 */
	private $ruleset_map;
	/**
	 * Post types.
	 *
	 * @var array
	 */
	private $post_types;

	/**
	 * Conditional_Display constructor.
	 */
	public function __construct() {
		add_action( 'user_register', [ $this, 'bust_users_cache' ] );
		add_action( 'deleted_user', [ $this, 'bust_users_cache' ] );

		$this->post_types   = $this->get_post_types();
		$this->root_ruleset = [
			'post'    => [
				'label'   => __( 'Post', 'neve' ),
				'choices' => [
					'post_type'     => __( 'Post Type', 'neve' ),
					'post_taxonomy' => __( 'Post Taxonomy', 'neve' ),
					'post_author'   => __( 'Post Author', 'neve' ),
					'post'          => __( 'Post', 'neve' ),
				],
			],
			'page'    => [
				'label'   => __( 'Page', 'neve' ),
				'choices' => [
					'page_type'     => __( 'Page Type', 'neve' ),
					'page_template' => __( 'Page Template', 'neve' ),
					'page'          => __( 'Page', 'neve' ),
					'page_parent'   => __( 'Page Parent', 'neve' ),
					'page_ancestor' => __( 'Page Ancestor', 'neve' ),
				],
			],
			'archive' => [
				'label'   => __( 'Archive', 'neve' ),
				'choices' => [
					'archive_type'     => __( 'Archive Type', 'neve' ),
					'archive_taxonomy' => __( 'Archive Taxonomy', 'neve' ),
					'archive_term'     => __( 'Archive Term', 'neve' ),
					'archive_author'   => __( 'Archive Author', 'neve' ),
				],
			],
			'user'    => [
				'label'   => __( 'User', 'neve' ),
				'choices' => [
					'user_status' => __( 'User Status', 'neve' ),
					'user_role'   => __( 'User Role', 'neve' ),
					'user'        => __( 'User', 'neve' ),
				],
			],
		];

		$this->end_ruleset['post_types']     = $this->get_post_types();
		$this->end_ruleset['posts']          = $this->get_page_post_list();
		$this->end_ruleset['page_templates'] = $this->get_templates();
		$this->end_ruleset['pages']          = $this->get_page_post_list( 'page' );
		$this->end_ruleset['page_type']      = [
			'front_page' => __( 'Front Page', 'neve' ),
			'posts_page' => __( 'Posts Page', 'neve' ),
			'not_found'  => __( '404', 'neve' ),
		];
		$this->end_ruleset['page_parent']    = $this->get_page_post_list( 'page' );
		$this->end_ruleset['page_ancestor']  = $this->get_page_post_list( 'page' );
		$this->end_ruleset['terms']          = $this->get_all_taxonomies();
		$this->end_ruleset['taxonomies']     = $this->get_all_taxonomies();
		$this->end_ruleset['archive_types']  = $this->get_archive_types();
		$this->end_ruleset['users']          = $this->get_users();
		$this->end_ruleset['user_status']    = [
			'logged_in'  => __( 'Logged In', 'neve' ),
			'logged_out' => __( 'Logged Out', 'neve' ),
		];
		$this->end_ruleset['user_roles']     = $this->get_user_roles();

		$this->ruleset_map = [
			'post_types'     => [ 'post_type' ],
			'posts'          => [ 'post' ],
			'page_templates' => [ 'page_template' ],
			'pages'          => [ 'page' ],
			'page_type'      => [ 'page_type' ],
			'page_parent'    => [ 'page_parent' ],
			'page_ancestor'  => [ 'page_ancestor' ],
			'terms'          => [ 'post_taxonomy', 'archive_term' ],
			'taxonomies'     => [ 'archive_taxonomy' ],
			'archive_types'  => [ 'archive_type' ],
			'users'          => [ 'user', 'post_author', 'archive_author' ],
			'user_status'    => [ 'user_status' ],
			'user_roles'     => [ 'user_role' ],
		];

		if ( class_exists( 'WooCommerce', false ) ) {
			$this->add_woocommerce_root_rules();
			$this->add_woocommerce_end_rules();
			$this->add_woocommerce_ruleset_map();
		}

		if ( class_exists( 'LifterLMS', false ) ) {
			$this->add_lifter_root_rules();
			$this->add_lifter_end_rules();
			$this->add_lifter_ruleset_map();
		}

		if ( class_exists( 'SitePress', false ) ) {
			$this->add_wpml_root_rules();
			$this->add_wpml_end_rules();
			$this->add_wpml_ruleset_map();
		}

		if ( function_exists( 'pll_the_languages' ) ) {
			$this->add_pll_root_rules();
			$this->add_pll_end_rules();
			$this->add_pll_ruleset_map();
		}

		if ( defined( 'LEARNDASH_VERSION' ) ) {
			$this->add_learndash_root_rules();
			$this->add_learndash_end_rules();
			$this->add_learndash_ruleset_map();
		}
	}

	/**
	 * Get the end ruleset.
	 *
	 * @return array
	 */
	public function get_end_ruleset() {
		return $this->end_ruleset;
	}

	/**
	 * Get the root ruleset.
	 *
	 * @return array
	 */
	public function get_root_ruleset() {
		return $this->root_ruleset;
	}

	/**
	 * Get the ruleset map.
	 *
	 * @return array
	 */
	public function get_ruleset_map() {
		return $this->ruleset_map;
	}

	/**
	 * Get available archive types.
	 *
	 * @return array
	 */
	private function get_archive_types() {
		$archive_types = array(
			'date'   => __( 'Date', 'neve' ),
			'author' => __( 'Author', 'neve' ),
			'search' => __( 'Search', 'neve' ),
		);

		return array_merge( $archive_types, $this->get_post_types() );
	}

	/**
	 * Gets the page templates.
	 *
	 * @return array
	 */
	private function get_templates() {
		require_once ABSPATH . 'wp-admin/includes/theme.php';

		return array_flip( get_page_templates() );
	}


	/**
	 * Used to filter select results for large data.
	 *
	 * @param string $type [post/page].
	 * @param string $query search query.
	 *
	 * @return array
	 */
	public static function get_options_list( $type, $query ) {
		if ( ! in_array( $type, [ 'post', 'page' ], true ) ) {
			$type = 'post';
		}

		$cache_key      = 'neve_cache_' . $type . '_' . md5( $query );
		$cached_results = wp_cache_get( $cache_key );
		if ( ! empty( $cached_results ) ) {
			return $cached_results;
		}

		$post_types = array_filter(
			get_post_types( array( 'public' => true ) ),
			function ( $post_type ) {
				$excluded = array( 'attachment', 'neve_custom_layouts', 'page' );
				if ( in_array( $post_type, $excluded, true ) ) {
					return false;
				}

				return true;
			}
		);
		$post_types = 'page' === $type ? [ $type ] : $post_types;

		global $wpdb;
		$placeholders = implode( ', ', array_fill( 0, count( $post_types ), '%s' ) );
		$args         = array_merge(
			[ '%' . $wpdb->esc_like( $query ) . '%' ],
			$post_types,
			[ self::RESULTS_POST_LIMIT ]
		);
		$query_string = "SELECT ID, post_title FROM $wpdb->posts WHERE post_title LIKE %s AND post_type IN ($placeholders) AND post_status='publish' LIMIT %d";
		// phpcs:ignore
		$sql_query    = $wpdb->prepare(	$query_string, $args );

		// phpcs:ignore
		$posts     = $wpdb->get_results( $sql_query );
		$post_list = array();

		if ( ! empty( $posts ) ) {
			foreach ( $posts as $post ) {
				$post_list[ $post->ID ] = $post->post_title;
			}
		}

		wp_cache_set( $cache_key, $post_list, '', MINUTE_IN_SECONDS );

		return $post_list;
	}

	/**
	 * Get the pages and posts.
	 *
	 * @param string $type [post/page].
	 *
	 * @return array
	 */
	private function get_page_post_list( $type = 'post' ) {
		if ( $type === 'post' ) {
			$posts = get_posts( [ 'numberposts' => self::RESULTS_POST_LIMIT ] ); // phpcs:ignore WordPress.WP.PostsPerPage.posts_per_page_numberposts
		}

		if ( $type === 'page' ) {
			$posts = array_filter(
				get_pages( [ 'number' => self::RESULTS_POST_LIMIT ] ),
				function ( $item ) {
					if ( (string) $item->ID === get_option( 'page_for_posts' ) ) {
						return false;
					}
					if ( (string) $item->ID === get_option( 'woocommerce_shop_page_id' ) ) {
						return false;
					}
					return true;
				}
			);
		}
		$post_list = array();

		if ( ! empty( $posts ) ) {
			foreach ( $posts as $post ) {
				$post_list[ $post->ID ] = $post->post_title;
			}
		}

		return $post_list;
	}

	/**
	 * Bust the users transient cache.
	 */
	public function bust_users_cache() {
		delete_transient( self::USERS_CACHE_KEY );
	}

	/**
	 * Get the site users.
	 *
	 * @return array
	 */
	private function get_users() {
		$cache = get_transient( self::USERS_CACHE_KEY );

		if ( $cache !== false ) {
			return $cache;
		}

		$users    = array();
		$wp_users = get_users();

		foreach ( $wp_users as $user_data ) {
			$users[ $user_data->ID ] = $user_data->display_name;
		}

		set_transient( self::USERS_CACHE_KEY, $users );

		return $users;
	}

	/**
	 * Get all user roles.
	 *
	 * @return array
	 */
	private function get_user_roles() {
		global $wp_roles;
		$roles              = $wp_roles->get_names();
		$user_roles_choices = array(
			'all' => esc_html__( 'All', 'neve' ),
		);
		foreach ( $roles as $role_slug => $role_name ) {
			$user_roles_choices[ $role_slug ] = $role_name;
		}

		return $user_roles_choices;
	}

	/**
	 * Get all the taxonomies.
	 *
	 * @return array
	 */
	private function get_all_taxonomies() {
		$taxonomies = array();
		foreach ( $this->post_types as $post_type => $label ) {
			$all_taxes = get_object_taxonomies( $post_type );
			foreach ( $all_taxes as $single_tax ) {
				$tax_obj = get_taxonomy( $single_tax );
				if ( ! $tax_obj->publicly_queryable ) {
					continue;
				}
				$tax_terms = get_terms( array( 'taxonomy' => $single_tax ) );

				$taxonomies[ $post_type ][] = array(
					'nicename' => $tax_obj->label,
					'name'     => $tax_obj->name,
					'terms'    => $tax_terms,
				);
			}
		}

		return $taxonomies;
	}

	/**
	 * Get the post types.
	 *
	 * @return array
	 */
	private function get_post_types() {
		$post_types = array_filter(
			get_post_types( array( 'public' => true ) ),
			function ( $post_type ) {
				$excluded = array( 'attachment', 'neve_custom_layouts' );
				if ( in_array( $post_type, $excluded, true ) ) {
					return false;
				}

				return true;
			}
		);
		foreach ( $post_types as $post_type ) {
			$pt_object                = get_post_type_object( $post_type );
			$post_types[ $post_type ] = $pt_object->label;

			if ( ! in_array( $post_type, [ 'page', 'post' ], true ) ) {
				$posts = get_posts(
					[
						'post_type'   => $post_type,
						'numberposts' => self::RESULTS_POST_LIMIT,
					]
				);
				if ( ! empty( $posts ) ) {
					$post_list = [];
					foreach ( $posts as $post ) {
						$post_list[ $post->ID ] = $post->post_title;
					}
					$this->end_ruleset['posts'] = array_replace( $this->end_ruleset['posts'], $post_list );
				}
			}
		}

		return $post_types;
	}

	/**
	 * Add WooCommerce root rules to root rules array.
	 * 
	 * @return void 
	 */
	private function add_woocommerce_root_rules() {
		$wc_root_rules = [
			'woocommerce' => [
				'label'   => __( 'WooCommerce', 'neve' ),
				'choices' => [
					'product_purchase'               => __( 'Product Purchase', 'neve' ),
					'product_category_purchase'      => __( 'Product Category Purchase', 'neve' ),
					'product_added_to_cart'          => __( 'Product Added to Cart', 'neve' ),
					'product_category_added_to_cart' => __( 'Product Category Added to Cart', 'neve' ),
				],
			],
		];

		$this->root_ruleset = array_merge( $this->root_ruleset, $wc_root_rules );
	}

	/**
	 * Add WooCommerce end rules to end rules array.
	 * 
	 * @return void 
	 */
	private function add_woocommerce_end_rules() {
		$wc_end_rules = [
			'product_purchase'               => $this->get_post_type_posts( 'product' ),
			'product_category_purchase'      => $this->get_woocommerce_categories(),
			'product_added_to_cart'          => $this->get_post_type_posts( 'product' ),
			'product_category_added_to_cart' => $this->get_woocommerce_categories(),
		];

		$this->end_ruleset = array_merge( $this->end_ruleset, $wc_end_rules );
	}

	/**
	 * Add WooCommerce ruleset map to ruleset map array.
	 * 
	 * @return void 
	 */
	private function add_woocommerce_ruleset_map() {
		$wc_ruleset_map = [
			'product_purchase'               => [ 'product_purchase' ],
			'product_category_purchase'      => [ 'product_category_purchase' ],
			'product_added_to_cart'          => [ 'product_added_to_cart' ],
			'product_category_added_to_cart' => [ 'product_category_added_to_cart' ],
		];

		$this->ruleset_map = array_merge( $this->ruleset_map, $wc_ruleset_map );
	}

	/**
	 * Add Lifter LMS root rules to root rules array.
	 * 
	 * @return void 
	 */
	private function add_lifter_root_rules() {
		$lifter_root_rules = [
			'lifter_lms' => [
				'label'   => __( 'Lifter LMS', 'neve' ),
				'choices' => [
					'lifter_student_quiz_status'   => __( 'Quiz Status', 'neve' ),
					'lifter_student_course_status' => __( 'Course Status', 'neve' ),
					'lifter_membership'            => __( 'Memberships', 'neve' ),
				],
			],
		];

		$this->root_ruleset = array_merge( $this->root_ruleset, $lifter_root_rules );
	}

	/**
	 * Add Lifter LMS end rules to end rules array.
	 * 
	 * @return void 
	 */
	private function add_lifter_end_rules() {
		$lifter_end_rules = [
			'lifter_student_quiz_status'   => $this->get_post_type_posts( 'llms_quiz' ),
			'lifter_student_course_status' => $this->get_post_type_posts( 'course' ),
			'lifter_membership'            => $this->get_post_type_posts( 'llms_membership' ),
		];

		$this->end_ruleset = array_merge( $this->end_ruleset, $lifter_end_rules );
	}

	/**
	 * Add Lifter LMS ruleset map to ruleset map array.
	 * 
	 * @return void 
	 */
	private function add_lifter_ruleset_map() {
		$lifter_ruleset_map = [
			'lifter_student_quiz_status'   => [ 'lifter_student_quiz_status' ],
			'lifter_student_course_status' => [ 'lifter_student_course_status' ],
			'lifter_membership'            => [ 'lifter_membership' ],
		];

		$this->ruleset_map = array_merge( $this->ruleset_map, $lifter_ruleset_map );
	}

	/**
	 * Add LearnDash LMS root rules to root rules array.
	 * 
	 * @return void 
	 */
	private function add_learndash_root_rules() {
		$learndash_root_rules = [
			'learndash_lms' => [
				'label'   => __( 'LearnDash LMS', 'neve' ),
				'choices' => [
					'learndash_student_quiz_status'   => __( 'Quiz Status', 'neve' ),
					'learndash_student_course_status' => __( 'Course Status', 'neve' ),
					'learndash_group'                 => __( 'Groups', 'neve' ),
				],
			],
		];

		$this->root_ruleset = array_merge( $this->root_ruleset, $learndash_root_rules );
	}

	/**
	 * Add LearnDash LMS end rules to end rules array.
	 * 
	 * @return void 
	 */
	private function add_learndash_end_rules() {
		$learndash_end_rules = [
			'learndash_student_quiz_status'   => $this->get_post_type_posts( 'sfwd-quiz' ),
			'learndash_student_course_status' => $this->get_post_type_posts( 'sfwd-courses' ),
			'learndash_group'                 => $this->get_post_type_posts( 'groups' ),
		];

		$this->end_ruleset = array_merge( $this->end_ruleset, $learndash_end_rules );
	}

	/**
	 * Add LearnDash LMS ruleset map to ruleset map array.
	 * 
	 * @return void 
	 */
	private function add_learndash_ruleset_map() {
		$learndash_ruleset_map = [
			'learndash_student_quiz_status'   => [ 'learndash_student_quiz_status' ],
			'learndash_student_course_status' => [ 'learndash_student_course_status' ],
			'learndash_group'                 => [ 'learndash_group' ],
		];

		$this->ruleset_map = array_merge( $this->ruleset_map, $learndash_ruleset_map );
	}
	
	/**
	 * Add WPML root rules to root rules array.
	 * 
	 * @return void 
	 */
	private function add_wpml_root_rules() {
		$wpml_root_rules = [
			'wpml' => [
				'label'   => __( 'WPML', 'neve' ),
				'choices' => [
					'wpml_language' => __( 'Current Language', 'neve' ),
				],
			],
		];

		$this->root_ruleset = array_merge( $this->root_ruleset, $wpml_root_rules );
	}

	/**
	 * Add WMPL end rules to end rules array.
	 * 
	 * @return void 
	 */
	private function add_wpml_end_rules() {

		$wpml_active_languages = apply_filters( 'wpml_active_languages', null, array( 'skip_missing' => 1 ) );

		$languages_array = array_column( $wpml_active_languages, 'native_name', 'language_code' );

		$wpml_end_rules = [
			'wpml_language' => $languages_array,
		];

		$this->end_ruleset = array_merge( $this->end_ruleset, $wpml_end_rules );
	}

	/**
	 * Add WPML ruleset map to ruleset map array.
	 * 
	 * @return void 
	 */
	private function add_wpml_ruleset_map() {
		$wpml_ruleset_map = [
			'wpml_language' => [ 'wpml_language' ],
		];

		$this->ruleset_map = array_merge( $this->ruleset_map, $wpml_ruleset_map );
	}

	/**
	 * Add Polylang root rules to root rules array.
	 * 
	 * @return void 
	 */
	private function add_pll_root_rules() {
		$pll_root_rules = [
			'wpml' => [
				'label'   => __( 'Polylang', 'neve' ),
				'choices' => [
					'pll_language' => __( 'Current Language', 'neve' ),
				],
			],
		];

		$this->root_ruleset = array_merge( $this->root_ruleset, $pll_root_rules );
	}

	/**
	 * Add Polylang end rules to end rules array.
	 * 
	 * @return void 
	 */
	private function add_pll_end_rules() {
		
		$languages_array = array();
		$languages       = get_terms(
			'language', // @phpstan-ignore-line - format is for legacy invocation of get_terms().
			array(
				'hide_empty' => false,
				'orderby'    => 'term_group',
			) 
		);
		
		if ( ! empty( $languages ) && is_array( $languages ) ) {
			foreach ( $languages as $language_key => $language_object ) {
				$languages_array[ $language_object->slug ] = $language_object->name;
			}
		}

		$pll_end_rules = [
			'pll_language' => $languages_array,
		];

		$this->end_ruleset = array_merge( $this->end_ruleset, $pll_end_rules );
	}

	/**
	 * Add Polylang ruleset map to ruleset map array.
	 * 
	 * @return void 
	 */
	private function add_pll_ruleset_map() {
		$pll_ruleset_map = [
			'pll_language' => [ 'pll_language' ],
		];

		$this->ruleset_map = array_merge( $this->ruleset_map, $pll_ruleset_map );
	}

	/**
	 * Get WooCommerce Product Categories.
	 * 
	 * @return array 
	 */
	private function get_woocommerce_categories() {

		$product_taxonomies = $this->get_all_taxonomies()['product'] ?? '';

		if ( empty( $product_taxonomies ) ) {
			return array();
		}

		$terms = array();

		foreach ( $product_taxonomies as $product_taxonomy => $details ) {
			if ( $details['name'] === 'product_cat' ) {
				$terms = $details['terms'];
				break;
			}
		}
		
		if ( ! is_array( $terms ) || empty( $terms ) ) {
			return array();
		}

		$categories = array_column( $terms, 'name', 'slug' );

		return $categories;
	}

	/**
	 * Get a formatted list of posts based on post type.
	 *  
	 * @param string $post_type 
	 * @return array 
	 */
	private function get_post_type_posts( $post_type ) {

		$number_of_results = apply_filters( "neve_custom_layouts_num_{$post_type}", 99 );

		$args = array(
			'numberposts' => $number_of_results,
			'post_type'   => $post_type,
		);

		$results     = get_posts( $args );
		$result_list = array();

		if ( ! empty( $results ) ) {
			foreach ( $results as $result ) {
				$result_list[ $result->ID ] = $result->post_title;
			}
		}

		return $result_list;
	}

	/**
	 * Create a text map for the condition portion of the custom layouts conditional fields.
	 * 
	 * @return array 
	 */
	public static function create_custom_layouts_condition_text_map() {

		return array(

			'product_purchase'                => array(
				'===' => __( 'has purchased', 'neve' ),
				'!==' => __( 'has not purchased', 'neve' ),
			),
			'product_category_purchase'       => array(
				'===' => __( 'has purchased', 'neve' ),
				'!==' => __( 'has not purchased', 'neve' ),
			),
			'product_added_to_cart'           => array(
				'===' => __( 'in cart', 'neve' ),
				'!==' => __( 'not in cart', 'neve' ),
			),
			'product_category_added_to_cart'  => array(
				'===' => __( 'in cart', 'neve' ),
				'!==' => __( 'not in cart', 'neve' ),
			),
			'lifter_student_quiz_status'      => array(
				'===' => __( 'passed', 'neve' ),
				'!==' => __( 'failed', 'neve' ),
			),
			'lifter_student_course_status'    => array(
				'===' => __( 'completed', 'neve' ),
				'!==' => __( 'not completed', 'neve' ),
			),
			'lifter_membership'               => array(
				'===' => __( 'has membership', 'neve' ),
				'!==' => __( 'does not have membership', 'neve' ),
			),
			'learndash_student_quiz_status'   => array(
				'===' => __( 'passed', 'neve' ),
				'!==' => __( 'failed', 'neve' ),
			),
			'learndash_student_course_status' => array(
				'===' => __( 'completed', 'neve' ),
				'!==' => __( 'not completed', 'neve' ),
			),
			'learndash_group'                 => array(
				'===' => __( 'in group', 'neve' ),
				'!==' => __( 'not in group', 'neve' ),
			),
			'default'                         => array(
				'===' => __( 'is equal to', 'neve' ),
				'!==' => __( 'is not equal to', 'neve' ),
			),

		);

	}

}
