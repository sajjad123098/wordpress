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
 * Class Typography
 *
 * @package Neve_Pro\Modules\Easy_Digital_Downloads\Customizer
 */
class Typography extends Base_Customizer {

	/**
	 * Function that should be extended to add customizer controls.
	 *
	 * @return void
	 */
	public function add_controls() {
		$this->add_edd_sections();
		$this->add_archive_title_controls();
		$this->add_archive_meta_controls();

		$this->add_single_title_controls();
		$this->add_single_meta_controls();
	}

	/**
	 * Add customizer sections
	 */
	private function add_edd_sections() {

		$this->add_section(
			new Section(
				'neve_edd_typography',
				array(
					'priority' => 60,
					'title'    => esc_html__( 'Easy Digital Downloads', 'neve' ),
					'panel'    => 'neve_typography',
				)
			)
		);

	}

	/**
	 * Add EDD archive title typography controls.
	 */
	private function add_archive_title_controls() {

		$this->add_control(
			new Control(
				'neve_edd_archive_title_accordion',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'label'            => esc_html__( 'Download Title', 'neve' ),
					'category_label'   => __( 'Download Archive', 'neve' ),
					'section'          => 'neve_edd_typography',
					'priority'         => 5,
					'class'            => 'archive-download-title-accordion',
					'accordion'        => true,
					'controls_to_wrap' => 1,
				),
				'Neve\Customizer\Controls\Heading'
			)
		);

		$this->add_control(
			new Control(
				'neve_edd_archive_title_typography',
				[
					'transport' => $this->selective_refresh,
				],
				[
					'priority'              => 5,
					'section'               => 'neve_edd_typography',
					'type'                  => 'neve_typeface_control',
					'live_refresh_selector' => true,
					'refresh_on_reset'      => true,
					'live_refresh_css_prop' => [
						'cssVar' => [
							'vars'     => [
								'--texttransform' => 'textTransform',
								'--fontweight'    => 'fontWeight',
								'--fontsize'      => [
									'key'        => 'fontSize',
									'responsive' => true,
								],
								'--lineheight'    => [
									'key'        => 'lineHeight',
									'responsive' => true,
								],
								'--letterspacing' => [
									'key'        => 'letterSpacing',
									'suffix'     => 'px',
									'responsive' => true,
								],
							],
							'selector' => '.nv-edd #nv-edd-download-archive-container .nv-edd-download-title',
						],
					],
					'input_attrs'           => array(
						'default_is_empty'       => true,
						'size_units'             => [ 'em', 'px', 'rem' ],
						'weight_default'         => 'none',
						'size_default'           => array(
							'suffix'  => array(
								'mobile'  => 'px',
								'tablet'  => 'px',
								'desktop' => 'px',
							),
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '',
						),
						'line_height_default'    => array(
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '',
						),
						'letter_spacing_default' => array(
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '',
						),
					),
				],
				'\Neve\Customizer\Controls\React\Typography'
			)
		);

	}

	/**
	 * Add EDD archive meta typography controls.
	 */
	private function add_archive_meta_controls() {

		$this->add_control(
			new Control(
				'neve_edd_archive_meta_accordion',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'label'            => esc_html__( 'Download Meta', 'neve' ),
					'section'          => 'neve_edd_typography',
					'priority'         => 10,
					'class'            => 'archive-download-meta-accordion',
					'accordion'        => true,
					'controls_to_wrap' => 1,
				),
				'Neve\Customizer\Controls\Heading'
			)
		);

		$this->add_control(
			new Control(
				'neve_edd_archive_meta_typography',
				[
					'transport' => $this->selective_refresh,
				],
				[
					'priority'              => 10,
					'section'               => 'neve_edd_typography',
					'type'                  => 'neve_typeface_control',
					'live_refresh_selector' => true,
					'refresh_on_reset'      => true,
					'live_refresh_css_prop' => [
						'cssVar' => [
							'vars'     => [
								'--texttransform' => 'textTransform',
								'--fontweight'    => 'fontWeight',
								'--fontsize'      => [
									'key'        => 'fontSize',
									'responsive' => true,
								],
								'--lineheight'    => [
									'key'        => 'lineHeight',
									'responsive' => true,
								],
								'--letterspacing' => [
									'key'        => 'letterSpacing',
									'suffix'     => 'px',
									'responsive' => true,
								],
							],
							'selector' => '#nv-edd-download-archive-container .nv-edd-download-meta',
						],
					],
					'input_attrs'           => array(
						'default_is_empty'       => true,
						'size_units'             => [ 'em', 'px', 'rem' ],
						'weight_default'         => 'none',
						'size_default'           => array(
							'suffix'  => array(
								'mobile'  => 'px',
								'tablet'  => 'px',
								'desktop' => 'px',
							),
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '',
						),
						'line_height_default'    => array(
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '',
						),
						'letter_spacing_default' => array(
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '',
						),
					),
				],
				'\Neve\Customizer\Controls\React\Typography'
			)
		);


	}

	/**
	 * Add EDD single download title typography controls.
	 */
	private function add_single_title_controls() {

		$this->add_control(
			new Control(
				'neve_edd_single_title_accordion',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'label'            => esc_html__( 'Download Title', 'neve' ),
					'category_label'   => __( 'Single Download', 'neve' ),
					'section'          => 'neve_edd_typography',
					'priority'         => 15,
					'class'            => 'single-download-title-accordion',
					'accordion'        => true,
					'controls_to_wrap' => 1,
				),
				'Neve\Customizer\Controls\Heading'
			)
		);

		$this->add_control(
			new Control(
				'neve_edd_single_title_typography',
				[
					'transport' => $this->selective_refresh,
				],
				[
					'priority'              => 15,
					'section'               => 'neve_edd_typography',
					'type'                  => 'neve_typeface_control',
					'live_refresh_selector' => true,
					'refresh_on_reset'      => true,
					'live_refresh_css_prop' => [
						'cssVar' => [
							'vars'     => [
								'--texttransform' => 'textTransform',
								'--fontweight'    => 'fontWeight',
								'--fontsize'      => [
									'key'        => 'fontSize',
									'responsive' => true,
								],
								'--lineheight'    => [
									'key'        => 'lineHeight',
									'responsive' => true,
								],
								'--letterspacing' => [
									'key'        => 'letterSpacing',
									'suffix'     => 'px',
									'responsive' => true,
								],
							],
							'selector' => '#nv-single-download-container .nv-page-title h1',
						],
					],
					'input_attrs'           => array(
						'default_is_empty'       => true,
						'size_units'             => [ 'em', 'px', 'rem' ],
						'weight_default'         => 'none',
						'size_default'           => array(
							'suffix'  => array(
								'mobile'  => 'px',
								'tablet'  => 'px',
								'desktop' => 'px',
							),
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '',
						),
						'line_height_default'    => array(
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '',
						),
						'letter_spacing_default' => array(
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '',
						),
					),
				],
				'\Neve\Customizer\Controls\React\Typography'
			)
		);

	}

	/**
	 * Add EDD single download meta typography controls.
	 */
	private function add_single_meta_controls() {

		$this->add_control(
			new Control(
				'neve_edd_single_meta_accordion',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'label'            => esc_html__( 'Download Meta', 'neve' ),
					'section'          => 'neve_edd_typography',
					'priority'         => 20,
					'class'            => 'single-download-meta-accordion',
					'accordion'        => true,
					'controls_to_wrap' => 1,
				),
				'Neve\Customizer\Controls\Heading'
			)
		);

		$this->add_control(
			new Control(
				'neve_edd_single_meta_typography',
				[
					'transport' => $this->selective_refresh,
				],
				[
					'priority'              => 20,
					'section'               => 'neve_edd_typography',
					'type'                  => 'neve_typeface_control',
					'live_refresh_selector' => true,
					'refresh_on_reset'      => true,
					'live_refresh_css_prop' => [
						'cssVar' => [
							'vars'     => [
								'--texttransform' => 'textTransform',
								'--fontweight'    => 'fontWeight',
								'--fontsize'      => [
									'key'        => 'fontSize',
									'responsive' => true,
								],
								'--lineheight'    => [
									'key'        => 'lineHeight',
									'responsive' => true,
								],
								'--letterspacing' => [
									'key'        => 'letterSpacing',
									'suffix'     => 'px',
									'responsive' => true,
								],
							],
							'selector' => '#nv-single-download-container .nv-edd-single-download-meta',
						],
					],
					'input_attrs'           => array(
						'default_is_empty'       => true,
						'size_units'             => [ 'em', 'px', 'rem' ],
						'weight_default'         => 'none',
						'size_default'           => array(
							'suffix'  => array(
								'mobile'  => 'px',
								'tablet'  => 'px',
								'desktop' => 'px',
							),
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '',
						),
						'line_height_default'    => array(
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '',
						),
						'letter_spacing_default' => array(
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '',
						),
					),
				],
				'\Neve\Customizer\Controls\React\Typography'
			)
		);

	}

}
