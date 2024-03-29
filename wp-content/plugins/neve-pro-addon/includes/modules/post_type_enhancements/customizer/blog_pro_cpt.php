<?php
/**
 * Handles the layout customizer options for a custom post type archive when Blog Pro is enabled.
 *
 * Author:      Bogdan Preda <bogdan.preda@themeisle.com>
 * Created on:  15-12-{2021}
 *
 * @package Neve Pro Addon
 */

namespace Neve_Pro\Modules\Post_Type_Enhancements\Customizer;

use HFG\Traits\Core;
use Neve\Core\Settings\Mods;
use Neve\Customizer\Base_Customizer;
use Neve\Customizer\Types\Control;
use Neve_Pro\Core\Loader;
use Neve_Pro\Modules\Blog_Pro\Module as BlogProModule;
use Neve_Pro\Modules\Post_Type_Enhancements\Model\CPT_Model;
use Neve_Pro\Traits\Sanitize_Functions;

/**
 * Class Blog_Pro_CPT
 *
 * @since 3.1.0
 * @package Neve Pro Addon
 */
class Blog_Pro_CPT extends Base_Customizer {
	use Core;
	use Sanitize_Functions;

	/**
	 * Holds the current model.
	 *
	 * @var CPT_Model $model
	 */
	private $model;

	/**
	 * Customizer section id.
	 *
	 * @var string
	 */
	private $section = 'neve_blog_archive_layout';

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
	 * Add controls for custom archive
	 *
	 * @since 3.1.0
	 */
	public function add_controls() {
		$this->section = 'neve_' . $this->model->get_archive_type() . '_layout';
		$this->add_blog_pro_layout_controls();
		$this->add_ordering_content_controls();
		$this->add_read_more_controls();

		if ( ! BlogProModule::has_single_compatibility() ) {
			$this->add_blog_pro_post_meta_controls();
		}
		add_action( 'customize_register', [ $this, 'adjust_headings' ], PHP_INT_MAX );
	}

	/**
	 * Adjust Headings.
	 */
	public function adjust_headings() {
		$this->change_customizer_object( 'control', 'neve_' . $this->model->get_archive_type() . '_layout_heading', 'controls_to_wrap', 21 );
		$this->change_customizer_object( 'control', 'neve_' . $this->model->get_archive_type() . '_ordering_content_heading', 'controls_to_wrap', 7 );
		if ( ! BlogProModule::has_single_compatibility() ) {
			$this->change_customizer_object( 'control', 'neve_' . $this->model->get_archive_type() . '_post_meta_heading', 'controls_to_wrap', 4 );
		}
	}

	/**
	 * Register Blog Pro layout controls.
	 *
	 * @since 3.1.0
	 * @return void
	 */
	private function add_blog_pro_layout_controls() {
		/**
		 * Grid Spacing Control
		 */
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_archive_type() . '_grid_spacing',
				[
					'sanitize_callback' => 'neve_sanitize_range_value',
					'transport'         => $this->selective_refresh,
					'default'           => Mods::get( 'neve_blog_grid_spacing', '{ "mobile": 30, "tablet": 30, "desktop": 30 }' ),
				],
				[
					'label'                 => esc_html__( 'Grid Spacing', 'neve' ),
					'section'               => $this->section,
					'type'                  => 'neve_responsive_range_control',
					'input_attrs'           => [
						'min'        => 0,
						'max'        => 300,
						'units'      => [ 'px' ],
						'defaultVal' => [
							'mobile'  => 30,
							'tablet'  => 30,
							'desktop' => 30,
							'suffix'  => [
								'mobile'  => 'px',
								'tablet'  => 'px',
								'desktop' => 'px',
							],
						],
					],
					'priority'              => 12,
					'active_callback'       => function () {
						return $this->model->is_custom_layout_archive_enabled() && ! $this->is_list_layout();
					},
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar'     => [
							'vars'       => '--gridspacing',
							'suffix'     => 'px',
							'responsive' => true,
							'selector'   => '.posts-wrapper',
						],
						'responsive' => true,
						'template'   =>
							'body .posts-wrapper > article.layout-covers,
                             body .posts-wrapper > article.layout-grid {
							    margin-bottom: {{value}}px;
							    padding-right: calc({{value}}px/2);
							    padding-left: calc({{value}}px/2);
					    	}',
					],
				],
				'\Neve\Customizer\Controls\React\Responsive_Range'
			)
		);

		/**
		 * List Spacing Control
		 */
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_archive_type() . '_list_spacing',
				[
					'sanitize_callback' => 'neve_sanitize_range_value',
					'transport'         => $this->selective_refresh,
					'default'           => Mods::get( 'neve_blog_list_spacing', '{ "mobile": 60, "tablet": 60, "desktop": 60 }' ),
				],
				[
					'label'                 => esc_html__( 'List Spacing', 'neve' ),
					'section'               => $this->section,
					'type'                  => 'neve_responsive_range_control',
					'input_attrs'           => [
						'min'        => 0,
						'max'        => 300,
						'units'      => [ 'px' ],
						'defaultVal' => [
							'mobile'  => 60,
							'tablet'  => 60,
							'desktop' => 60,
							'suffix'  => [
								'mobile'  => 'px',
								'tablet'  => 'px',
								'desktop' => 'px',
							],
						],
					],
					'priority'              => 12,
					'active_callback'       => function () {
						return $this->model->is_custom_layout_archive_enabled() && $this->is_list_layout();
					},
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar'     => [
							'vars'       => '--spacing',
							'suffix'     => 'px',
							'responsive' => true,
							'selector'   => '.posts-wrapper',
						],
						'responsive' => true,
						'template'   =>
							'body .posts-wrapper .nv-non-grid-article {
							    margin-bottom: {{value}}px;
					    	}',
					],
				],
				'\Neve\Customizer\Controls\React\Responsive_Range'
			)
		);

		/**
		 * Cover Min Height Control
		 */
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_archive_type() . '_covers_min_height',
				[
					'sanitize_callback' => 'neve_sanitize_range_value',
					'transport'         => $this->selective_refresh,
					'default'           => Mods::get( 'neve_blog_covers_min_height', '{ "mobile": 350, "tablet": 350, "desktop": 350 }' ),
				],
				[
					'label'                 => esc_html__( 'Card Min Height', 'neve' ),
					'section'               => $this->section,
					'type'                  => 'neve_responsive_range_control',
					'input_attrs'           => [
						'min'        => 0,
						'max'        => 1000,
						'units'      => [ 'px' ],
						'defaultVal' => [
							'mobile'  => 350,
							'tablet'  => 350,
							'desktop' => 350,
							'suffix'  => [
								'mobile'  => 'px',
								'tablet'  => 'px',
								'desktop' => 'px',
							],
						],
					],
					'priority'              => 13,
					'active_callback'       => function () {
						return $this->model->is_custom_layout_archive_enabled() && $this->is_covers_layout();
					},
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar'     => [
							'vars'       => '--coverheight',
							'suffix'     => 'px',
							'responsive' => true,
							'selector'   => '.posts-wrapper',
						],
						'responsive' => true,
						'template'   =>
							'body .cover-post .inner {
							    min-height: {{value}}px;
					    	}',
					],
				],
				'\Neve\Customizer\Controls\React\Responsive_Range'
			)
		);

		/**
		 * Items Border Radius Control
		 */
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_archive_type() . '_items_border_radius',
				[
					'sanitize_callback' => 'neve_sanitize_range_value',
					'transport'         => $this->selective_refresh,
					'default'           => Mods::get( 'neve_blog_items_border_radius', 0 ),
				],
				[
					'label'                 => esc_html__( 'Border Radius', 'neve' ),
					'section'               => $this->section,
					'type'                  => 'neve_range_control',
					'input_attrs'           => [
						'min'        => 0,
						'max'        => 100,
						'defaultVal' => 0,
					],
					'priority'              => 13,
					'active_callback'       => function () {
						return $this->model->is_custom_layout_archive_enabled() && ! $this->is_list_layout();
					},
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar'   => [
							'vars'     => '--borderradius',
							'suffix'   => 'px',
							'fallback' => '0',
							'selector' => '.posts-wrapper',
						],
						'fallback' => 0,
						'template' =>
							'body .cover-post, body .layout-grid .article-content-col .content {
							    border-radius: {{value}}px;
					    	}',
					],
				],
				'\Neve\Customizer\Controls\React\Range'
			)
		);

		/**
		 * Covers Overlay Color Control
		 */
		$default_covers_overlay_color = Mods::get( 'neve_blog_covers_overlay_color', 'rgba(0,0,0,0.75)' );
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_archive_type() . '_covers_overlay_color',
				[
					'sanitize_callback' => 'neve_sanitize_colors',
					'default'           => $default_covers_overlay_color,
					'transport'         => $this->selective_refresh,
				],
				[
					'label'                 => esc_html__( 'Overlay Color', 'neve' ),
					'section'               => $this->section,
					'priority'              => 14,
					'active_callback'       => function () {
						return $this->model->is_custom_layout_archive_enabled() && $this->is_covers_layout();
					},
					'default'               => $default_covers_overlay_color,
					'input_attrs'           => [
						'allow_gradient' => Loader::has_compatibility( 'gradient_picker' ),
					],
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar'   => [
							'vars'     => '--overlay',
							'selector' => '.posts-wrapper, .nv-ft-post',
						],
						'template' =>
							'body .cover-post:after {
							background: {{value}};
						}',
					],
				],
				'Neve\Customizer\Controls\React\Color'
			)
		);

		/**
		 * Content Padding Control
		 */
		$content_padding_default_base = [
			'mobile'       => [
				'top'    => '',
				'right'  => '',
				'bottom' => '',
				'left'   => '',
			],
			'tablet'       => [
				'top'    => '',
				'right'  => '',
				'bottom' => '',
				'left'   => '',
			],
			'desktop'      => [
				'top'    => '',
				'right'  => '',
				'bottom' => '',
				'left'   => '',
			],
			'mobile-unit'  => 'px',
			'tablet-unit'  => 'px',
			'desktop-unit' => 'px',
		];
		$default_content_padding      = Mods::get( 'neve_blog_content_padding', $content_padding_default_base );
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_archive_type() . '_content_padding',
				[
					'sanitize_callback' => [ $this, 'sanitize_spacing_array' ],
					'default'           => $default_content_padding,
				],
				[
					'label'                 => esc_html__( 'Content Padding', 'neve' ),
					'section'               => $this->section,
					'priority'              => 15,
					'active_callback'       => function () {
						return $this->model->is_custom_layout_archive_enabled();
					},
					'input_attrs'           => [
						'units' => [ 'px', 'em', 'rem' ],
						'min'   => 0,
					],
					'default'               => $default_content_padding,
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'responsive'  => true,
						'directional' => true,
						'template'    =>
							'body .cover-post .inner, 
						 body .nv-non-grid-article .content .non-grid-content,
						 body .nv-non-grid-article .content .non-grid-content.alternative-layout-content {
							padding-top: {{value.top}};
							padding-right: {{value.right}};
							padding-bottom: {{value.bottom}};
							padding-left: {{value.left}};
						}
						body .layout-grid .article-content-col .content {
						    padding-top: {{value.top}};
							padding-right: {{value.right}};
							padding-bottom: {{value.bottom}};
							padding-left: {{value.left}};
						}
						body .layout-grid .article-content-col .nv-post-thumbnail-wrap{
							margin-right: -{{value.right}};
							margin-left: -{{value.left}};
						}
						',
					],
				],
				'\Neve\Customizer\Controls\React\Spacing'
			)
		);

		/**
		 * Image style effect.
		 */
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_archive_type() . '_image_hover',
				[
					'default'           => Mods::get( 'neve_blog_image_hover', 'none' ),
					'sanitize_callback' => array( $this, 'sanitize_image_hover' ),
				],
				[
					'label'           => esc_html__( 'Image style', 'neve' ),
					'section'         => $this->section,
					'priority'        => 15,
					'active_callback' => function () {
						if ( ! $this->model->is_custom_layout_archive_enabled() ) {
							return false;
						}
						if ( $this->is_list_layout() ) {
							return Mods::get( 'neve_' . $this->model->get_archive_type() . '_list_image_position', Mods::get( 'neve_blog_list_image_position', 'left' ) ) !== 'no';
						}
						return true;
					},
					'description'     => __( 'Select a hover effect for the post images.', 'neve' ),
					'type'            => 'select',
					'choices'         => array(
						'none'      => esc_html__( 'None', 'neve' ),
						'zoom'      => esc_html__( 'Zoom', 'neve' ),
						'next'      => esc_html__( 'Next Image', 'neve' ),
						'swipe'     => esc_html__( 'Swipe Next Image', 'neve' ),
						'blur'      => esc_html__( 'Blur', 'neve' ),
						'fadein'    => esc_html__( 'Fade In', 'neve' ),
						'fadeout'   => esc_html__( 'Fade Out', 'neve' ),
						'glow'      => esc_html__( 'Glow', 'neve' ),
						'colorize'  => esc_html__( 'Colorize', 'neve' ),
						'grayscale' => esc_html__( 'Grayscale', 'neve' ),
					),
				]
			)
		);

		/**
		 * Show on Hover Control
		 */
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_archive_type() . '_show_on_hover',
				[
					'sanitize_callback' => 'neve_sanitize_checkbox',
					'default'           => Mods::get( 'neve_blog_show_on_hover', false ),
				],
				[
					'type'            => 'neve_toggle_control',
					'priority'        => 36,
					'section'         => $this->section,
					'label'           => esc_html__( 'Show Content Only On Hover', 'neve' ),
					'active_callback' => function () {
						return $this->model->is_custom_layout_archive_enabled() && $this->is_covers_layout();
					},
				]
			)
		);

		/**
		 * List Image Position Control
		 */
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_archive_type() . '_list_image_position',
				[
					'sanitize_callback' => function ( $value ) {
						if ( ! in_array( $value, [ 'left', 'no', 'right' ], true ) ) {
							return 'left';
						}

						return $value;
					},
					'default'           => Mods::get( 'neve_blog_list_image_position', 'left' ),
				],
				[
					'label'           => esc_html__( 'Image Position', 'neve' ),
					'section'         => $this->section,
					'choices'         => [
						'left'  => [
							'tooltip' => __( 'Left', 'neve' ),
							'icon'    => 'align-pull-left',
						],
						'no'    => [
							'tooltip' => __( 'No image', 'neve' ),
							'icon'    => 'menu-alt',
						],
						'right' => [
							'tooltip' => __( 'Right', 'neve' ),
							'icon'    => 'align-pull-right',
						],
					],
					'show_labels'     => true,
					'priority'        => 14,
					'active_callback' => function () {
						return $this->model->is_custom_layout_archive_enabled() && $this->is_list_layout();
					},
				],
				'\Neve\Customizer\Controls\React\Radio_Buttons'
			)
		);

		/**
		 * List Image Width Control
		 */
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_archive_type() . '_list_image_width',
				[
					'sanitize_callback' => 'absint',
					'default'           => Mods::get( 'neve_blog_list_image_width', 35 ),
				],
				[
					'label'                 => esc_html__( 'Image Width', 'neve' ),
					'section'               => $this->section,
					'type'                  => 'neve_range_control',
					'input_attrs'           => [
						'min'        => 0,
						'max'        => 100,
						'units'      => [ '%' ],
						'defaultVal' => 35,
					],
					'priority'              => 15,
					'active_callback'       => function () {
						return $this->model->is_custom_layout_archive_enabled() &&
							$this->is_list_layout() &&
							Mods::get( 'neve_' . $this->model->get_archive_type() . '_list_image_position', 'left' ) !== 'no';
					},
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'template' =>
							'body .nv-non-grid-article.has-post-thumbnail .non-grid-content {
							    width: calc(100% - {{value}}%);
					    	}
					    	body .layout-default .nv-post-thumbnail-wrap, body .layout-alternative .nv-post-thumbnail-wrap {
							    width: {{value}}%;
							    max-width: {{value}}%;
					    	}',
					],

				],
				'\Neve\Customizer\Controls\React\Range'
			)
		);

		/**
		 * Post Separator Checkbox Control
		 */
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_archive_type() . '_separator',
				[
					'sanitize_callback' => 'neve_sanitize_checkbox',
					'default'           => Mods::get( 'neve_blog_separator', false ),
				],
				[
					'type'            => 'neve_toggle_control',
					'priority'        => 19,
					'section'         => $this->section,
					'label'           => esc_html__( 'Add Separator between posts', 'neve' ),
					'active_callback' => function () {
						return $this->model->is_custom_layout_archive_enabled() && ! $this->is_covers_layout();
					},
				]
			)
		);

		/**
		 * Separator Width Control
		 */
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_archive_type() . '_separator_width',
				[
					'sanitize_callback' => 'neve_sanitize_range_value',
					'transport'         => $this->selective_refresh,
					'default'           => Mods::get( 'neve_blog_separator_width', '{ "mobile": 1, "tablet": 1, "desktop": 1 }' ),
				],
				[
					'label'                 => esc_html__( 'Separator Weight', 'neve' ),
					'section'               => $this->section,
					'type'                  => 'neve_responsive_range_control',
					'input_attrs'           => [
						'min'        => 0,
						'max'        => 100,
						'step'       => 1,
						'units'      => [ 'px' ],
						'defaultVal' => [
							'mobile'  => 1,
							'tablet'  => 1,
							'desktop' => 1,
							'suffix'  => [
								'mobile'  => 'px',
								'tablet'  => 'px',
								'desktop' => 'px',
							],
						],
					],
					'priority'              => 21,
					'active_callback'       => function () {
						return $this->model->is_custom_layout_archive_enabled() &&
							! $this->is_covers_layout() &&
							Mods::get( 'neve_' . $this->model->get_archive_type() . '_separator' ) === true;
					},
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar'     => [
							'vars'       => '--borderwidth',
							'suffix'     => 'px',
							'responsive' => true,
							'selector'   => '.posts-wrapper',
						],
						'responsive' => true,
						'template'   =>
							'body .article-content-col .content {
							    border-width: {{value}}px;
						    }',
					],
				],
				'\Neve\Customizer\Controls\React\Responsive_Range'
			)
		);

		/**
		 * Separator Color Control
		 */
		$separator_color_default = 'var(--nv-light-bg)';
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_archive_type() . '_separator_color',
				[
					'sanitize_callback' => 'neve_sanitize_colors',
					'transport'         => $this->selective_refresh,
					'default'           => Mods::get( 'neve_blog_separator_color', $separator_color_default ),
				],
				[
					'label'                 => esc_html__( 'Separator Color', 'neve' ),
					'section'               => $this->section,
					'priority'              => 22,
					'active_callback'       => function () {
						return $this->model->is_custom_layout_archive_enabled() &&
							! $this->is_covers_layout() &&
							Mods::get( 'neve_' . $this->model->get_archive_type() . '_separator' ) === true;
					},
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar'   => [
							'vars'     => '--bordercolor',
							'selector' => '.posts-wrapper',
						],
						'template' =>
							'body .article-content-col .content {
							    border-color: {{value}};
						    }',
					],
				],
				'Neve\Customizer\Controls\React\Color'
			)
		);

		/**
		 * Card Style Toggle Control
		 */
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_archive_type() . '_enable_card_style',
				[
					'sanitize_callback' => 'neve_sanitize_checkbox',
					'default'           => Mods::get( 'neve_enable_card_style', false ),
				],
				[
					'type'            => 'neve_toggle_control',
					'priority'        => 36,
					'section'         => $this->section,
					'label'           => esc_html__( 'Enable Card Style', 'neve' ),
					'active_callback' => function () {
						return $this->model->is_custom_layout_archive_enabled() && $this->is_grid_layout();
					},
				]
			)
		);

		/**
		 * Grid Card Background Color Control
		 */
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_archive_type() . '_grid_card_bg_color',
				[
					'sanitize_callback' => 'neve_sanitize_colors',
					'default'           => Mods::get( 'neve_blog_grid_card_bg_color', '#333333' ),
					'transport'         => $this->selective_refresh,
				],
				[
					'label'                 => esc_html__( 'Card Background Color', 'neve' ),
					'section'               => $this->section,
					'priority'              => 37,
					'default'               => Mods::get( 'neve_blog_grid_card_bg_color', '#333333' ),
					'active_callback'       => function () {
						return $this->model->is_custom_layout_archive_enabled() &&
							$this->is_grid_layout() &&
							Mods::get( 'neve_' . $this->model->get_archive_type() . '_enable_card_style', false ) === true;
					},
					'input_attrs'           => [
						'allow_gradient' => Loader::has_compatibility( 'gradient_picker' ),
					],
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar'   => [
							'vars'     => '--bgcolor',
							'selector' => '.posts-wrapper',
						],
						'template' =>
							'.layout-grid .article-content-col .content {
							background: {{value}};
						}',
					],
				],
				'Neve\Customizer\Controls\React\Color'
			)
		);

		/**
		 * Grid Text Color Control
		 */
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_archive_type() . '_grid_text_color',
				[
					'sanitize_callback' => 'neve_sanitize_colors',
					'default'           => Mods::get( 'neve_blog_grid_text_color', '#ffffff' ),
					'transport'         => $this->selective_refresh,
				],
				[
					'label'                 => esc_html__( 'Text Color', 'neve' ),
					'section'               => $this->section,
					'priority'              => 38,
					'default'               => Mods::get( 'neve_blog_grid_text_color', '#ffffff' ),
					'active_callback'       => function () {
						return $this->model->is_custom_layout_archive_enabled() &&
							$this->is_grid_layout() &&
							Mods::get( 'neve_' . $this->model->get_archive_type() . '_enable_card_style', false ) === true;
					},
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar'   => [
							'vars'     => '--color',
							'selector' => '.posts-wrapper',
						],
						'template' =>
							'.layout-grid .article-content-col .content, .layout-grid .article-content-col .content a:not(.button), .layout-grid .article-content-col .content li {
							color: {{value}};
						}',
					],
				],
				'Neve\Customizer\Controls\React\Color'
			)
		);

		/**
		 * Card Shadow Control
		 */
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_archive_type() . '_card_shadow',
				[
					'sanitize_callback' => 'neve_sanitize_range_value',
					'default'           => Mods::get( 'neve_blog_card_shadow', 0 ),
				],
				[
					'label'                 => esc_html__( 'Card Box Shadow', 'neve' ),
					'section'               => $this->section,
					'type'                  => 'neve_range_control',
					'input_attrs'           => [
						'min'        => 0,
						'max'        => 5,
						'defaultVal' => 0,
					],
					'priority'              => 39,
					'active_callback'       => function () {
						return $this->model->is_custom_layout_archive_enabled() &&
							$this->is_grid_layout() &&
							Mods::get( 'neve_' . $this->model->get_archive_type() . '_enable_card_style', false ) === true;
					},
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'fallback' => 0,
						'template' =>
							'body .layout-grid .article-content-col .content {
							    box-shadow: 0 0 calc({{value}}px * 4) 0 rgba(0,0,0,calc(0.1 + 0.{{value}}));
					    	}',
					],
				],
				'\Neve\Customizer\Controls\React\Range'
			)
		);
	}

	/**
	 * Register Blog Pro ordering content controls.
	 *
	 * @since 3.1.0
	 * @return void
	 */
	private function add_ordering_content_controls() {
		/**
		 * Posts Order Control
		 */
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_archive_type() . '_posts_order',
				[
					'default'           => Mods::get( 'neve_posts_order', 'date_posted_desc' ),
					'sanitize_callback' => [ 'Neve_Pro\Modules\Blog_Pro\Customizer\Blog_Pro', 'sanitize_posts_sorting' ],
				],
				[
					'label'           => esc_html__( 'Order posts by', 'neve' ),
					'section'         => $this->section,
					'priority'        => 51,
					'active_callback' => function () {
						return $this->model->is_custom_layout_archive_enabled();
					},
					'type'            => 'select',
					'choices'         => [
						'date_posted_desc' => esc_html__( 'Date posted descending', 'neve' ),
						'date_posted_asc'  => esc_html__( 'Date posted ascending', 'neve' ),
						'date_updated'     => esc_html__( 'Date updated', 'neve' ),
					],
				]
			)
		);

		/**
		 * Content Alignment Control
		 */
		$align_choices = [
			'left'   => [
				'tooltip' => __( 'Left', 'neve' ),
				'icon'    => 'editor-alignleft',
			],
			'center' => [
				'tooltip' => __( 'Center', 'neve' ),
				'icon'    => 'editor-aligncenter',
			],
			'right'  => [
				'tooltip' => __( 'Right', 'neve' ),
				'icon'    => 'editor-alignright',
			],
		];
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_archive_type() . '_content_alignment',
				[
					'sanitize_callback' => function ( $value ) {
						if ( ! in_array( $value, [ 'left', 'center', 'right' ], true ) ) {
							return 'left';
						}

						return $value;
					},
					'default'           => 'left',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'                 => esc_html__( 'Content Alignment', 'neve' ),
					'section'               => $this->section,
					'choices'               => $align_choices,
					'show_labels'           => true,
					'priority'              => 56,
					'active_callback'       => function () {
						return $this->model->is_custom_layout_archive_enabled();
					},
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar'   => [
							'vars'     => '--alignment',
							'selector' => '.posts-wrapper',
						],
						'template' =>
							'body .cover-post .inner, 
                            body .nv-non-grid-article .content  .non-grid-content, 
							body .nv-non-grid-article .content .non-grid-content.alternative-layout-content,
                            body .article-content-col .content, 
                            body .article-content-col .content a, 
                            body .article-content-col .content li {
							    text-align: {{value}};
					    	}
					    	.layout-grid .nv-post-thumbnail-wrap a {
					    	    display: inline-block;
					    	}
					    	',
					],
				],
				'\Neve\Customizer\Controls\React\Radio_Buttons'
			)
		);

		/**
		 * Vertical Alignment Control
		 */
		$align_choices = [
			'flex-start' => [
				'tooltip' => __( 'Top', 'neve' ),
				'icon'    => 'verticalTop',
			],
			'center'     => [
				'tooltip' => __( 'Middle', 'neve' ),
				'icon'    => 'verticalMiddle',
			],
			'flex-end'   => [
				'tooltip' => __( 'Bottom', 'neve' ),
				'icon'    => 'verticalBottom',
			],
		];
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_archive_type() . '_content_vertical_alignment',
				[
					'sanitize_callback' => function ( $value ) {
						if ( ! in_array( $value, [ 'flex-start', 'center', 'flex-end' ], true ) ) {
							return 'flex-end';
						}

						return $value;
					},
					'transport'         => $this->selective_refresh,
					'default'           => 'bottom',
				],
				[
					'label'                 => esc_html__( 'Content Alignment', 'neve' ),
					'section'               => $this->section,
					'show_labels'           => true,
					'choices'               => $align_choices,
					'priority'              => 57,
					'active_callback'       => function () {
						return $this->model->is_custom_layout_archive_enabled() && $this->is_covers_layout();
					},
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar'   => [
							'vars'     => '--justify',
							'selector' => '.posts-wrapper',
						],
						'template' =>
							'body .cover-post .inner {
							    justify-content: {{value}};
					    	}',
					],
				],
				'\Neve\Customizer\Controls\React\Radio_Buttons'
			)
		);
	}

	/**
	 * Register Blog Pro read more controls.
	 *
	 * @since 3.1.0
	 * @return void
	 */
	private function add_read_more_controls() {
		/*
		 * Heading for Read More options Control
		 */
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_archive_type() . '_read_more_options',
				[
					'sanitize_callback' => 'sanitize_text_field',
				],
				[
					'label'            => esc_html__( 'Read More', 'neve' ),
					'section'          => $this->section,
					'priority'         => 85,
					'active_callback'  => function () {
						return $this->model->is_custom_layout_archive_enabled();
					},
					'class'            => 'blog-layout-read-more-accordion',
					'accordion'        => true,
					'expanded'         => false,
					'controls_to_wrap' => 2,
				],
				'Neve\Customizer\Controls\Heading'
			)
		);

		/*
		 * Read More Text Control
		 */
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_archive_type() . '_read_more_text',
				[
					'sanitize_callback' => 'sanitize_text_field',
					'default'           => esc_html__( 'Read More', 'neve' ) . ' &raquo;',
				],
				[
					'priority'        => 90,
					'active_callback' => function () {
						return $this->model->is_custom_layout_archive_enabled();
					},
					'section'         => $this->section,
					'label'           => esc_html__( 'Text', 'neve' ),
					'type'            => 'text',
				]
			)
		);

		/*
		 * Read More Style Control
		 */
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_archive_type() . '_read_more_style',
				[
					'default'           => 'text',
					'sanitize_callback' => [ $this, 'sanitize_read_more_style' ],
				],
				[
					'label'           => esc_html__( 'Style', 'neve' ),
					'section'         => $this->section,
					'priority'        => 95,
					'active_callback' => function () {
						return $this->model->is_custom_layout_archive_enabled();
					},
					'type'            => 'select',
					'choices'         => [
						'text'             => esc_html__( 'Text', 'neve' ),
						'primary_button'   => esc_html__( 'Primary Button', 'neve' ),
						'secondary_button' => esc_html__( 'Secondary Button', 'neve' ),
					],
				]
			)
		);
	}

	/**
	 * Register Blog Pro post meta controls
	 */
	private function add_blog_pro_post_meta_controls() {
		/**
		 * Metadata Separator Control
		 */
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_archive_type() . '_metadata_separator',
				[
					'sanitize_callback' => 'sanitize_text_field',
					'default'           => Mods::get( 'neve_metadata_separator', esc_html( '/' ) ),
				],
				[
					'priority'        => 77,
					'active_callback' => function () {
						return $this->model->is_custom_layout_archive_enabled();
					},
					'section'         => $this->section,
					'label'           => esc_html__( 'Separator', 'neve' ),
					'description'     => esc_html__( 'For special characters make sure to use Unicode. For example > can be displayed using \003E.', 'neve' ),
					'type'            => 'text',
				]
			)
		);

		/**
		 * Author Avatar Size
		 */
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_archive_type() . '_author_avatar_size',
				[
					'sanitize_callback' => 'neve_sanitize_range_value',
					'default'           => Mods::get(
						'neve_author_avatar_size',
						wp_json_encode(
							[
								'desktop' => 20,
								'tablet'  => 20,
								'mobile'  => 20,
							]
						)
					),
				],
				[
					'label'           => esc_html__( 'Avatar Size', 'neve' ),
					'section'         => $this->section,
					'units'           => [
						'px',
					],
					'input_attr'      => [
						'mobile'  => [
							'min'          => 20,
							'max'          => 50,
							'default'      => 20,
							'default_unit' => 'px',
						],
						'tablet'  => [
							'min'          => 20,
							'max'          => 50,
							'default'      => 20,
							'default_unit' => 'px',
						],
						'desktop' => [
							'min'          => 20,
							'max'          => 50,
							'default'      => 20,
							'default_unit' => 'px',
						],
					],
					'input_attrs'     => [
						'step'       => 1,
						'min'        => 20,
						'max'        => 50,
						'defaultVal' => [
							'mobile'  => 20,
							'tablet'  => 20,
							'desktop' => 20,
							'suffix'  => [
								'mobile'  => 'px',
								'tablet'  => 'px',
								'desktop' => 'px',
							],
						],
						'units'      => [ 'px' ],
					],
					'priority'        => 81,
					'active_callback' => function () {
						return $this->model->is_custom_layout_archive_enabled() && Mods::get( 'neve_' . $this->model->get_archive_type() . '_author_avatar', false );
					},
					'responsive'      => true,
				],
				'Neve\Customizer\Controls\React\Responsive_Range'
			)
		);
	}

	/**
	 * Checks if is list layout blog
	 *
	 * @since 3.1.0
	 * @return bool
	 */
	public function is_list_layout() {
		return Mods::get( $this->section, 'grid' ) === 'default';
	}

	/**
	 * Checks if is covers layout blog
	 *
	 * @since 3.1.0
	 * @return bool
	 */
	public function is_covers_layout() {
		return Mods::get( $this->section, 'grid' ) === 'covers';
	}

	/**
	 * Checks if is grid layout blog
	 *
	 * @since 3.1.0
	 * @return bool
	 */
	public function is_grid_layout() {
		return Mods::get( $this->section, 'grid' ) === 'grid';
	}

	/**
	 * Sanitize read more button style
	 *
	 * @since 3.1.0
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_read_more_style( $value ) {
		$allowed_values = [ 'text', 'primary_button', 'secondary_button' ];
		if ( ! in_array( $value, $allowed_values, true ) ) {
			return 'number';
		}

		return esc_html( $value );
	}
}
