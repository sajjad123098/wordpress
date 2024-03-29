<?php
/**
 * Main customizer addon.
 *
 * Author:          Andrei Baicus <andrei@themeisle.com>
 * Created on:      2018-12-03
 *
 * @package Neve Pro Addon
 */

namespace Neve_Pro\Customizer\Options;

use Neve\Customizer\Base_Customizer;
use Neve\Customizer\Types\Panel;

/**
 * Class Main
 *
 * @since   0.0.1
 * @package Neve Pro Addon
 */
class Main extends Base_Customizer {

	/**
	 * Add controls.
	 *
	 * @access private
	 * @since  0.0.1
	 */
	public function add_controls() {
		
		$this->add_main_panels();

	}

	/**
	 * Check if Easy Digital Downloads is active.
	 * 
	 * @return bool
	 */
	private function is_edd_active() {
		return class_exists( 'Easy_Digital_Downloads' );
	}

	/**
	 * Add main panels.
	 */
	private function add_main_panels() {

		if ( ! $this->is_edd_active() ) {
			return; 
		}

		$panels = array();

		/**
		 * Add EDD Panel if plugin active.
		 */
		$panels['neve_download'] = array(
			'priority' => 45,
			'title'    => __( 'Easy Digital Downloads', 'neve' ),
		);

		foreach ( $panels as $panel_id => $panel ) {
			$this->add_panel(
				new Panel(
					$panel_id,
					array(
						'priority' => $panel['priority'],
						'title'    => $panel['title'],
					)
				)
			);
		}

	}
	
}
