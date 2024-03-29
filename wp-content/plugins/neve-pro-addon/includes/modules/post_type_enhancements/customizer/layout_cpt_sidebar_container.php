<?php
/**
 * Handles the sidebar and container for a custom post type.
 *
 * Author:      Bogdan Preda <bogdan.preda@themeisle.com>
 * Created on:  15-12-{2021}
 *
 * @package Neve Pro Addon
 */

namespace Neve_Pro\Modules\Post_Type_Enhancements\Customizer;

use Neve_Pro\Modules\Post_Type_Enhancements\Model\CPT_Model;

/**
 * Class Layout_CPT_Sidebar_Container
 *
 * @since   3.1.0
 * @package Neve Pro Addon
 */
class Layout_CPT_Sidebar_Container {

	/**
	 * Holds the current model.
	 *
	 * @var CPT_Model $model
	 */
	private $model;

	/**
	 * Constructor for the class.
	 *
	 * @since 3.1.0
	 * @param CPT_Model $cpt_model The Custom Post Type Model.
	 */
	public function __construct( $cpt_model ) {
		$this->model = $cpt_model;
		$this->init();
	}

	/**
	 * Initialize the module
	 *
	 * @since 3.1.0
	 * @return void
	 */
	final public function init() {
		add_filter( 'neve_container_style_filter', [ $this, 'add_to_container_styles' ] );
		add_filter( 'neve_sidebar_controls_filter', [ $this, 'add_to_sidebar_controls' ] );
		add_filter( 'neve_sidebar_setup_filter', [ $this, 'filter_sidebar_setup' ] );
		add_filter( 'neve_sidebar_layout_alignment_defaults', [ $this, 'sidebar_layout_alignment_defaults_filter' ] );
		add_filter( 'neve_sidebar_full_width_defaults', [ $this, 'sidebar_full_width_defaults_filter' ] );
	}

	/**
	 * Filters the container styles to allow for custom post type support.
	 *
	 * @since 3.1.0
	 * @param array $container_styles_controls The container styles controls array.
	 *
	 * @return array
	 */
	final public function add_to_container_styles( $container_styles_controls ) {
		if ( $this->model->has_archive() ) {
			$container_styles_controls[ 'neve_' . $this->model->get_type() . '_archive_container_style' ] = [
				'priority' => 40,
				/* translators: %s a custom post type singular name */
				'label'    => sprintf( esc_html__( 'Archive %s / Container Style', 'neve' ), $this->model->get_plural() ),
			];
		}

		$container_styles_controls[ 'neve_single_' . $this->model->get_type() . '_container_style' ] = [
			'priority' => 40,
			/* translators: %s a custom post type singular name */
			'label'    => sprintf( esc_html__( 'Single %s Container Style', 'neve' ), $this->model->get_singular() ),
		];

		return $container_styles_controls;
	}

	/**
	 * Filters the sidebar controls to add additional controls for supported post types.
	 *
	 * @since 3.1.0
	 * @param array $advanced_controls The advanced controls to filter.
	 *
	 * @return array
	 */
	final public function add_to_sidebar_controls( $advanced_controls ) {
		$new_control[ 'single_' . $this->model->get_type() ] = esc_html__( 'Single', 'neve' ) . ' ' . $this->model->get_singular();
		if ( $this->model->has_archive() ) {
			$new_control[ $this->model->get_type() . '_archive' ] = $this->model->get_plural() . ' / ' . esc_html__( 'Archive', 'neve' );
		}
		return $this->push_at_to_associative_array( $advanced_controls, 'single_post', $new_control );
	}

	/**
	 * Utility method to push before a specific key if we need to keep a specific order.
	 *
	 * @since 3.1.0
	 * @param array      $array The array to mutate.
	 * @param string|int $key The key after witch to insert.
	 * @param mixed      $new The value to insert.
	 *
	 * @return array
	 */
	private function push_at_to_associative_array( $array, $key, $new ) {
		$keys  = array_keys( $array );
		$index = array_search( $key, $keys, true );
		$pos   = false === $index ? count( $array ) : $index + 1;

		return array_slice( $array, 0, $pos, true ) + $new + array_slice( $array, $pos, count( $array ) - 1, true );
	}

	/**
	 * Filter the setup for the sidebar to allow for custom post types.
	 *
	 * @since 3.1.0
	 * @param array $sidebar_setup The sidebar setup.
	 *
	 * @return array
	 */
	final public function filter_sidebar_setup( $sidebar_setup ) {
		$current_post_type = get_post_type();
		if (
			$current_post_type === false ||
			in_array( $current_post_type, [ 'post', 'page' ] ) ||
			! in_array( $current_post_type, apply_filters( 'neve_post_type_supported_list', [], 'block_editor' ) )
		) {
			return $sidebar_setup;
		}

		if ( is_post_type_archive( $current_post_type ) ) {
			$sidebar_setup['theme_mod']     = 'neve_' . $current_post_type . '_archive_sidebar_layout';
			$sidebar_setup['content_width'] = 'neve_' . $current_post_type . '_archive_content_width';

			return $sidebar_setup;
		}

		if ( is_singular( $current_post_type ) ) {
			$sidebar_setup['theme_mod']     = 'neve_single_' . $current_post_type . '_sidebar_layout';
			$sidebar_setup['content_width'] = 'neve_single_' . $current_post_type . '_content_width';
		}

		return $sidebar_setup;
	}

	/**
	 * Filter allowed sidebar full layout defaults.
	 *
	 * @since 3.1.0
	 * @param array $full_width List of allowed full layout defaults.
	 *
	 * @return array
	 */
	final public function sidebar_layout_alignment_defaults_filter( $full_width ) {
		$full_width[] = 'neve_' . $this->model->get_type() . '_archive_sidebar_layout';
		return $full_width;
	}

	/**
	 * Filter allowed sidebar full widths.
	 *
	 * @since 3.1.0
	 * @param array $full_width List of allowed full widths defaults.
	 *
	 * @return array
	 */
	final public function sidebar_full_width_defaults_filter( $full_width ) {
		$full_width[] = 'neve_' . $this->model->get_type() . '_archive_content_width';

		return $full_width;
	}
}
