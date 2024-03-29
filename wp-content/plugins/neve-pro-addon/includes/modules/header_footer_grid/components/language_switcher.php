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

/**
 * Class Language_Switcher
 *
 * @package Neve_Pro\Modules\Header_Footer_Grid\Components
 */
class Language_Switcher extends Abstract_Component {
	const COMPONENT_ID = 'language_switcher';

	const POLYLANG       = 'polylang';
	const WPML           = 'wpml';
	const TRANSLATEPRESS = 'translatepress';
	const WEGLOT         = 'weglot';

	const PLL_SHOW_FLAGS          = 'pll_show_flags';
	const PLL_HIDE_NO_TRANSLATION = 'pll_hide_no_translation';
	const PLL_NAMES               = 'pll_show_names';
	const PLL_FORCE_FP            = 'pll_force_fp';
	const PLL_HIDE_CURRENT        = 'pll_hide_current';


	/**
	 * The active language switcher plugin.
	 *
	 * @var null | string
	 */
	private $current_plugin = null;

	/**
	 * Language Switcher constructor.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function init() {
		$this->set_property( 'label', __( 'Language Switcher', 'neve' ) );
		$this->set_property( 'id', self::COMPONENT_ID );
		$this->set_property( 'width', 2 );
		$this->set_property( 'section', 'language_switcher' );
		$this->set_property( 'icon', 'translation' );
	}

	/**
	 * Called to register component controls.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function add_settings() {
		if ( $this->current_plugin === 'polylang' ) {
			$this->add_pll_settings();
		}
	}

	/**
	 * Add settings for Polylang.
	 *
	 * @return void
	 */
	private function add_pll_settings() {
		$toggles = [
			self::PLL_SHOW_FLAGS          => [
				'label'   => __( 'Show Flags', 'neve' ),
				'default' => true,
			],
			self::PLL_NAMES               => [
				'label'   => __( 'Show Language Names', 'neve' ),
				'default' => true,
			],
			self::PLL_HIDE_NO_TRANSLATION => [
				'label'   => __( 'Hide if no Translation', 'neve' ),
				'default' => false,
			],
			self::PLL_FORCE_FP            => [
				'label'   => __( 'Link to Front Page', 'neve' ),
				'default' => false,
			],
			self::PLL_HIDE_CURRENT        => [
				'label'   => __( 'Hide Current Language', 'neve' ),
				'default' => false,
			],
		];

		$priority = 10;

		foreach ( $toggles as $id => $args ) {
			SettingsManager::get_instance()->add(
				array(
					'id'                => $id,
					'section'           => $this->section,
					'label'             => $args['label'],
					'default'           => $args['default'],
					'priority'          => $priority,

					'transport'         => 'post' . $this->get_class_const( 'COMPONENT_ID' ),
					'tab'               => SettingsManager::TAB_GENERAL,
					'type'              => 'neve_toggle_control',
					'group'             => $this->get_id(),
					'sanitize_callback' => 'absint',
				)
			);
			$priority ++;
		}
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
		$this->default_selector = '.builder-item--' . $this->get_id() . ' > .component-wrap > :first-child';

		return parent::add_style( $css_array );
	}

	/**
	 * The render method for the component.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function render_component() {
		Main::get_instance()->load( 'component-language-switcher' );
	}

	/**
	 * Check if component should be active.
	 *
	 * @return bool
	 */
	public function is_active() {
		$plugins = array(
			self::WPML           => defined( 'ICL_SITEPRESS_VERSION' ),
			self::TRANSLATEPRESS => defined( 'TRP_PLUGIN_VERSION' ),
			self::POLYLANG       => defined( 'POLYLANG_VERSION' ),
			self::WEGLOT         => defined( 'WEGLOT_VERSION' ),
		);
		foreach ( $plugins as $key => $plugin_status ) {
			if ( $plugin_status === true ) {
				$this->current_plugin = $key;

				return true;
			}
		}

		return false;
	}
}
