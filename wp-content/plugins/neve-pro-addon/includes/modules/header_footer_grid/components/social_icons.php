<?php
/**
 * Button Component class for Header Footer Grid.
 *
 * Name:    Header Footer Grid
 * Author:  Bogdan Preda <bogdan.preda@themeisle.com>
 *
 * @version 1.0.0
 * @package HFG
 */

namespace Neve_Pro\Modules\Header_Footer_Grid\Components;

use HFG\Core\Components\Abstract_Component;
use HFG\Core\Settings\Manager as SettingsManager;
use HFG\Main;
use Neve_Pro\Core\Loader;
use Neve_Pro\Traits\Core;
use Neve_Pro\Traits\Sanitize_Functions;

/**
 * Class Social_Icons
 *
 * @package Neve_Pro\Modules\Header_Footer_Grid\Components
 */
class Social_Icons extends Abstract_Component {
	use Sanitize_Functions;
	use Core;

	/**
	 * The minimum value of some customizer controls is 0 to able to allow usability relative to CSS units.
	 * That can be removed after the https://github.com/Codeinwp/neve/issues/3609 issue is handled.
	 *
	 * That is defined here against the usage of old Neve versions, Base_Customizer class of the stable Neve version already has the RELATIVE_CSS_UNIT_SUPPORTED_MIN_VALUE constant.
	 */
	const RELATIVE_CSS_UNIT_SUPPORTED_MIN_VALUE = 0;

	/**
	 * Holds the instance count.
	 * Starts at 1 since the base component is not altered.
	 *
	 * @since   1.0.0
	 * @access  protected
	 * @var int
	 */
	protected static $instance_count = 0;
	/**
	 * Holds the current instance count.
	 *
	 * @since   1.0.0
	 * @access  protected
	 * @var int
	 */
	protected $instance_number;
	/**
	 * The maximum allowed instances of this class.
	 * This refers to the global scope, across all builders.
	 *
	 * @since   1.0.0
	 * @access  protected
	 * @var int
	 */
	protected $max_instance = 2;
	/**
	 * The default value of icon size.
	 *
	 * @since   2.4.0
	 * @access  protected
	 * @var array
	 */
	protected $icon_size_default;
	/**
	 * The default value of icon spacing.
	 *
	 * @since   2.4.0
	 * @access  protected
	 * @var array
	 */
	protected $icon_spacing_default;
	/**
	 * The default value of border radius.
	 *
	 * @since   2.4.0
	 * @access  protected
	 * @var array
	 */
	protected $icon_border_radius_default;

	const COMPONENT_ID             = 'social_icons';
	const REPEATER_ID              = 'content_setting';
	const NEW_TAB                  = 'new_tab';
	const ICON_SIZE                = 'icon_size';
	const ICON_SIZE_RESPONSIVE     = 'icon_size_responsive';
	const ICON_SPACING             = 'icon_spacing';
	const ICON_SPACING_RESPONSIVE  = 'icon_spacing_responsive';
	const ICON_PADDING             = 'icon_padding';
	const BORDER_RADIUS            = 'border_radius';
	const BORDER_RADIUS_RESPONSIVE = 'border_radius_responsive';
	/**
	 * Repeater defaults
	 *
	 * @since   1.0.0
	 * @access  private
	 * @var array
	 */
	private $repeater_default = array(
		array(
			'title'            => 'Facebook',
			'url'              => '#',
			'icon'             => 'facebook',
			'visibility'       => 'yes',
			'icon_color'       => '#fff',
			'background_color' => '#3b5998',
		),
		array(
			'title'            => 'X',
			'url'              => '#',
			'icon'             => 'twitter',
			'visibility'       => 'yes',
			'icon_color'       => '#fff',
			'background_color' => '#1da1f2',
		),
		array(
			'title'            => 'Youtube',
			'url'              => '#',
			'icon'             => 'youtube-play',
			'visibility'       => 'yes',
			'icon_color'       => '#fff',
			'background_color' => '#cd201f',
		),
		array(
			'title'            => 'Instagram',
			'url'              => '#',
			'icon'             => 'instagram',
			'visibility'       => 'yes',
			'icon_color'       => '#fff',
			'background_color' => '#e1306c',
		),
	);

	/**
	 * Social_Icons constructor.
	 *
	 * @param string $panel Builder panel.
	 */
	public function __construct( $panel ) {
		self::$instance_count ++;
		$this->instance_number = self::$instance_count;
		parent::__construct( $panel );
		$this->set_property( 'section', $this->get_class_const( 'COMPONENT_ID' ) );

		$icon_size               = SettingsManager::get_instance()->get( $this->get_id() . '_' . self::ICON_SIZE, 18 );
		$this->icon_size_default = [
			'mobile'  => $icon_size,
			'tablet'  => $icon_size,
			'desktop' => $icon_size,
			'suffix'  => [
				'mobile'  => 'px',
				'tablet'  => 'px',
				'desktop' => 'px',
			],
		];

		$icon_spacing               = SettingsManager::get_instance()->get( $this->get_id() . '_' . self::ICON_SPACING, 10 );
		$this->icon_spacing_default = [
			'mobile'  => $icon_spacing,
			'tablet'  => $icon_spacing,
			'desktop' => $icon_spacing,
			'suffix'  => [
				'mobile'  => 'px',
				'tablet'  => 'px',
				'desktop' => 'px',
			],
		];

		$border_radius                    = SettingsManager::get_instance()->get( $this->get_id() . '_' . self::BORDER_RADIUS, 5 );
		$this->icon_border_radius_default = [
			'mobile'  => $border_radius,
			'tablet'  => $border_radius,
			'desktop' => $border_radius,
			'suffix'  => [
				'mobile'  => 'px',
				'tablet'  => 'px',
				'desktop' => 'px',
			],
		];
	}


	/**
	 * Initialize.
	 *
	 * @access  public
	 */
	public function init() {
		$this->set_property( 'label', __( 'Social Icons', 'neve' ) );
		$this->set_property( 'id', $this->get_class_const( 'COMPONENT_ID' ) );
		$this->set_property( 'width', 4 );
		$this->set_property( 'section', 'social_icons_' . $this->instance_number );
		$this->set_property( 'icon', 'share' );
	}

	/**
	 * Called to register component controls.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function add_settings() {
		SettingsManager::get_instance()->add(
			array(
				'id'                => self::REPEATER_ID,
				'group'             => $this->get_class_const( 'COMPONENT_ID' ),
				'tab'               => SettingsManager::TAB_GENERAL,
				'transport'         => 'post' . $this->get_class_const( 'COMPONENT_ID' ),
				'sanitize_callback' => array( $this, 'sanitize_social_icons_repeater' ),
				'default'           => wp_json_encode( $this->repeater_default ),
				'label'             => __( 'Social Icons', 'neve' ),
				'type'              => Loader::has_compatibility( 'repeater_control' ) ? '\Neve\Customizer\Controls\React\Repeater' : 'Neve_Pro\Customizer\Controls\Repeater',
				'options'           => array(
					'fields' => array(
						'title'            => array(
							'type'  => 'text',
							'label' => 'Title',
						),
						'icon'             => array(
							'type'  => 'icon',
							'label' => 'Icon',
						),
						'url'              => array(
							'type'  => 'text',
							'label' => 'Link',
						),
						'icon_color'       => array(
							'type'  => 'color',
							'label' => 'Icon Color',
						),
						'background_color' => array(
							'type'     => 'color',
							'label'    => 'Background Color',
							'gradient' => true,
						),
					),
				),
				'section'           => $this->section,
			)
		);

		SettingsManager::get_instance()->add(
			array(
				'id'                => self::NEW_TAB,
				'group'             => $this->get_id(),
				'tab'               => SettingsManager::TAB_GENERAL,
				'transport'         => 'post' . $this->get_class_const( 'COMPONENT_ID' ),
				'sanitize_callback' => 'absint',
				'default'           => 0,
				'label'             => __( 'Open in new tab', 'neve' ),
				'type'              => 'neve_toggle_control',
				'section'           => $this->section,
			)
		);


		$icon_size_settings = [
			'id'                    => self::ICON_SIZE_RESPONSIVE,
			'group'                 => $this->get_class_const( 'COMPONENT_ID' ),
			'tab'                   => SettingsManager::TAB_STYLE,
			'transport'             => 'post' . $this->get_class_const( 'COMPONENT_ID' ),
			'sanitize_callback'     => [ $this, 'sanitize_responsive_int_json' ],
			'default'               => $this->icon_size_default,
			'label'                 => __( 'Icon Size', 'neve' ),
			'type'                  => '\Neve\Customizer\Controls\React\Responsive_Range',
			'live_refresh_selector' => true,
			'live_refresh_css_prop' => [
				'cssVar'     => [
					'responsive' => true,
					'vars'       => '--icon-size',
					'suffix'     => 'px',
					'selector'   => '.builder-item--' . $this->get_id(),
				],
				'responsive' => true,
			],
			'options'               => [
				'input_attrs' => [
					'step'       => 1,
					'min'        => self::RELATIVE_CSS_UNIT_SUPPORTED_MIN_VALUE,
					'max'        => 40,
					'defaultVal' => $this->icon_size_default,
					'units'      => [ 'px', 'em', 'rem' ],
				],
			],
			'section'               => $this->section,
			'conditional_header'    => $this->get_builder_id() === 'header',
		];

		$icon_spacing_settings = [
			'id'                    => self::ICON_SPACING_RESPONSIVE,
			'group'                 => $this->get_class_const( 'COMPONENT_ID' ),
			'tab'                   => SettingsManager::TAB_STYLE,
			'transport'             => 'post' . $this->get_class_const( 'COMPONENT_ID' ),
			'sanitize_callback'     => [ $this, 'sanitize_responsive_int_json' ],
			'default'               => $this->icon_spacing_default,
			'label'                 => __( 'Icon Spacing', 'neve' ),
			'type'                  => '\Neve\Customizer\Controls\React\Responsive_Range',
			'live_refresh_selector' => true,
			'live_refresh_css_prop' => [
				'cssVar'     => [
					'responsive' => true,
					'vars'       => '--spacing',
					'suffix'     => 'px',
					'selector'   => '.builder-item--' . $this->get_id(),
				],
				'responsive' => true,
			],
			'options'               => [
				'input_attrs' => [
					'step'       => 1,
					'min'        => 0,
					'max'        => 100,
					'defaultVal' => $this->icon_spacing_default,
					'units'      => [ 'px', 'em', 'rem' ],
				],
			],
			'section'               => $this->section,
			'conditional_header'    => $this->get_builder_id() === 'header',
		];

		$border_radius_settings = [
			'id'                    => self::BORDER_RADIUS_RESPONSIVE,
			'group'                 => $this->get_class_const( 'COMPONENT_ID' ),
			'tab'                   => SettingsManager::TAB_STYLE,
			'transport'             => 'post' . $this->get_class_const( 'COMPONENT_ID' ),
			'sanitize_callback'     => [ $this, 'sanitize_responsive_int_json' ],
			'default'               => $this->icon_border_radius_default,
			'label'                 => __( 'Border Radius (px)', 'neve' ),
			'type'                  => '\Neve\Customizer\Controls\React\Responsive_Range',
			'live_refresh_selector' => true,
			'live_refresh_css_prop' => [
				'cssVar'     => [
					'responsive' => true,
					'vars'       => '--borderradius',
					'suffix'     => 'px',
					'selector'   => '.builder-item--' . $this->get_id(),
					'fallback'   => '0',
				],
				'responsive' => true,
			],
			'options'               => [
				'input_attrs' => [
					'step'       => 1,
					'min'        => 0,
					'max'        => 50,
					'defaultVal' => $this->icon_border_radius_default,
					'units'      => [ 'px', '%' ],
				],
			],
			'section'               => $this->section,
			'conditional_header'    => $this->get_builder_id() === 'header',
		];

		$icon_padding_settings = [
			'id'                    => self::ICON_PADDING,
			'group'                 => $this->get_id(),
			'tab'                   => SettingsManager::TAB_STYLE,
			'transport'             => 'post' . $this->get_class_const( 'COMPONENT_ID' ),
			'sanitize_callback'     => [ $this, 'sanitize_spacing_array' ],
			'conditional_header'    => $this->get_builder_id() === 'header',
			'default'               => [
				'desktop'      => [
					'top'    => 5,
					'right'  => 5,
					'bottom' => 5,
					'left'   => 5,
				],
				'tablet'       => [
					'top'    => 5,
					'right'  => 5,
					'bottom' => 5,
					'left'   => 5,
				],
				'mobile'       => [
					'top'    => 5,
					'right'  => 5,
					'bottom' => 5,
					'left'   => 5,
				],
				'desktop-unit' => 'px',
				'tablet-unit'  => 'px',
				'mobile-unit'  => 'px',
			],
			'options'               => [
				'input_attrs' => [
					'units' => [ 'px', 'em', 'rem', '%' ],
				],
			],
			'live_refresh_selector' => true,
			'live_refresh_css_prop' => [
				'cssVar' => [
					'responsive' => true,
					'vars'       => '--iconpadding',
					'selector'   => '.builder-item--' . $this->get_id(),
				],
			],
			'label'                 => __( 'Icon Padding', 'neve' ),
			'type'                  => '\Neve\Customizer\Controls\React\Spacing',
			'section'               => $this->section,
		];

		SettingsManager::get_instance()->add( $icon_size_settings );
		SettingsManager::get_instance()->add( $icon_spacing_settings );
		SettingsManager::get_instance()->add( $border_radius_settings );
		SettingsManager::get_instance()->add( $icon_padding_settings );
	}

	/**
	 * Method to add Component css styles.
	 *
	 * @param array $css_array An array containing css rules.
	 *
	 * @return array
	 * @since   1.0.0
	 * @access  public
	 */
	public function add_style( array $css_array = array() ) {
		$rules = [
			'--spacing'      => [
				'key'           => $this->id . '_' . self::ICON_SPACING_RESPONSIVE,
				'is_responsive' => true,
				'suffix'        => 'responsive_suffix',
				'default'       => $this->icon_spacing_default,
			],
			'--borderradius' => [
				'key'           => $this->get_id() . '_' . self::BORDER_RADIUS_RESPONSIVE,
				'is_responsive' => true,
				'suffix'        => 'responsive_suffix',
				'default'       => $this->icon_border_radius_default,
			],
			'--iconpadding'  => [
				'key'              => $this->get_id() . '_' . self::ICON_PADDING,
				'is_responsive'    => true,
				'directional-prop' => 'padding',
				'suffix'           => 'responsive_unit',
			],
			'--icon-size'    => [
				'key'           => $this->get_id() . '_' . self::ICON_SIZE_RESPONSIVE,
				'is_responsive' => true,
				'suffix'        => 'responsive_suffix',
				'default'       => $this->icon_size_default,
			],
		];

		$css_array[] = [
			'selectors' => '.builder-item--' . $this->get_id(),
			'rules'     => $rules,
		];

		return parent::add_style( $css_array );
	}

	/**
	 * Sanitize repeater values.
	 *
	 * @param string $value repeater json value.
	 *
	 * @return string
	 */
	public function sanitize_social_icons_repeater( $value ) {
		$fields = array(
			'title',
			'url',
			'icon',
			'visibility',
			'icon_color',
			'background_color',
		);

		$valid_data = [];
		$decoded    = json_decode( $value, true );
		if ( ! is_array( $decoded ) ) {
			return wp_json_encode( $this->repeater_default );
		}

		foreach ( $decoded as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}

			if ( ! array_key_exists( 'icon', $item ) || ! array_key_exists( 'visibility', $item ) ) {
				continue;
			}

			$item_keys = array_keys( $item );
			if ( array_intersect( $item_keys, $fields ) !== $item_keys ) {
				continue;
			}

			$valid_data[] = $item;
		}

		return wp_json_encode( $valid_data );
	}

	/**
	 * The render method for the component.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function render_component() {
		Main::get_instance()->load( 'component-social-icons' );
	}

	/**
	 * Allow for constant changes in pro.
	 *
	 * @param string $const Name of the constant.
	 *
	 * @return mixed
	 * @since   1.0.0
	 * @access  protected
	 */
	protected function get_class_const( $const ) {
		return $this->instance_number > 1 ? constant( 'static::' . $const ) . '_' . $this->instance_number : constant( 'static::' . $const );
	}

	/**
	 * Method to filter component loading if needed.
	 *
	 * @return bool
	 * @since   1.0.1
	 * @access  public
	 */
	public function is_active() {
		if ( $this->max_instance < $this->instance_number ) {
			return false;
		}

		return parent::is_active();
	}
}
