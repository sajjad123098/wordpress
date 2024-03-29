<?php
/**
 * Additional pro controls for menu icon
 *
 * @package Neve Pro
 */

namespace Neve_Pro\Modules\Header_Footer_Grid\Customizer;

use Neve\Customizer\Base_Customizer;
use HFG\Core\Settings\Manager as SettingsManager;
use Neve_Pro\Traits\Sanitize_Functions;

/**
 * Class Menu_Icon
 *
 * @package Neve_Pro\Modules\Header_Footer_Grid\Customizer
 */
class Menu_Icon extends Base_Customizer {
	use Sanitize_Functions;

	/**
	 * Add customizer controls.
	 */
	public function add_controls() {
		add_action( 'customize_register', [ $this, 'add_svg_option' ], PHP_INT_MAX );

		SettingsManager::get_instance()->add(
			[
				'id'                => 'svg_menu_icon',
				'group'             => 'nav-icon',
				'tab'               => SettingsManager::TAB_STYLE,
				'transport'         => 'refresh',
				'sanitize_callback' => 'neve_kses_svg',
				'label'             => __( 'Custom SVG', 'neve' ),
				'type'              => 'textarea',
				'section'           => 'header_menu_icon',
				'options'           => [
					'active_callback' => [ $this, 'svg_menu_icon_active_callback' ],
					'priority'        => 10,
				],
			]
		);
	}

	/**
	 * Add svg option.
	 */
	public function add_svg_option() {
		$this->change_customizer_object(
			'control',
			'nav-icon_menu_icon',
			'options',
			[
				'default' => 'Default',
				'arrow'   => 'Arrow',
				'donner'  => 'Donner',
				'dots'    => 'Dots',
				'minus'   => 'Minus',
				'vortex'  => 'Vortex',
				'squeeze' => 'Squeeze',
				'svg'     => __( 'Custom SVG', 'neve' ),
			]
		);

		$this->change_customizer_object( 'control', 'nav-icon_button_appearance', 'priority', 11 );
	}

	/**
	 * Callback to check if the menu icon is set to svg.
	 *
	 * @return bool
	 */
	public function svg_menu_icon_active_callback() {
		return get_theme_mod( 'nav-icon_menu_icon', 'default' ) === 'svg';
	}
}
