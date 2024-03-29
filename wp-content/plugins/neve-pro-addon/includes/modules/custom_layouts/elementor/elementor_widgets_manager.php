<?php
/**
 * Elementor widget for custom layouts.
 *
 * @package Neve Pro Addon
 */

namespace Neve_Pro\Modules\Custom_Layouts\Elementor;

use Elementor\Elements_Manager;
use Elementor\Plugin;
use Neve_Pro\Modules\Custom_Layouts\Elementor\Custom_Layout;
use Neve_Pro\Modules\Elementor_Booster\Module;

/**
 * Class Elementor_Custom_Layout
 */
class Elementor_Widgets_Manager {

	/**
	 * Check if module should be loaded.
	 *
	 * @return bool
	 */
	private function should_load() {
		return defined( 'ELEMENTOR_VERSION' );
	}

	/**
	 * Add Elementor custom layout widget.
	 *
	 * @return bool
	 */
	public function run() {
		if ( ! $this->should_load() ) {
			return false;
		}
		add_action( 'elementor/elements/categories_registered', array( $this, 'add_elementor_widget_category' ) );
		add_action( Module::get_register_widget_hook(), array( $this, 'register_widget' ) );

		return true;
	}

	/**
	 * Register Elementor custom layout widget.
	 */
	public function register_widget() {
		Module::wrapper_register_widget( new Custom_Layout() );
	}

	/**
	 * Add a new category of widgets.
	 *
	 * @param Elements_Manager $elements_manager Elementor elements manager.
	 */
	public function add_elementor_widget_category( $elements_manager ) {
		// add_category function already checks if category exists or not
		$elements_manager->add_category(
			'neve-elementor-widgets',
			array(
				'title' => esc_html__( 'Neve Pro Addon Widgets', 'neve' ),
				'icon'  => 'fa fa-plug',
			)
		);
	}

}
