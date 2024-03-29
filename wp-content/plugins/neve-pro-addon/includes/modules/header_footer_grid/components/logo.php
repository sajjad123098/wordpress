<?php
/**
 * Logo Component Wrapper class extends Header Footer Grid Component.
 *
 * Name:    Header Footer Grid
 * Author:  Bogdan Preda <bogdan.preda@themeisle.com>
 *
 * @version 1.0.0
 * @package HFG
 */

namespace Neve_Pro\Modules\Header_Footer_Grid\Components;

use HFG\Core\Components\PaletteSwitch;
use HFG\Core\Settings\Manager as SettingsManager;
use HFG\Core\Components\Logo as CoreLogo;
use Neve\Core\Settings\Mods;
use Neve_Pro\Core\Loader;

/**
 * Class Logo
 *
 * @package Neve_Pro\Modules\Header_Footer_Grid\Components
 */
class Logo extends CoreLogo {
	const CUSTOM_LOGO = 'custom_logo';
	const LOGO        = 'logo';
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
	protected $max_instance = 2;

	/**
	 * Logo constructor.
	 *
	 * @param string $panel Builder panel.
	 */
	public function __construct( $panel ) {
		self::$instance_count ++;
		$this->instance_number = self::$instance_count;
		parent::__construct( $panel );
		$this->set_property( 'section', 'title_tagline_' . $this->instance_number );
	}

	/**
	 * Logo init.
	 *
	 * @since   3.0.0
	 * @access  public
	 */
	public function init() {
		parent::init();
		if ( $this->instance_number > 1 ) {
			$this->set_property( 'label', $this->label . ' ' . ( $this->instance_number - 1 ) );
		}
	}

	/**
	 * Generate the variants for the Logo
	 *
	 * @param array $variants Contains other variants from similar components.
	 *
	 * @return array
	 */
	public function filter_logo_variants( $variants ) {
		$main_logo            = get_theme_mod( 'custom_logo' );
		$custom_logo          = Mods::get( $this->get_class_const( 'COMPONENT_ID' ) . '_' . self::CUSTOM_LOGO, $main_logo );
		$conditional_custom   = json_decode( Mods::get( $this->get_class_const( 'COMPONENT_ID' ) . '_' . self::LOGO, self::sanitize_logo_json( $custom_logo ) ), true );
		$logo_custom_light_id = $main_logo;
		$logo_custom_dark_id  = $logo_custom_light_id;
		$logo_custom_same     = true;
		if ( ! empty( $conditional_custom ) ) {
			$logo_custom_light_id = isset( $conditional_custom['light'] ) ? $conditional_custom['light'] : $custom_logo;
			$logo_custom_dark_id  = isset( $conditional_custom['dark'] ) ? $conditional_custom['dark'] : $logo_custom_light_id;
			$logo_custom_same     = isset( $conditional_custom['same'] ) ? $conditional_custom['same'] : $logo_custom_same;
		}
		$variants[ $this->get_id() ] = array(
			'light' => array(
				'src'    => wp_get_attachment_image_url( $logo_custom_light_id, apply_filters( 'hfg_logo_image_size', 'full' ), false ),
				'srcset' => wp_get_attachment_image_srcset( $logo_custom_light_id, apply_filters( 'hfg_logo_image_size', 'full' ) ),
				'sizes'  => wp_get_attachment_image_sizes( $logo_custom_light_id, apply_filters( 'hfg_logo_image_size', 'full' ) ),
			),
			'dark'  => array(
				'src'    => wp_get_attachment_image_url( $logo_custom_dark_id, apply_filters( 'hfg_logo_image_size', 'full' ), false ),
				'srcset' => wp_get_attachment_image_srcset( $logo_custom_dark_id, apply_filters( 'hfg_logo_image_size', 'full' ) ),
				'sizes'  => wp_get_attachment_image_sizes( $logo_custom_dark_id, apply_filters( 'hfg_logo_image_size', 'full' ) ),
			),
			'same'  => $logo_custom_same,
		);

		return $variants;
	}

	/**
	 * Called to register component controls.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function add_settings() {
		parent::add_settings();

		if ( $this->instance_number <= 1 ) {
			return;
		}

		$custom_logo_args    = get_theme_support( 'custom-logo' );
		$default_custom_logo = Mods::get( $this->get_class_const( 'COMPONENT_ID' ) . '_' . self::CUSTOM_LOGO, get_theme_mod( 'custom_logo' ) );
		$default             = array(
			'light' => $default_custom_logo,
			'dark'  => $default_custom_logo,
			'same'  => true,
		);

		$settings_manager_logo = [
			'id'                => self::CUSTOM_LOGO,
			'group'             => $this->get_class_const( 'COMPONENT_ID' ),
			'tab'               => SettingsManager::TAB_GENERAL,
			'transport'         => 'post' . $this->get_class_const( 'COMPONENT_ID' ),
			'sanitize_callback' => 'absint',
			'default'           => get_theme_mod( 'custom_logo' ),
			'label'             => __( 'Logo', 'neve' ),
			'type'              => '\WP_Customize_Cropped_Image_Control',
			'options'           => [
				'priority'      => 0,
				'height'        => isset( $custom_logo_args[0]['height'] ) ? $custom_logo_args[0]['height'] : null,
				'width'         => isset( $custom_logo_args[0]['width'] ) ? $custom_logo_args[0]['width'] : null,
				'flex_height'   => isset( $custom_logo_args[0]['flex-height'] ) ? $custom_logo_args[0]['flex-height'] : null,
				'flex_width'    => isset( $custom_logo_args[0]['flex-width'] ) ? $custom_logo_args[0]['flex-width'] : null,
				'button_labels' => array(
					'select'       => __( 'Select logo', 'neve' ),
					'change'       => __( 'Change logo', 'neve' ),
					'remove'       => __( 'Remove', 'neve' ),
					'default'      => __( 'Default', 'neve' ),
					'placeholder'  => __( 'No logo selected', 'neve' ),
					'frame_title'  => __( 'Select logo', 'neve' ),
					'frame_button' => __( 'Choose logo', 'neve' ),
				),
			],
			'section'           => $this->section,
		];
		if ( Loader::has_compatibility( 'palette_logo' ) ) {
			$settings_manager_logo = [
				'id'                => self::LOGO,
				'group'             => $this->get_class_const( 'COMPONENT_ID' ),
				'tab'               => SettingsManager::TAB_GENERAL,
				'transport'         => 'refresh',
				'sanitize_callback' => array( $this, 'sanitize_logo_json' ),
				'default'           => wp_json_encode( $default ),
				'label'             => __( 'Logo base', 'neve' ),
				'type'              => '\Neve\Customizer\Controls\React\Logo_Palette',
				'options'           => [
					'priority'    => 0,
					'input_attrs' => [
						'builderListen' => 'hfg_header_layout' . ( neve_is_new_builder() ? '_v2' : '' ),
						'compChange'    => PaletteSwitch::COMPONENT_ID,
						'sameLabel'     => __( 'Use one logo for both modes', 'neve' ),
						'height'        => isset( $custom_logo_args[0]['height'] ) ? $custom_logo_args[0]['height'] : null,
						'width'         => isset( $custom_logo_args[0]['width'] ) ? $custom_logo_args[0]['width'] : null,
						'flexHeight'    => isset( $custom_logo_args[0]['flex-height'] ) ? $custom_logo_args[0]['flex-height'] : true,
						'flexWidth'     => false, // this can not flex as to allow correct cropping
					],
				],
				'section'           => $this->section,
			];
		}

		SettingsManager::get_instance()->add( $settings_manager_logo );

		SettingsManager::get_instance()->add(
			[
				'id'                => 'shortcut',
				'group'             => $this->get_class_const( 'COMPONENT_ID' ),
				'tab'               => SettingsManager::TAB_GENERAL,
				'transport'         => 'postMessage',
				'sanitize_callback' => 'esc_attr',
				'type'              => '\Neve\Customizer\Controls\Button',
				'options'           => [
					'button_text'      => __( 'Edit Title, Tagline & Site Icon', 'neve' ),
					'icon_class'       => 'nametag',
					'control_to_focus' => 'blogname',
				],
				'section'           => $this->section,
			]
		);
	}

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
	 * @param string $const Name of the constant.
	 *
	 * @return mixed
	 * @since   1.0.0
	 * @access  protected
	 */
	protected function get_class_const( $const ) {
		return constant( 'static::' . $const ) . '_' . $this->instance_number;
	}
}
