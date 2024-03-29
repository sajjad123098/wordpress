<?php
/**
 * Author:          Stefan Cotitosu <stefan@themeisle.com>
 * Created on:      2019-02-27
 *
 * @package Neve Pro
 */

namespace Neve_Pro\Modules\Blog_Pro\Customizer;

use HFG\Traits\Core;
use Neve\Core\Settings\Mods;
use Neve\Customizer\Base_Customizer;
use Neve\Customizer\Types\Control;
use Neve_Pro\Core\Loader;
use Neve_Pro\Modules\Blog_Pro\Module;
use Neve_Pro\Traits\Sanitize_Functions;

/**
 * Class Blog_Pro
 *
 * @package Neve_Pro\Modules\Blog_Pro\Customizer
 */
class Blog_Pro extends Base_Customizer {
	use Core;
	use Sanitize_Functions;

	/**
	 * The minimum value of some customizer controls is 0 to able to allow usability relative to CSS units.
	 * That can be removed after the https://github.com/Codeinwp/neve/issues/3609 issue is handled.
	 *
	 * That is defined here against the usage of old Neve versions, Base_Customizer class of the stable Neve version already has the RELATIVE_CSS_UNIT_SUPPORTED_MIN_VALUE constant.
	 */
	const RELATIVE_CSS_UNIT_SUPPORTED_MIN_VALUE = 0;

	/**
	 * Holds the section name.
	 *
	 * @var string $section
	 */
	private $section = 'neve_blog_archive_layout';

	/**
	 * Base initialization
	 */
	public function init() {

		parent::init();
		add_filter( 'neve_single_post_elements', array( $this, 'filter_single_post_elements' ) );
	}

	/**
	 * Add customizer section and controls
	 */
	public function add_controls() {

		$this->add_blog_layout_controls();
		$this->add_ordering_content_controls();
		$this->add_read_more_controls();
		$this->add_featured_post_controls();

		if ( ! Module::has_single_compatibility() ) {
			$this->add_post_meta_controls();
		}

		add_action( 'customize_register', [ $this, 'adjust_headings' ], PHP_INT_MAX );
		if ( Loader::has_compatibility( 'meta_custom_fields' ) ) {
			add_action( 'customize_register', [ $this, 'add_meta_custom_fields' ], PHP_INT_MAX );
		}
	}

	/**
	 * Adjust Headings.
	 */
	public function adjust_headings() {
		$this->change_customizer_object( 'control', 'neve_blog_layout_heading', 'controls_to_wrap', 23 );
		$this->change_customizer_object( 'control', 'neve_blog_ordering_content_heading', 'controls_to_wrap', 7 );
		if ( Loader::has_compatibility( 'featured_post' ) ) {
			$this->change_customizer_object( 'control', 'neve_featured_post_heading', 'controls_to_wrap', 8 );
		}
		if ( ! Module::has_single_compatibility() ) {
			$this->change_customizer_object( 'control', 'neve_blog_post_meta_heading', 'controls_to_wrap', 4 );
		}
	}

	/**
	 * Allow meta controls defined in lite to add more items.
	 */
	public function add_meta_custom_fields() {
		$this->change_customizer_object( 'control', 'neve_blog_post_meta_fields', 'allow_new_fields', 'yes' );
		$this->change_customizer_object( 'control', 'neve_blog_post_meta_fields', 'fields', $this->get_blocked_elements_fields() );
		$this->change_customizer_object( 'control', 'neve_blog_post_meta_fields', 'new_item_fields', $this->get_new_elements_fields() );

		$this->change_customizer_object( 'control', 'neve_single_post_meta_fields', 'allow_new_fields', 'yes' );
		$this->change_customizer_object( 'control', 'neve_single_post_meta_fields', 'fields', $this->get_blocked_elements_fields() );
		$this->change_customizer_object( 'control', 'neve_single_post_meta_fields', 'new_item_fields', $this->get_new_elements_fields() );
	}

	/**
	 * Add blog layout controls.
	 */
	private function add_blog_layout_controls() {

		$this->add_control(
			new Control(
				'neve_blog_grid_spacing',
				[
					'sanitize_callback' => 'neve_sanitize_range_value',
					'transport'         => 'postMessage',
					'default'           => '{ "mobile": 30, "tablet": 30, "desktop": 30 }',
				],
				[
					'label'                 => esc_html__( 'Grid Spacing', 'neve' ),
					'section'               => $this->section,
					'type'                  => 'neve_responsive_range_control',
					'input_attrs'           => [
						'min'        => 0,
						'max'        => 300,
						'units'      => [ 'px', 'em', 'rem' ],
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
						return ! $this->is_list_layout();
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

		$this->add_control(
			new Control(
				'neve_blog_list_spacing',
				[
					'sanitize_callback' => 'neve_sanitize_range_value',
					'transport'         => 'postMessage',
					'default'           => '{ "mobile": 60, "tablet": 60, "desktop": 60 }',
				],
				[
					'label'                 => esc_html__( 'List Spacing', 'neve' ),
					'section'               => $this->section,
					'type'                  => 'neve_responsive_range_control',
					'input_attrs'           => [
						'min'        => 0,
						'max'        => 300,
						'units'      => [ 'px', 'em', 'rem' ],
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
					'active_callback'       => [ $this, 'is_list_layout' ],
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

		$this->add_control(
			new Control(
				'neve_blog_covers_min_height',
				[
					'sanitize_callback' => 'neve_sanitize_range_value',
					'transport'         => 'postMessage',
					'default'           => '{ "mobile": 350, "tablet": 350, "desktop": 350 }',
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
					'active_callback'       => [ $this, 'is_covers_layout' ],
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

		$this->add_control(
			new Control(
				'neve_blog_items_border_radius',
				[
					'sanitize_callback' => 'neve_sanitize_range_value',
					'transport'         => 'postMessage',
					'default'           => 0,
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
						return ! $this->is_list_layout();
					},
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar'   => [
							'vars'     => '--borderradius',
							'suffix'   => 'px',
							'fallback' => '0',
							'selector' => '.posts-wrapper, .nv-ft-post',
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

		$this->add_control(
			new Control(
				'neve_blog_covers_overlay_color',
				array(
					'sanitize_callback' => 'neve_sanitize_colors',
					'default'           => 'rgba(0,0,0,0.75)',
					'transport'         => 'postMessage',
				),
				array(
					'label'                 => esc_html__( 'Overlay Color', 'neve' ),
					'section'               => $this->section,
					'priority'              => 14,
					'active_callback'       => [ $this, 'is_covers_layout' ],
					'default'               => 'rgba(0,0,0,0.75)',
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
				),
				'Neve\Customizer\Controls\React\Color'
			)
		);

		$content_padding_default = [
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

		$this->add_control(
			new Control(
				'neve_blog_content_padding',
				array(
					'sanitize_callback' => array( $this, 'sanitize_spacing_array' ),
					'transport'         => 'refresh',
					'default'           => $content_padding_default,
				),
				array(
					'label'                 => esc_html__( 'Content Padding', 'neve' ),
					'section'               => $this->section,
					'priority'              => 15,
					'input_attrs'           => array(
						'units' => [ 'px', 'em', 'rem' ],
						'min'   => 0,
					),
					'default'               => $content_padding_default,
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => array(
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
					),
				),
				'\Neve\Customizer\Controls\React\Spacing'
			)
		);

		$this->add_control(
			new Control(
				'neve_blog_show_on_hover',
				array(
					'sanitize_callback' => 'neve_sanitize_checkbox',
					'default'           => false,
				),
				array(
					'type'            => 'neve_toggle_control',
					'priority'        => 36,
					'section'         => $this->section,
					'label'           => esc_html__( 'Show Content Only On Hover', 'neve' ),
					'active_callback' => [ $this, 'is_covers_layout' ],
				)
			)
		);

		$this->add_control(
			new Control(
				'neve_blog_list_image_position',
				[
					'sanitize_callback' => function ( $value ) {
						if ( ! in_array( $value, [ 'left', 'no', 'right' ], true ) ) {
							return 'left';
						}

						return $value;
					},
					'default'           => 'left',
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
					'active_callback' => [ $this, 'is_list_layout' ],
				],
				'\Neve\Customizer\Controls\React\Radio_Buttons'
			)
		);
		$this->add_control(
			new Control(
				'neve_blog_list_image_width',
				[
					'sanitize_callback' => 'absint',
					'transport'         => 'refresh',
					'default'           => 35,
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
						return $this->is_list_layout() && Mods::get( 'neve_blog_list_image_position', 'left' ) !== 'no';
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
		$this->add_control(
			new Control(
				'neve_blog_image_hover',
				array(
					'default'           => 'none',
					'sanitize_callback' => array( $this, 'sanitize_image_hover' ),
				),
				array(
					'label'           => esc_html__( 'Image style', 'neve' ),
					'section'         => $this->section,
					'priority'        => 15,
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
					'active_callback' => function () {
						if ( $this->is_list_layout() ) {
							return Mods::get( 'neve_blog_list_image_position', 'left' ) !== 'no';
						}
						return true;
					},
				)
			)
		);
		$this->add_control(
			new Control(
				'neve_blog_separator',
				array(
					'sanitize_callback' => 'neve_sanitize_checkbox',
					'default'           => false,
				),
				array(
					'type'            => 'neve_toggle_control',
					'priority'        => 19,
					'section'         => $this->section,
					'label'           => esc_html__( 'Add Separator between posts', 'neve' ),
					'active_callback' => function () {
						return get_theme_mod( $this->section ) !== 'covers';
					},
				)
			)
		);
		$this->add_control(
			new Control(
				'neve_blog_separator_width',
				[
					'sanitize_callback' => 'neve_sanitize_range_value',
					'transport'         => $this->selective_refresh,
					'default'           => '{ "mobile": 1, "tablet": 1, "desktop": 1 }',
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
						return get_theme_mod( $this->section ) !== 'covers' && get_theme_mod( 'neve_blog_separator' ) === true;
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
		$this->add_control(
			new Control(
				'neve_blog_separator_color',
				array(
					'sanitize_callback' => 'neve_sanitize_colors',
					'transport'         => $this->selective_refresh,
					'default'           => 'var(--nv-light-bg)',
				),
				array(
					'label'                 => esc_html__( 'Separator Color', 'neve' ),
					'section'               => $this->section,
					'priority'              => 22,
					'active_callback'       => function () {
						return get_theme_mod( $this->section ) !== 'covers' && get_theme_mod( 'neve_blog_separator' ) === true;
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
				),
				'Neve\Customizer\Controls\React\Color'
			)
		);
		$this->add_control(
			new Control(
				'neve_enable_card_style',
				array(
					'sanitize_callback' => 'neve_sanitize_checkbox',
					'default'           => false,
				),
				array(
					'type'     => 'neve_toggle_control',
					'priority' => 36,
					'section'  => $this->section,
					'label'    => esc_html__( 'Enable Card Style', 'neve' ),
				)
			)
		);
		$this->add_control(
			new Control(
				'neve_blog_grid_card_bg_color',
				array(
					'sanitize_callback' => 'neve_sanitize_colors',
					'default'           => '#333333',
					'transport'         => 'postMessage',
				),
				array(
					'label'                 => esc_html__( 'Card Background Color', 'neve' ),
					'section'               => $this->section,
					'priority'              => 37,
					'default'               => '#333333',
					'active_callback'       => function () {
						return ( $this->is_grid_layout() || $this->is_list_layout() ) && Mods::get( 'neve_enable_card_style', false ) === true;
					},
					'input_attrs'           => [
						'allow_gradient' => Loader::has_compatibility( 'gradient_picker' ),
					],
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar'   => [
							'vars'     => '--cardbgcolor',
							'selector' => '.posts-wrapper',
						],
						'template' =>
							'.layout-grid .article-content-col .content {
							background: {{value}};
						}',
					],
				),
				'Neve\Customizer\Controls\React\Color'
			)
		);

		$this->add_control(
			new Control(
				'neve_blog_grid_text_color',
				array(
					'sanitize_callback' => 'neve_sanitize_colors',
					'default'           => '#ffffff',
					'transport'         => 'postMessage',
				),
				array(
					'label'                 => esc_html__( 'Text Color', 'neve' ),
					'section'               => $this->section,
					'priority'              => 38,
					'default'               => '#ffffff',
					'active_callback'       => function () {
						return ( $this->is_grid_layout() || $this->is_list_layout() ) && Mods::get( 'neve_enable_card_style', false ) === true;
					},
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar'   => [
							'vars'     => '--cardcolor',
							'selector' => '.posts-wrapper',
						],
						'template' =>
							'.layout-grid .article-content-col .content, .layout-grid .article-content-col .content a:not(.button), .layout-grid .article-content-col .content li {
							color: {{value}};
						}',
					],
				),
				'Neve\Customizer\Controls\React\Color'
			)
		);

		$this->add_control(
			new Control(
				'neve_blog_card_shadow',
				[
					'sanitize_callback' => 'neve_sanitize_range_value',
					'transport'         => 'refresh',
					'default'           => 0,
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
						return Mods::get( 'neve_enable_card_style', false ) === true;
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
	 * Add ordering controls.
	 */
	private function add_ordering_content_controls() {
		$this->add_control(
			new Control(
				'neve_posts_order',
				array(
					'default'           => 'date_posted_desc',
					'sanitize_callback' => array( $this, 'sanitize_posts_sorting' ),
				),
				array(
					'label'    => esc_html__( 'Order posts by', 'neve' ),
					'section'  => $this->section,
					'priority' => 51,
					'type'     => 'select',
					'choices'  => array(
						'date_posted_desc' => esc_html__( 'Date posted descending', 'neve' ),
						'date_posted_asc'  => esc_html__( 'Date posted ascending', 'neve' ),
						'date_updated'     => esc_html__( 'Date updated', 'neve' ),
					),
				)
			)
		);
		// content alignment
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
				'neve_blog_content_alignment',
				[
					'sanitize_callback' => function ( $value ) {
						if ( ! in_array( $value, [ 'left', 'center', 'right' ], true ) ) {
							return 'left';
						}

						return $value;
					},
					'default'           => 'left',
					'transport'         => 'postMessage',
				],
				[
					'label'                 => esc_html__( 'Content Alignment', 'neve' ),
					'section'               => $this->section,
					'choices'               => $align_choices,
					'show_labels'           => true,
					'priority'              => 56,
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
		// vertical alignment
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
				'neve_blog_content_vertical_alignment',
				[
					'sanitize_callback' => function ( $value ) {
						if ( ! in_array( $value, [ 'flex-start', 'center', 'flex-end' ], true ) ) {
							return 'flex-end';
						}

						return $value;
					},
					'transport'         => 'postMessage',
					'default'           => 'bottom',
				],
				[
					'label'                 => esc_html__( 'Content Alignment', 'neve' ),
					'section'               => $this->section,
					'show_labels'           => true,
					'choices'               => $align_choices,
					'priority'              => 57,
					'active_callback'       => [ $this, 'is_covers_layout' ],
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
	 * Add post meta controls.
	 */
	private function add_post_meta_controls() {
		$this->add_control(
			new Control(
				'neve_metadata_separator',
				array(
					'sanitize_callback' => 'sanitize_text_field',
					'default'           => esc_html( '/' ),
				),
				array(
					'priority'    => 77,
					'section'     => $this->section,
					'label'       => esc_html__( 'Separator', 'neve' ),
					'description' => esc_html__( 'For special characters make sure to use Unicode. For example > can be displayed using \003E.', 'neve' ),
					'type'        => 'text',
				)
			)
		);

		$this->add_control(
			new Control(
				'neve_author_avatar_size',
				array(
					'sanitize_callback' => 'neve_sanitize_range_value',
					'default'           => wp_json_encode(
						array(
							'desktop' => 20,
							'tablet'  => 20,
							'mobile'  => 20,
						)
					),
				),
				array(
					'label'           => esc_html__( 'Avatar Size', 'neve' ),
					'section'         => $this->section,
					'units'           => array(
						'px',
					),
					'input_attr'      => array(
						'mobile'  => array(
							'min'          => 20,
							'max'          => 50,
							'default'      => 20,
							'default_unit' => 'px',
						),
						'tablet'  => array(
							'min'          => 20,
							'max'          => 50,
							'default'      => 20,
							'default_unit' => 'px',
						),
						'desktop' => array(
							'min'          => 20,
							'max'          => 50,
							'default'      => 20,
							'default_unit' => 'px',
						),
					),
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
						return get_theme_mod( 'neve_author_avatar', false );
					},
					'responsive'      => true,
				),
				'Neve\Customizer\Controls\React\Responsive_Range'
			)
		);
	}

	/**
	 * Read More Options
	 */
	public function add_read_more_controls() {
		/*
		 * Heading for Read More options
		 */
		$this->add_control(
			new Control(
				'neve_read_more_options',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'label'            => esc_html__( 'Read More', 'neve' ),
					'section'          => $this->section,
					'priority'         => 85,
					'class'            => 'blog-layout-read-more-accordion',
					'accordion'        => true,
					'expanded'         => false,
					'controls_to_wrap' => 2,
				),
				'Neve\Customizer\Controls\Heading'
			)
		);

		/*
		 * Read More Text
		 */
		$this->add_control(
			new Control(
				'neve_read_more_text',
				array(
					'sanitize_callback' => 'sanitize_text_field',
					'default'           => esc_html__( 'Read More', 'neve' ) . ' &raquo;',
				),
				array(
					'priority' => 90,
					'section'  => $this->section,
					'label'    => esc_html__( 'Text', 'neve' ),
					'type'     => 'text',
				)
			)
		);

		/*
		 * Read More Style
		 */
		$this->add_control(
			new Control(
				'neve_read_more_style',
				array(
					'default'           => 'text',
					'sanitize_callback' => array( $this, 'sanitize_read_more_style' ),
				),
				array(
					'label'    => esc_html__( 'Style', 'neve' ),
					'section'  => $this->section,
					'priority' => 95,
					'type'     => 'select',
					'choices'  => array(
						'text'             => esc_html__( 'Text', 'neve' ),
						'primary_button'   => esc_html__( 'Primary Button', 'neve' ),
						'secondary_button' => esc_html__( 'Secondary Button', 'neve' ),
					),
				)
			)
		);
	}

	/**
	 * Add controls for featured post.
	 */
	public function add_featured_post_controls() {
		if ( ! Loader::has_compatibility( 'featured_post' ) ) {
			return;
		}
		$this->add_control(
			new Control(
				'neve_featured_post_image_position',
				[
					'sanitize_callback' => [ $this, 'sanitize_fp_image_position' ],
					'transport'         => $this->selective_refresh,
					'default'           => 'top',
				],
				[
					'label'                 => esc_html__( 'Image Position', 'neve' ),
					'section'               => $this->section,
					'priority'              => 43,
					'choices'               => [
						'left'  => [
							'tooltip' => esc_html__( 'Left', 'neve' ),
							'icon'    => 'align-pull-left',
						],
						'top'   => [
							'tooltip' => esc_html__( 'Top', 'neve' ),
							'icon'    => 'align-full-width',
						],
						'right' => [
							'tooltip' => esc_html__( 'Right', 'neve' ),
							'icon'    => 'align-pull-right',
						],
					],
					'show_labels'           => true,
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar' => [
							'vars'       => [
								'--ftposttemplate',
								'--ftpostimgorder',
								'--ftpostcontentorder',
							],
							'valueRemap' => [
								'--ftposttemplate'     => [
									'left'   => '1fr 1.25fr',
									'center' => '1fr',
									'right'  => '1.25fr 1fr',
								],
								'--ftpostimgorder'     => [
									'left'  => '0',
									'right' => '1',
								],
								'--ftpostcontentorder' => [
									'left'  => '1',
									'right' => '0',
								],
							],
							'selector'   => '.nv-ft-post .content',
						],
					],
					'active_callback'       => [ $this, 'is_featured_post_grid_list' ],
				],
				'\Neve\Customizer\Controls\React\Radio_Buttons'
			)
		);

		$this->add_control(
			new Control(
				'neve_featured_post_image_align',
				[
					'sanitize_callback' => [ $this, 'sanitize_fp_image_align' ],
					'transport'         => $this->selective_refresh,
					'default'           => 'center',
				],
				[
					'label'                 => esc_html__( 'Image Alignment', 'neve' ),
					'section'               => $this->section,
					'priority'              => 44,
					'choices'               => [
						'top'    => [
							'tooltip' => esc_html__( 'Top', 'neve' ),
							'icon'    => 'verticalTop',
						],
						'center' => [
							'tooltip' => esc_html__( 'Middle', 'neve' ),
							'icon'    => 'verticalMiddle',
						],
						'bottom' => [
							'tooltip' => esc_html__( 'Bottom', 'neve' ),
							'icon'    => 'verticalBottom',
						],
					],
					'show_labels'           => true,
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar' => [
							'vars'     => '--ftpostimgalign',
							'selector' => '.nv-ft-post',
						],
					],
					'active_callback'       => [ $this, 'has_fp_img_top' ],
				],
				'\Neve\Customizer\Controls\React\Radio_Buttons'
			)
		);

		$this->add_control(
			new Control(
				'neve_featured_post_content_align',
				[
					'sanitize_callback' => [ $this, 'sanitize_fp_content_position' ],
					'transport'         => $this->selective_refresh,
					'default'           => 'center',
				],
				[
					'label'                 => esc_html__( 'Content Alignment', 'neve' ),
					'section'               => $this->section,
					'choices'               => [
						'self-start' => [
							'tooltip' => esc_html__( 'Top', 'neve' ),
							'icon'    => 'verticalTop',
						],
						'center'     => [
							'tooltip' => esc_html__( 'Middle', 'neve' ),
							'icon'    => 'verticalMiddle',
						],
						'self-end'   => [
							'tooltip' => esc_html__( 'Bottom', 'neve' ),
							'icon'    => 'verticalBottom',
						],
					],
					'show_labels'           => true,
					'priority'              => 45,
					'active_callback'       => [ $this, 'is_featured_post' ],
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar' => [
							'vars'     => '--ftpostcontentalign',
							'selector' => '.nv-ft-post',
						],
					],

				],
				'\Neve\Customizer\Controls\React\Radio_Buttons'
			)
		);

		$this->add_control(
			new Control(
				'neve_featured_post_background',
				[
					'sanitize_callback' => 'neve_sanitize_colors',
					'transport'         => $this->selective_refresh,
					'default'           => 'var(--nv-light-bg)',
				],
				[
					'label'                 => esc_html__( 'Background Color', 'neve' ),
					'section'               => $this->section,
					'type'                  => 'neve_color_control',
					'priority'              => 46,
					'active_callback'       => [ $this, 'is_featured_post_grid_list' ],
					'input_attrs'           => [
						'allow_gradient' => Loader::has_compatibility( 'gradient_picker' ),
					],
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar' => [
							'vars'     => '--fpbackground',
							'selector' => '.nv-ft-post',
						],
					],
				],
				'Neve\Customizer\Controls\React\Color'
			)
		);

		$this->add_control(
			new Control(
				'neve_featured_post_padding',
				[
					'sanitize_callback' => [ $this, 'sanitize_spacing_array' ],
					'transport'         => $this->selective_refresh,
					'default'           => $this->responsive_padding_default(),
				],
				[
					'label'                 => esc_html__( 'Content Padding', 'neve' ),
					'section'               => $this->section,
					'priority'              => 47,
					'input_attrs'           => [
						'units' => [ 'px', 'em', 'rem' ],
						'min'   => 0,
					],
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar' => [
							'vars'       => '--fppadding',
							'selector'   => '.nv-ft-post',
							'responsive' => true,
						],
					],
					'active_callback'       => [ $this, 'is_featured_post' ],
				],
				'\Neve\Customizer\Controls\React\Spacing'
			)
		);

		$this->add_control(
			new Control(
				'neve_featured_post_min_height',
				[
					'sanitize_callback' => 'neve_sanitize_range_value',
					'transport'         => $this->selective_refresh,
					'default'           => '{ "mobile": 300, "tablet": 300, "desktop": 300 }',
				],
				[
					'label'                 => esc_html__( 'Post height', 'neve' ),
					'section'               => $this->section,
					'priority'              => 48,
					'type'                  => 'neve_responsive_range_control',
					'input_attrs'           => [
						'min'        => self::RELATIVE_CSS_UNIT_SUPPORTED_MIN_VALUE,
						'max'        => 800,
						'units'      => [ 'px', 'em', 'rem' ],
						'defaultVal' => [
							'mobile'  => 300,
							'tablet'  => 300,
							'desktop' => 300,
							'suffix'  => [
								'mobile'  => 'px',
								'tablet'  => 'px',
								'desktop' => 'px',
							],
						],
					],
					'active_callback'       => [ $this, 'is_featured_post' ],
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar'     => [
							'vars'       => '--fpminheight',
							'suffix'     => 'px',
							'responsive' => true,
							'selector'   => '.nv-ft-post',
						],
						'responsive' => true,
					],
				],
				'\Neve\Customizer\Controls\React\Responsive_Range'
			)
		);
	}

	/**
	 * Sanitize freatured post image position
	 *
	 * @param string $value Control value.
	 *
	 * @return string
	 */
	public function sanitize_fp_image_position( $value ) {
		if ( ! in_array( $value, [ 'top', 'left', 'right' ], true ) ) {
			return 'top';
		}
		return $value;
	}

	/**
	 * Sanitize freatured post image alignment
	 *
	 * @param string $value Control value.
	 *
	 * @return string
	 */
	public function sanitize_fp_image_align( $value ) {
		if ( ! in_array( $value, [ 'top', 'center', 'bottom' ], true ) ) {
			return 'center';
		}
		return $value;
	}

	/**
	 * Sanitize freatured post content position
	 *
	 * @param string $value Control value.
	 *
	 * @return string
	 */
	public function sanitize_fp_content_position( $value ) {
		if ( ! in_array( $value, [ 'self-start', 'center', 'self-end' ], true ) ) {
			return 'left';
		}
		return $value;
	}

	/**
	 * Sanitize read more button style
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_read_more_style( $value ) {
		$allowed_values = array( 'text', 'primary_button', 'secondary_button' );
		if ( ! in_array( $value, $allowed_values, true ) ) {
			return 'number';
		}

		return esc_html( $value );
	}

	/**
	 * Filter single post elements
	 *
	 * @param array $input - controls registered by the theme.
	 *
	 * @return array
	 */
	public function filter_single_post_elements( $input ) {

		$new_controls = array(
			'author-biography' => __( 'Author Biography', 'neve' ),
			'related-posts'    => __( 'Related Posts', 'neve' ),
			'sharing-icons'    => __( 'Sharing Icons', 'neve' ),
		);

		$single_post_elements = array_merge( $input, $new_controls );

		return $single_post_elements;
	}

	/**
	 * Sanitize posts sorting
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_posts_sorting( $value ) {
		$allowed_values = array( 'date_posted_asc', 'date_posted_desc', 'date_updated' );
		if ( ! in_array( $value, $allowed_values, true ) ) {
			return 'date_posted_desc';
		}

		return esc_html( $value );
	}

	/**
	 * Active callback for image alignment.
	 *
	 * @return bool
	 */
	public function has_fp_img_top() {
		if ( ! $this->is_featured_post() ) {
			return false;
		}
		if ( ! $this->is_featured_post_grid_list() ) {
			return true;
		}
		$image_position = get_theme_mod( 'neve_featured_post_image_position', 'top' );
		return $image_position === 'top';
	}

	/**
	 * Active callback for featured posts control.
	 *
	 * @return bool
	 */
	public function is_featured_post_grid_list() {
		return $this->is_featured_post() && ( $this->is_grid_layout() || $this->is_list_layout() );
	}

	/**
	 * Check is featured post is enabled.
	 *
	 * @return bool
	 */
	public function is_featured_post() {
		return get_theme_mod( 'neve_enable_featured_post', false );
	}

	/**
	 * Checks if is list layout blog
	 *
	 * @return bool
	 */
	public function is_list_layout() {
		return get_theme_mod( $this->section, 'grid' ) === 'default';
	}

	/**
	 * Checks if is covers layout blog
	 *
	 * @return bool
	 */
	public function is_covers_layout() {
		return get_theme_mod( $this->section, 'grid' ) === 'covers';
	}

	/**
	 * Checks if is grid layout blog
	 *
	 * @return bool
	 */
	public function is_grid_layout() {
		return get_theme_mod( $this->section, 'grid' ) === 'grid';
	}
}
