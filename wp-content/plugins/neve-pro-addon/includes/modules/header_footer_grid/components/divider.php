<?php
/**
 * Divider Component class for Header Footer Grid.
 *
 * Name:    Header Footer Grid
 *
 * @package HFG
 */
namespace Neve_Pro\Modules\Header_Footer_Grid\Components;

use HFG\Core\Components\Abstract_Component;
use HFG\Core\Settings\Manager as SettingsManager;
use HFG\Main;
use Neve\Core\Styles\Dynamic_Selector;

/**
 * Class Divider
 *
 * @package Neve_Pro\Modules\Header_Footer_Grid\Components
 */
class Divider extends Abstract_Component {
	const COMPONENT_ID = 'divider';
	const SIZE         = 'size';
	const WIDTH        = 'width';
	const BORDER_COLOR = 'border_color';
	const BORDER_STYLE = 'border_style';

	/**
	 * Holds the instance count.
	 *
	 * @var int
	 */
	protected static $instance_count = 0;

	/**
	 * Holds the current instance count.
	 *
	 * @var int
	 */
	protected $instance_number;

	/**
	 * The maximum allowed instances of this class.
	 * This refers to the global scope, across all builders.
	 *
	 * @var int
	 */
	protected $max_instance = 4;

	/**
	 * Divider constructor.
	 *
	 * @param string $panel Builder panel.
	 */
	public function __construct( $panel ) {
		self::$instance_count ++;
		$this->instance_number = self::$instance_count;
		parent::__construct( $panel );
		$this->set_property( 'section', $this->get_class_const( 'COMPONENT_ID' ) );
	}

	/**
	 * Initialize
	 *
	 * @return void
	 */
	public function init() {
		$this->set_property( 'label', __( 'Divider', 'neve' ) . ' ' . self::$instance_count );
		$this->set_property( 'id', $this->get_class_const( 'COMPONENT_ID' ) );
		$this->set_property( 'section', 'divider_' . $this->instance_number );
	}

	/**
	 * Render component
	 *
	 * @return void
	 */
	public function render_component() {
		Main::get_instance()->load( 'component-divider' );
	}

	/**
	 * Adds settings
	 */
	public function add_settings() {
		SettingsManager::get_instance()->add(
			[
				'id'                    => self::BORDER_STYLE,
				'group'                 => $this->get_id(),
				'tab'                   => SettingsManager::TAB_GENERAL,
				'transport'             => 'postMessage',
				'sanitize_callback'     => 'wp_filter_nohtml_kses',
				'label'                 => __( 'Style', 'neve' ),
				'type'                  => 'Neve\Customizer\Controls\React\Inline_Select',
				'live_refresh_selector' => true,
				'live_refresh_css_prop' => [
					'cssVar' => [
						'vars'       => '--divstyle',
						'fallback'   => 'solid',
						'selector'   => '.builder-item--' . $this->get_id(),
						'defaultVal' => 'solid',
					],
				],
				'default'               => 'solid',
				'options'               => [
					'options' => [
						'solid'  => 'Solid',
						'dotted' => 'Dotted',
						'dashed' => 'Dashed',
						'double' => 'Double',
					],
					'default' => 'solid',
				],
				'section'               => $this->section,
				'conditional_header'    => $this->get_builder_id() === 'header',
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                    => self::WIDTH,
				'group'                 => $this->get_id(),
				'tab'                   => SettingsManager::TAB_GENERAL,
				'section'               => $this->section,
				'label'                 => __( 'Width', 'neve' ),
				'type'                  => '\Neve\Customizer\Controls\React\Responsive_Range',
				'live_refresh_selector' => true,
				'live_refresh_css_prop' => [
					'cssVar' => [
						'vars'       => '--divwidth',
						'responsive' => true,
						'suffix'     => 'px',
						'fallback'   => '0',
						'selector'   => '.builder-item--' . $this->get_id(),
					],
				],
				'options'               => [
					'input_attrs' => [
						'step'       => 1,
						'min'        => 0,
						'max'        => 50,
						'defaultVal' => [
							'mobile'  => 5,
							'tablet'  => 5,
							'desktop' => 5,
							'suffix'  => [
								'mobile'  => 'px',
								'tablet'  => 'px',
								'desktop' => 'px',
							],
						],
						'units'      => [ 'px' ],
					],
				],
				'default'               => '{ "mobile": "5", "tablet": "5", "desktop": "5" }',
				'transport'             => 'postMessage',
				'sanitize_callback'     => array( $this, 'sanitize_responsive_int_json' ),
				'conditional_header'    => $this->get_builder_id() === 'header',
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                    => self::SIZE,
				'group'                 => $this->get_id(),
				'tab'                   => SettingsManager::TAB_GENERAL,
				'section'               => $this->section,
				'label'                 => __( 'Size', 'neve' ),
				'type'                  => '\Neve\Customizer\Controls\React\Responsive_Range',
				'live_refresh_selector' => true,
				'live_refresh_css_prop' => [
					'cssVar' => [
						'vars'       => '--divsize',
						'responsive' => true,
						'suffix'     => '%',
						'fallback'   => '0',
						'selector'   => '.builder-item--' . $this->get_id(),
					],
				],
				'options'               => [
					'input_attrs' => [
						'step'       => 1,
						'min'        => 0,
						'max'        => 100,
						'defaultVal' => [
							'mobile'  => 80,
							'tablet'  => 80,
							'desktop' => 80,
							'suffix'  => [
								'mobile'  => '%',
								'tablet'  => '%',
								'desktop' => '%',
							],
						],
						'units'      => [ '%' ],
					],
				],
				'default'               => '{ "mobile": "80", "tablet": "80", "desktop": "80" }',
				'transport'             => 'postMessage',
				'sanitize_callback'     => array( $this, 'sanitize_responsive_int_json' ),
				'conditional_header'    => $this->get_builder_id() === 'header',
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                    => self::BORDER_COLOR,
				'group'                 => $this->get_id(),
				'tab'                   => SettingsManager::TAB_GENERAL,
				'label'                 => __( 'Color', 'neve' ),
				'section'               => $this->section,
				'type'                  => 'neve_color_control',
				'transport'             => 'postMessage',
				'live_refresh_selector' => true,
				'live_refresh_css_prop' => [
					'cssVar' => [
						'vars'       => '--divcolor',
						'selector'   => '.builder-item--' . $this->get_id(),
						'defaultVal' => 'var(--nv-text-color)',
					],
				],
				'sanitize_callback'     => 'neve_sanitize_colors',
				'default'               => 'var(--nv-text-color)',
				'conditional_header'    => $this->get_builder_id() === 'header',
			]
		);
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
			'--divstyle' => [
				Dynamic_Selector::META_KEY     => $this->id . '_' . self::BORDER_STYLE,
				Dynamic_Selector::META_DEFAULT => 'solid',
			],
			'--divwidth' => [
				Dynamic_Selector::META_KEY           => $this->get_id() . '_' . self::WIDTH,
				Dynamic_Selector::META_SUFFIX        => 'px',
				Dynamic_Selector::META_DEFAULT       => '5',
				Dynamic_Selector::META_IS_RESPONSIVE => true,
			],
			'--divsize'  => [
				Dynamic_Selector::META_KEY           => $this->get_id() . '_' . self::SIZE,
				Dynamic_Selector::META_SUFFIX        => '%',
				Dynamic_Selector::META_DEFAULT       => '80',
				Dynamic_Selector::META_IS_RESPONSIVE => true,
			],
			'--divcolor' => [
				Dynamic_Selector::META_KEY     => $this->get_id() . '_' . self::BORDER_COLOR,
				Dynamic_Selector::META_DEFAULT => 'var(--nv-text-color)',
			],
		];

		$css_array[] = [
			'selectors' => '.builder-item--' . $this->get_id(),
			'rules'     => $rules,
		];

		return parent::add_style( $css_array );
	}

	/**
	 * Allow for constant changes in pro.
	 *
	 * @param string $const constant name.
	 * @return mixed|string
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
