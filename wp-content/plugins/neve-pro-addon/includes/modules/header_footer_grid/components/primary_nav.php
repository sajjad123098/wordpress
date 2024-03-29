<?php
/**
 * Primary Nav Component Wrapper class extends Header Footer Grid Component.
 *
 * Name:    Header Footer Grid
 * Author:  Bogdan Preda <bogdan.preda@themeisle.com>
 *
 * @version 1.0.0
 * @package HFG
 */

namespace Neve_Pro\Modules\Header_Footer_Grid\Components;

use HFG\Core\Components\Nav as CoreNav;
use HFG\Core\Settings\Manager as SettingsManager;

/**
 * Class Primary_Nav
 *
 * @package Neve_Pro\Modules\Header_Footer_Grid\Components
 */
class Primary_Nav extends CoreNav {

	const SELECTED_NAV = 'selected_nav';

	/**
	 * Holds the instance count.
	 * Starts at 1 since the base component is not altered.
	 *
	 * @since   1.0.0
	 * @access  protected
	 * @var int
	 */
	protected static $instance_count = 1;
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
	protected $max_instance = 3;

	/**
	 * Primary Nav constructor.
	 *
	 * @param string $panel Builder panel.
	 */
	public function __construct( $panel ) {
		self::$instance_count ++;
		$this->instance_number = self::$instance_count;
		parent::__construct( $panel );
		$this->set_property( 'section', 'header_menu_primary_' . $this->instance_number );

	}

	/**
	 * Primary Nav init.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function init() {
		parent::init();
		if ( $this->instance_number > 1 ) {
			$this->set_property( 'label', $this->label . ' ' . ( $this->instance_number - 1 ) );
		}
	}

	/**
	 * Add customizer classes.
	 *
	 * @param array $classes loaded classes.
	 *
	 * @return array
	 */
	public function add_customizer_classes( $classes ) {
		$classes[] = 'Neve_Pro\Modules\Header_Footer_Grid\Customizer\Primary_Nav_Submenus';
		return $classes;
	}

	/**
	 * Method to filter component loading if needed.
	 *
	 * @since   1.0.1
	 * @access public
	 * @return bool
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

	/**
	 * Called to register component controls.
	 *
	 * @since   2.1.5
	 * @access  public
	 */
	public function add_settings() {
		parent::add_settings();

		SettingsManager::get_instance()->add(
			[
				'id'                 => self::SELECTED_NAV,
				'group'              => $this->get_class_const( 'COMPONENT_ID' ),
				'tab'                => SettingsManager::TAB_GENERAL,
				'transport'          => 'post' . $this->get_class_const( 'COMPONENT_ID' ),
				'sanitize_callback'  => 'absint',
				'default'            => 0,
				'label'              => __( 'Navigation Menu', 'neve' ),
				'type'               => 'select',
				'options'            => [
					'choices' => $this->get_menu_select_choices(),
				],
				'section'            => $this->section,
				'priority'           => 20,
				'conditional_header' => true,
			]
		);
	}

	/**
	 * Get the available menus as [ ID => Name ]
	 *
	 * @since   2.1.5
	 * @return array
	 */
	private function get_available_menus() {
		$menus   = wp_get_nav_menus();
		$options = [];

		foreach ( $menus as $menu ) {
			$options[ $menu->term_id ] = $menu->name;
		}

		return $options;
	}

	/**
	 * Get the available menu choices [ ID => Name ]
	 *
	 * @since   2.1.5
	 * @return array
	 */
	private function get_menu_select_choices() {
		$menus = wp_get_nav_menus();

		$options = [
			0 => __( 'Default', 'neve' ),
		];

		foreach ( $menus as $menu ) {
			$options[ $menu->term_id ] = $menu->name;
		}

		$current = SettingsManager::get_instance()->get( $this->get_id() . '_' . self::SELECTED_NAV, 0 );

		if ( ! array_key_exists( $current, $options ) ) {
			$options[ $current ] = __( 'Deleted Menu', 'neve' );
		}

		return $options;
	}

	/**
	 * The render method for the component.
	 *
	 * @since   2.1.5
	 * @access  public
	 */
	public function render_component() {
		add_filter( 'wp_nav_menu_args', [ $this, 'filter_nav_menu_args' ] );
		parent::render_component();
		remove_filter( 'wp_nav_menu_args', [ $this, 'filter_nav_menu_args' ] );
	}

	/**
	 * Filter the nav menu arguments.
	 *
	 * @param array $args Nav menu arguments as provided to wp_nav_menu().
	 *
	 * @since   2.1.5
	 * @return array
	 */
	public function filter_nav_menu_args( $args ) {
		$selected_menu = get_theme_mod( $this->get_id() . '_' . self::SELECTED_NAV );

		// Nothing specific is selected.
		if ( empty( $selected_menu ) ) {
			return $args;
		}

		// Maybe the menu was deleted.
		if ( ! array_key_exists( $selected_menu, $this->get_available_menus() ) ) {
			return $args;
		}

		$args['menu'] = $selected_menu;

		return $args;
	}
}
