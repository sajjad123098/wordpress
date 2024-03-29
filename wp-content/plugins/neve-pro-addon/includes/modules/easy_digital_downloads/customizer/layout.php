<?php
/**
 * Author:          Uriahs Victor
 * Created on:      15/09/2021 (d/m/y)
 *
 * @package Neve_Pro\Modules\Easy_Digital_Downloads
 */

namespace Neve_Pro\Modules\Easy_Digital_Downloads\Customizer;

use Neve\Customizer\Base_Customizer;
use Neve\Customizer\Types\Control;
use Neve\Customizer\Types\Section;

/**
 * Class Layout
 *
 * @package Neve_Pro\Modules\Easy_Digital_Downloads\Customizer
 */
class Layout extends Base_Customizer {

	/**
	 * Function that should be extended to add customizer controls.
	 *
	 * @return void
	 */
	public function add_controls() {
		$this->add_edd_sections();
		$this->add_archive_downloads_layout_controls();
	}

	/**
	 *  Check if ajax checkout button is in use.
	 * 
	 * @return bool 
	 */
	public function using_ajax_buy_btn() {
		return get_theme_mod( 'neve_edd_archive_buy_button_type', 'go-to-download' ) === 'ajax-add-to-cart';
	}

	/**
	 * Add customizer sections
	 */
	private function add_edd_sections() {

		$this->add_section(
			new Section(
				'neve_edd_general',
				array(
					'priority' => 10,
					'title'    => esc_html__( 'General', 'neve' ),
					'panel'    => 'neve_download',
				)
			)
		);

		$this->add_section(
			new Section(
				'neve_edd_archive',
				array(
					'priority' => 10,
					'title'    => esc_html__( 'Download Catalog', 'neve' ),
					'panel'    => 'neve_download',
				)
			)
		);

	}

	/**
	 * Add EDD archive layout controls.
	 */
	private function add_archive_downloads_layout_controls() {
	
		$this->add_control(
			new Control(
				'neve_edd_archive_download_grid',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'label'            => esc_html__( 'Download Grid', 'neve' ),
					'section'          => 'neve_edd_archive',
					'priority'         => 15,
					'class'            => 'grid-layout-accordion',
					'accordion'        => true,
					'controls_to_wrap' => 2,
				),
				'\Neve\Customizer\Controls\Heading'
			)
		);

		$this->add_control(
			new Control(
				'neve_edd_grid_columns',
				[
					'sanitize_callback' => 'neve_sanitize_range_value',
					'transport'         => $this->selective_refresh,
					'default'           => '{"desktop":3,"tablet":2,"mobile":1}',
				],
				[
					'label'                 => esc_html__( 'Columns', 'neve' ),
					'section'               => 'neve_edd_archive',
					'type'                  => 'neve_responsive_range_control',
					'input_attrs'           => [
						'max'        => 6,
						'defaultVal' => [
							'mobile'  => 1,
							'tablet'  => 2,
							'desktop' => 3,
						],
					],
					'priority'              => 20,
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar' => [
							'responsive' => true,
							'vars'       => '--grid-cols',
							'selector'   => '#nv-edd-download-archive-container #nv-edd-grid-container',
						],
					],
				],
				'\Neve\Customizer\Controls\React\Responsive_Range'
			)
		);

		$this->add_control(
			new Control(
				'neve_edd_grid_spacing',
				[
					'sanitize_callback' => 'neve_sanitize_range_value',
					'transport'         => $this->selective_refresh,
					'default'           => '{"desktop":40,"tablet":30,"mobile":20}',
				],
				[
					'label'                 => esc_html__( 'Grid Spacing', 'neve' ),
					'section'               => 'neve_edd_archive',
					'type'                  => 'neve_responsive_range_control',
					'input_attrs'           => [
						'max'        => 80,
						'units'      => [ 'px' ],
						'defaultVal' => [
							'mobile'  => 20,
							'tablet'  => 30,
							'desktop' => 40,
							'suffix'  => [
								'mobile'  => 'px',
								'tablet'  => 'px',
								'desktop' => 'px',
							],
						],
					],
					'priority'              => 25,
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar' => [
							'responsive' => true,
							'vars'       => '--grid-cols-spacing',
							'selector'   => '#nv-edd-download-archive-container #nv-edd-grid-container',
							'suffix'     => 'px',
						],
					],
				],
				'\Neve\Customizer\Controls\React\Responsive_Range'
			)
		);

		$this->add_control(
			new Control(
				'neve_edd_archive_buy_button_type',
				array(
					'default' => 'go-to-download',
				),
				array(
					'label'    => esc_html__( 'Buy Button Behavior', 'neve' ),
					'section'  => 'neve_edd_archive',
					'priority' => 40,
					'type'     => 'select',
					'choices'  => array(
						'go-to-download'   => __( 'Go to download page', 'neve' ),
						'ajax-add-to-cart' => __( 'Add to Cart', 'neve' ),
					),
				)
			)
		);

		$this->add_control(
			new Control(
				'neve_edd_ajax_buy_button_show_price',
				array(
					'sanitize_callback' => 'neve_sanitize_checkbox',
					'default'           => false,
				),
				array(
					'type'            => 'neve_toggle_control',
					'priority'        => 45,
					'section'         => 'neve_edd_archive',
					'label'           => esc_html__( 'Show Price in Buy Button', 'neve' ),
					'active_callback' => [ $this, 'using_ajax_buy_btn' ],
				)
			)
		);



	}

}
