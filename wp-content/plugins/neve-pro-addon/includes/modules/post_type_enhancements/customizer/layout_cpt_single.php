<?php
/**
 * Handles the layout customizer options for a custom post type.
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
use Neve\Customizer\Defaults\Single_Post;
use Neve\Customizer\Types\Control;
use Neve\Customizer\Types\Section;
use Neve_Pro\Core\Loader;
use Neve_Pro\Modules\Post_Type_Enhancements\Model\CPT_Model;
use Neve_Pro\Traits\Utils;
use Neve_Pro\Traits\Sanitize_Functions;

/**
 * Class Layout_CPT_Single
 *
 * @since   3.1.0
 * @package Neve Pro Addon
 */
class Layout_CPT_Single extends Base_Customizer {
	use Core;
	use Single_Post;
	use Sanitize_Functions;
	use Utils;

	/**
	 * The minimum value of some customizer controls is 0 to able to allow usability relative to CSS units.
	 * That can be removed after the https://github.com/Codeinwp/neve/issues/3609 issue is handled.
	 *
	 * That is defined here against the usage of old Neve versions, Base_Customizer class of the stable Neve version already has the RELATIVE_CSS_UNIT_SUPPORTED_MIN_VALUE constant.
	 */
	const RELATIVE_CSS_UNIT_SUPPORTED_MIN_VALUE = 0;

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
	private $section = 'neve_single_post_layout';


	/**
	 * Has support for boxed layout.
	 *
	 * @var bool
	 */
	private $supports_boxed = false;

	/**
	 * Is Blog Pro active.
	 *
	 * @var bool
	 */
	private $is_blog_pro_enabled = false;

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
		add_action( 'customize_controls_enqueue_scripts', [ $this, 'add_customizer_style_for_sections' ] );

		parent::init();
	}

	/**
	 * Add styles for spacing custom customizer sections for custom types layouts.
	 *
	 * @since 3.1.0
	 * @return void
	 */
	final public function add_customizer_style_for_sections() {
		$style = '#accordion-section-neve_single_' . $this->model->get_type() . '_layout { margin-top: 8px; }
			#accordion-section-neve_single_' . $this->model->get_type() . '_layout::before {
				content: "' . $this->model->get_singular() . '";
				margin: 16px;
				line-height: 3em;
				font-weight: bold;
			}
		';
		wp_add_inline_style( 'customize-controls', $style );
	}

	/**
	 * Add customizer controls.
	 *
	 * This is hooked to run on `customize_register` from the base class.
	 *
	 * @since 3.1.0
	 * @return void
	 */
	public function add_controls() {
		$this->section             = 'neve_single_' . $this->model->get_type() . '_layout';
		$this->supports_boxed      = method_exists( $this, 'add_boxed_layout_controls' );
		$this->is_blog_pro_enabled = get_option( 'nv_pro_blog_pro_status' );

		$this->section_single_post();
		$this->use_custom_control();
		$this->control_content_order();
		$this->content_vspacing();

		$this->add_subsections();
		$this->header_layout();
		$this->post_meta();
		$this->comments();

		add_action( 'customize_register', [ $this, 'adjust_headings' ], PHP_INT_MAX );

		if ( $this->is_blog_pro_enabled ) {
			$this->headings();
			$this->sharing();
			$this->related_posts();
			$this->author_box();
			$this->post_navigation();
		}
	}

	/**
	 * Add a customize section for this post type.
	 *
	 * @since 3.1.0
	 * @return void
	 */
	private function section_single_post() {
		$this->add_section(
			new Section(
				$this->section,
				[
					'priority' => 1000 + (int) $this->model->get_priority(),
					'title'    => esc_html__( 'Single', 'neve' ) . ' ' . $this->model->get_singular(),
					'panel'    => 'neve_layout',
				]
			)
		);
	}

	/**
	 * Register a new control that toggles if the custom options should apply or if the single posts defaults are used.
	 *
	 * @since 3.1.0
	 */
	private function use_custom_control() {
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_type() . '_use_custom',
				[
					'sanitize_callback' => 'neve_sanitize_checkbox',
					'transport'         => 'refresh',
					'default'           => false,
				],
				[
					'label'       => esc_html__( 'Use a custom layout', 'neve' ),
					'description' => esc_html__( 'By default the settings are inherited from the Single post layout.', 'neve' ),
					'section'     => $this->section,
					'type'        => 'neve_toggle_control',
					'priority'    => 0,
				],
				'Neve\Customizer\Controls\Checkbox'
			)
		);
	}

	/**
	 * Get default values for ordering control
	 *
	 * @since 3.1.0
	 * @return array
	 */
	public function default_post_ordering() {
		$default_components = $this->post_ordering();
		if ( ! $this->model->is_custom_layout_enabled() ) {
			return $default_components;
		}

		$default_components = [
			'title-meta',
			'thumbnail',
			'content',
			'tags',
			'comments',
		];

		if ( $this->model->is_cover_layout() ) {
			$default_components = [
				'content',
				'tags',
				'comments',
			];
		}

		return $default_components;
	}

	/**
	 * Get default values for content vertical spacing.
	 *
	 * @return array
	 */
	public function content_vspacing_default() {
		return [
			'mobile'       => [
				'top'    => 0,
				'bottom' => 0,
			],
			'tablet'       => [
				'top'    => 0,
				'bottom' => 0,
			],
			'desktop'      => [
				'top'    => 0,
				'bottom' => 0,
			],
			'mobile-unit'  => 'px',
			'tablet-unit'  => 'px',
			'desktop-unit' => 'px',
		];
	}

	/**
	 * Active callback for sharing controls.
	 *
	 * @param string $element Post page element.
	 *
	 * @return bool
	 */
	public function element_is_enabled( $element ) {
		$default_order = $this->default_post_ordering();

		$content_order = Mods::get( 'neve_layout_single_' . $this->model->get_type() . '_elements_order', wp_json_encode( $default_order ) );
		$content_order = json_decode( $content_order, true );
		if ( ! in_array( $element, $content_order, true ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Sanitize content order control.
	 *
	 * @param mixed $value Value to sanitize.
	 *
	 * @return string
	 */
	public function sanitize_post_elements_ordering( $value ) {
		$allowed = [
			'thumbnail',
			'title-meta',
			'content',
			'tags',
			'post-navigation',
			'comments',
			'author-biography',
			'related-posts',
			'sharing-icons',
		];

		if ( empty( $value ) ) {
			return wp_json_encode( $allowed );
		}

		$decoded = json_decode( $value, true );

		foreach ( $decoded as $val ) {
			if ( ! in_array( $val, $allowed, true ) ) {
				return wp_json_encode( $allowed );
			}
		}

		return $value;
	}

	/**
	 * Add content order control.
	 *
	 * @since 3.1.0
	 * @return void
	 */
	private function control_content_order() {
		$all_components = [
			'title-meta'      => __( 'Title & Meta', 'neve' ),
			'thumbnail'       => __( 'Thumbnail', 'neve' ),
			'content'         => __( 'Content', 'neve' ),
			'tags'            => __( 'Tags', 'neve' ),
			'post-navigation' => __( 'Post navigation', 'neve' ),
			'comments'        => __( 'Comments', 'neve' ),
		];

		if ( $this->model->is_cover_layout() ) {
			$all_components = [
				'content'         => __( 'Content', 'neve' ),
				'tags'            => __( 'Tags', 'neve' ),
				'post-navigation' => __( 'Post navigation', 'neve' ),
				'comments'        => __( 'Comments', 'neve' ),
			];
		}

		$order_default_components = $this->default_post_ordering();

		/**
		 * Filters the elements on the single post page.
		 *
		 * @param array $all_components Single post page components.
		 *
		 * @since 2.11.4
		 */
		$components = apply_filters( 'neve_single_post_elements', $all_components );

		$this->add_control(
			new Control(
				'neve_layout_single_' . $this->model->get_type() . '_elements_order',
				[
					'sanitize_callback' => [ $this, 'sanitize_post_elements_ordering' ],
					'default'           => wp_json_encode( $order_default_components ),
				],
				[
					'label'           => esc_html__( 'Elements Order', 'neve' ),
					'section'         => $this->section,
					'components'      => $components,
					'priority'        => 8,
					'active_callback' => function () {
						return $this->model->is_custom_layout_enabled();
					},
				],
				'Neve\Customizer\Controls\React\Ordering'
			)
		);

		$this->add_control(
			new Control(
				'neve_single_' . $this->model->get_type() . '_elements_spacing',
				[
					'sanitize_callback' => 'neve_sanitize_range_value',
					'transport'         => $this->selective_refresh,
					'default'           => '{"desktop":60,"tablet":60,"mobile":60}',
				],
				[
					'label'                 => esc_html__( 'Spacing between elements', 'neve' ),
					'section'               => $this->section,
					'type'                  => 'neve_responsive_range_control',
					'input_attrs'           => [
						'max'        => 500,
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
					'priority'              => 8,
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar' => [
							'responsive' => true,
							'vars'       => '--spacing',
							'selector'   => '.nv-single-post-wrap',
							'suffix'     => 'px',
						],
					],
					'active_callback'       => function () {
						return $this->model->is_custom_layout_enabled();
					},
				],
				'\Neve\Customizer\Controls\React\Responsive_Range'
			)
		);
	}

	/**
	 * Add content spacing control.
	 */
	private function content_vspacing() {

		$single_default_value = get_theme_mod( 'neve_post_inherit_vspacing', 'inherit' );
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_type() . '_inherit_vspacing',
				[
					'sanitize_callback' => 'neve_sanitize_vspace_type',
					'default'           => $single_default_value,
				],
				[
					'label'              => esc_html__( 'Content Vertical Spacing', 'neve' ),
					'section'            => $this->section,
					'priority'           => 10,
					'choices'            => [
						'inherit'  => [
							'tooltip' => esc_html__( 'Inherit', 'neve' ),
							'icon'    => 'text',
						],
						'specific' => [
							'tooltip' => esc_html__( 'Custom', 'neve' ),
							'icon'    => 'text',
						],
					],
					'footer_description' => [
						'inherit' => [
							'template'         => esc_html__( 'Customize the default vertical spacing <ctaButton>here</ctaButton>.', 'neve' ),
							'control_to_focus' => 'neve_content_vspacing',
						],
					],
					'active_callback'    => function () {
						return $this->model->is_custom_layout_enabled();
					},
				],
				'\Neve\Customizer\Controls\React\Radio_Buttons'
			)
		);

		$global_default_value = get_theme_mod( 'neve_content_vspacing', $this->content_vspacing_default() );
		$single_default_value = get_theme_mod( 'neve_post_content_vspacing', $global_default_value );
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_type() . '_content_vspacing',
				[
					'default'   => $single_default_value,
					'transport' => $this->selective_refresh,
				],
				[
					'label'                 => __( 'Custom Value', 'neve' ),
					'sanitize_callback'     => [ $this, 'sanitize_spacing_array' ],
					'section'               => $this->section,
					'input_attrs'           => [
						'units'     => [ 'px', 'vh' ],
						'axis'      => 'vertical',
						'dependsOn' => [ 'neve_' . $this->model->get_type() . '_inherit_vspacing' => 'specific' ],
					],
					'default'               => $single_default_value,
					'priority'              => 10,
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar'      => [
							'vars'       => '--c-vspace',
							'selector'   => 'body.single-' . $this->model->get_type() . ':not(.single-product) .neve-main',
							'responsive' => true,
							'fallback'   => '',
						],
						'directional' => true,
					],
					'active_callback'       => function () {
						return $this->model->is_custom_layout_enabled();
					},
				],
				'\Neve\Customizer\Controls\React\Spacing'
			)
		);
	}

	/**
	 * Add sections headings.
	 *
	 * @since 3.1.0
	 * @return void
	 */
	private function add_subsections() {
		$headings = [
			'header_layout'    => [
				'title'            => esc_html__( 'Header Layout', 'neve' ),
				'priority'         => 5,
				'controls_to_wrap' => 15,
				'active_callback'  => function () {
					return $this->model->is_custom_layout_enabled();
				},
			],
			'page_elements'    => [
				'title'            => esc_html__( 'Page Elements', 'neve' ),
				'priority'         => 7,
				'controls_to_wrap' => 2,
				'expanded'         => false,
				'active_callback'  => function () {
					return $this->model->is_custom_layout_enabled();
				},
			],
			'page_settings'    => [
				'title'            => esc_html__( 'Page Settings', 'neve' ),
				'priority'         => 9,
				'controls_to_wrap' => 2,
				'expanded'         => false,
				'active_callback'  => function () {
					return $this->model->is_custom_layout_enabled();
				},
			],
			'meta'             => [
				'title'            => esc_html__( 'Post Meta', 'neve' ),
				'priority'         => 11,
				'controls_to_wrap' => 5,
				'expanded'         => false,
				'active_callback'  => function () {
					return $this->model->is_custom_layout_enabled();
				},
			],
			'comments_section' => [
				'title'           => esc_html__( 'Comments Section', 'neve' ),
				'priority'        => 150,
				'expanded'        => true,
				'accordion'       => false,
				'active_callback' => function () {
					return $this->model->is_custom_layout_enabled() && $this->element_is_enabled( 'comments' );
				},
			],
			'comments_form'    => [
				'title'           => esc_html__( 'Submit Form Section', 'neve' ),
				'priority'        => 171,
				'expanded'        => true,
				'accordion'       => false,
				'active_callback' => function () {
					return $this->model->is_custom_layout_enabled() && $this->element_is_enabled( 'comments' );
				},
			],
		];

		foreach ( $headings as $heading_id => $heading_data ) {
			$this->add_control(
				new Control(
					'neve_' . $this->model->get_type() . '_' . $heading_id . '_heading',
					[
						'sanitize_callback' => 'sanitize_text_field',
					],
					[
						'label'            => $heading_data['title'],
						'section'          => $this->section,
						'priority'         => $heading_data['priority'],
						'class'            => $heading_id . '-accordion',
						'expanded'         => array_key_exists( 'expanded', $heading_data ) ? $heading_data['expanded'] : true,
						'accordion'        => array_key_exists( 'accordion', $heading_data ) ? $heading_data['accordion'] : true,
						'controls_to_wrap' => array_key_exists( 'controls_to_wrap', $heading_data ) ? $heading_data['controls_to_wrap'] : 0,
						'active_callback'  => $heading_data['active_callback'],
					],
					'Neve\Customizer\Controls\Heading'
				)
			);
		}
	}

	/**
	 * Add header layout controls.
	 *
	 * @since 3.1.0
	 * @return void
	 */
	private function header_layout() {
		/**
		 * Header Layout Control
		 */
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_type() . '_header_layout',
				[
					'sanitize_callback' => 'wp_filter_nohtml_kses',
					'default'           => Mods::get( 'neve_post_header_layout', 'normal' ),
				],
				[
					'section'         => $this->section,
					'priority'        => 6,
					'choices'         => [
						'normal' => [
							'name'  => esc_html__( 'Normal', 'neve' ),
							'image' => 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iODUiIGhlaWdodD0iMTE4IiB2aWV3Qm94PSIwIDAgODUgMTE4IiBmaWxsPSJub25lIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgogICAgPHJlY3QgeD0iMi4yNSIgeT0iMi40NjM4NyIgd2lkdGg9IjgwIiBoZWlnaHQ9IjExMyIgZmlsbD0id2hpdGUiLz4KICAgIDxyZWN0IHg9IjE3LjI1IiB5PSIxNC42MDQ1IiB3aWR0aD0iNTAiIGhlaWdodD0iMzQuNTUzNyIgZmlsbD0iIzc4QjZGRiIgZmlsbC1vcGFjaXR5PSIwLjQiLz4KICAgIDxsaW5lIHgxPSIxNy4yNSIgeTE9IjYyLjY5MjQiIHgyPSI2Ny4wNzkyIiB5Mj0iNjIuNjkyNCIgc3Ryb2tlPSIjQzRDNEM0IiBzdHJva2Utd2lkdGg9IjIiLz4KICAgIDxsaW5lIHgxPSIxNy4yNSIgeTE9IjY3Ljc2OTUiIHgyPSIyNS4yNSIgeTI9IjY3Ljc2OTUiIHN0cm9rZT0iI0M0QzRDNCIgc3Ryb2tlLXdpZHRoPSIyIi8+CiAgICA8bGluZSB4MT0iMTcuMjUiIHkxPSI1Ny40OTcxIiB4Mj0iNTEuMDA1MyIgeTI9IjU3LjQ5NzEiIHN0cm9rZT0iI0M0QzRDNCIgc3Ryb2tlLXdpZHRoPSIyIi8+CiAgICA8bGluZSB4MT0iMTcuMjUiIHkxPSI4Ny4zODA5IiB4Mj0iNjcuMDc5MiIgeTI9Ijg3LjM4MDkiIHN0cm9rZT0iI0M0QzRDNCIgc3Ryb2tlLXdpZHRoPSIyIi8+CiAgICA8bGluZSB4MT0iMTcuMjUiIHkxPSI5Mi41NzYyIiB4Mj0iNjcuMDc5MiIgeTI9IjkyLjU3NjIiIHN0cm9rZT0iI0M0QzRDNCIgc3Ryb2tlLXdpZHRoPSIyIi8+CiAgICA8bGluZSB4MT0iMTcuMjUiIHkxPSI5OC4wNjE1IiB4Mj0iNjcuMDc5MiIgeTI9Ijk4LjA2MTUiIHN0cm9rZT0iI0M0QzRDNCIgc3Ryb2tlLXdpZHRoPSIyIi8+Cjwvc3ZnPgo=',
						],
						'cover'  => [
							'name'  => esc_html__( 'Cover', 'neve' ),
							'image' => 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iODYiIGhlaWdodD0iMTIwIiB2aWV3Qm94PSIwIDAgODYgMTIwIiBmaWxsPSJub25lIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgogICAgPHJlY3QgeD0iMyIgeT0iMy40NjM4NyIgd2lkdGg9IjgwIiBoZWlnaHQ9IjExMyIgZmlsbD0id2hpdGUiLz4KICAgIDxyZWN0IHg9IjMiIHk9IjMuNDYzODciIHdpZHRoPSI4MCIgaGVpZ2h0PSI1MS4zNjM2IiBmaWxsPSIjNzhCNkZGIiBmaWxsLW9wYWNpdHk9IjAuNCIvPgogICAgPGxpbmUgeDE9IjE5IiB5MT0iNjcuNDI4NyIgeDI9IjY4LjgyOTIiIHkyPSI2Ny40Mjg3IiBzdHJva2U9IiNDNEM0QzQiIHN0cm9rZS13aWR0aD0iMiIvPgogICAgPGxpbmUgeDE9IjE5IiB5MT0iODMuNzExOSIgeDI9IjY4LjgyOTIiIHkyPSI4My43MTE5IiBzdHJva2U9IiNDNEM0QzQiIHN0cm9rZS13aWR0aD0iMiIvPgogICAgPGxpbmUgeDE9IjE5IiB5MT0iNzIuNjI0IiB4Mj0iNjguODI5MiIgeTI9IjcyLjYyNCIgc3Ryb2tlPSIjQzRDNEM0IiBzdHJva2Utd2lkdGg9IjIiLz4KICAgIDxsaW5lIHgxPSIxOSIgeTE9Ijg4LjkwNzIiIHgyPSI2OC44MjkyIiB5Mj0iODguOTA3MiIgc3Ryb2tlPSIjQzRDNEM0IiBzdHJva2Utd2lkdGg9IjIiLz4KICAgIDxsaW5lIHgxPSIxOSIgeTE9Ijc4LjEwODQiIHgyPSI2OC44MjkyIiB5Mj0iNzguMTA4NCIgc3Ryb2tlPSIjQzRDNEM0IiBzdHJva2Utd2lkdGg9IjIiLz4KICAgIDxsaW5lIHgxPSIxOSIgeTE9Ijk0LjM5MjYiIHgyPSI2OC44MjkyIiB5Mj0iOTQuMzkyNiIgc3Ryb2tlPSIjQzRDNEM0IiBzdHJva2Utd2lkdGg9IjIiLz4KICAgIDxsaW5lIHgxPSIxOSIgeTE9IjgzLjcxMTkiIHgyPSI0OCIgeTI9IjgzLjcxMTkiIHN0cm9rZT0iI0M0QzRDNCIgc3Ryb2tlLXdpZHRoPSIyIi8+CiAgICA8bGluZSB4MT0iMTkiIHkxPSI5OS45OTYxIiB4Mj0iNDgiIHkyPSI5OS45OTYxIiBzdHJva2U9IiNDNEM0QzQiIHN0cm9rZS13aWR0aD0iMiIvPgogICAgPGxpbmUgeDE9IjE5IiB5MT0iNDQuNDg5MyIgeDI9IjUyLjc1NTMiIHkyPSI0NC40ODkzIiBzdHJva2U9IndoaXRlIiBzdHJva2Utd2lkdGg9IjIiLz4KICAgIDxsaW5lIHgxPSIxOSIgeTE9IjM4Ljg4NTciIHgyPSI2OSIgeTI9IjM4Ljg4NTciIHN0cm9rZT0id2hpdGUiIHN0cm9rZS13aWR0aD0iMiIvPgogICAgPHJlY3QgeD0iMS41IiB5PSIxLjk2Mzg3IiB3aWR0aD0iODMiIGhlaWdodD0iMTE2IiBzdHJva2U9IndoaXRlIiBzdHJva2Utd2lkdGg9IjMiLz4KPC9zdmc+Cg==',
						],
					],
					'active_callback' => function () {
						return $this->model->is_custom_layout_enabled();
					},
				],
				'\Neve\Customizer\Controls\React\Radio_Image'
			)
		);

		/**
		 * Cover Height Control
		 */
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_type() . '_cover_height',
				[
					'sanitize_callback' => 'neve_sanitize_range_value',
					'transport'         => $this->selective_refresh,
					'default'           => Mods::get( 'neve_post_cover_height', '{ "mobile": 400, "tablet": 400, "desktop": 400 }' ),
				],
				[
					'label'                 => esc_html__( 'Cover height', 'neve' ),
					'section'               => $this->section,
					'type'                  => 'neve_responsive_range_control',
					'input_attrs'           => [
						'max'        => 700,
						'units'      => [ 'px', 'vh' ],
						'defaultVal' => [
							'mobile'  => 250,
							'tablet'  => 320,
							'desktop' => 400,
							'suffix'  => [
								'mobile'  => 'px',
								'tablet'  => 'px',
								'desktop' => 'px',
							],
						],
					],
					'priority'              => 6,
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar' => [
							'responsive' => true,
							'vars'       => '--height',
							'selector'   => '.nv-post-cover',
							'suffix'     => 'px',
						],
					],
					'active_callback'       => function () {
						return $this->model->is_custom_layout_enabled() && $this->model->is_cover_layout();
					},
				],
				'\Neve\Customizer\Controls\React\Responsive_Range'
			)
		);

		/**
		 * Cover Padding Control
		 */
		$cover_padding_defaults = Mods::get( 'neve_post_cover_padding', $this->padding_default( 'cover' ) );
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_type() . '_cover_padding',
				[
					'sanitize_callback' => [ $this, 'sanitize_spacing_array' ],
					'transport'         => $this->selective_refresh,
					'default'           => $cover_padding_defaults,
				],
				[
					'label'                 => esc_html__( 'Cover padding', 'neve' ),
					'section'               => $this->section,
					'input_attrs'           => [
						'units' => [ 'em', 'px' ],
						'min'   => 0,
					],
					'default'               => $cover_padding_defaults,
					'priority'              => 6,
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar' => array(
							'vars'       => '--padding',
							'selector'   => '.nv-post-cover',
							'responsive' => true,
						),
					],
					'active_callback'       => function () {
						return $this->model->is_custom_layout_enabled() && $this->model->is_cover_layout();
					},
				],
				'\Neve\Customizer\Controls\React\Spacing'
			)
		);

		/**
		 * Title Alignment Control
		 */
		$title_alignment_defaults = Mods::get( 'neve_post_title_alignment', $this->post_title_alignment() );
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_type() . '_title_alignment',
				[
					'sanitize_callback' => 'neve_sanitize_alignment',
					'transport'         => $this->selective_refresh,
					'default'           => $title_alignment_defaults,
				],
				[
					'label'                 => esc_html__( 'Title Alignment', 'neve' ),
					'section'               => $this->section,
					'priority'              => 6,
					'choices'               => [
						'left'   => [
							'tooltip' => esc_html__( 'Left', 'neve' ),
							'icon'    => 'editor-alignleft',
						],
						'center' => [
							'tooltip' => esc_html__( 'Center', 'neve' ),
							'icon'    => 'editor-aligncenter',
						],
						'right'  => [
							'tooltip' => esc_html__( 'Right', 'neve' ),
							'icon'    => 'editor-alignright',
						],
					],
					'show_labels'           => true,
					'live_refresh_selector' => '.nv-post-cover .nv-title-meta-wrap, .entry-header .entry-title',
					'live_refresh_css_prop' => [
						'cssVar' => [
							'vars'       => [
								'--textalign',
								'--justify',
							],
							'valueRemap' => [
								'--justify' => [
									'left'   => 'flex-start',
									'center' => 'center',
									'right'  => 'flex-end',
								],
							],
							'responsive' => true,
							'selector'   => '.nv-post-cover .container, .nv-post-cover, .entry-header',
						],
					],
					'active_callback'       => function () {
						return $this->model->is_custom_layout_enabled();
					},
				],
				'\Neve\Customizer\Controls\React\Responsive_Radio_Buttons'
			)
		);

		/**
		 * Title Position Control
		 */
		$title_position_defaults = Mods::get(
			'neve_post_title_position',
			[
				'mobile'  => 'center',
				'tablet'  => 'center',
				'desktop' => 'center',
			]
		);
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_type() . '_title_position',
				[
					'sanitize_callback' => 'neve_sanitize_position',
					'transport'         => $this->selective_refresh,
					'default'           => $title_position_defaults,
				],
				[
					'label'                 => esc_html__( 'Title Position', 'neve' ),
					'section'               => $this->section,
					'priority'              => 6,
					'choices'               => [
						'flex-start' => [
							'tooltip' => esc_html__( 'Top', 'neve' ),
							'icon'    => 'arrow-up',
						],
						'center'     => [
							'tooltip' => esc_html__( 'Middle', 'neve' ),
							'icon'    => 'sort',
						],
						'flex-end'   => [
							'tooltip' => esc_html__( 'Bottom', 'neve' ),
							'icon'    => 'arrow-down',
						],
					],
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar' => [
							'vars'       => '--valign',
							'responsive' => true,
							'selector'   => '.nv-post-cover',
						],
					],
					'show_labels'           => true,
					'active_callback'       => function () {
						return $this->model->is_custom_layout_enabled() && $this->model->is_cover_layout();
					},
				],
				'\Neve\Customizer\Controls\React\Responsive_Radio_Buttons'
			)
		);

		/**
		 * Cover Meta Before Title Checkbox Control
		 */
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_type() . '_cover_meta_before_title',
				[
					'sanitize_callback' => 'neve_sanitize_checkbox',
					'default'           => Mods::get( 'neve_post_cover_meta_before_title', false ),
				],
				[
					'label'           => esc_html__( 'Display meta before title', 'neve' ),
					'section'         => $this->section,
					'type'            => 'neve_toggle_control',
					'priority'        => 6,
					'active_callback' => function () {
						return $this->model->is_custom_layout_enabled() && $this->model->is_cover_layout();
					},
				],
				'Neve\Customizer\Controls\Checkbox'
			)
		);

		/**
		 * Cover Background Color Control
		 */
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_type() . '_cover_background_color',
				[
					'sanitize_callback' => 'neve_sanitize_colors',
					'default'           => 'var(--nv-dark-bg)',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'                 => esc_html__( 'Overlay color', 'neve' ),
					'section'               => $this->section,
					'priority'              => 6,
					'input_attrs'           => [
						'allow_gradient' => Loader::has_compatibility( 'gradient_picker' ),
					],
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar' => array(
							'vars'     => '--bgcolor',
							'selector' => '.single .nv-post-cover .nv-overlay',
						),
					],
					'active_callback'       => function () {
						return $this->model->is_custom_layout_enabled() && $this->model->is_cover_layout();
					},
				],
				'Neve\Customizer\Controls\React\Color'
			)
		);

		/**
		 * Cover Text Color Control
		 */
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_type() . '_cover_text_color',
				[
					'sanitize_callback' => 'neve_sanitize_colors',
					'default'           => Mods::get( 'neve_post_cover_text_color', 'var(--nv-text-dark-bg)' ),
					'transport'         => $this->selective_refresh,
				],
				[
					'label'                 => esc_html__( 'Text color', 'neve' ),
					'section'               => $this->section,
					'priority'              => 6,
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar' => [
							'vars'     => '--color',
							'selector' => '.single .nv-post-cover .nv-title-meta-wrap',
						],
					],
					'active_callback'       => function () {
						return $this->model->is_custom_layout_enabled() && $this->model->is_cover_layout();
					},
				],
				'Neve\Customizer\Controls\React\Color'
			)
		);

		/**
		 * Cover Overlay Opacity Control
		 */
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_type() . '_cover_overlay_opacity',
				[
					'sanitize_callback' => 'neve_sanitize_range_value',
					'transport'         => $this->selective_refresh,
					'default'           => Mods::get( 'neve_post_cover_overlay_opacity', 50 ),
				],
				[
					'label'                 => esc_html__( 'Overlay opacity', 'neve' ) . '(%)',
					'section'               => $this->section,
					'input_attrs'           => [
						'min'        => 0,
						'max'        => 100,
						'step'       => 1,
						'defaultVal' => 50,
					],
					'priority'              => 6,
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar' => [
							'vars'     => '--opacity',
							'selector' => '.nv-overlay',
						],
					],
					'active_callback'       => function () {
						return $this->model->is_custom_layout_enabled() && $this->model->is_cover_layout();
					},
				],
				'Neve\Customizer\Controls\React\Range'
			)
		);

		/**
		 * Cover Hide Thumbnail Control
		 */
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_type() . '_cover_hide_thumbnail',
				[
					'sanitize_callback' => 'neve_sanitize_checkbox',
					'default'           => Mods::get( 'neve_post_cover_hide_thumbnail', false ),
				],
				[
					'label'           => esc_html__( 'Hide featured image', 'neve' ),
					'section'         => $this->section,
					'type'            => 'neve_toggle_control',
					'priority'        => 6,
					'active_callback' => function () {
						return $this->model->is_custom_layout_enabled() && $this->model->is_cover_layout();
					},
				],
				'Neve\Customizer\Controls\Checkbox'
			)
		);

		/**
		 * Cover Blend Mode Control
		 */
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_type() . '_cover_blend_mode',
				[
					'default'           => Mods::get( 'neve_post_cover_blend_mode', 'normal' ),
					'sanitize_callback' => 'neve_sanitize_blend_mode',
					'transport'         => $this->selective_refresh,
				],
				[
					'label'                 => esc_html__( 'Blend mode', 'neve' ),
					'section'               => $this->section,
					'priority'              => 6,
					'type'                  => 'select',
					'choices'               => [
						'normal'      => esc_html__( 'Normal', 'neve' ),
						'multiply'    => esc_html__( 'Multiply', 'neve' ),
						'screen'      => esc_html__( 'Screen', 'neve' ),
						'overlay'     => esc_html__( 'Overlay', 'neve' ),
						'darken'      => esc_html__( 'Darken', 'neve' ),
						'lighten'     => esc_html__( 'Lighten', 'neve' ),
						'color-dodge' => esc_html__( 'Color Dodge', 'neve' ),
						'saturation'  => esc_html__( 'Saturation', 'neve' ),
						'color'       => esc_html__( 'Color', 'neve' ),
						'difference'  => esc_html__( 'Difference', 'neve' ),
						'exclusion'   => esc_html__( 'Exclusion', 'neve' ),
						'hue'         => esc_html__( 'Hue', 'neve' ),
						'luminosity'  => esc_html__( 'Luminosity', 'neve' ),
					],
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar' => [
							'vars'     => '--blendmode',
							'selector' => '.nv-overlay',
						],
					],
					'active_callback'       => function () {
						return $this->model->is_custom_layout_enabled() && $this->model->is_cover_layout();
					},
				]
			)
		);

		/**
		 * Cover Container Control
		 */
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_type() . '_cover_container',
				[
					'default'           => Mods::get( 'neve_post_cover_container', 'contained' ),
					'sanitize_callback' => 'neve_sanitize_container_layout',
				],
				[
					'label'           => esc_html__( 'Cover container', 'neve' ),
					'section'         => $this->section,
					'priority'        => 6,
					'type'            => 'select',
					'choices'         => [
						'contained'  => esc_html__( 'Contained', 'neve' ),
						'full-width' => esc_html__( 'Full width', 'neve' ),
					],
					'active_callback' => function () {
						return $this->model->is_custom_layout_enabled() && $this->model->is_cover_layout();
					},
				]
			)
		);

		/**
		 * Cover Title Boxed Controls
		 */
		$this->add_boxed_layout_controls(
			$this->model->get_type() . '_cover_title',
			[
				'priority'               => 6,
				'section'                => $this->section,
				'has_text_color'         => false,
				'padding_default'        => $this->padding_default( 'cover' ),
				'background_default'     => 'var(--nv-dark-bg)',
				'boxed_selector'         => '.nv-is-boxed.nv-title-meta-wrap',
				'toggle_active_callback' => function () {
					return $this->model->is_custom_layout_enabled() && $this->model->is_cover_layout();
				},
				'active_callback'        => function () {
					return $this->model->is_custom_layout_enabled()
						&& $this->model->is_cover_layout()
						&& Mods::get( 'neve_' . $this->model->get_type() . '_cover_title_boxed_layout', false );
				},
			]
		);
	}

	/**
	 * Add post meta controls.
	 *
	 * @since 3.1.0
	 * @return void
	 */
	private function post_meta() {
		$order_default_components = Mods::get(
			'neve_post_meta_ordering',
			wp_json_encode(
				[
					'author',
					'date',
					'comments',
				]
			)
		);

		/**
		 * Filters the elements that appears in meta.
		 *
		 * @param array $elements Array of meta elements.
		 *
		 * @since 2.11.4
		 */
		$components = apply_filters(
			'neve_meta_filter',
			[
				'author'   => __( 'Author', 'neve' ),
				'category' => __( 'Category', 'neve' ),
				'date'     => __( 'Date', 'neve' ),
				'comments' => __( 'Comments', 'neve' ),
			]
		);

		/**
		 * Meta Ordering Control
		 */
		$has_custom_meta   = Loader::has_compatibility( 'meta_custom_fields' );
		$default_value     = Mods::get( 'neve_single_post_meta_ordering', $order_default_components );
		$name              = 'neve_single_' . $this->model->get_type() . '_meta_ordering';
		$class             = 'Neve\Customizer\Controls\React\Ordering';
		$sanitize_function = 'neve_sanitize_meta_ordering';
		if ( $has_custom_meta ) {
			// We replaced the previous meta control with a new one that has another id and another format of data.
			// The default value is based on previous control default data and the control from blog.
			$default_value     = $this->get_default_meta_value( 'neve_single_' . $this->model->get_type() . '_meta_ordering', Mods::get( 'neve_single_post_meta_ordering', $order_default_components ) );
			$default_value     = Mods::get( 'neve_single_post_meta_fields', $default_value );
			$name              = 'neve_single_' . $this->model->get_type() . '_meta_fields';
			$class             = '\Neve\Customizer\Controls\React\Repeater';
			$sanitize_function = 'neve_sanitize_meta_repeater';
		}

		$this->add_control(
			new Control(
				$name,
				[
					'sanitize_callback' => $sanitize_function,
					'default'           => $default_value,
				],
				[
					'label'           => esc_html__( 'Meta Order', 'neve' ),
					'section'         => $this->section,
					'components'      => $components,
					'new_item_fields' => $this->get_new_elements_fields( $this->model->get_type() ),
					'fields'          => $this->get_blocked_elements_fields(),
					'priority'        => 12,
					'active_callback' => function () {
						return $this->model->is_custom_layout_enabled();
					},
				],
				$class
			)
		);

		/**
		 * Meta Separator Control
		 */
		$default_separator = Mods::get( 'neve_metadata_separator', esc_html( '/' ) );
		$this->add_control(
			new Control(
				'neve_single_' . $this->model->get_type() . '_metadata_separator',
				[
					'sanitize_callback' => 'sanitize_text_field',
					'default'           => $default_separator,
				],
				[
					'priority'        => 12,
					'section'         => $this->section,
					'label'           => esc_html__( 'Separator', 'neve' ),
					'description'     => esc_html__( 'For special characters make sure to use Unicode. For example > can be displayed using \003E.', 'neve' ),
					'type'            => 'text',
					'active_callback' => function () {
						return $this->model->is_custom_layout_enabled();
					},
				]
			)
		);

		/**
		 * Author Avatar Control
		 */
		$author_avatar_default = Mods::get( 'neve_author_avatar', false );
		$this->add_control(
			new Control(
				'neve_single_' . $this->model->get_type() . '_author_avatar',
				[
					'sanitize_callback' => 'neve_sanitize_checkbox',
					'default'           => $author_avatar_default,
				],
				[
					'label'           => esc_html__( 'Show Author Avatar', 'neve' ),
					'section'         => $this->section,
					'type'            => 'neve_toggle_control',
					'priority'        => 12,
					'active_callback' => function () {
						return $this->model->is_custom_layout_enabled();
					},
				]
			)
		);

		/**
		 * Author Avatar Size Control
		 */
		$avatar_size_default = '{ "mobile": 20, "tablet": 20, "desktop": 20 }';
		$this->add_control(
			new Control(
				'neve_single_' . $this->model->get_type() . '_avatar_size',
				[
					'sanitize_callback' => 'neve_sanitize_range_value',
					'default'           => Mods::get( 'neve_single_post_avatar_size', $avatar_size_default ),
				],
				[
					'label'           => esc_html__( 'Avatar Size', 'neve' ),
					'section'         => $this->section,
					'units'           => [ 'px' ],
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
						'min'        => self::RELATIVE_CSS_UNIT_SUPPORTED_MIN_VALUE,
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
						'units'      => [ 'px', 'em', 'rem' ],
					],
					'priority'        => 12,
					'active_callback' => function () {
						return $this->model->is_custom_layout_enabled() && Mods::get( 'neve_single_' . $this->model->get_type() . '_author_avatar', false );
					},
					'responsive'      => true,
				],
				'Neve\Customizer\Controls\React\Responsive_Range'
			)
		);

		/**
		 * Last Updated Checkbox Control
		 */
		$this->add_control(
			new Control(
				'neve_single_' . $this->model->get_type() . '_show_last_updated_date',
				[
					'sanitize_callback' => 'neve_sanitize_checkbox',
					'default'           => Mods::get( 'neve_show_last_updated_date', false ),
				],
				[
					'label'           => esc_html__( 'Use last updated date instead of the published one', 'neve' ),
					'section'         => $this->section,
					'type'            => 'neve_toggle_control',
					'priority'        => 12,
					'active_callback' => function () {
						return $this->model->is_custom_layout_enabled();
					},
				]
			)
		);
	}

	/**
	 * Add comments controls.
	 *
	 * @since 3.1.0
	 * @return void
	 */
	private function comments() {
		/**
		 * Heading for Related posts options
		 */
		if ( $this->is_blog_pro_enabled ) {
			$this->add_control(
				new Control(
					'neve_' . $this->model->get_type() . '_comments_heading',
					[
						'sanitize_callback' => 'sanitize_text_field',
					],
					[
						'label'            => esc_html__( 'Comments', 'neve' ),
						'section'          => $this->section,
						'priority'         => 140,
						'class'            => 'comments-accordion',
						'accordion'        => true,
						'expanded'         => false,
						'controls_to_wrap' => 1,
						'active_callback'  => function () {
							return $this->model->is_custom_layout_enabled() && $this->element_is_enabled( 'comments' );
						},
					],
					'Neve\Customizer\Controls\Heading'
				)
			);

			$this->add_control(
				new Control(
					'neve_' . $this->model->get_type() . '_comment_section_style',
					[
						'default'           => Mods::get( 'neve_comment_section_style', 'always' ),
						'sanitize_callback' => 'wp_filter_nohtml_kses',
					],
					[
						'label'           => esc_html__( 'Comment Section Style', 'neve' ),
						'section'         => $this->section,
						'priority'        => 145,
						'type'            => 'select',
						'choices'         => [
							'always' => esc_html__( 'Always Show', 'neve' ),
							'toggle' => esc_html__( 'Show/Hide mechanism', 'neve' ),
						],
						'active_callback' => function () {
							return $this->model->is_custom_layout_enabled() && $this->element_is_enabled( 'comments' );
						},
					]
				)
			);
		}

		/**
		 * Comment Section Title Control
		 */
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_type() . '_comment_section_title',
				[
					'sanitize_callback' => 'sanitize_text_field',
				],
				[
					'label'           => esc_html__( 'Section title', 'neve' ),
					'description'     => esc_html__( 'The following magic tags are available for this field: {title} and {comments_number}. Leave this field empty for default behavior.', 'neve' ),
					'priority'        => 155,
					'section'         => $this->section,
					'type'            => 'text',
					'active_callback' => function () {
						return $this->model->is_custom_layout_enabled() && $this->element_is_enabled( 'comments' );
					},
				]
			)
		);

		/**
		 * Comments Boxed Controls
		 */
		$this->add_boxed_layout_controls(
			$this->model->get_type() . '_comments',
			[
				'priority'                  => 160,
				'section'                   => $this->section,
				'padding_default'           => $this->padding_default(),
				'background_default'        => 'var(--nv-light-bg)',
				'color_default'             => 'var(--nv-text-color)',
				'boxed_selector'            => '.nv-is-boxed.nv-comments-wrap',
				'text_color_css_selector'   => '.nv-comments-wrap.nv-is-boxed, .nv-comments-wrap.nv-is-boxed a',
				'border_color_css_selector' => '.nv-comments-wrap.nv-is-boxed .nv-comment-article',
				'toggle_active_callback'    => function () {
					return $this->model->is_custom_layout_enabled();
				},
				'active_callback'           => function () {
					return $this->model->is_custom_layout_enabled()
						&& $this->element_is_enabled( 'comments' )
						&& Mods::get( 'neve_' . $this->model->get_type() . '_comments_boxed_layout', false );
				},
			]
		);

		/**
		 * Comment Form Title Control
		 */
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_type() . '_comment_form_title',
				[
					'sanitize_callback' => 'sanitize_text_field',
				],
				[
					'label'           => esc_html__( 'Section title', 'neve' ),
					'priority'        => 180,
					'section'         => $this->section,
					'type'            => 'text',
					'active_callback' => function () {
						return $this->model->is_custom_layout_enabled() && $this->element_is_enabled( 'comments' );
					},
				]
			)
		);

		/**
		 * Comment Form Button Style Control
		 */
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_type() . '_comment_form_button_style',
				[
					'default'           => Mods::get( 'neve_post_comment_form_button_style', 'primary' ),
					'sanitize_callback' => 'neve_sanitize_button_type',
				],
				[
					'label'           => esc_html__( 'Button style', 'neve' ),
					'section'         => $this->section,
					'priority'        => 185,
					'type'            => 'select',
					'choices'         => [
						'primary'   => esc_html__( 'Primary', 'neve' ),
						'secondary' => esc_html__( 'Secondary', 'neve' ),
					],
					'active_callback' => function () {
						return $this->model->is_custom_layout_enabled() && $this->element_is_enabled( 'comments' );
					},
				]
			)
		);

		/**
		 * Comment Form Button Text Control
		 */
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_type() . '_comment_form_button_text',
				[
					'sanitize_callback' => 'sanitize_text_field',
				],
				[
					'label'           => esc_html__( 'Button text', 'neve' ),
					'priority'        => 190,
					'section'         => $this->section,
					'type'            => 'text',
					'active_callback' => function () {
						return $this->model->is_custom_layout_enabled() && $this->element_is_enabled( 'comments' );
					},
				]
			)
		);

		/**
		 * Comments Form Boxed Controls
		 */
		$this->add_boxed_layout_controls(
			$this->model->get_type() . '_comments_form',
			[
				'priority'                => 195,
				'section'                 => $this->section,
				'padding_default'         => $this->padding_default(),
				'is_boxed_default'        => true,
				'background_default'      => 'var(--nv-light-bg)',
				'color_default'           => 'var(--nv-text-color)',
				'boxed_selector'          => '.nv-is-boxed.comment-respond',
				'text_color_css_selector' => '.comment-respond.nv-is-boxed, .comment-respond.nv-is-boxed a',
				'toggle_active_callback'  => function () {
					return $this->model->is_custom_layout_enabled();
				},
				'active_callback'         => function () {
					return $this->model->is_custom_layout_enabled()
						&& $this->element_is_enabled( 'comments' )
						&& Mods::get( 'neve_' . $this->model->get_type() . '_comments_form_boxed_layout', true );
				},
			]
		);
	}

	/**
	 * Add headings controls.
	 */
	private function headings() {
		$related_no_controls = $this->supports_boxed ? 11 : 7;
		$sharing_no_controls = 13;

		$headings = [
			$this->model->get_type() . '_sharing'       => [
				'label'            => esc_html__( 'Sharing icons', 'neve' ),
				'priority'         => 205,
				'expanded'         => false,
				'controls_to_wrap' => $sharing_no_controls,
				'active_callback'  => function () {
					return $this->model->is_custom_layout_enabled() && $this->element_is_enabled( 'sharing-icons' );
				},
			],
			$this->model->get_type() . '_related_posts' => [
				'label'            => esc_html__( 'Related Posts', 'neve' ),
				'priority'         => 285,
				'expanded'         => false,
				'controls_to_wrap' => $related_no_controls,
				'active_callback'  => function () {
					return $this->model->is_custom_layout_enabled() && $this->element_is_enabled( 'related-posts' );
				},
			],
			$this->model->get_type() . '_post_nav'      => [
				'label'            => esc_html__( 'Post Navigation', 'neve' ),
				'priority'         => 350,
				'expanded'         => false,
				'controls_to_wrap' => 1,
				'active_callback'  => function() {
					return $this->model->is_custom_layout_enabled() && $this->element_is_enabled( 'post-navigation' );
				},
			],
		];

		$headings[ $this->model->get_type() . '_author_box' ] = [
			'label'            => esc_html__( 'Author Box', 'neve' ),
			'priority'         => 230,
			'expanded'         => false,
			'controls_to_wrap' => $this->supports_boxed ? 10 : 6,
			'active_callback'  => function () {
				return $this->model->is_custom_layout_enabled() && $this->element_is_enabled( 'author-biography' );
			},
		];

		foreach ( $headings as $heading_id => $heading_data ) {
			$this->add_control(
				new Control(
					'neve_' . $this->model->get_type() . '_' . $heading_id . '_heading',
					[
						'sanitize_callback' => 'sanitize_text_field',
					],
					[
						'label'            => $heading_data['label'],
						'section'          => $this->section,
						'priority'         => $heading_data['priority'],
						'class'            => $this->model->get_type() . '_' . $heading_id . '-accordion',
						'expanded'         => $heading_data['expanded'],
						'accordion'        => true,
						'controls_to_wrap' => $heading_data['controls_to_wrap'],
						'active_callback'  => $heading_data['active_callback'],
					],
					'Neve\Customizer\Controls\Heading'
				)
			);
		}
	}

	/**
	 * Add post navigation related controls.
	 */
	private function post_navigation() {
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_type() . '_nav_infinite',
				[
					'sanitize_callback' => 'neve_sanitize_checkbox',
					'default'           => false,
				],
				[
					'label'           => esc_html__( 'Enable infinite scroll', 'neve' ),
					'description'     => apply_filters( 'neve_external_link', 'https://bit.ly/nv-sp-inf', __( 'View more details about this', 'neve' ) ),
					'section'         => $this->section,
					'type'            => 'neve_toggle_control',
					'priority'        => 360,
					'active_callback' => function() {
						return $this->model->is_custom_layout_enabled() && $this->element_is_enabled( 'post-navigation' );
					},
				]
			)
		);
	}

	/**
	 * Add author box settings.
	 */
	public function author_box() {

		$this->add_control(
			new Control(
				'neve_author_' . $this->model->get_type() . '_box_enable_avatar',
				[
					'sanitize_callback' => 'neve_sanitize_checkbox',
					'default'           => true,
				],
				[
					'label'           => esc_html__( 'Show author image', 'neve' ),
					'section'         => $this->section,
					'type'            => 'neve_toggle_control',
					'priority'        => 235,
					'active_callback' => function () {
						return $this->model->is_custom_layout_enabled() && $this->element_is_enabled( 'author-biography' );
					},
				]
			)
		);

		$this->add_control(
			new Control(
				'neve_author_' . $this->model->get_type() . '_box_avatar_size',
				[
					'sanitize_callback' => 'neve_sanitize_range_value',
					'transport'         => $this->selective_refresh,
					'default'           => '{ "mobile": 100, "tablet": 100, "desktop": 100 }',
				],
				[
					'label'                 => esc_html__( 'Image Size', 'neve' ),
					'section'               => $this->section,
					'type'                  => 'neve_responsive_range_control',
					'input_attrs'           => [
						'min'        => 0,
						'max'        => 100,
						'defaultVal' => [
							'mobile'  => 100,
							'tablet'  => 100,
							'desktop' => 100,
							'suffix'  => [
								'mobile'  => 'px',
								'tablet'  => 'px',
								'desktop' => 'px',
							],
						],
						'units'      => [ 'px' ],
					],
					'priority'              => 240,
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar'     => [
							'selector'   => '.nv-author-biography',
							'vars'       => '--avatarsize',
							'suffix'     => 'px',
							'responsive' => true,
						],
						'responsive' => true,
						'prop'       => 'width',
						'template'   => '.nv-author-bio-image {
							width: {{value}}px;
						}',
					],
					'active_callback'       => function () {
						return $this->model->is_custom_layout_enabled() && $this->element_is_enabled( 'author-biography' )
								&& Mods::get( 'neve_author_' . $this->model->get_type() . '_box_enable_avatar', true );
					},
				],
				'\Neve\Customizer\Controls\React\Responsive_Range'
			)
		);

		$this->add_control(
			new Control(
				'neve_author_' . $this->model->get_type() . '_box_avatar_position',
				[
					'sanitize_callback' => 'wp_filter_nohtml_kses',
					'transport'         => 'refresh',
					'default'           => 'left',
				],
				[
					'label'                 => esc_html__( 'Image position', 'neve' ),
					'section'               => $this->section,
					'priority'              => 245,
					'choices'               => [
						'left'   => [
							'tooltip' => esc_html__( 'Left', 'neve' ),
							'icon'    => 'align-left',
						],
						'center' => [
							'tooltip' => esc_html__( 'Center', 'neve' ),
							'icon'    => 'align-center',
						],
						'right'  => [
							'tooltip' => esc_html__( 'Right', 'neve' ),
							'icon'    => 'align-right',
						],
					],
					'show_labels'           => true,
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'template' =>
							'.nv-author-biography .nv-author-elements-wrapper {
							    flex-direction: {{value}}!important;
					    	}',
					],
					'active_callback'       => function () {
						return $this->model->is_custom_layout_enabled() && $this->element_is_enabled( 'author-biography' )
								&& Mods::get( 'neve_author_' . $this->model->get_type() . '_box_enable_avatar', true );
					},
				],
				'\Neve\Customizer\Controls\React\Radio_Buttons'
			)
		);

		$this->add_control(
			new Control(
				'neve_author_' . $this->model->get_type() . '_box_avatar_border_radius',
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
						'max'        => 50,
						'defaultVal' => 0,
					],
					'priority'              => 250,
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar'   => [
							'vars'     => '--borderradius',
							'suffix'   => '%',
							'selector' => '.nv-author-biography',
						],
						'fallback' => 0,
						'template' =>
							'.nv-author-bio-image {
							    border-radius: {{value}}px;
					    	}',
					],
					'active_callback'       => function () {
						return $this->model->is_custom_layout_enabled() && $this->element_is_enabled( 'author-biography' )
								&& Mods::get( 'neve_author_' . $this->model->get_type() . '_box_enable_avatar', true );
					},
				],
				'\Neve\Customizer\Controls\React\Range'
			)
		);

		$this->add_control(
			new Control(
				'neve_author_' . $this->model->get_type() . '_box_enable_archive_link',
				[
					'sanitize_callback' => 'neve_sanitize_checkbox',
					'default'           => false,
				],
				[
					'label'           => esc_html__( 'Show archive link', 'neve' ),
					'section'         => $this->section,
					'type'            => 'neve_toggle_control',
					'priority'        => 255,
					'active_callback' => function () {
						return $this->model->is_custom_layout_enabled() && $this->element_is_enabled( 'author-biography' );
					},
				]
			)
		);

		$this->add_control(
			new Control(
				'neve_author_' . $this->model->get_type() . '_box_content_alignment',
				[
					'sanitize_callback' => 'wp_filter_nohtml_kses',
					'transport'         => $this->selective_refresh,
					'default'           => 'left',
				],
				[
					'label'                 => esc_html__( 'Content alignment', 'neve' ),
					'section'               => $this->section,
					'priority'              => 260,
					'choices'               => [
						'left'   => [
							'tooltip' => esc_html__( 'Left', 'neve' ),
							'icon'    => 'editor-alignleft',
						],
						'center' => [
							'tooltip' => esc_html__( 'Center', 'neve' ),
							'icon'    => 'editor-aligncenter',
						],
						'right'  => [
							'tooltip' => esc_html__( 'Right', 'neve' ),
							'icon'    => 'editor-alignright',
						],
					],
					'show_labels'           => true,
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'template' =>
							'.nv-author-bio-text-wrapper {
							    text-align: {{value}}!important;
					    	}',
					],
					'active_callback'       => function () {
						return $this->model->is_custom_layout_enabled() && $this->element_is_enabled( 'author-biography' );
					},
				],
				'\Neve\Customizer\Controls\React\Radio_Buttons'
			)
		);

		if ( $this->supports_boxed ) {
			$this->add_boxed_layout_controls(
				'author_' . $this->model->get_type() . '_box',
				[
					'priority'                => 265,
					'section'                 => $this->section,
					'padding_default'         => $this->responsive_padding_default(),
					'background_default'      => 'var(--nv-light-bg)',
					'color_default'           => 'var(--nv-text-color)',
					'boxed_selector'          => '.nv-author-biography.nv-is-boxed',
					'text_color_css_selector' => '.nv-author-biography.nv-is-boxed, .nv-author-biography.nv-is-boxed a',
					'toggle_active_callback'  => function () {
						return $this->model->is_custom_layout_enabled() && $this->element_is_enabled( 'author-biography' );
					},
					'active_callback'         => function () {
						return $this->model->is_custom_layout_enabled() && $this->element_is_enabled( 'author-biography' )
								&& Mods::get( 'neve_author_' . $this->model->get_type() . '_box_boxed_layout', true );
					},
				]
			);
		}
	}

	/**
	 * Related Custom Posts customizer controls
	 */
	public function related_posts() {

		$this->add_control(
			new Control(
				'neve_related_' . $this->model->get_type() . '_title',
				[
					'sanitize_callback' => 'wp_kses_post',
					'default'           => esc_html__( 'Related Posts', 'neve' ),
				],
				[
					'priority'        => 290,
					'section'         => $this->section,
					'label'           => esc_html__( 'Title', 'neve' ),
					'type'            => 'text',
					'active_callback' => function () {
						return $this->model->is_custom_layout_enabled() && $this->element_is_enabled( 'related-posts' );
					},
				]
			)
		);

		$this->add_control(
			new Control(
				'neve_related_' . $this->model->get_type() . '_title_tag',
				[
					'sanitize_callback' => [ $this, 'sanitize_title_html_tag' ],
					'default'           => 'h2',
				],
				[
					'priority'        => 295,
					'section'         => $this->section,
					'label'           => esc_html__( 'Title HTML tag', 'neve' ),
					'type'            => 'select',
					'choices'         => [
						'h1' => 'H1',
						'h2' => 'H2',
						'h3' => 'H3',
						'h4' => 'H4',
						'h5' => 'H5',
						'h6' => 'H6',
					],
					'active_callback' => function () {
						return $this->model->is_custom_layout_enabled() && $this->element_is_enabled( 'related-posts' );
					},
				]
			)
		);

		$this->add_control(
			new Control(
				'neve_related_' . $this->model->get_type() . '_taxonomy',
				[
					'default'           => 'category',
					'sanitize_callback' => 'wp_filter_nohtml_kses',
				],
				[
					'label'           => esc_html__( 'Related Posts By', 'neve' ),
					'section'         => $this->section,
					'priority'        => 295,
					'type'            => 'select',
					'choices'         => [
						'category' => esc_html__( 'Categories', 'neve' ),
						'post_tag' => esc_html__( 'Tags', 'neve' ),
					],
					'active_callback' => function () {
						return $this->model->is_custom_layout_enabled() && $this->element_is_enabled( 'related-posts' );
					},
				]
			)
		);

		$this->add_control(
			new Control(
				'neve_related_' . $this->model->get_type() . '_number',
				[
					'sanitize_callback' => 'absint',
					'default'           => 3,
				],
				[
					'label'           => esc_html__( 'Number of Related Posts', 'neve' ),
					'section'         => $this->section,
					'input_attrs'     => array(
						'min'  => 1,
						'max'  => 50,
						'step' => 1,
					),
					'priority'        => 305,
					'type'            => 'number',
					'active_callback' => function () {
						return $this->model->is_custom_layout_enabled() && $this->element_is_enabled( 'related-posts' );
					},
				]
			)
		);

		$this->add_control(
			new Control(
				'neve_related_' . $this->model->get_type() . '_excerpt_length',
				[
					'sanitize_callback' => 'neve_sanitize_range_value',
					'default'           => 25,
				],
				[
					'label'           => esc_html__( 'Excerpt Length', 'neve' ),
					'section'         => $this->section,
					'step'            => 5,
					'input_attr'      => [
						'min'     => 5,
						'max'     => 300,
						'default' => 25,
					],
					'input_attrs'     => [
						'min'     => 5,
						'max'     => 300,
						'default' => 25,
					],
					'priority'        => 310,
					'active_callback' => function () {
						return $this->model->is_custom_layout_enabled() && $this->element_is_enabled( 'related-posts' );
					},
				],
				class_exists( 'Neve\Customizer\Controls\React\Range' ) ? 'Neve\Customizer\Controls\React\Range' : 'Neve\Customizer\Controls\Range'
			)
		);

		$default_related_posts_nb = $this->responsive_related_posts_nb( 'neve_related_' . $this->model->get_type() . '_columns' );
		$this->add_control(
			new Control(
				'neve_related_' . $this->model->get_type() . '_col_nb',
				array(
					'sanitize_callback' => 'neve_sanitize_range_value',
					'default'           => wp_json_encode( $default_related_posts_nb ),
				),
				array(
					'label'           => esc_html__( 'Columns', 'neve' ),
					'section'         => $this->section,
					'units'           => array(
						'items',
					),
					'input_attrs'     => [
						'min'        => 1,
						'max'        => 6,
						'defaultVal' => $default_related_posts_nb,
					],
					'priority'        => 315,
					'active_callback' => function () {
						return $this->model->is_custom_layout_enabled() && $this->element_is_enabled( 'related-posts' );
					},
				),
				'Neve\Customizer\Controls\React\Responsive_Range'
			)
		);

		$this->add_control(
			new Control(
				'neve_related_' . $this->model->get_type() . '_enable_featured_image',
				[
					'sanitize_callback' => 'neve_sanitize_checkbox',
					'default'           => true,
				],
				[
					'label'           => esc_html__( 'Show featured image', 'neve' ),
					'section'         => $this->section,
					'type'            => 'neve_toggle_control',
					'priority'        => 325,
					'active_callback' => function () {
						return $this->model->is_custom_layout_enabled() && $this->element_is_enabled( 'related-posts' );
					},
				]
			)
		);

		if ( $this->supports_boxed ) {
			$this->add_boxed_layout_controls(
				'related_' . $this->model->get_type(),
				[
					'priority'                  => 320,
					'section'                   => $this->section,
					'padding_default'           => $this->responsive_padding_default(),
					'background_default'        => 'var(--nv-light-bg)',
					'color_default'             => 'var(--nv-text-color)',
					'boxed_selector'            => '.nv-related-posts.nv-is-boxed',
					'border_color_css_selector' => '.nv-related-posts.nv-is-boxed .posts-wrapper .related-post .content',
					'text_color_css_selector'   => '.nv-related-posts.nv-is-boxed, .nv-related-posts.nv-is-boxed a',
					'toggle_active_callback'    => function () {
						return $this->model->is_custom_layout_enabled() && $this->element_is_enabled( 'related-posts' );
					},
					'active_callback'           => function () {
						return $this->model->is_custom_layout_enabled() && $this->element_is_enabled( 'related-posts' )
								&& Mods::get( 'neve_related_' . $this->model->get_type() . '_boxed_layout', false );
					},
				]
			);
		}
	}

	/**
	 * Add single post sharing controls.
	 */
	public function sharing() {
		// Content
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_type() . '_sharing_icons',
				[
					'sanitize_callback' => [ $this, 'sanitize_sharing_icons_repeater' ],
					'default'           => Mods::get( 'neve_sharing_icons', wp_json_encode( $this->social_icons_default() ) ),
				],
				[
					'label'           => esc_html__( 'Choose your social icons', 'neve' ),
					'section'         => $this->section,
					'fields'          => [
						'title'            => [
							'type'  => 'text',
							'label' => esc_html__( 'Title', 'neve' ),
						],
						'social_network'   => [
							'type'    => 'select',
							'label'   => __( 'Social Network', 'neve' ),
							'choices' => [
								'facebook'  => 'Facebook',
								'twitter'   => 'X',
								'email'     => 'Email',
								'pinterest' => 'Pinterest',
								'linkedin'  => 'LinkedIn',
								'tumblr'    => 'Tumblr',
								'reddit'    => 'Reddit',
								'whatsapp'  => 'WhatsApp',
								'sms'       => 'SMS',
								'vk'        => 'VKontakte',
							],
						],
						'icon_color'       => array(
							'type'  => 'color',
							'label' => esc_html__( 'Icon Color', 'neve' ),
						),
						'background_color' => array(
							'type'     => 'color',
							'label'    => esc_html__( 'Background Color', 'neve' ),
							'gradient' => true,
						),
						'display_desktop'  => [
							'type'  => 'checkbox',
							'label' => esc_html__( 'Show on Desktop', 'neve' ),
						],
						'display_mobile'   => [
							'type'  => 'checkbox',
							'label' => esc_html__( 'Show on Mobile', 'neve' ),
						],
					],
					'priority'        => 225,
					'active_callback' => function () {
						return $this->model->is_custom_layout_enabled() && $this->element_is_enabled( 'sharing-icons' );
					},
				],
				Loader::has_compatibility( 'repeater_control' ) ? '\Neve\Customizer\Controls\React\Repeater' : 'Neve_Pro\Customizer\Controls\Repeater'
			)
		);

		$this->add_control(
			new Control(
				'neve_' . $this->model->get_type() . '_sharing_icon_style',
				[
					'default'           => Mods::get( 'neve_sharing_icon_style', 'round' ),
					'sanitize_callback' => 'wp_filter_nohtml_kses',
				],
				[
					'label'           => esc_html__( 'Icon style', 'neve' ),
					'section'         => $this->section,
					'priority'        => 210,
					'type'            => 'select',
					'choices'         => [
						'plain' => esc_html__( 'Plain', 'neve' ),
						'round' => esc_html__( 'Round', 'neve' ),
					],
					'active_callback' => function () {
						return $this->model->is_custom_layout_enabled() && $this->element_is_enabled( 'sharing-icons' );
					},
				]
			)
		);

		$this->add_control(
			new Control(
				'neve_' . $this->model->get_type() . '_sharing_enable_custom_color',
				[
					'sanitize_callback' => 'neve_sanitize_checkbox',
					'default'           => Mods::get( 'neve_sharing_enable_custom_color', false ),
				],
				[
					'label'           => esc_html__( 'Use custom icon color', 'neve' ),
					'section'         => $this->section,
					'type'            => 'neve_toggle_control',
					'priority'        => 215,
					'active_callback' => function () {
						return $this->model->is_custom_layout_enabled() && $this->element_is_enabled( 'sharing-icons' );
					},
				]
			)
		);

		$default_old_sharing_custom_color = Mods::get( 'neve_' . $this->model->get_type() . '_sharing_custom_color', 'var(--nv-primary-accent)' );
		$default_value                    = Mods::get( 'neve_' . $this->model->get_type() . '_sharing_icon_style', 'round' ) === 'plain' ? $default_old_sharing_custom_color : '#fff';
		// Icon Color
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_type() . '_sharing_icon_color',
				[
					'sanitize_callback' => 'neve_sanitize_colors',
					'transport'         => $this->selective_refresh,
					'default'           => Mods::get( 'neve_sharing_icon_color', $default_value ),
				],
				[
					'label'                 => esc_html__( 'Icon color', 'neve' ),
					'section'               => $this->section,
					'priority'              => 220,
					'input_attrs'           => [
						'allow_gradient' => false,
					],
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar'   => [
							'vars'     => '--hex',
							'selector' => '.nv-social-icon a',
						],
						'template' => '
							.nv-post-share.round-style .nv-social-icon svg,
							.nv-post-share.round-style .nv-social-icon a svg  {
								fill: {{value}};
							}
							.nv-post-share:not(.nv-is-boxed).custom-color .nv-social-icon svg,
							.nv-post-share:not(.nv-is-boxed).custom-color .nv-social-icon a svg  {
								fill: {{value}};
							}',
					],
					'active_callback'       => function () {
						return $this->model->is_custom_layout_enabled() && $this->element_is_enabled( 'sharing-icons' )
								&& Mods::get( 'neve_' . $this->model->get_type() . '_sharing_enable_custom_color', false );
					},
				],
				'Neve\Customizer\Controls\React\Color'
			)
		);

		// Background Color / Previously Custom Color
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_type() . '_sharing_custom_color',
				[
					'sanitize_callback' => 'neve_sanitize_colors',
					'transport'         => $this->selective_refresh,
					'default'           => Mods::get( 'neve_sharing_custom_color', 'var(--nv-primary-accent)' ),
				],
				[
					'label'                 => esc_html__( 'Custom icon color', 'neve' ),
					'section'               => $this->section,
					'priority'              => 220,
					'input_attrs'           => [
						'allow_gradient' => Loader::has_compatibility( 'gradient_picker' ),
					],
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar'   => [
							'vars'     => '--bgsocial',
							'selector' => '.nv-social-icon a',
						],
						'template' => '
							.nv-post-share:not(.nv-is-boxed).custom-color .nv-social-icon svg,
							.nv-post-share:not(.nv-is-boxed).custom-color .nv-social-icon a svg  {
								fill: {{value}};
							}
							.nv-post-share.nv-is-boxed.custom-color .social-share,
							.nv-post-share.nv-is-boxed.custom-color .nv-social-icon a  {
								background: {{value}};
							}',
					],
					'active_callback'       => function () {
						return $this->model->is_custom_layout_enabled() && $this->element_is_enabled( 'sharing-icons' ) &&
							Mods::get( 'neve_' . $this->model->get_type() . '_sharing_enable_custom_color', false ) &&
							Mods::get( 'neve_' . $this->model->get_type() . 'sharing_icon_style', 'round' ) === 'round';
					},
				],
				'Neve\Customizer\Controls\React\Color'
			)
		);

		// Icon Size
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_type() . '_sharing_icon_size',
				[
					'sanitize_callback' => 'neve_sanitize_range_value',
					'transport'         => $this->selective_refresh,
					'default'           => Mods::get( 'neve_sharing_icon_size', '{"desktop":20,"tablet":20,"mobile":20}' ),
				],
				[
					'label'                 => esc_html__( 'Icon size', 'neve' ),
					'section'               => $this->section,
					'type'                  => 'neve_responsive_range_control',
					'input_attrs'           => [
						'max'        => 500,
						'units'      => [ 'px' ],
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
					],
					'priority'              => 220,
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar' => [
							'responsive' => true,
							'vars'       => '--iconsizesocial',
							'selector'   => '.nv-social-icon a',
							'suffix'     => 'px',
						],
					],
					'active_callback'       => function () {
						return $this->model->is_custom_layout_enabled() && $this->element_is_enabled( 'sharing-icons' );
					},
				],
				'\Neve\Customizer\Controls\React\Responsive_Range'
			)
		);

		// Icon Padding
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_type() . '_sharing_icon_padding',
				[
					'sanitize_callback' => 'neve_sanitize_range_value',
					'transport'         => $this->selective_refresh,
					'default'           => Mods::get( 'neve_sharing_icon_padding', '{"desktop":15,"tablet":15,"mobile":15}' ),
				],
				[
					'label'                 => esc_html__( 'Padding', 'neve' ),
					'section'               => $this->section,
					'type'                  => 'neve_responsive_range_control',
					'input_attrs'           => [
						'max'        => 500,
						'units'      => [ 'px' ],
						'defaultVal' => [
							'mobile'  => 15,
							'tablet'  => 15,
							'desktop' => 15,
							'suffix'  => [
								'mobile'  => 'px',
								'tablet'  => 'px',
								'desktop' => 'px',
							],
						],
					],
					'priority'              => 220,
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar' => [
							'responsive' => true,
							'vars'       => '--iconpaddingsocial',
							'selector'   => '.nv-social-icon a',
							'suffix'     => 'px',
						],
					],
					'active_callback'       => function () {
						return $this->model->is_custom_layout_enabled() && $this->element_is_enabled( 'sharing-icons' ) &&
							Mods::get( 'neve_' . $this->model->get_type() . 'sharing_icon_style', 'round' ) === 'round';
					},
				],
				'\Neve\Customizer\Controls\React\Responsive_Range'
			)
		);

		// Enable Text Label
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_type() . '_sharing_enable_text_label',
				[
					'sanitize_callback' => 'neve_sanitize_checkbox',
					'default'           => Mods::get( 'neve_sharing_enable_text_label', false ),
				],
				[
					'label'           => esc_html__( 'Add text label', 'neve' ),
					'section'         => $this->section,
					'type'            => 'neve_toggle_control',
					'priority'        => 220,
					'active_callback' => function () {
						return $this->model->is_custom_layout_enabled() && $this->element_is_enabled( 'sharing-icons' );
					},
				]
			)
		);

		// Text label
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_type() . '_sharing_label',
				[
					'sanitize_callback' => 'sanitize_text_field',
					'default'           => Mods::get( 'neve_sharing_label', esc_html__( 'Share this post on social!', 'neve' ) ),
				],
				[
					'priority'        => 220,
					'section'         => $this->section,
					'label'           => esc_html__( 'Label', 'neve' ),
					'type'            => 'text',
					'active_callback' => function () {
						return $this->model->is_custom_layout_enabled() && $this->element_is_enabled( 'sharing-icons' ) && Mods::get( 'neve_' . $this->model->get_type() . '_sharing_enable_text_label', false );
					},
				]
			)
		);

		// Label tag
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_type() . '_sharing_label_tag',
				[
					'sanitize_callback' => [ $this, 'sanitize_sharing_icons_tag' ],
					'default'           => Mods::get( 'neve_sharing_label_tag', 'span' ),
				],
				[
					'label'           => esc_html__( 'Label HTML tag', 'neve' ),
					'section'         => $this->section,
					'priority'        => 220,
					'type'            => 'select',
					'choices'         => [
						'span' => 'span',
						'p'    => 'p',
						'h1'   => 'H1',
						'h2'   => 'H2',
						'h3'   => 'H3',
						'h4'   => 'H4',
						'h5'   => 'H5',
						'h6'   => 'H6',
					],
					'active_callback' => function () {
						return $this->model->is_custom_layout_enabled() && $this->element_is_enabled( 'sharing-icons' ) && Mods::get( 'neve_' . $this->model->get_type() . '_sharing_enable_text_label', false );
					},
				]
			)
		);

		// Text position
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_type() . '_sharing_label_position',
				[
					'sanitize_callback' => 'wp_filter_nohtml_kses',
					'default'           => Mods::get( 'neve_sharing_label_position', 'before' ),
				],
				[
					'label'           => esc_html__( 'Label position', 'neve' ),
					'section'         => $this->section,
					'priority'        => 220,
					'type'            => 'select',
					'choices'         => [
						'before' => esc_html__( 'Before icons', 'neve' ),
						'after'  => esc_html__( 'After icons', 'neve' ),
						'above'  => esc_html__( 'Above icons', 'neve' ),
						'below'  => esc_html__( 'Below icons', 'neve' ),
					],
					'active_callback' => function () {
						return $this->model->is_custom_layout_enabled() && $this->element_is_enabled( 'sharing-icons' ) && Mods::get( 'neve_' . $this->model->get_type() . '_sharing_enable_text_label', false );
					},
				]
			)
		);

		// Icons Alignment
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_type() . '_sharing_icons_alignment',
				[
					'sanitize_callback' => 'neve_sanitize_alignment',
					'transport'         => $this->selective_refresh,
					'default'           => Mods::get( 'neve_sharing_icons_alignment', 'left' ),
				],
				[
					'label'                 => esc_html__( 'Icons alignment', 'neve' ),
					'section'               => $this->section,
					'priority'              => 220,
					'choices'               => [
						'left'   => [
							'tooltip' => esc_html__( 'Left', 'neve' ),
							'icon'    => 'align-left',
						],
						'center' => [
							'tooltip' => esc_html__( 'Center', 'neve' ),
							'icon'    => 'align-center',
						],
						'right'  => [
							'tooltip' => esc_html__( 'Right', 'neve' ),
							'icon'    => 'align-right',
						],
					],
					'show_labels'           => true,
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar' => [
							'vars'       => '--iconalignsocial',
							'responsive' => true,
							'selector'   => '.nv-post-share',
						],
					],
					'active_callback'       => function () {
						return $this->model->is_custom_layout_enabled() && $this->element_is_enabled( 'sharing-icons' );
					},
				],
				'\Neve\Customizer\Controls\React\Responsive_Radio_Buttons'
			)
		);

		// Icons Spacing
		$this->add_control(
			new Control(
				'neve_' . $this->model->get_type() . '_sharing_icon_spacing',
				[
					'sanitize_callback' => 'neve_sanitize_range_value',
					'transport'         => $this->selective_refresh,
					'default'           => Mods::get( 'neve_sharing_icon_spacing', '{"desktop":10,"tablet":10,"mobile":10}' ),
				],
				[
					'label'                 => esc_html__( 'Space between', 'neve' ),
					'section'               => $this->section,
					'type'                  => 'neve_responsive_range_control',
					'input_attrs'           => [
						'max'        => 500,
						'units'      => [ 'px' ],
						'defaultVal' => [
							'mobile'  => 10,
							'tablet'  => 10,
							'desktop' => 10,
							'suffix'  => [
								'mobile'  => 'px',
								'tablet'  => 'px',
								'desktop' => 'px',
							],
						],
					],
					'priority'              => 220,
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar' => [
							'responsive' => true,
							'vars'       => '--icongapsocial',
							'selector'   => '.nv-post-share',
							'suffix'     => 'px',
						],
					],
					'active_callback'       => function () {
						return $this->model->is_custom_layout_enabled() && $this->element_is_enabled( 'sharing-icons' );
					},
				],
				'\Neve\Customizer\Controls\React\Responsive_Range'
			)
		);
	}

	/**
	 * Change heading controls properties.
	 *
	 * @since 3.1.0
	 * @return void
	 */
	public function adjust_headings() {
		$this->change_customizer_object( 'control', 'neve_' . $this->model->get_type() . '_comments_heading', 'controls_to_wrap', 15 );
	}
}
