<?php
/**
 * Handles the CPT Registration.
 *
 * Created on:      2020-01-21
 *
 * @package Neve Pro
 */

namespace Neve_Pro\Admin;

use Neve_Pro\Core\Loader;
use Neve_Pro\Modules\Header_Footer_Grid\Module;
use Neve_Pro\Modules\Custom_Layouts\Utilities;
use WP_Query;

/**
 * Class Custom_Layouts_Cpt
 *
 * @package Neve_Pro\Admin
 */
class Custom_Layouts_Cpt {
	use Utilities;

	/**
	 * CPT edit screen id.
	 *
	 * @var string
	 */
	private $cpt_screen_id = 'edit-neve_custom_layouts';

	/**
	 * Initialize the Custom Layouts CPT class.
	 */
	public function init() {
		$this->register_custom_post_type();
		add_action( 'init', [ $this, 'add_role_caps' ], 11 );
		add_filter( 'parse_query', [ $this, 'remove_other_types' ] );
		add_filter( 'views_' . $this->cpt_screen_id, [ $this, 'recount_posts' ] );
		add_filter( 'wp_count_posts', [ $this, 'recount_posts' ], 10, 1 );
		add_action( 'admin_menu', [ $this, 'change_cpt_label' ], 100 );
		add_action( 'save_post', array( $this, 'remove_custom_layouts_transient' ) );
	}

	/**
	 * Remove custom layouts transient at post save.
	 *
	 * @param int $post_id Post id.
	 */
	function remove_custom_layouts_transient( $post_id ) {
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		$post_type = get_post_type( $post_id );

		if ( 'neve_custom_layouts' !== $post_type ) {
			return;
		}
		delete_transient( 'custom_layouts_post_map_v3' );
	}

	/**
	 * Change CPT label and position to be under the last page registered inside Neve or TPC.
	 */
	public function change_cpt_label() {
		global $submenu;

		$supports_new_menu = Loader::has_compatibility( 'theme_dedicated_menu' );

		if ( $supports_new_menu && 'valid' === apply_filters( 'product_neve_license_status', false ) ) {
			$theme_page = 'neve-welcome';
			$capability = 'manage_options';
			if ( ! isset( $submenu[ $theme_page ] ) ) {
				return;
			}

			if ( empty( get_option( 'nv_pro_custom_layouts_status', true ) ) ) {
				return;
			}

			add_submenu_page(
				$theme_page,
				__( 'Custom Layouts', 'neve' ),
				__( 'Custom Layouts', 'neve' ),
				$capability,
				'edit.php?post_type=neve_custom_layouts'
			);

			$item = array_pop( $submenu[ $theme_page ] );
			array_splice( $submenu[ $theme_page ], 2, 0, [ $item ] );

			return;
		}

		if ( ! isset( $submenu['themes.php'] ) ) {
			return;
		}

		$last_theme_page_position = false;
		$cpt_position             = false;
		foreach ( $submenu['themes.php'] as $index => $item ) {
			if ( $item[2] === 'neve-welcome' || $item[2] === 'tiob-starter-sites' ) {
				$last_theme_page_position = $index;
				continue;
			}

			if ( $item[2] === 'edit.php?post_type=neve_custom_layouts' ) {
				$cpt_position = $index;
				$style        = 'display:inline-block;';

				if ( ! is_rtl() ) {
					$style .= 'transform:scaleX(-1);margin-right:5px;';
				} else {
					$style .= 'margin-left:5px;';
				}

				$prefix = '<span style="' . esc_attr( $style ) . '">&crarr;</span>';

				$submenu['themes.php'][ $index ][0] = $prefix . $submenu['themes.php'][ $index ][0]; //phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			}
		}

		if ( $last_theme_page_position && $cpt_position ) {
			$cpt = $submenu['themes.php'][ $cpt_position ];
			unset( $submenu['themes.php'][ $cpt_position ] );
			array_splice( $submenu['themes.php'], $last_theme_page_position + 1, 0, [ $cpt ] );
		}
	}

	/**
	 * Add capabilities for administrators and editors to create a custom layout.
	 */
	public function add_role_caps() {
		/**
		 * Filters the user roles allowed to create a custom layout post.
		 *
		 * @param array $roles Allowed user roles.
		 */
		$roles = apply_filters( 'neve_custom_layouts_roles', [ 'administrator', 'editor' ] );
		foreach ( $roles as $current_role ) {
			$role = get_role( $current_role );
			if ( $role === null ) {
				continue;
			}
			if ( ! method_exists( $role, 'add_cap' ) ) {
				continue;
			}
			$role->add_cap( 'edit_custom_layout', true );
			$role->add_cap( 'edit_custom_layouts', true );
			$role->add_cap( 'edit_others_custom_layouts', true );
			$role->add_cap( 'publish_custom_layouts', true );
			$role->add_cap( 'read_custom_layout', true );
			$role->add_cap( 'read_private_custom_layouts', true );
			$role->add_cap( 'delete_custom_layout', true );
		}
	}


	/**
	 * Register Custom Layouts post type.
	 */
	private function register_custom_post_type() {

		$labels = array(
			'name'          => esc_html_x( 'Custom Layouts', 'advanced-hooks general name', 'neve' ),
			'singular_name' => esc_html_x( 'Custom Layout', 'advanced-hooks singular name', 'neve' ),
			'search_items'  => esc_html__( 'Search Custom Layouts', 'neve' ),
			'all_items'     => esc_html__( 'Custom Layouts', 'neve' ),
			'edit_item'     => esc_html__( 'Edit Custom Layout', 'neve' ),
			'view_item'     => esc_html__( 'View Custom Layout', 'neve' ),
			'add_new'       => esc_html__( 'Add New', 'neve' ),
			'update_item'   => esc_html__( 'Update Custom Layout', 'neve' ),
			'add_new_item'  => esc_html__( 'Add New', 'neve' ),
			'new_item_name' => esc_html__( 'New Custom Layout Name', 'neve' ),
		);

		$args = array(
			'labels'              => $labels,
			'public'              => false,
			'query_var'           => true,
			'can_export'          => true,
			'exclude_from_search' => true,
			'show_in_rest'        => true,
			'supports'            => array( 'title', 'editor', 'elementor' ),
			'capability_type'     => 'custom_layout',
			'capabilities'        => array(
				'edit_post'          => 'edit_custom_layout',
				'edit_posts'         => 'edit_custom_layouts',
				'edit_others_posts'  => 'edit_others_custom_layouts',
				'publish_posts'      => 'publish_custom_layouts',
				'read_post'          => 'read_custom_layout',
				'read_private_posts' => 'read_private_custom_layouts',
				'delete_post'        => 'delete_custom_layout',
			),
		);

		register_post_type( 'neve_custom_layouts', apply_filters( 'neve_custom_layouts_post_type_args', $args ) );
	}

	/**
	 * Remove conditional headers from the post list table in the admin.
	 *
	 * @param WP_Query $query the query.
	 *
	 * @return WP_Query
	 */
	public function remove_other_types( WP_Query $query ) {
		if ( ! function_exists( 'get_current_screen' ) ) {
			return $query;
		}
		$screen = get_current_screen();

		if ( ! $screen || $screen->id !== $this->cpt_screen_id ) {
			return $query;
		}

		$query->query_vars['meta_query']['relation'] = 'AND';
		$query->query_vars['meta_query'][]           = [
			'key'     => 'header-layout',
			'compare' => 'NOT EXISTS',
		];

		$query->query_vars['meta_query'][] = [
			'relation' => 'OR',
			[
				'key'     => 'cs-layout',
				'value'   => false,
				'compare' => '=',
			],
			[
				'key'     => 'cs-layout',
				'compare' => 'NOT EXISTS',
			],
		];

		return $query;
	}

	/**
	 * Filter the count.
	 *
	 * @param object|array $count the count object.
	 *
	 * @return mixed
	 */
	public function recount_posts( $count ) {
		$post_type = get_post_type();
		if ( $post_type !== 'neve_custom_layouts' ) {
			return $count;
		}

		// TODO: Make sure to fix `Mine` here.
		if ( ! is_object( $count ) ) {
			return $count;
		}

		$count->publish = absint( $count->publish ) - count( self::get_conditional_headers() ) - count( self::get_custom_sidebars() );

		return $count;
	}

	/**
	 * Get the custom layouts.
	 *
	 * @return array
	 */
	public static function get_custom_layouts() {
		$posts = self::get_posts();

		return $posts['custom_layouts'];
	}

	/**
	 * Get the conditional headers.
	 *
	 * @return array
	 */
	public static function get_conditional_headers() {
		$posts = self::get_posts();

		return $posts['conditional_headers'];
	}

	/**
	 * Get the custom sidebars.
	 *
	 * @return array
	 */
	public static function get_custom_sidebars() {
		$posts = self::get_posts();

		if ( ! isset( $posts['custom_sidebars'] ) ) {
			delete_transient( 'custom_layouts_post_map_v3' );
			return [];
		}

		return $posts['custom_sidebars'];
	}

	/**
	 * Get all the custom layouts post in array under two keys
	 *
	 * [custom_layouts, conditional_headers]
	 *
	 * @return array
	 */
	public static function get_posts() {
		$cache = get_transient( 'custom_layouts_post_map_v3' );
		if ( ! empty( $cache ) ) {
			return $cache;
		}
		$query = new \WP_Query(
			array(
				'post_type'              => 'neve_custom_layouts',
				'posts_per_page'         => 100,
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'fields'                 => 'ids',
				'post_status'            => 'publish',
			)
		);
		$posts = [
			'custom_layouts'      => [],
			'conditional_headers' => [],
			'custom_sidebars'     => [],
		];
		if ( ! $query->have_posts() ) {
			return $posts;
		}
		foreach ( $query->posts as $pid ) {
			$is_header_layout = get_post_meta( $pid, 'header-layout', true );
			if ( $is_header_layout ) {

				$posts['conditional_headers'][ $pid ] = get_post_meta( $pid, 'theme-mods', true );
				continue;
			}

			$is_custom_sidebar = get_post_meta( $pid, 'cs-layout', true );
			if ( $is_custom_sidebar ) {

				$conditions                       = get_post_meta( $pid, 'custom-layout-conditional-logic', true );
				$title                            = get_the_title( $pid );
				$posts['custom_sidebars'][ $pid ] = [
					'title'      => $title,
					'conditions' => $conditions,
				];
				continue;
			}

			$layout = get_post_meta( $pid, 'custom-layout-options-layout', true );
			if ( ! ( $layout ) ) {
				continue;
			}

			$priority = self::get_priority( $pid );
			if ( $layout === 'hooks' ) {
				$layout = get_post_meta( $pid, 'custom-layout-options-hook', true );
			}
			if ( $layout === 'custom' ) {
				$layout = get_post_meta( $pid, 'custom-layout-specific-hook', true );
			}
			$posts['custom_layouts'][ $layout ][ $pid ] = $priority;
		}
		set_transient( 'custom_layouts_post_map_v3', $posts, 0 );

		return $posts;
	}
}
