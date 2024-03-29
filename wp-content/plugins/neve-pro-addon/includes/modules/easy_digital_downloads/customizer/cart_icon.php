<?php
/**
 * Cart Icon class for Easy Digital Downloads Booster.
 *
 * Holds the Pro related functionality.
 *
 * Author:          Uriahs Victor
 * Created on:      06/01/2022 (d/m/y)
 *
 * @package Neve Pro
 */


namespace Neve_Pro\Modules\Easy_Digital_Downloads\Customizer;

use HFG\Core\Components\EddCartIcon;
use HFG\Core\Settings\Manager as SettingsManager;

/**
 * Class Cart_Icon
 *
 * @package Neve_Pro\Modules\Easy_Digital_Downloads\Customizer
 */
class Cart_Icon {

	/**
	 * Init function.
	 */
	public function init() {
		add_action( 'hfg_component_settings', array( $this, 'add_cart_icon_features' ) );
	}

	/**
	 * Add cart icon features in PRO.
	 */
	public function add_cart_icon_features() {

		$default_selector = '.builder-item--' . EddCartIcon::COMPONENT_ID;
		$section          = EddCartIcon::COMPONENT_ID;

		SettingsManager::get_instance()->add(
			[
				'id'                 => EddCartIcon::ICON_SELECTOR,
				'group'              => EddCartIcon::COMPONENT_ID,
				'tab'                => SettingsManager::TAB_GENERAL,
				'transport'          => 'post' . EddCartIcon::COMPONENT_ID,
				'sanitize_callback'  => 'wp_filter_nohtml_kses',
				'default'            => 'cart-icon-style1',
				'label'              => __( 'Select Icon', 'neve' ),
				'type'               => '\Neve\Customizer\Controls\React\Radio_Buttons',
				'options'            => [
					'priority'      => 10,
					'is_for'        => 'cart_component',
					'large_buttons' => false,
				],
				'section'            => $section,
				'conditional_header' => true,
			]
		);
	
		SettingsManager::get_instance()->add(
			[
				'id'                 => EddCartIcon::CART_LABEL,
				'group'              => EddCartIcon::COMPONENT_ID,
				'tab'                => SettingsManager::TAB_GENERAL,
				'transport'          => 'post' . EddCartIcon::COMPONENT_ID,
				'sanitize_callback'  => 'sanitize_text_field',
				'label'              => __( 'Cart label', 'neve' ),
				'type'               => 'text',
				'section'            => $section,
				'use_dynamic_fields' => array( 'edd_custom_cart' ),
				'conditional_header' => true,
			]
		);
	
		SettingsManager::get_instance()->add(
			[
				'id'                    => EddCartIcon::LABEL_SIZE_ID,
				'group'                 => EddCartIcon::COMPONENT_ID,
				'tab'                   => SettingsManager::TAB_STYLE,
				'transport'             => 'postMessage',
				'sanitize_callback'     => 'absint',
				'default'               => 15,
				'label'                 => __( 'Label Size', 'neve' ),
				'type'                  => 'neve_range_control',
				'live_refresh_selector' => $default_selector . ' .edd-cart-icon-label',
				'live_refresh_css_prop' => array(
					'type' => 'font-size',
				),
				'section'               => $section,
				'conditional_header'    => true,
			]
		);
	
	}

	
}
