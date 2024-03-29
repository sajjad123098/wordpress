<?php
/**
 * Advance Search Form component class for Header Footer Grid.
 *
 * @version 2.4.0
 * @package HFG
 */
namespace Neve_Pro\Modules\Header_Footer_Grid\Components;

use HFG\Core\Settings\Manager as SettingsManager;
use HFG\Main;
use Neve\Core\Settings\Config;
use Neve\Core\Styles\Dynamic_Selector;

/**
 * Class Advanced_Search_Field
 *
 * @package Neve_Pro\Modules\Header_Footer_Grid\Components
 */
class Advanced_Search_Form extends Advanced_Search_Core {
	const COMPONENT_ID = 'advanced_search_form';

	/**
	 * The minimum value of some customizer controls is 0 to able to allow usability relative to CSS units.
	 * That can be removed after the https://github.com/Codeinwp/neve/issues/3609 issue is handled.
	 *
	 * That is defined here against the usage of old Neve versions, Base_Customizer class of the stable Neve version already has the RELATIVE_CSS_UNIT_SUPPORTED_MIN_VALUE constant.
	 */
	const RELATIVE_CSS_UNIT_SUPPORTED_MIN_VALUE = 0;

	/**
	 * Has support for the text based button?
	 *
	 * @var bool
	 */
	protected $has_textbutton_support = true;

	/**
	 * Component label
	 *
	 * @since   2.4.0
	 * @access  protected
	 * @var string
	 */
	protected $label;

	/**
	 * Holds the component icon.
	 *
	 * @since   2.4.0
	 * @access  protected
	 * @var string
	 */
	protected $icon = 'code-standards';

	/**
	 * Holds the instance count.
	 *
	 * @since   2.4.0
	 * @access  protected
	 * @var int
	 */
	protected static $instance_count = 0;

	/**
	 * The maximum allowed instances of this class.
	 * This refers to the global scope, across all builders.
	 *
	 * @since   2.4.0
	 * @access  protected
	 * @var int
	 */
	protected $max_instance = 2;

	/**
	 * Advanced_Search_Form constructor.
	 *
	 * @param string $panel Builder panel.
	 *
	 * @since   2.4.0
	 * @access  public
	 */
	public function __construct( $panel ) {
		$this->label = __( 'Advanced Search Form', 'neve' );
		self::$instance_count ++;
		$this->instance_number = self::$instance_count;
		parent::__construct( $panel );
		$this->set_property( 'section', $this->get_class_const( 'COMPONENT_ID' ) );
	}

	/**
	 * Adds settings
	 *
	 * @since   2.4.0
	 * @access  public
	 */
	public function add_settings() {

		SettingsManager::get_instance()->add(
			[
				'id'                 => self::PLACEHOLDER_ID,
				'group'              => $this->get_class_const( 'COMPONENT_ID' ),
				'tab'                => SettingsManager::TAB_GENERAL,
				'transport'          => 'post' . $this->get_class_const( 'COMPONENT_ID' ),
				'sanitize_callback'  => 'wp_filter_nohtml_kses',
				'default'            => __( 'Search for...', 'neve' ),
				'label'              => __( 'Placeholder', 'neve' ),
				'type'               => 'text',
				'section'            => $this->section,
				'conditional_header' => true,
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                    => self::FIELD_HEIGHT,
				'group'                 => $this->get_class_const( 'COMPONENT_ID' ),
				'tab'                   => SettingsManager::TAB_STYLE,
				'section'               => $this->section,
				'label'                 => __( 'Height', 'neve' ),
				'type'                  => '\Neve\Customizer\Controls\React\Responsive_Range',
				'live_refresh_selector' => true,
				'live_refresh_css_prop' => [
					'cssVar'     => [
						'responsive' => true,
						'vars'       => '--height',
						'suffix'     => 'px',
						'selector'   => '.builder-item--' . $this->get_id(),
					],
					'responsive' => true,
					'template'   =>
						'body ' . $this->default_selector . ' input[type=search] {
							height: {{value}}px;
						}',
				],
				'options'               => [
					'input_attrs' => [
						'step'       => 1,
						'min'        => self::RELATIVE_CSS_UNIT_SUPPORTED_MIN_VALUE,
						'max'        => 200,
						'defaultVal' => [
							'mobile'  => 40,
							'tablet'  => 40,
							'desktop' => 40,
							'suffix'  => [
								'mobile'  => 'px',
								'tablet'  => 'px',
								'desktop' => 'px',
							],
						],
						'units'      => [ 'px', 'em', 'rem' ],
					],
				],
				'transport'             => 'postMessage',
				'sanitize_callback'     => array( $this, 'sanitize_responsive_int_json' ),
				'default'               => [
					'mobile'  => 40,
					'tablet'  => 40,
					'desktop' => 40,
					'suffix'  => [
						'mobile'  => 'px',
						'tablet'  => 'px',
						'desktop' => 'px',
					],
				],
				'conditional_header'    => true,
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                    => self::FIELD_FONT_SIZE,
				'group'                 => $this->get_class_const( 'COMPONENT_ID' ),
				'tab'                   => SettingsManager::TAB_STYLE,
				'section'               => $this->section,
				'label'                 => __( 'Font Size', 'neve' ),
				'type'                  => '\Neve\Customizer\Controls\React\Responsive_Range',
				'live_refresh_selector' => true,
				'live_refresh_css_prop' => [
					'cssVar'     => [
						'responsive' => true,
						'vars'       => self::supports_customizable_iconbutton() ? [
							'--formfieldfontsize',
							'--btnfs',
						] : '--formfieldfontsize',
						'suffix'     => 'px',
						'selector'   => '.builder-item--' . $this->get_id(),
					],
					'responsive' => true,
					'template'   =>
						'body ' . $this->default_selector . ' input[type=search] {
							font-size: {{value}}px;
							padding-right: calc({{value}}px + 5px);
						}
						body ' . $this->default_selector . ' .nv-search-icon-wrap .nv-icon svg {
							width: {{value}}px;
							height: {{value}}px;
						}
						body ' . $this->default_selector . ' .search-form input[type=submit], body ' . $this->default_selector . ' .search-form .nv-search-icon-wrap {
							width: {{value}}px;
						}',
				],
				'options'               => [
					'input_attrs' => [
						'step'       => 1,
						'min'        => self::RELATIVE_CSS_UNIT_SUPPORTED_MIN_VALUE,
						'max'        => 200,
						'suffix'     => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
						'defaultVal' => [
							'mobile'  => 14,
							'tablet'  => 14,
							'desktop' => 14,
							'suffix'  => [
								'mobile'  => 'px',
								'tablet'  => 'px',
								'desktop' => 'px',
							],
						],
						'units'      => [ 'px' ],
					],
				],
				'transport'             => 'postMessage',
				'sanitize_callback'     => array( $this, 'sanitize_responsive_int_json' ),
				'default'               => [
					'mobile'  => 14,
					'tablet'  => 14,
					'desktop' => 14,
					'suffix'  => [
						'mobile'  => 'px',
						'tablet'  => 'px',
						'desktop' => 'px',
					],
				],
				'conditional_header'    => true,
			]
		);

		$per_device           = [
			'top'    => 2,
			'right'  => 2,
			'bottom' => 2,
			'left'   => 2,
		];
		$default_border_width = [
			'desktop-unit' => 'px',
			'tablet-unit'  => 'px',
			'mobile-unit'  => 'px',
			'desktop'      => $per_device,
			'tablet'       => $per_device,
			'mobile'       => $per_device,
		];

		SettingsManager::get_instance()->add(
			[
				'id'                    => self::FIELD_BORDER_WIDTH,
				'group'                 => $this->get_id(),
				'tab'                   => SettingsManager::TAB_STYLE,
				'transport'             => 'postMessage',
				'sanitize_callback'     => array( $this, 'sanitize_spacing_array' ),
				'default'               => $default_border_width,
				'label'                 => __( 'Border Width', 'neve' ),
				'type'                  => '\Neve\Customizer\Controls\React\Spacing',
				'options'               => [
					'input_attrs' => array(
						'min'   => 0,
						'max'   => 20,
						'units' => [ 'px' ],
					),
					'default'     => $default_border_width,
				],
				'live_refresh_selector' => true,
				'live_refresh_css_prop' => [
					'cssVar'      => [
						'responsive' => true,
						'vars'       => '--formfieldborderwidth',
						'suffix'     => 'px',
						'selector'   => '.builder-item--' . $this->get_id(),
					],
					'responsive'  => true,
					'directional' => true,
					'template'    =>
						'body ' . $this->default_selector . ' input[type=search] {
							border-top-width: {{value.top}};
							border-right-width: {{value.right}};
							border-bottom-width: {{value.bottom}};
							border-left-width: {{value.left}};
						}',
				],
				'section'               => $this->section,
				'conditional_header'    => true,
			]
		);
		$default_border_radius = [
			'desktop-unit' => 'px',
			'tablet-unit'  => 'px',
			'mobile-unit'  => 'px',
			'desktop'      => [
				'top'    => 3,
				'right'  => 3,
				'bottom' => 3,
				'left'   => 3,
			],
			'tablet'       => [
				'top'    => 3,
				'right'  => 3,
				'bottom' => 3,
				'left'   => 3,
			],
			'mobile'       => [
				'top'    => 3,
				'right'  => 3,
				'bottom' => 3,
				'left'   => 3,
			],
		];
		SettingsManager::get_instance()->add(
			[
				'id'                    => self::FIELD_BORDER_RADIUS,
				'group'                 => $this->get_id(),
				'tab'                   => SettingsManager::TAB_STYLE,
				'transport'             => 'postMessage',
				'sanitize_callback'     => array( $this, 'sanitize_spacing_array' ),
				'default'               => $default_border_width,
				'label'                 => __( 'Border Radius', 'neve' ),
				'type'                  => '\Neve\Customizer\Controls\React\Spacing',
				'options'               => [
					'input_attrs' => array(
						'min'   => 0,
						'max'   => 100,
						'units' => [ 'px' ],
					),
					'default'     => $default_border_radius,
				],
				'live_refresh_selector' => true,
				'live_refresh_css_prop' => [
					'cssVar'      => [
						'responsive' => true,
						'vars'       => '--formfieldborderradius',
						'suffix'     => 'px',
						'selector'   => '.builder-item--' . $this->get_id(),
					],
					'responsive'  => true,
					'directional' => true,
					'template'    =>
						'body ' . $this->default_selector . ' input[type=search] {
							border-top-left-radius: {{value.top}};
							border-top-right-radius: {{value.right}};
							border-bottom-right-radius: {{value.bottom}};
							border-bottom-left-radius: {{value.left}};
						}',
				],
				'section'               => $this->section,
				'conditional_header'    => true,
			]
		);
		SettingsManager::get_instance()->add(
			[
				'id'                    => self::FIELD_BG,
				'group'                 => $this->get_class_const( 'COMPONENT_ID' ),
				'tab'                   => SettingsManager::TAB_STYLE,
				'transport'             => 'postMessage',
				'sanitize_callback'     => 'neve_sanitize_colors',
				'label'                 => __( 'Background Color', 'neve' ),
				'type'                  => '\Neve\Customizer\Controls\React\Color',
				'section'               => $this->section,
				'options'               => [
					'input_attrs' => [
						'allow_gradient' => true,
					],
				],
				'live_refresh_selector' => true,
				'live_refresh_css_prop' => [
					'cssVar'   => [
						'vars'     => '--formfieldbgcolor',
						'selector' => '.builder-item--' . $this->get_id(),
					],
					'template' =>
						'body ' . $this->default_selector . ' input[type=search] {
							background: {{value}} !important;
						}',
					'fallback' => '#ffffff',
				],
				'conditional_header'    => true,
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                    => self::FIELD_TEXT_COLOR,
				'group'                 => $this->get_class_const( 'COMPONENT_ID' ),
				'tab'                   => SettingsManager::TAB_STYLE,
				'transport'             => 'postMessage',
				'sanitize_callback'     => 'neve_sanitize_colors',
				'label'                 => __( 'Text and Border', 'neve' ),
				'type'                  => 'neve_color_control',
				'section'               => $this->section,
				'live_refresh_selector' => true,
				'live_refresh_css_prop' => [
					'cssVar'   => [
						'vars'     => [
							'--formfieldcolor',
							'--formfieldbordercolor',
						],
						'selector' => '.builder-item--' . $this->get_id(),
					],
					'template' =>
						'body ' . $this->default_selector . ' input[type=search], body ' . $this->default_selector . ' input::placeholder {
							color: {{value}};
						}
						body ' . $this->default_selector . ' input[type=search] {
							border-color: {{value}};
						}
						body ' . $this->default_selector . ' .nv-search-icon-wrap .nv-icon svg {
							fill: {{value}};
						}',
				],
				'conditional_header'    => true,
			]
		);

		parent::add_settings();
	}

	/**
	 * Method to add Component css styles.
	 *
	 * @param array $css_array An array containing css rules.
	 *
	 * @since   2.4.0
	 * @access  public
	 * @return array
	 */
	public function add_style( array $css_array = array() ) {
		$rules = [
			'--height'                => [
				Dynamic_Selector::META_KEY           => $this->get_id() . '_' . self::FIELD_HEIGHT,
				Dynamic_Selector::META_IS_RESPONSIVE => true,
				Dynamic_Selector::META_SUFFIX        => 'responsive_suffix',
				Dynamic_Selector::META_DEFAULT       => SettingsManager::get_instance()->get_default( $this->get_id() . '_' . self::FIELD_HEIGHT ),
			],
			'--formfieldfontsize'     => [
				Dynamic_Selector::META_KEY           => $this->get_id() . '_' . self::FIELD_FONT_SIZE,
				Dynamic_Selector::META_IS_RESPONSIVE => true,
				Dynamic_Selector::META_SUFFIX        => 'responsive_suffix',
			],
			'--formfieldborderwidth'  => [
				Dynamic_Selector::META_IS_RESPONSIVE => true,
				Dynamic_Selector::META_KEY           => $this->get_id() . '_' . self::FIELD_BORDER_WIDTH,
				Dynamic_Selector::META_SUFFIX        => 'px',
				Dynamic_Selector::META_DEFAULT       => SettingsManager::get_instance()->get_default( $this->get_id() . '_' . self::FIELD_BORDER_WIDTH ),
				'directional-prop'                   => Config::CSS_PROP_BORDER_WIDTH,
			],
			'--formfieldborderradius' => [
				Dynamic_Selector::META_KEY           => $this->get_id() . '_' . self::FIELD_BORDER_RADIUS,
				Dynamic_Selector::META_IS_RESPONSIVE => true,
				Dynamic_Selector::META_DEFAULT       => SettingsManager::get_instance()->get_default( $this->get_id() . '_' . self::FIELD_BORDER_RADIUS ),
				'directional-prop'                   => Config::CSS_PROP_BORDER_RADIUS,
			],
			'--formfieldbgcolor'      => [
				Dynamic_Selector::META_KEY     => $this->get_id() . '_' . self::FIELD_BG,
				Dynamic_Selector::META_DEFAULT => SettingsManager::get_instance()->get_default( $this->get_id() . '_' . self::FIELD_BG ),
			],
			'--formfieldbordercolor'  => [
				Dynamic_Selector::META_KEY     => $this->get_id() . '_' . self::FIELD_TEXT_COLOR,
				Dynamic_Selector::META_DEFAULT => SettingsManager::get_instance()->get_default( $this->get_id() . '_' . self::FIELD_TEXT_COLOR ),
			],
			'--formfieldcolor'        => [
				Dynamic_Selector::META_KEY     => $this->get_id() . '_' . self::FIELD_TEXT_COLOR,
				Dynamic_Selector::META_DEFAULT => SettingsManager::get_instance()->get_default( $this->get_id() . '_' . self::FIELD_TEXT_COLOR ),
			],
		];

		if ( self::supports_customizable_iconbutton() ) {
			// If button mode is enabled, get inherit the input font size for button font size. (That's here instead of hard-coded css to avoid size limit being exceeded)
			if ( $this->search_icon_button_instance->is_button_mode_enabled() ) {
				$rules['--btnfs'] = $rules['--formfieldfontsize'];
			}
		}

		$css_array[] = [
			Dynamic_Selector::KEY_SELECTOR => '.builder-item--' . $this->get_id(),
			Dynamic_Selector::KEY_RULES    => $rules,
		];


		return parent::add_style( $css_array );
	}

	/**
	 * Load search template
	 *
	 * @since   2.4.0
	 * @access  public
	 */
	protected function load_template() {
		add_filter( 'nv_search_placeholder', [ $this, 'change_placeholder' ] );
		Main::get_instance()->load( 'component-advanced-search-field' );
		remove_filter( 'nv_search_placeholder', [ $this, 'change_placeholder' ] );
	}

	/**
	 * Change the form placeholder.
	 *
	 * @param string $placeholder placeholder string.
	 *
	 * @since   2.4.0
	 * @access  public
	 * @return string
	 */
	public function change_placeholder( $placeholder ) {
		return get_theme_mod( $this->get_id() . '_' . self::PLACEHOLDER_ID, __( 'Search for...', 'neve' ) );
	}
}
