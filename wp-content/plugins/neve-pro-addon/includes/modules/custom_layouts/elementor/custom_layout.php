<?php
/**
 * Elementor Content Switcher Widget
 *
 * @package Neve_Pro\Modules\Elementor_Booster\Widgets
 */

namespace Neve_Pro\Modules\Custom_Layouts\Elementor;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Neve_Pro\Modules\Custom_Layouts\Admin\Builders\Loader;
use WP_Query;

/**
 * Class Content_Switcher
 *
 * @package Neve_Pro\Modules\Elementor_Booster\Widgets
 */
class Custom_Layout extends Widget_Base {

	/**
	 * Widget slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'neve_custom_layout';
	}

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 * @access public
	 */
	public function get_title() {
		return __( 'Custom Layout', 'neve' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string Widget icon.
	 * @access public
	 */
	public function get_icon() {
		return 'fa fa-edit';
	}

	/**
	 * Set the category of the widget.
	 *
	 * @return array
	 */
	public function get_categories() {
		return array( 'neve-elementor-widgets' );
	}

	/**
	 * Register widget controls
	 */
	protected function register_controls() {
		$this->register_content_controls();
	}

	/**
	 * Get widget keywords
	 *
	 * @return array
	 */
	public function get_keywords() {
		return [ 'neve', 'layout', 'custom' ];
	}

	/**
	 * Register content related controls
	 */
	protected function register_content_controls() {
		$this->start_controls_section(
			'custom_layout_settings',
			[
				'label' => __( 'Custom layout', 'neve' ),
			]
		);

		$custom_layouts = $this->get_custom_layouts();
		$this->add_control(
			'custom_layout',
			[
				'label'   => __( 'Select custom layout', 'neve' ),
				'type'    => Controls_Manager::SELECT,
				'options' => $custom_layouts,
				'default' => 'none',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Get custom layouts of type individual.
	 *
	 * @return array
	 */
	private function get_custom_layouts() {
		$choices = array( 'none' => __( 'None', 'neve' ) );
		$args    = array(
			'post_type'      => 'neve_custom_layouts',
			'posts_per_page' => 50,
			'meta_query'     => array( //phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'   => 'custom-layout-options-layout',
					'value' => 'individual',
				),
			),
		);
		$query   = new WP_Query( $args );
		if ( $query->have_posts() ) {
			// @phpstan-ignore-next-line - impure WP function.
			while ( $query->have_posts() ) :
				$query->the_post();

				$custom_layout_id             = get_the_ID();
				$custom_layout_title          = get_the_title();
				$choices[ $custom_layout_id ] = $custom_layout_title;

			endwhile; // @phpstan-ignore-next-line - code is reachable.
			wp_reset_postdata();
		}

		return $choices;
	}


	/**
	 * Renders the widget
	 *
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$template_to_display = $settings['custom_layout'];
		if ( $template_to_display === 'none' ) {
			return;
		}
		$post = get_post( $template_to_display );
		if ( empty( $post ) ) {
			return;
		}
		$loader = new Loader( 'Neve_Pro\Modules\Custom_Layouts\Admin\Builders\\', false );
		$loader->render_specific_markup( $template_to_display );
	}
}
