<?php
/**
 * Primary nav submenu class.
 *
 * Holds the Pro features for Primary nav submenus.
 *
 * @package Neve Pro Addon
 */
namespace Neve_Pro\Modules\Header_Footer_Grid\Submenu\Customizer;

use HFG\Core\Settings\Manager as SettingsManager;
use HFG\Traits\Core;
use Neve\Core\Settings\Mods;

/**
 * Class Component_Settings
 *
 * @package Neve_Pro\Modules\Header_Footer_Grid\Customizer
 */
class Component_Settings {
	use Core;

	/**
	 * Submenu icons setting ids.
	 */
	const SM_ICON_HEADING       = 'sm_icon_heading';
	const SM_ICON_SIDE          = 'sm_icon_side';
	const SM_ICON_TYPE          = 'sm_icon_type';
	const SM_ICON               = 'sm_icon';
	const SM_IMAGE              = 'sm_image';
	const SM_ICON_STYLE_HEADING = 'sm_icon_style_heading';
	const SM_ICON_COLOR         = 'sm_icon_color';
	const SM_ICON_SIZE          = 'sm_icon_size';
	const SM_ICON_DEFAULT_SIZE  = 7;

	/**
	 * Submenu layout setting ids.
	 */
	const SM_LAYOUT_HEADING      = 'sm_layout_heading';
	const SM_ANIMATION           = 'sm_animation';
	const SM_CONTAINER_ALIGNMENT = 'sm_container_alignment';
	const SM_CONTENT_ALIGNMENT   = 'sm_content_alignment';
	const SM_SPACING             = 'sm_spacing';

	/**
	 * Submenu container style setting ids.
	 */
	const SM_CONTAINER_HEADING        = 'sm_container_heading';
	const SM_CONTAINER_BORDER_HEADING = 'sm_container_border_heading';
	const SM_BORDER_STYLE             = 'sm_border_style';
	const SM_BORDER_WIDTH             = 'sm_border_width';
	const SM_BORDER_RADIUS            = 'sm_border_radius';
	const SM_BOX_SHADOW               = 'sm_box_shadow';
	const SM_CONTAINER_COLORS_HEADING = 'sm_container_colors_heading';
	const SM_BORDER_COLOR             = 'sm_border_color';
	const SM_BG_COLOR                 = 'sm_bg_color';

	/**
	 * Submenu item style setting ids.
	 */
	const SM_ITEM_HEADING            = 'sm_item_heading';
	const SM_HOVER_SKIN              = 'sm_hover_skin';
	const SM_ITEM_BORDER_HEADING     = 'sm_item_border_heading';
	const SM_ITEM_BORDER_STYLE       = 'sm_item_border_style';
	const SM_ITEM_BORDER_WIDTH       = 'sm_item_border_width';
	const SM_ITEM_BORDER_RADIUS      = 'sm_item_border_radius';
	const SM_ITEM_COLORS_HEADING     = 'sm_item_colors_heading';
	const SM_ITEM_BORDER_COLOR       = 'sm_item_border_color';
	const SM_ITEM_BORDER_COLOR_HOVER = 'sm_item_border_color_hover';
	const SM_ITEM_BG_COLOR           = 'sm_item_bg_color';
	const SM_ITEM_BG_COLOR_HOVER     = 'sm_item_bg_color_hover';
	const SM_ITEM_COLOR              = 'sm_item_color';
	const SM_ITEM_COLOR_HOVER        = 'sm_item_color_hover';
	const SM_HOVER_TEXT_COLOR        = 'sm_hover_text_color';

	/**
	 * Submenu typography setting ids.
	 */
	const SM_TYPOGRAPHY_HEADING = 'sm_typography_heading';
	const SM_FONT_FAMILY        = 'sm_font_family';
	const SM_TYPEFACE           = 'sm_typeface';

	/**
	 * Init function.
	 *
	 * @access public
	 * @version 2.4
	 */
	public function init() {
		add_action( 'hfg_component_settings', [ $this, 'add_sub_menu_features' ], 10, 2 );
	}

	/**
	 * Add customizer options.
	 *
	 * @param string $component_id Component id.
	 * @param string $section Section id.
	 */
	public function add_sub_menu_features( $component_id, $section ) {

		if ( strpos( $component_id, 'primary-menu' ) === false ) {
			return;
		}

		$this->add_sm_icon_settings( $component_id, $section );

		$this->add_sm_layout_settings( $component_id, $section );

		$this->add_sm_container_style_settings( $component_id, $section );

		$this->add_sm_item_style_settings( $component_id, $section );

		$this->add_sm_typography_settings( $component_id, $section );
	}

	/**
	 * Add customizer settings for menu icons.
	 *
	 * @param string $component_id Component id.
	 * @param string $section Section id.
	 */
	private function add_sm_icon_settings( $component_id, $section ) {

		$menu_selector = '.builder-item--' . $component_id;

		SettingsManager::get_instance()->add(
			[
				'id'                 => self::SM_ICON_HEADING,
				'group'              => $component_id,
				'tab'                => SettingsManager::TAB_LAYOUT,
				'transport'          => 'postMessage',
				'sanitize_callback'  => 'sanitize_text_field',
				'label'              => __( 'Submenu Icon', 'neve' ),
				'type'               => '\Neve\Customizer\Controls\React\Heading',
				'options'            => [
					'accordion'        => true,
					'controls_to_wrap' => 4,
					'expanded'         => true,
					'class'            => esc_attr( 'primary-nav-accordion-' . self::SM_ICON_HEADING ),
				],
				'section'            => $section,
				'conditional_header' => true,
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                 => self::SM_ICON_SIDE,
				'group'              => $component_id,
				'tab'                => SettingsManager::TAB_LAYOUT,
				'transport'          => 'refresh',
				'sanitize_callback'  => 'sanitize_text_field',
				'default'            => 'right',
				'label'              => __( 'Side', 'neve' ),
				'type'               => 'select',
				'options'            => [
					'choices' => [
						'right' => __( 'Right', 'neve' ),
						'left'  => __( 'Left', 'neve' ),
					],
				],
				'section'            => $section,
				'conditional_header' => true,
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                 => self::SM_ICON_TYPE,
				'group'              => $component_id,
				'tab'                => SettingsManager::TAB_LAYOUT,
				'transport'          => 'refresh',
				'sanitize_callback'  => 'sanitize_text_field',
				'default'            => 'icon',
				'label'              => __( 'Type', 'neve' ),
				'type'               => 'select',
				'options'            => [
					'choices' => [
						'icon'  => __( 'Icon', 'neve' ),
						'image' => __( 'Image', 'neve' ),
					],
				],
				'section'            => $section,
				'conditional_header' => true,
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                 => self::SM_ICON,
				'group'              => $component_id,
				'tab'                => SettingsManager::TAB_LAYOUT,
				'transport'          => 'post' . $component_id,
				'default'            => 'mc-icon-style-1',
				'sanitize_callback'  => 'wp_filter_nohtml_kses',
				'conditional_header' => true,
				'label'              => __( 'Icon', 'neve' ),
				'type'               => '\Neve\Customizer\Controls\React\Radio_Buttons',
				'section'            => $section,
				'options'            => [
					'large_buttons'   => false,
					'is_for'          => 'menu_caret',
					'active_callback' => function() use ( $component_id ) {
						return get_theme_mod( $component_id . '_sm_icon_type', 'icon' ) === 'icon';
					},
				],
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                => self::SM_IMAGE,
				'group'             => $component_id,
				'tab'               => SettingsManager::TAB_LAYOUT,
				'transport'         => 'post' . $component_id,
				'sanitize_callback' => 'absint',
				'label'             => __( 'Image', 'neve' ),
				'type'              => '\WP_Customize_Cropped_Image_Control',
				'options'           => [
					'flex_height'     => true,
					'flex_width'      => true,
					'width'           => 50,
					'height'          => 50,
					'active_callback' => function() use ( $component_id ) {
						return get_theme_mod( $component_id . '_sm_icon_type', 'icon' ) === 'image';
					},
				],
				'section'           => $section,
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                 => self::SM_ICON_STYLE_HEADING,
				'group'              => $component_id,
				'tab'                => SettingsManager::TAB_STYLE,
				'transport'          => 'postMessage',
				'sanitize_callback'  => 'sanitize_text_field',
				'label'              => __( 'Submenu Icon', 'neve' ),
				'type'               => '\Neve\Customizer\Controls\React\Heading',
				'options'            => [
					'accordion'        => true,
					'controls_to_wrap' => 2,
					'expanded'         => false,
					'class'            => esc_attr( 'primary-nav-accordion-' . self::SM_ICON_STYLE_HEADING ),
				],
				'section'            => $section,
				'conditional_header' => true,
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                    => self::SM_ICON_COLOR,
				'group'                 => $component_id,
				'tab'                   => SettingsManager::TAB_STYLE,
				'transport'             => 'postMessage',
				'sanitize_callback'     => 'neve_sanitize_colors',
				'label'                 => __( 'Color', 'neve' ),
				'type'                  => 'neve_color_control',
				'options'               => [
					'active_callback' => function() use ( $component_id ) {
						return get_theme_mod( $component_id . '_sm_icon_type', 'icon' ) === 'icon';
					},
				],
				'section'               => $section,
				'live_refresh_selector' => true,
				'live_refresh_css_prop' => [
					'cssVar' => [
						'vars'     => '--smiconcolor',
						'selector' => $menu_selector,
					],
					[
						'selector' => $menu_selector . ' .caret',
						'prop'     => 'color',
						'fallback' => 'inherit',
					],
				],
			]
		);

		$default_sm_icon_size = [
			'mobile'  => self::SM_ICON_DEFAULT_SIZE,
			'tablet'  => self::SM_ICON_DEFAULT_SIZE,
			'desktop' => self::SM_ICON_DEFAULT_SIZE,
			'suffix'  => [
				'mobile'  => 'px',
				'tablet'  => 'px',
				'desktop' => 'px',
			],
		];
		SettingsManager::get_instance()->add(
			[
				'id'                    => self::SM_ICON_SIZE,
				'group'                 => $component_id,
				'tab'                   => SettingsManager::TAB_STYLE,
				'section'               => $section,
				'label'                 => __( 'Size', 'neve' ),
				'type'                  => 'Neve\Customizer\Controls\React\Responsive_Range',
				'transport'             => 'postMessage',
				'sanitize_callback'     => [ $this, 'sanitize_responsive_int_json' ],
				'default'               => $default_sm_icon_size,
				'options'               => [
					'input_attrs' => [
						'min'        => 0,
						'max'        => 50,
						'units'      => [ 'px', 'em', 'rem' ],
						'defaultVal' => $default_sm_icon_size,
					],
				],
				'live_refresh_selector' => true,
				'live_refresh_css_prop' => [
					'cssVar' => [
						'responsive'           => true,
						'vars'                 => '--smiconsize',
						'suffix'               => 'px',
						'fallback'             => '0',
						'selector'             => $menu_selector . ' .caret svg, ' . $menu_selector . ' .caret img',
						'dispatchWindowResize' => true,
					],
				],
				'conditional_header'    => true,
			]
		);
	}

	/**
	 * Add customizer settings for submenu container.
	 *
	 * @param string $component_id Component id.
	 * @param string $section Section id.
	 */
	private function add_sm_layout_settings( $component_id, $section ) {
		$submenu_selector = '.builder-item--' . $component_id . ' .nav-ul .sub-menu';

		SettingsManager::get_instance()->add(
			[
				'id'                 => self::SM_LAYOUT_HEADING,
				'group'              => $component_id,
				'tab'                => SettingsManager::TAB_LAYOUT,
				'transport'          => 'postMessage',
				'sanitize_callback'  => 'sanitize_text_field',
				'label'              => __( 'Submenu Container', 'neve' ),
				'type'               => '\Neve\Customizer\Controls\React\Heading',
				'options'            => [
					'accordion'        => true,
					'controls_to_wrap' => 4,
					'expanded'         => true,
					'class'            => esc_attr( 'primary-nav-accordion-' . self::SM_LAYOUT_HEADING ),
				],
				'section'            => $section,
				'conditional_header' => true,
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                 => self::SM_ANIMATION,
				'group'              => $component_id,
				'tab'                => SettingsManager::TAB_LAYOUT,
				'transport'          => 'refresh',
				'sanitize_callback'  => 'sanitize_text_field',
				'default'            => '',
				'label'              => __( 'Animation', 'neve' ),
				'type'               => 'select',
				'options'            => [
					'choices' => [
						''           => __( 'Default', 'neve' ),
						'slide-down' => __( 'Slide down', 'neve' ),
						'slide-up'   => __( 'Slide up', 'neve' ),
						'fade'       => __( 'Fade', 'neve' ),
					],
				],
				'section'            => $section,
				'conditional_header' => true,
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                    => self::SM_CONTAINER_ALIGNMENT,
				'group'                 => $component_id,
				'tab'                   => SettingsManager::TAB_LAYOUT,
				'transport'             => 'post' . $component_id,
				'sanitize_callback'     => 'wp_filter_nohtml_kses',
				'conditional_header'    => true,
				'label'                 => __( 'Container Alignment', 'neve' ),
				'type'                  => '\Neve\Customizer\Controls\React\Radio_Buttons',
				'section'               => $section,
				'options'               => [
					'large_buttons' => true,
					'choices'       => [
						'0'    => [
							'tooltip' => __( 'Right', 'neve' ),
							'icon'    => 'editor-alignright',
						],
						'auto' => [
							'tooltip' => __( 'Left', 'neve' ),
							'icon'    => 'editor-alignleft',
						],
					],
				],
				'live_refresh_selector' => true,
				'live_refresh_css_prop' => [
					'cssVar' => [
						'vars'     => '--alignment',
						'selector' => $submenu_selector,
					],
				],
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                    => self::SM_CONTENT_ALIGNMENT,
				'group'                 => $component_id,
				'tab'                   => SettingsManager::TAB_LAYOUT,
				'transport'             => 'postMessage',
				'label'                 => __( 'Item Alignment', 'neve' ),
				'type'                  => '\Neve\Customizer\Controls\React\Responsive_Radio_Buttons',
				'section'               => $section,
				'options'               => [
					'choices' => [
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
					],
				],
				'conditional_header'    => true,
				'sanitize_callback'     => 'neve_sanitize_alignment',
				'live_refresh_selector' => true,
				'live_refresh_css_prop' => [
					'cssVar' => [
						'vars'       => [
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
						'selector'   => $submenu_selector,
					],
				],
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                    => self::SM_SPACING,
				'group'                 => $component_id,
				'tab'                   => SettingsManager::TAB_LAYOUT,
				'section'               => $section,
				'label'                 => __( 'Items Spacing', 'neve' ),
				'type'                  => 'Neve\Customizer\Controls\React\Responsive_Range',
				'transport'             => 'postMessage',
				'sanitize_callback'     => [ $this, 'sanitize_responsive_int_json' ],
				'options'               => [
					'input_attrs' => [
						'min'        => 0,
						'max'        => 100,
						'units'      => [ 'px', 'em', 'rem' ],
						'defaultVal' => [
							'mobile'  => 0,
							'tablet'  => 0,
							'desktop' => 0,
							'suffix'  => [
								'mobile'  => 'px',
								'tablet'  => 'px',
								'desktop' => 'px',
							],
						],
					],
				],
				'live_refresh_selector' => true,
				'live_refresh_css_prop' => [
					'cssVar' => [
						'responsive'           => true,
						'vars'                 => '--spacebetween',
						'suffix'               => 'px',
						'fallback'             => '0',
						'selector'             => $submenu_selector . ' li',
						'dispatchWindowResize' => true,
					],
				],
				'conditional_header'    => true,
			]
		);
	}

	/**
	 * Add customizer settings for submenu container style.
	 *
	 * @param string $component_id Component id.
	 * @param string $section Section id.
	 */
	private function add_sm_container_style_settings( $component_id, $section ) {

		$submenu_selector = '.builder-item--' . $component_id . ' .nav-ul .sub-menu';

		SettingsManager::get_instance()->add(
			[
				'id'                 => self::SM_CONTAINER_HEADING,
				'group'              => $component_id,
				'tab'                => SettingsManager::TAB_STYLE,
				'transport'          => 'postMessage',
				'sanitize_callback'  => 'sanitize_text_field',
				'label'              => __( 'Submenu Container', 'neve' ),
				'type'               => '\Neve\Customizer\Controls\React\Heading',
				'options'            => [
					'accordion'        => true,
					'controls_to_wrap' => 8,
					'expanded'         => false,
					'class'            => esc_attr( 'primary-nav-accordion-' . self::SM_CONTAINER_HEADING ),
				],
				'section'            => $section,
				'conditional_header' => true,
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                 => self::SM_CONTAINER_BORDER_HEADING,
				'group'              => $component_id,
				'tab'                => SettingsManager::TAB_STYLE,
				'transport'          => 'postMessage',
				'sanitize_callback'  => 'sanitize_text_field',
				'label'              => __( 'Border and Shadow', 'neve' ),
				'type'               => '\Neve\Customizer\Controls\React\Heading',
				'options'            => [
					'accordion' => false,
				],
				'section'            => $section,
				'conditional_header' => true,
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                 => self::SM_BORDER_STYLE,
				'group'              => $component_id,
				'tab'                => SettingsManager::TAB_STYLE,
				'transport'          => 'refresh',
				'sanitize_callback'  => 'sanitize_text_field',
				'default'            => 'none',
				'label'              => __( 'Border Style', 'neve' ),
				'type'               => 'select',
				'options'            => [
					'choices' => [
						'none'   => __( 'None', 'neve' ),
						'solid'  => __( 'Solid', 'neve' ),
						'dotted' => __( 'Dotted', 'neve' ),
						'dashed' => __( 'Dashed', 'neve' ),
						'double' => __( 'Double', 'neve' ),
						'groove' => __( 'Groove', 'neve' ),
						'ridge'  => __( 'Ridge', 'neve' ),
						'inset'  => __( 'Inset', 'neve' ),
						'outset' => __( 'Outset', 'neve' ),
					],
				],
				'section'            => $section,
				'conditional_header' => true,
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                    => self::SM_BORDER_RADIUS,
				'group'                 => $component_id,
				'tab'                   => SettingsManager::TAB_STYLE,
				'section'               => $section,
				'label'                 => __( 'Border Radius', 'neve' ),
				'type'                  => 'Neve\Customizer\Controls\React\Responsive_Range',
				'transport'             => 'postMessage',
				'sanitize_callback'     => [ $this, 'sanitize_responsive_int_json' ],
				'default'               => [
					'mobile'  => 0,
					'tablet'  => 0,
					'desktop' => 0,
					'suffix'  => [
						'mobile'  => 'px',
						'tablet'  => 'px',
						'desktop' => 'px',
					],
				],
				'options'               => [
					'input_attrs' => [
						'min'        => 0,
						'max'        => 100,
						'units'      => [ 'px', '%' ],
						'defaultVal' => [
							'mobile'  => 0,
							'tablet'  => 0,
							'desktop' => 0,
							'suffix'  => [
								'mobile'  => 'px',
								'tablet'  => 'px',
								'desktop' => 'px',
							],
						],
					],
				],
				'live_refresh_selector' => true,
				'live_refresh_css_prop' => [
					'cssVar' => [
						'responsive'           => true,
						'vars'                 => '--bradius',
						'suffix'               => 'px',
						'fallback'             => '0',
						'selector'             => $submenu_selector,
						'dispatchWindowResize' => true,
					],
				],
				'conditional_header'    => true,
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                    => self::SM_BORDER_WIDTH,
				'group'                 => $component_id,
				'tab'                   => SettingsManager::TAB_STYLE,
				'transport'             => 'postMessage',
				'sanitize_callback'     => [ $this, 'sanitize_spacing_array' ],
				'conditional_header'    => true,
				'options'               => [
					'input_attrs'     => [
						'min'   => 0,
						'max'   => 20,
						'units' => [ 'px' ],
					],
					'active_callback' => function() use ( $component_id ) {
						return get_theme_mod( $component_id . '_' . self::SM_BORDER_STYLE, 'none' ) !== 'none';
					},
				],
				'live_refresh_selector' => true,
				'live_refresh_css_prop' => [
					'cssVar'      => [
						'responsive' => true,
						'vars'       => '--bwidth',
						'suffix'     => 'px',
						'selector'   => $submenu_selector,
					],
					'responsive'  => true,
					'directional' => true,
					'template'    =>
						$submenu_selector . '{
							border-top-width: {{value.top}};
							border-right-width: {{value.right}};
							border-bottom-width: {{value.bottom}};
							border-left-width: {{value.left}};
						}',
				],
				'label'                 => __( 'Border Width', 'neve' ),
				'type'                  => '\Neve\Customizer\Controls\React\Spacing',
				'section'               => $section,
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                    => self::SM_BOX_SHADOW,
				'group'                 => $component_id,
				'tab'                   => SettingsManager::TAB_STYLE,
				'section'               => $section,
				'label'                 => __( 'Box Shadow', 'neve' ),
				'type'                  => 'Neve\Customizer\Controls\React\Responsive_Range',
				'transport'             => 'postMessage',
				'sanitize_callback'     => [ $this, 'sanitize_responsive_int_json' ],
				'options'               => [
					'input_attrs' => [
						'min'        => 0,
						'max'        => 5,
						'defaultVal' => [
							'mobile'  => 0,
							'tablet'  => 0,
							'desktop' => 0,
						],
					],
				],
				'live_refresh_selector' => true,
				'live_refresh_css_prop' => [
					'responsive' => true,
					'fallback'   => 0,
					'template'   =>
						$submenu_selector . ' {
                            box-shadow: 0 0 calc({{value}}px * 4) 0 rgba(0,0,0,calc(0.1 + 0.{{value}}))!important;
                        }',
				],
				'conditional_header'    => true,
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                 => self::SM_CONTAINER_COLORS_HEADING,
				'group'              => $component_id,
				'tab'                => SettingsManager::TAB_STYLE,
				'transport'          => 'postMessage',
				'sanitize_callback'  => 'sanitize_text_field',
				'label'              => __( 'Colors', 'neve' ),
				'type'               => '\Neve\Customizer\Controls\React\Heading',
				'options'            => [
					'accordion' => false,
				],
				'section'            => $section,
				'conditional_header' => true,
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                    => self::SM_BORDER_COLOR,
				'group'                 => $component_id,
				'tab'                   => SettingsManager::TAB_STYLE,
				'transport'             => 'postMessage',
				'sanitize_callback'     => 'neve_sanitize_colors',
				'label'                 => __( 'Border', 'neve' ),
				'type'                  => 'neve_color_control',
				'section'               => $section,
				'live_refresh_selector' => true,
				'options'               => [
					'active_callback' => function() use ( $component_id ) {
						return get_theme_mod( $component_id . '_' . self::SM_BORDER_STYLE, 'none' ) !== 'none';
					},
				],
				'live_refresh_css_prop' => [
					'cssVar' => [
						'vars'     => '--bcolor',
						'selector' => $submenu_selector,
					],
					[
						'selector' => $submenu_selector,
						'prop'     => 'border-color',
						'fallback' => 'inherit',
					],
				],
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                    => self::SM_BG_COLOR,
				'group'                 => $component_id,
				'tab'                   => SettingsManager::TAB_STYLE,
				'transport'             => 'postMessage',
				'sanitize_callback'     => 'neve_sanitize_colors',
				'label'                 => __( 'Background', 'neve' ),
				'type'                  => 'neve_color_control',
				'section'               => $section,
				'live_refresh_selector' => true,
				'live_refresh_css_prop' => [
					'cssVar' => [
						'vars'     => '--bgcolor',
						'selector' => $submenu_selector,
					],
					[
						'selector' => $submenu_selector,
						'prop'     => 'background',
						'fallback' => 'inherit',
					],
				],
			]
		);
	}

	/**
	 * Add customizer settings for submenu item style.
	 *
	 * @param string $component_id Component id.
	 * @param string $section Section id.
	 */
	private function add_sm_item_style_settings( $component_id, $section ) {

		$submenu_selector = '.builder-item--' . $component_id . ' .nav-ul .sub-menu';

		SettingsManager::get_instance()->add(
			[
				'id'                 => self::SM_ITEM_HEADING,
				'group'              => $component_id,
				'tab'                => SettingsManager::TAB_STYLE,
				'transport'          => 'postMessage',
				'sanitize_callback'  => 'sanitize_text_field',
				'label'              => __( 'Submenu Item', 'neve' ),
				'type'               => '\Neve\Customizer\Controls\React\Heading',
				'options'            => [
					'accordion'        => true,
					'controls_to_wrap' => 13,
					'expanded'         => false,
					'class'            => esc_attr( 'primary-nav-accordion-' . self::SM_ITEM_HEADING ),
				],
				'section'            => $section,
				'conditional_header' => true,
			]
		);

		$menu_style = Mods::get( $component_id . '_style', 'style-plain' );
		SettingsManager::get_instance()->add(
			[
				'id'                 => self::SM_HOVER_SKIN,
				'group'              => $component_id,
				'tab'                => SettingsManager::TAB_STYLE,
				'transport'          => 'refresh',
				'sanitize_callback'  => 'wp_filter_nohtml_kses',
				'conditional_header' => true,
				'default'            => $menu_style,
				'label'              => __( 'Hover Skin Mode', 'neve' ),
				'type'               => '\Neve\Customizer\Controls\React\Radio_Buttons',
				'section'            => $section,
				'options'            => [
					'large_buttons' => true,
					'is_for'        => 'menu',
				],
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                 => self::SM_ITEM_BORDER_HEADING,
				'group'              => $component_id,
				'tab'                => SettingsManager::TAB_STYLE,
				'transport'          => 'postMessage',
				'sanitize_callback'  => 'sanitize_text_field',
				'label'              => __( 'Border', 'neve' ),
				'type'               => '\Neve\Customizer\Controls\React\Heading',
				'options'            => [
					'accordion' => false,
				],
				'section'            => $section,
				'conditional_header' => true,
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                 => self::SM_ITEM_BORDER_STYLE,
				'group'              => $component_id,
				'tab'                => SettingsManager::TAB_STYLE,
				'transport'          => 'refresh',
				'sanitize_callback'  => 'sanitize_text_field',
				'default'            => 'none',
				'label'              => __( 'Border Style', 'neve' ),
				'type'               => 'select',
				'options'            => [
					'choices' => [
						'none'   => __( 'None', 'neve' ),
						'solid'  => __( 'Solid', 'neve' ),
						'dotted' => __( 'Dotted', 'neve' ),
						'dashed' => __( 'Dashed', 'neve' ),
						'double' => __( 'Double', 'neve' ),
						'groove' => __( 'Groove', 'neve' ),
						'ridge'  => __( 'Ridge', 'neve' ),
						'inset'  => __( 'Inset', 'neve' ),
						'outset' => __( 'Outset', 'neve' ),
					],
				],
				'section'            => $section,
				'conditional_header' => true,
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                    => self::SM_ITEM_BORDER_RADIUS,
				'group'                 => $component_id,
				'tab'                   => SettingsManager::TAB_STYLE,
				'section'               => $section,
				'label'                 => __( 'Border Radius', 'neve' ),
				'type'                  => 'Neve\Customizer\Controls\React\Responsive_Range',
				'transport'             => 'postMessage',
				'sanitize_callback'     => [ $this, 'sanitize_responsive_int_json' ],
				'default'               => [
					'mobile'  => 0,
					'tablet'  => 0,
					'desktop' => 0,
					'suffix'  => [
						'mobile'  => 'px',
						'tablet'  => 'px',
						'desktop' => 'px',
					],
				],
				'options'               => [
					'input_attrs' => [
						'min'        => 0,
						'max'        => 100,
						'units'      => [ 'px', '%' ],
						'defaultVal' => [
							'mobile'  => 0,
							'tablet'  => 0,
							'desktop' => 0,
						],
					],
				],
				'live_refresh_selector' => true,
				'live_refresh_css_prop' => [
					'cssVar' => [
						'responsive'           => true,
						'vars'                 => '--itembradius',
						'suffix'               => 'px',
						'fallback'             => '0',
						'selector'             => $submenu_selector,
						'dispatchWindowResize' => true,
					],
				],
				'conditional_header'    => true,
			]
		);

		SettingsManager::get_instance()->add(
			[

				'id'                    => self::SM_ITEM_BORDER_WIDTH,
				'group'                 => $component_id,
				'tab'                   => SettingsManager::TAB_STYLE,
				'transport'             => 'postMessage',
				'sanitize_callback'     => [ $this, 'sanitize_spacing_array' ],
				'conditional_header'    => true,
				'options'               => [
					'input_attrs'     => [
						'min'   => 0,
						'max'   => 20,
						'units' => [ 'px' ],
					],
					'active_callback' => function() use ( $component_id ) {
						return get_theme_mod( $component_id . '_' . self::SM_ITEM_BORDER_STYLE, 'none' ) !== 'none';
					},
				],
				'live_refresh_selector' => true,
				'live_refresh_css_prop' => [
					'cssVar'      => [
						'responsive' => true,
						'vars'       => '--itembwidth',
						'suffix'     => 'px',
						'selector'   => $submenu_selector,
					],
					'responsive'  => true,
					'directional' => true,
					'template'    =>
						$submenu_selector . ' li {
							border-top-width: {{value.top}};
							border-right-width: {{value.right}};
							border-bottom-width: {{value.bottom}};
							border-left-width: {{value.left}};
						}',
				],
				'label'                 => __( 'Item Border Width', 'neve' ),
				'type'                  => '\Neve\Customizer\Controls\React\Spacing',
				'section'               => $section,
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                 => self::SM_ITEM_COLORS_HEADING,
				'group'              => $component_id,
				'tab'                => SettingsManager::TAB_STYLE,
				'transport'          => 'postMessage',
				'sanitize_callback'  => 'sanitize_text_field',
				'label'              => __( 'Colors', 'neve' ),
				'type'               => '\Neve\Customizer\Controls\React\Heading',
				'options'            => [
					'accordion' => false,
				],
				'section'            => $section,
				'conditional_header' => true,
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                    => self::SM_ITEM_BORDER_COLOR,
				'group'                 => $component_id,
				'tab'                   => SettingsManager::TAB_STYLE,
				'transport'             => 'postMessage',
				'sanitize_callback'     => 'neve_sanitize_colors',
				'label'                 => __( 'Item Border', 'neve' ),
				'type'                  => 'neve_color_control',
				'section'               => $section,
				'options'               => [
					'active_callback' => function() use ( $component_id ) {
						return get_theme_mod( $component_id . '_' . self::SM_ITEM_BORDER_STYLE, 'none' ) !== 'none';
					},
				],
				'live_refresh_selector' => true,
				'live_refresh_css_prop' => [
					'cssVar' => [
						'vars'     => '--itembcolor',
						'selector' => $submenu_selector,
					],
					[
						'selector' => $submenu_selector . ' li',
						'prop'     => 'border-color',
						'fallback' => 'inherit',
					],
				],
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                    => self::SM_ITEM_BORDER_COLOR_HOVER,
				'group'                 => $component_id,
				'tab'                   => SettingsManager::TAB_STYLE,
				'transport'             => 'postMessage',
				'sanitize_callback'     => 'neve_sanitize_colors',
				'label'                 => __( 'Item Border Hover', 'neve' ),
				'type'                  => 'neve_color_control',
				'section'               => $section,
				'options'               => [
					'active_callback' => function() use ( $component_id ) {
						return get_theme_mod( $component_id . '_' . self::SM_ITEM_BORDER_STYLE, 'none' ) !== 'none';
					},
				],
				'live_refresh_selector' => true,
				'live_refresh_css_prop' => [
					'cssVar' => [
						'vars'     => '--itembcolorhover',
						'selector' => $submenu_selector,
					],
					[
						'selector' => $submenu_selector . ' li:hover',
						'prop'     => 'border-color',
						'fallback' => 'inherit',
					],
				],
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                    => self::SM_ITEM_BG_COLOR,
				'group'                 => $component_id,
				'tab'                   => SettingsManager::TAB_STYLE,
				'transport'             => 'postMessage',
				'sanitize_callback'     => 'neve_sanitize_colors',
				'label'                 => __( 'Item Background', 'neve' ),
				'type'                  => 'neve_color_control',
				'section'               => $section,
				'live_refresh_selector' => true,
				'live_refresh_css_prop' => [
					'cssVar' => [
						'vars'     => '--itembgcolor',
						'selector' => $submenu_selector,
					],
					[
						'selector' => $submenu_selector . ' li > a',
						'prop'     => 'background',
						'fallback' => 'inherit',
					],
				],
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                    => self::SM_ITEM_BG_COLOR_HOVER,
				'group'                 => $component_id,
				'tab'                   => SettingsManager::TAB_STYLE,
				'transport'             => 'postMessage',
				'sanitize_callback'     => 'neve_sanitize_colors',
				'label'                 => __( 'Item Background Hover', 'neve' ),
				'type'                  => 'neve_color_control',
				'section'               => $section,
				'live_refresh_selector' => true,
				'live_refresh_css_prop' => [
					'cssVar' => [
						'vars'     => '--itembgcolorhover',
						'selector' => $submenu_selector,
					],
					[
						'selector' => $submenu_selector . ' li > a:hover',
						'prop'     => 'background',
						'fallback' => 'inherit',
					],
				],
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                    => self::SM_ITEM_COLOR,
				'group'                 => $component_id,
				'tab'                   => SettingsManager::TAB_STYLE,
				'transport'             => 'postMessage',
				'sanitize_callback'     => 'neve_sanitize_colors',
				'label'                 => __( 'Item Text', 'neve' ),
				'type'                  => 'neve_color_control',
				'section'               => $section,
				'live_refresh_selector' => true,
				'live_refresh_css_prop' => [
					'cssVar' => [
						'vars'     => '--color',
						'selector' => $submenu_selector,
					],
					[
						'selector' => $submenu_selector . ' li > a',
						'prop'     => 'color',
						'fallback' => 'inherit',
					],
				],
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                    => self::SM_ITEM_COLOR_HOVER,
				'group'                 => $component_id,
				'tab'                   => SettingsManager::TAB_STYLE,
				'transport'             => 'postMessage',
				'sanitize_callback'     => 'neve_sanitize_colors',
				'label'                 => __( 'Item Text Hover', 'neve' ),
				'type'                  => 'neve_color_control',
				'section'               => $section,
				'live_refresh_selector' => true,
				'live_refresh_css_prop' => [
					'cssVar' => [
						'vars'     => '--hovercolor',
						'selector' => $submenu_selector,
					],
					[
						'selector' => $submenu_selector . ' li > a:hover',
						'prop'     => 'color',
						'fallback' => 'inherit',
					],
				],
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                    => self::SM_HOVER_TEXT_COLOR,
				'group'                 => $component_id,
				'tab'                   => SettingsManager::TAB_STYLE,
				'transport'             => 'postMessage',
				'sanitize_callback'     => 'neve_sanitize_colors',
				'label'                 => __( 'Hover Skin Mode Color', 'neve' ),
				'type'                  => 'neve_color_control',
				'section'               => $section,
				'live_refresh_selector' => true,
				'live_refresh_css_prop' => [
					'cssVar' => [
						'vars'     => '--hovertextcolor',
						'selector' => $submenu_selector,
					],
				],
				'options'               => [
					'active_callback' => function() use ( $component_id ) {
						$menu_style = get_theme_mod( $component_id . '_style', 'style-plain' );
						return get_theme_mod( $component_id . '_' . self::SM_HOVER_SKIN, $menu_style ) === 'style-full-height';
					},
				],
			]
		);

	}

	/**
	 * Get the default typography for submenus.
	 *
	 * @param string $component_id Component id.
	 *
	 * @return array
	 */
	private function get_submenu_typography_default( $component_id ) {
		$default_typography                     = SettingsManager::get_instance()->get_default( $component_id . '_component_typeface' );
		$default_submenu_typography             = SettingsManager::get_instance()->get( $component_id . '_component_typeface', $default_typography );
		$default_submenu_typography['fontSize'] = [
			'suffix'  => [
				'mobile'  => 'em',
				'tablet'  => 'em',
				'desktop' => 'em',
			],
			'mobile'  => '1',
			'tablet'  => '1',
			'desktop' => '1',
		];
		return $default_submenu_typography;
	}

	/**
	 * Add customizer settings for submenu typography.
	 *
	 * @param string $component_id Component id.
	 * @param string $section Section id.
	 */
	private function add_sm_typography_settings( $component_id, $section ) {

		$submenu_selector = '.builder-item--' . $component_id . ' .nav-ul .sub-menu';

		SettingsManager::get_instance()->add(
			[
				'id'                 => self::SM_TYPOGRAPHY_HEADING,
				'group'              => $component_id,
				'tab'                => SettingsManager::TAB_STYLE,
				'transport'          => 'postMessage',
				'sanitize_callback'  => 'sanitize_text_field',
				'label'              => __( 'Submenu Typography', 'neve' ),
				'type'               => '\Neve\Customizer\Controls\React\Heading',
				'options'            => [
					'accordion'        => true,
					'controls_to_wrap' => 2,
					'expanded'         => false,
					'class'            => esc_attr( 'primary-nav-accordion-' . self::SM_TYPOGRAPHY_HEADING ),
				],
				'section'            => $section,
				'conditional_header' => true,
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                    => self::SM_FONT_FAMILY,
				'group'                 => $component_id,
				'tab'                   => SettingsManager::TAB_STYLE,
				'transport'             => 'postMessage',
				'type'                  => '\Neve\Customizer\Controls\React\Font_Family',
				'sanitize_callback'     => 'sanitize_text_field',
				'live_refresh_selector' => true,
				'live_refresh_css_prop' => array(
					'cssVar' => [
						'vars'     => '--fontfamily',
						'selector' => $submenu_selector,
					],
				),
				'section'               => $section,
				'options'               => [
					'input_attrs' => [
						'default_is_inherit' => true,
					],
				],
			]
		);


		$default_value = $this->get_submenu_typography_default( $component_id );
		SettingsManager::get_instance()->add(
			[
				'id'                    => self::SM_TYPEFACE,
				'group'                 => $component_id,
				'tab'                   => SettingsManager::TAB_STYLE,
				'transport'             => 'postMessage',
				'type'                  => '\Neve\Customizer\Controls\React\Typography',
				'live_refresh_selector' => true,
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
						'selector' => $submenu_selector,
					],
				],
				'section'               => $section,
				'default'               => $default_value,
				'sanitize_callback'     => 'neve_sanitize_typography_control',
				'options'               => [
					'input_attrs'         => [
						'size_units'             => [ 'em', 'px', 'rem' ],
						'weight_default'         => $default_value['fontWeight'],
						'size_default'           => $default_value['fontSize'],
						'line_height_default'    => $default_value['lineHeight'],
						'letter_spacing_default' => $default_value['letterSpacing'],
					],
					'font_family_control' => $component_id . '_' . self::SM_FONT_FAMILY,
				],
			]
		);
	}
}
