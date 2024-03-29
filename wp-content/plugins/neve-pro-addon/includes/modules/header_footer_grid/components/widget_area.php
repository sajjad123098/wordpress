<?php
/**
 * Widget Area Component class for Header Footer Grid.
 *
 * Name:    Header Footer Grid
 *
 * @version 1.0.0
 * @package HFG
 */
namespace Neve_Pro\Modules\Header_Footer_Grid\Components;

use HFG\Main;
use WP_Customize_Manager;

/**
 * Class Widget_Area
 *
 * @package Neve_Pro\Modules\Header_Footer_Grid\Components
 */
class Widget_Area extends \HFG\Core\Components\Abstract_Component {

	const COMPONENT_ID = 'widget-area';

	/**
	 * Holds the instance count.
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
	protected $max_instance = 9;

	/**
	 * Widget area constructor.
	 *
	 * @param string $panel Builder panel.
	 */
	public function __construct( $panel ) {
		self::$instance_count ++;
		$this->instance_number = self::$instance_count;
		parent::__construct( $panel );
		$this->set_property( 'section', 'neve_sidebar-widgets-widget-area-' . $this->instance_number );
	}

	/**
	 * Initialize
	 *
	 * @return void
	 */
	public function init() {
		$label = __( 'Widget Area', 'neve' ) . ' ' . $this->instance_number;
		$this->set_property( 'label', $label );
		$this->set_property( 'id', $this->get_class_const( 'COMPONENT_ID' ) . $this->instance_number );
		$this->set_property( 'default_selector', '.builder-item--' . $this->get_id() );

		add_filter( 'customize_section_active', array( $this, 'widget_areas_show' ), 15, 2 );
	}

	/**
	 * Method to show widget areas
	 *
	 * @param bool   $active Is active.
	 * @param object $section The section id.
	 *
	 * @return bool
	 * @since   1.0.0
	 * @access  public
	 */
	public function widget_areas_show( $active, $section ) {
		if ( strpos( $section->id, 'widget-area-' ) ) {
			$active = true;
		}

		return $active;
	}

	/**
	 * Changes section's panel
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer object.
	 *
	 * @return WP_Customize_Manager Customizer object.
	 */
	public function customize_register( WP_Customize_Manager $wp_customize ) {
		parent::customize_register( $wp_customize );

		$widget = $wp_customize->get_section( 'sidebar-widgets-widget-area-' . $this->instance_number );
		if ( $widget ) {
			$widget->panel = $this->panel;
		}

		return $wp_customize;
	}

	/**
	 * Gets the component ID
	 *
	 * @return string
	 */
	public function get_id() {
		return 'widget-area-' . $this->instance_number;
	}

	/**
	 * The render method for the component.
	 *
	 * @return void
	 */
	public function render_component() {
		Main::get_instance()->load( 'component-widget-area' );
	}

	/**
	 * Called to register component controls.
	 *
	 * @return void
	 */
	public function add_settings() {}

	/**
	 * Method to filter component loading if needed.
	 *
	 * @return bool
	 * @since   1.0.1
	 * @access public
	 */
	public function is_active() {
		if ( $this->max_instance < $this->instance_number ) {
			return false;
		}
		return parent::is_active();
	}

	/**
	 * Allow for constant changes in pro.
	 *
	 * @since   1.0.0
	 * @access  protected
	 * @param string $const Name of the constant.
	 *
	 * @return mixed
	 */
	protected function get_class_const( $const ) {
		return constant( 'static::' . $const ) . '_' . $this->instance_number;
	}
}
