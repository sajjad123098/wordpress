<?php
/**
 * Class that modify Memberships and Courses archives pages.
 *
 * @package Neve_Pro\Modules\LifterLMS_Booster\Views
 */

namespace Neve_Pro\Modules\LifterLMS_Booster\Views;

/**
 * Class Course_Membership
 *
 * @package Neve_Pro\Modules\LifterLMS_Booster\Views
 */
class Course_Membership {

	/**
	 * Init function.
	 */
	public function register_hooks() {
		add_filter( 'lifterlms_loop_columns', array( $this, 'lifterlms_desktop_columns' ) );
		add_filter( 'llms_get_loop_list_classes', array( $this, 'lifterlms_responsive_columns' ) );
		add_action( 'wp', array( $this, 'run' ) );
	}

	/**
	 * Run the module.
	 */
	public function run() {
		if ( is_admin() ) {
			return false;
		}
		$this->archives_pagination();
		$this->list_layout();
	}

	/**
	 * Detect if current page is a course page.
	 *
	 * @return bool
	 */
	public static function is_courses() {
		if ( ! is_singular() && ! is_courses() ) {
			return false;
		}
		global $post;
		return ( ! empty( $post ) && has_shortcode( $post->post_content, 'lifterlms_courses' ) ) || is_courses();
	}

	/**
	 * Detect if current page is a membership page.
	 *
	 * @return bool
	 */
	public static function is_memberships() {
		if ( ! is_singular() && ! is_memberships() ) {
			return false;
		}
		global $post;
		return ( ! empty( $post ) && has_shortcode( $post->post_content, 'lifterlms_memberships' ) ) || is_memberships();
	}

	/**
	 * Courses and Memberships Archives pagination
	 *
	 * @return bool
	 */
	private function archives_pagination() {

		add_action( 'lifterlms_before_loop_item', array( $this, 'wrap_lifter_card_media' ), 0 );
		add_action( 'lifterlms_before_loop_item_title', array( $this, 'wrap_close' ), 40 );
		add_action( 'lifterlms_before_loop_item_title', array( $this, 'wrap_lifter_card_content' ), 45 );
		add_action( 'lifterlms_after_loop_item', array( $this, 'wrap_close' ), 40 );
		add_filter( 'llms_get_template_part', array( $this, 'add_rel_next_prev' ), 10, 2 );

		$theme_mod = '';
		if ( self::is_courses() ) {
			$theme_mod = 'neve_course_pagination_type';
		}
		if ( self::is_memberships() ) {
			$theme_mod = 'neve_membership_pagination_type';
		}
		if ( empty( $theme_mod ) ) {
			return false;
		}
		$pagination_type = get_theme_mod( $theme_mod, 'number' );
		if ( 'number' === $pagination_type || neve_is_amp() ) {
			return false;
		}

		add_filter( 'llms_get_pagination_wrapper_classes', array( $this, 'add_pagination_class' ) );
		add_filter( 'neve_lifter_wrap_classes', array( $this, 'lifterlms_infinite_scroll' ) );
		add_action( 'lifterlms_after_loop', array( $this, 'load_more_courses_sentinel' ), 101 );

		return true;
	}

	/**
	 * Add class on pagination so we can hide it on infinite scroll.
	 *
	 * @param array $classes Navigation classes.
	 *
	 * @return array
	 */
	public function add_pagination_class( $classes ) {
		$classes[] = 'nv-infinite';
		return $classes;
	}

	/**
	 * Add rel next/prev.
	 *
	 * @param string $template Template.
	 * @param string $slug Slug.
	 *
	 * @return bool|string;
	 */
	public function add_rel_next_prev( $template, $slug ) {
		if ( 'loop/pagination' === $slug ) {
			ob_start();
			load_template( $template, false );
			$pagination = ob_get_clean();
			$pagination = str_replace(
				array( '<a class="prev', '<a class="next' ),
				array(
					'<a rel="prev" class="prev',
					'<a rel="next" class="next',
				),
				$pagination
			);

			echo wp_kses_post( $pagination );
			return false;
		}

		return $template;
	}

	/**
	 * List layout for courses / memberships.
	 */
	private function list_layout() {
		$theme_mod = '';
		if ( self::is_courses() ) {
			$theme_mod = 'neve_course_card_layout';
		}
		if ( self::is_memberships() ) {
			$theme_mod = 'neve_membership_card_layout';
		}
		if ( empty( $theme_mod ) ) {
			return;
		}
		$view = get_theme_mod( $theme_mod, 'grid' );
		if ( ! empty( $view ) && 'list' === $view ) {
			add_filter(
				'neve_lifter_wrap_classes',
				function ( $classes ) {
					return $classes . ' nv-lifter-list-view';
				}
			);
		}

	}

	/**
	 * Card media wrapper.
	 */
	public function wrap_lifter_card_media() {
		echo '<div class="nv-lifter-card-media-wrap">';
	}

	/**
	 * Close wrapper.
	 */
	public function wrap_close() {
		echo '</div>';
	}

	/**
	 * Card content wrapper.
	 */
	public function wrap_lifter_card_content() {
		echo '<div class="nv-lifter-card-content-wrap">';
	}


	/**
	 * Remove pagination when fetching posts.
	 *
	 * @param string $template Template.
	 * @param string $slug Slug.
	 *
	 * @return bool|string;
	 */
	public function remove_pagination( $template, $slug ) {
		if ( 'loop/pagination' === $slug ) {
			return false;
		}

		return $template;
	}

	/**
	 * Add infinite scroll on LifterLMS container.
	 *
	 * @param string $classes Container classes.
	 *
	 * @return string
	 */
	public function lifterlms_infinite_scroll( $classes ) {
		return $classes . ' nv-infinite-scroll';
	}

	/**
	 * Add a sentinel to know when the request should happen.
	 */
	public function load_more_courses_sentinel() {
		global $wp_current_filter;
		if ( 'lifterlms_archive_description' === $wp_current_filter[0] ) {
			return false;
		}

		$sentinel_name = '';
		if ( self::is_courses() ) {
			$sentinel_name = 'courses';
		}
		if ( self::is_memberships() ) {
			$sentinel_name = 'memberships';
		}
		if ( empty( $sentinel_name ) ) {
			return false;
		}
		echo '<div class="lifter-load-more-posts load-more-' . esc_attr( $sentinel_name ) . '"><span class="nv-loader" style="display: none;"></span><span class="infinite-scroll-trigger"></span></div>';

		return true;
	}

	/**
	 * Number of columns on desktop.
	 *
	 * @param int $default Default number of columns.
	 *
	 * @return int
	 */
	public function lifterlms_desktop_columns( $default ) {
		$theme_mod = '';
		if ( self::is_courses() ) {
			$theme_mod = 'neve_courses_per_row';
		}
		if ( self::is_memberships() ) {
			$theme_mod = 'neve_memberships_per_row';
		}
		if ( empty( $theme_mod ) ) {
			return $default;
		}
		$columns = get_theme_mod(
			$theme_mod,
			wp_json_encode(
				array(
					'desktop' => 3,
					'tablet'  => 2,
					'mobile'  => 1,
				)
			)
		);
		$columns = json_decode( $columns, true );
		if ( ! empty( $columns ) && ! empty( $columns['desktop'] ) ) {
			return $columns['desktop'];
		}

		return $default;
	}

	/**
	 * Number of columns on responsive.
	 *
	 * @param array $classes Array of classes.
	 *
	 * @return array
	 */
	public function lifterlms_responsive_columns( $classes ) {
		$theme_mod = '';
		if ( self::is_courses() ) {
			$theme_mod = 'neve_courses_per_row';
		}
		if ( self::is_memberships() ) {
			$theme_mod = 'neve_memberships_per_row';
		}
		if ( empty( $theme_mod ) ) {
			return $classes;
		}

		$columns = get_theme_mod(
			$theme_mod,
			wp_json_encode(
				array(
					'desktop' => 3,
					'tablet'  => 2,
					'mobile'  => 1,
				)
			)
		);
		$columns = json_decode( $columns, true );
		if ( ! empty( $columns ) ) {
			if ( ! empty( $columns['tablet'] ) ) {
				$classes[] = 'tablet-columns-' . $columns['tablet'];
			}
			if ( ! empty( $columns['mobile'] ) ) {
				$classes[] = 'mobile-columns-' . $columns['mobile'];
			}
		}

		// Fix the shortcode for memberships not having the llms-membership-list
		global $post;
		if ( is_singular() && has_shortcode( $post->post_content, 'lifterlms_memberships' ) ) {
			$classes[] = 'llms-membership-list';
		}

		return $classes;
	}

}
