<?php
/**
 * Customizer Class for Header Footer Grid.
 *
 * Name:    Header Footer Grid Addon
 * Author:  Bogdan Preda <bogdan.preda@themeisle.com>
 *
 * @version 1.0.0
 * @package Neve Pro Addon
 */

namespace Neve_Pro\Modules\Header_Footer_Grid\Customizer;

use Neve\Customizer\Base_Customizer;
use HFG\Core\Settings\Manager as SettingsManager;
use Neve_Pro\Modules\Header_Footer_Grid\Module;

/**
 * Class Header_Footer_Grid
 *
 * @package Neve_Pro\Customizer\Options
 */
class Sticky_Header extends Base_Customizer {
	/**
	 * A list of dependent controls.
	 *
	 * @var array
	 */
	protected $sticky_rows = array();

	/**
	 * Function that should be extended to add customizer controls.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'hfg_add_settings_to_rows', array( $this, 'hook_into_hfg_row_settings' ), 10, 4 );
	}

	/**
	 * Filter header row classes.
	 *
	 * @param array  $classes Classes added to row.
	 * @param string $row_index The row index.
	 *
	 * @return array
	 * @since   1.0.1
	 * @access  public
	 */
	public function header_row_classes( $classes, $row_index ) {
		$old_value           = get_theme_mod( 'hfg_header_layout_' . $row_index . '_sticky', false );
		$old_on_scroll_value = get_theme_mod( 'hfg_header_layout_' . $row_index . '_sticky_on_scroll', false );

		$is_sticky     = get_theme_mod(
			'hfg_header_layout_' . $row_index . '_sticky_responsive',
			[
				'mobile'  => $old_value,
				'desktop' => $old_value,
			]
		);
		$is_mobile_row = in_array( 'hide-on-desktop', $classes, true );
		$is_sticky     = $is_mobile_row ? $is_sticky['mobile'] : $is_sticky['desktop'];

		if ( $is_sticky ) {
			$classes[] = 'is_sticky';
			// Flag script for enqueue.
			Module::flag_for_enqueue();
			$old_on_scroll_value = get_theme_mod( 'hfg_header_layout_' . $row_index . '_sticky_on_scroll', false );
			$is_sticky_on_scroll = get_theme_mod(
				'hfg_header_layout_' . $row_index . '_sticky_on_scroll_responsive',
				[
					'mobile'  => $old_on_scroll_value,
					'desktop' => $old_on_scroll_value,
				]
			);
			$is_sticky_on_scroll = $is_mobile_row ? $is_sticky_on_scroll['mobile'] : $is_sticky_on_scroll['desktop'];
			if ( $is_sticky_on_scroll ) {
				$classes[] = 'is_sticky_on_scroll';
			}
		}

		return $classes;
	}

	/**
	 * Filter footer row classes.
	 *
	 * @param array  $classes Classes added to row.
	 * @param string $row_index The row index.
	 *
	 * @return array
	 * @since   1.1.7
	 * @access  public
	 */
	public function footer_row_classes( $classes, $row_index ) {
		$is_sticky = get_theme_mod( 'hfg_footer_layout_' . $row_index . '_sticky', false );
		if ( ! $is_sticky ) {
			return $classes;
		}
		$classes[] = 'is_sticky';
		// Flag script for enqueue.
		Module::flag_for_enqueue();

		return $classes;
	}

	/**
	 * Append to settings for row.
	 *
	 * @param SettingsManager $settings_manager An instance of the settings manager.
	 * @param string          $row_setting_id The row setting id.
	 * @param string          $row_id The row id.
	 *
	 * @since   1.0.0
	 * @updated 1.2.2
	 * @access  public
	 */
	public function hook_into_hfg_row_settings( SettingsManager $settings_manager, $row_setting_id = '', $row_id = '', $builder_id = '' ) {
		if ( $builder_id === 'header' && $row_id !== 'sidebar' ) {
			$old_value = get_theme_mod( 'hfg_header_layout_' . $row_id . '_sticky', false );
			$settings_manager->add(
				[
					'id'                 => 'sticky_responsive',
					'group'              => $row_setting_id,
					'tab'                => $settings_manager::TAB_LAYOUT,
					'section'            => $row_setting_id,
					'label'              => __( 'Stick to top', 'neve' ),
					'type'               => 'Neve\Customizer\Controls\React\Responsive_Toggle',
					'options'            => [
						'priority'         => 1,
						'excluded_devices' => [ 'tablet' ],
					],
					'transport'          => 'postheader',
					'sanitize_callback'  => [ $this, 'sanitize_sticky_header_responsive' ],
					'default'            => [
						'mobile'  => $old_value,
						'desktop' => $old_value,
					],
					'conditional_header' => true,
				]
			);
			$old_value = get_theme_mod( 'hfg_header_layout_' . $row_id . '_sticky_on_scroll', false );
			$settings_manager->add(
				[
					'id'                 => 'sticky_on_scroll_responsive',
					'group'              => $row_setting_id,
					'tab'                => $settings_manager::TAB_LAYOUT,
					'section'            => $row_setting_id,
					'label'              => __( 'Show only on scroll', 'neve' ),
					'type'               => 'Neve\Customizer\Controls\React\Responsive_Toggle',
					'options'            => [
						'priority'         => 2,
						'excluded_devices' => [ 'tablet' ],
					],
					'transport'          => 'postheader',
					'sanitize_callback'  => [ $this, 'sanitize_sticky_header_responsive' ],
					'default'            => [
						'mobile'  => $old_value,
						'desktop' => $old_value,
					],
					'conditional_header' => true,
				]
			);
			$this->sticky_rows[ $row_setting_id . '_sticky_responsive' ] = $row_setting_id . '_sticky_on_scroll';
		}

		if ( $builder_id === 'footer' ) {
			$settings_manager->add(
				array(
					'id'                 => 'sticky',
					'group'              => $row_setting_id,
					'tab'                => $settings_manager::TAB_LAYOUT,
					'section'            => $row_setting_id,
					'label'              => __( 'Stick to bottom', 'neve' ),
					'type'               => 'neve_toggle_control',
					'options'            => array(
						'priority' => 1,
					),
					'transport'          => 'postfooter',
					'sanitize_callback'  => 'neve_sanitize_checkbox',
					'default'            => false,
					'conditional_header' => true,
				)
			);
		}
	}

	/**
	 * Function that should be extended to add customizer controls.
	 *
	 * @return void
	 */
	public function add_controls() {
	}

	/**
	 * Sanitize the sticky header responsive value.
	 *
	 * @param array $arr array from theme mod.
	 * @return array
	 */
	public function sanitize_sticky_header_responsive( $arr ) {
		$default = [
			'mobile'  => false,
			'desktop' => false,
		];
		if ( ! is_array( $arr ) ) {
			return $default;
		}

		return array_merge( $default, $arr );
	}

	/**
	 * Adjust admin bar z-index.
	 *
	 * @return void
	 */
	public function adjust_admin_bar() {
		wp_add_inline_style( 'admin-bar', '#wpadminbar{z-index:100001;}' );
	}

	/**
	 * Adjust the lightbox z-index style if sticky is used for header or footer rows,
	 * this ensures that the lightbox preview will be on top.
	 *
	 * This is used for Elementor and Woo Photoswipe.
	 * We are using 999999 as the z-index for the header, and this is required.
	 *
	 * @return void
	 */
	public function adjust_lightbox_zindex() {
		$rows          = [ 'top', 'main', 'bottom' ];
		$sticky_header = false;
		$sticky_footer = false;
		foreach ( $rows as $row_id ) {
			$row_header_sticky = get_theme_mod( 'hfg_header_layout_' . $row_id . '_sticky_responsive', false );
			if ( is_array( $row_header_sticky ) && ( $row_header_sticky['mobile'] == true || $row_header_sticky['desktop'] == true ) ) {
				$sticky_header = true;
			}
			if ( get_theme_mod( 'hfg_footer_layout_' . $row_id . '_sticky', false ) ) {
				$sticky_footer = true;
			}
			if ( $sticky_header || $sticky_footer ) {
				wp_add_inline_style( 'photoswipe', '.pswp{z-index:100000 !important;}' );
				wp_add_inline_style( 'elementor-frontend', '.elementor-lightbox{z-index:100000 !important;}' );
				break;
			}
		}
	}
}
