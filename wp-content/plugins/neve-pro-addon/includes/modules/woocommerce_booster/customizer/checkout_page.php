<?php
/**
 * Author:          Andrei Baicus <andrei@themeisle.com>
 * Created on:      2019-02-11
 *
 * @package Neve Pro Addon
 */

namespace Neve_Pro\Modules\Woocommerce_Booster\Customizer;

use Neve\Customizer\Base_Customizer;
use Neve\Customizer\Types\Control;
use Neve\Customizer\Types\Partial;
use Neve_Pro\Core\Loader;

/**
 * Class Checkout_Page
 *
 * @package Neve_Pro\Modules\Woocommerce_Booster\Customizer
 */
class Checkout_Page extends Base_Customizer {

	/**
	 * Base initialization.
	 */
	public function init() {
		parent::init();
		add_action( 'customize_controls_print_styles', array( $this, 'add_customizer_style' ) );
	}

	/**
	 * Add customizer style.
	 */
	public function add_customizer_style() {
		$side = is_rtl() ? 'right' : 'left';
		echo '
		<style type="text/css">
			#sub-accordion-section-woocommerce_checkout .customize-control:not( #customize-control-neve_checkout_page_layout ):not(.customize-control-customizer-heading){
				margin-bottom: -1px;
				background-color: #fff;
				padding: 12px 15px;
				margin-' . esc_attr( $side ) . ': -15px;
				position: relative;
			}
			
			#sub-accordion-section-woocommerce_checkout .neve-white-background-control {
				border: none !important;
			}
			
			#sub-accordion-section-woocommerce_checkout #customize-control-neve_checkout_step1_label {
				margin-top: 12px !important;
			}
			
			#sub-accordion-section-woocommerce_checkout #customize-control-neve_checkout_step3_label
			#sub-accordion-section-woocommerce_checkout #customize-control-neve_checkout_labels_placeholders,
			#sub-accordion-section-woocommerce_checkout #customize-control-neve_enable_checkout_coupon {
				margin-bottom: 12px !important;
			}
			
			#sub-accordion-section-woocommerce_checkout #customize-control-neve_woo_checkout_settings_heading {
				margin-top: 12px;
			}
	}
		</style>';
	}

	/**
	 * Add customizer controls
	 */
	public function add_controls() {
		$this->group_controls();
		$this->add_checkout_style();
		$this->add_general_settings();
		$this->partial_refresh();
	}

	/**
	 * Add control groups to better organize the customizer.
	 */
	private function group_controls() {
		$checkout_section               = $this->wpc->get_section( 'woocommerce_checkout' );
		$checkout_section->description .= ' ' . apply_filters( 'neve_external_link', 'https://bit.ly/neve-woo-chk', __( 'Learn more', 'neve' ) );
		$this->wpc->add_section( $checkout_section );
		$this->add_control(
			new Control(
				'neve_checkout_settings_heading',
				array(
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $this->selective_refresh,
				),
				array(
					'label'            => esc_html__( 'Checkout Style', 'neve' ),
					'section'          => 'woocommerce_checkout',
					'priority'         => 0,
					'class'            => 'neve-checkout-accordion',
					'accordion'        => true,
					'controls_to_wrap' => 9,
					'expanded'         => true,
				),
				'Neve\Customizer\Controls\Heading'
			)
		);

		$this->add_control(
			new Control(
				'neve_woo_checkout_settings_heading',
				array(
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $this->selective_refresh,
				),
				array(
					'label'            => esc_html__( 'General', 'neve' ),
					'section'          => 'woocommerce_checkout',
					'priority'         => 100,
					'class'            => 'woo-checkout-accordion',
					'accordion'        => true,
					'controls_to_wrap' => 12,
					'expanded'         => true,
				),
				'Neve\Customizer\Controls\Heading'
			)
		);
	}

	/**
	 * Add checkout options from Neve.
	 */
	private function add_checkout_style() {
		$this->add_control(
			new Control(
				'neve_checkout_page_layout',
				[
					'default'           => 'standard',
					'sanitize_callback' => [ $this, 'sanitize_checkout_layout' ],
				],
				[
					'section'  => 'woocommerce_checkout',
					'priority' => 10,
					'choices'  => [
						'standard' => [
							'name'  => esc_html__( 'Standard', 'neve' ),
							'image' => 'data:image/png;base64,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',
						],
						'vertical' => [
							'name'  => esc_html__( 'Vertical', 'neve' ),
							'image' => 'data:image/png;base64,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',
						],
						'stepped'  => [
							'name'  => esc_html__( 'Stepped', 'neve' ),
							'image' => 'data:image/png;base64,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',
						],
					],
				],
				'\Neve\Customizer\Controls\React\Radio_Image'
			)
		);

		$this->add_control(
			new Control(
				'neve_checkout_boxed_layout',
				array(
					'sanitize_callback' => 'neve_sanitize_checkbox',
					'default'           => self::get_checkout_boxed_layout_default(),
				),
				array(
					'label'    => esc_html__( 'Boxed style', 'neve' ),
					'section'  => 'woocommerce_checkout',
					'type'     => 'neve_toggle_control',
					'priority' => 20,
				)
			)
		);

		$this->add_control(
			new Control(
				'neve_checkout_box_width',
				[
					'sanitize_callback' => 'neve_sanitize_range_value',
					'transport'         => $this->selective_refresh,
					'default'           => '{ "mobile": 100, "tablet": 100, "desktop": 100 }',
				],
				[
					'label'                 => esc_html__( 'Box width', 'neve' ),
					'section'               => 'woocommerce_checkout',
					'priority'              => 30,
					'type'                  => 'neve_responsive_range_control',
					'input_attrs'           => [
						'min'        => 1,
						'max'        => 100,
						'units'      => [ '%' ],
						'defaultVal' => [
							'mobile'  => 100,
							'tablet'  => 100,
							'desktop' => 100,
							'suffix'  => [
								'mobile'  => '%',
								'tablet'  => '%',
								'desktop' => '%',
							],
						],
					],
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar'     => [
							'vars'       => '--maxwidth',
							'selector'   => '.nv-checkout-boxed-style',
							'responsive' => true,
							'suffix'     => '%',
						],
						'responsive' => true,
						'template'   =>
							'.nv-checkout-boxed-style.nv-checkout-layout-stepped .woocommerce-checkout>.col2-set,
							.nv-checkout-boxed-style.nv-checkout-layout-vertical .woocommerce-checkout>.col2-set,
							.nv-checkout-boxed-style.nv-checkout-layout-stepped .woocommerce-checkout .woocommerce-checkout-review-order,
							.nv-checkout-boxed-style.nv-checkout-layout-vertical .woocommerce-checkout .woocommerce-checkout-review-order,
							.nv-checkout-boxed-style.nv-checkout-layout-stepped .next-step-button-wrapper
							{
							    width: {{value}}%;
					    	}',
					],
					'active_callback'       => array( $this, 'box_width_active_callback' ),
				],
				'\Neve\Customizer\Controls\React\Responsive_Range'
			)
		);

		$box_padding_default = self::get_box_padding_default_value();
		$this->add_control(
			new Control(
				'neve_checkout_box_padding',
				array(
					'sanitize_callback' => array( $this, 'sanitize_spacing_array' ),
					'transport'         => 'postMessage',
					'default'           => $box_padding_default,
				),
				array(
					'label'                 => esc_html__( 'Padding', 'neve' ),
					'section'               => 'woocommerce_checkout',
					'priority'              => 40,
					'default'               => $box_padding_default,
					'input_attrs'           => array(
						'units' => [ 'px', 'em', 'rem' ],
						'min'   => 0,
					),
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => array(
						'cssVar'      => [
							'vars'       => '--boxpadding',
							'selector'   => '.nv-checkout-boxed-style',
							'responsive' => true,
						],
						'responsive'  => true,
						'directional' => true,
						'template'    => '
						.woocommerce-checkout.nv-checkout-boxed-style .col2-set,
						.woocommerce-checkout.nv-checkout-boxed-style .woocommerce-checkout-review-order-table,
						.woocommerce-checkout.nv-checkout-boxed-style #payment{
							padding-top: {{value.top}};
							padding-right: {{value.right}};
							padding-bottom: {{value.bottom}};
							padding-left: {{value.left}};
						}',
					),
					'active_callback'       => array( $this, 'is_boxed_layout' ),
				),
				'\Neve\Customizer\Controls\React\Spacing'
			)
		);

		$checkout_background_default = 'var(--nv-site-bg)';
		$this->add_control(
			new Control(
				'neve_checkout_page_background_color',
				array(
					'sanitize_callback' => 'neve_sanitize_colors',
					'default'           => $checkout_background_default,
					'transport'         => 'postMessage',
				),
				array(
					'label'                 => esc_html__( 'Page Background Color', 'neve' ),
					'section'               => 'woocommerce_checkout',
					'priority'              => 50,
					'active_callback'       => array( $this, 'is_boxed_layout' ),
					'input_attrs'           => [
						'allow_gradient' => Loader::has_compatibility( 'gradient_picker' ),
					],
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar' => [
							'vars'     => '--bgcolor',
							'selector' => '.nv-checkout-boxed-style',
						],
					],
				),
				'Neve\Customizer\Controls\React\Color'
			)
		);

		$box_background_default = 'var(--nv-light-bg)';
		$this->add_control(
			new Control(
				'neve_checkout_box_background_color',
				array(
					'sanitize_callback' => 'neve_sanitize_colors',
					'transport'         => $this->selective_refresh,
					'default'           => $box_background_default,
				),
				array(
					'label'                 => esc_html__( 'Box Background Color', 'neve' ),
					'section'               => 'woocommerce_checkout',
					'priority'              => 60,
					'active_callback'       => array( $this, 'is_boxed_layout' ),
					'input_attrs'           => [
						'allow_gradient' => Loader::has_compatibility( 'gradient_picker' ),
					],
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar'   => [
							'vars'     => '--boxbgcolor',
							'selector' => '.nv-checkout-boxed-style .checkout',
						],
						'template' =>
							'
							.woocommerce-checkout.nv-checkout-boxed-style .col2-set,
							.woocommerce-checkout.nv-checkout-boxed-style .woocommerce-checkout-review-order-table,
							.woocommerce-checkout.nv-checkout-boxed-style #payment {
							    background: {{value}};
						    }',
					],
				),
				'Neve\Customizer\Controls\React\Color'
			)
		);

		$this->add_control(
			new Control(
				'neve_checkout_step1_label',
				array(
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $this->selective_refresh,
					'default'           => esc_html__( 'Billing and shipping', 'neve' ),
				),
				array(
					'priority'        => 70,
					'section'         => 'woocommerce_checkout',
					'label'           => esc_html__( 'Step 1 Label', 'neve' ),
					'type'            => 'text',
					'active_callback' => array( $this, 'is_stepped_layout' ),
				)
			)
		);

		$this->add_control(
			new Control(
				'neve_checkout_step2_label',
				array(
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $this->selective_refresh,
					'default'           => esc_html__( 'Proceed to Order review', 'neve' ),
				),
				array(
					'priority'        => 80,
					'section'         => 'woocommerce_checkout',
					'label'           => esc_html__( 'Step 2 Label', 'neve' ),
					'type'            => 'text',
					'active_callback' => array( $this, 'is_stepped_layout' ),
				)
			)
		);

		$this->add_control(
			new Control(
				'neve_checkout_step3_label',
				array(
					'sanitize_callback' => 'sanitize_text_field',
					'transport'         => $this->selective_refresh,
					'default'           => esc_html__( 'Proceed to Payment', 'neve' ),
				),
				array(
					'priority'        => 90,
					'section'         => 'woocommerce_checkout',
					'label'           => esc_html__( 'Step 3 Label', 'neve' ),
					'type'            => 'text',
					'active_callback' => array( $this, 'is_stepped_layout' ),
				)
			)
		);
	}

	/**
	 * Partial refresh
	 */
	private function partial_refresh() {
		$this->add_partial(
			new Partial(
				'neve_checkout_step1_label',
				array(
					'selector'            => '.nv-checkout-layout-stepped .nv-checkout-steps-wrapper',
					'settings'            => array(
						'neve_checkout_step1_label',
					),
					'render_callback'     => array( $this, 'render_steps_callback' ),
					'container_inclusive' => true,
				)
			)
		);

		$this->add_partial(
			new Partial(
				'neve_checkout_step2_label',
				array(
					'selector'            => '.nv-checkout-layout-stepped .nv-checkout-steps-wrapper',
					'settings'            => array(
						'neve_checkout_step2_label',
					),
					'render_callback'     => array( $this, 'render_steps_callback' ),
					'container_inclusive' => true,
				)
			)
		);

		$this->add_partial(
			new Partial(
				'neve_checkout_step3_label',
				array(
					'selector'            => '.nv-checkout-layout-stepped .nv-checkout-steps-wrapper',
					'settings'            => array(
						'neve_checkout_step3_label',
					),
					'render_callback'     => array( $this, 'render_steps_callback' ),
					'container_inclusive' => true,
				)
			)
		);
	}

	/**
	 * Render callback function for selective refresh
	 *
	 * @return false|string
	 */
	public function render_steps_callback() {
		$checkout_view = new \Neve_Pro\Modules\Woocommerce_Booster\Views\Checkout_Page();
		ob_start();
		$checkout_view->render_checkout_steps();
		$markup = ob_get_contents();
		ob_end_clean();

		return $markup;
	}

	/**
	 * Get box padding default value.
	 *
	 * @return array
	 */
	public static function get_box_padding_default_value() {
		return [
			'mobile'       => [
				'top'    => 20,
				'right'  => 20,
				'bottom' => 20,
				'left'   => 20,
			],
			'tablet'       => [
				'top'    => 40,
				'right'  => 40,
				'bottom' => 40,
				'left'   => 40,
			],
			'desktop'      => [
				'top'    => 40,
				'right'  => 40,
				'bottom' => 40,
				'left'   => 40,
			],
			'mobile-unit'  => 'px',
			'tablet-unit'  => 'px',
			'desktop-unit' => 'px',
		];
	}

	/**
	 * Add general settings
	 */
	private function add_general_settings() {
		$checkboxes = array(
			'neve_enable_checkout_fixed_order'  => array(
				'default'         => false,
				'priority'        => 150,
				'label'           => esc_html__( 'Enable Fixed Order Box', 'neve' ),
				'active_callback' => array( $this, 'is_standard_layout' ),
			),
			'neve_checkout_labels_placeholders' => array(
				'default'  => false,
				'priority' => 160,
				'label'    => esc_html__( 'Use Labels as Placeholders', 'neve' ),
			),
			'neve_enable_checkout_order_note'   => array(
				'default'  => true,
				'priority' => 170,
				'label'    => esc_html__( 'Show Order Note', 'neve' ),
			),
			'neve_enable_checkout_coupon'       => array(
				'default'  => true,
				'priority' => 180,
				'label'    => esc_html__( 'Show Coupon', 'neve' ),
			),
		);

		foreach ( $checkboxes as $id => $args ) {
			$this->add_control(
				new Control(
					$id,
					array(
						'default'           => $args['default'],
						'sanitize_callback' => 'neve_sanitize_checkbox',
					),
					array(
						'label'           => $args['label'],
						'section'         => 'woocommerce_checkout',
						'type'            => 'neve_toggle_control',
						'priority'        => $args['priority'],
						'active_callback' => isset( $args['active_callback'] ) ? $args['active_callback'] : '__return_true',
					)
				)
			);
		}
	}

	/**
	 * Change the controls added from WooCommerce.
	 */
	public function change_controls() {
		$changes = [
			'woocommerce_checkout_company_field'       => [ 'priority' => 110 ],
			'woocommerce_checkout_address_2_field'     => [ 'priority' => 120 ],
			'woocommerce_checkout_phone_field'         => [ 'priority' => 130 ],
			'woocommerce_checkout_highlight_required_fields' => [
				'priority' => 140,
				'type'     => 'neve_toggle_control',
			],
			'wp_page_for_privacy_policy'               => [ 'priority' => 190 ],
			'woocommerce_terms_page_id'                => [ 'priority' => 200 ],
			'woocommerce_checkout_privacy_policy_text' => [ 'priority' => 210 ],
			'woocommerce_checkout_terms_and_conditions_checkbox_text' => [ 'priority' => 220 ],

		];

		foreach ( $changes as $control_slug => $props ) {
			foreach ( $props as $prop => $new_value ) {
				$this->change_customizer_object( 'control', $control_slug, $prop, $new_value );
			}
		}
	}

	/**
	 * Get boxed layout default.
	 *
	 * @retun bool
	 */
	public static function get_checkout_boxed_layout_default() {
		return get_theme_mod( 'neve_checkout_page_style', 'normal' ) === 'boxed';
	}

	/**
	 * Active callback for box width option.
	 *
	 * @return bool
	 */
	public function box_width_active_callback() {
		return $this->is_boxed_layout() && ! $this->is_standard_layout();
	}

	/**
	 * Check if checkout layout is boxed. Used for controls active callback.
	 *
	 * @return bool
	 */
	public function is_boxed_layout() {
		return get_theme_mod( 'neve_checkout_boxed_layout', self::get_checkout_boxed_layout_default() ) !== false;
	}

	/**
	 * Check if checkout layout is standard. Used for controls active callback.
	 *
	 * @return bool
	 */
	public function is_standard_layout() {
		return get_theme_mod( 'neve_checkout_page_layout', 'standard' ) === 'standard';
	}

	/**
	 * Check if checkout layout is stepped. Used for controls active callback.
	 *
	 * @return bool
	 */
	public function is_stepped_layout() {
		return get_theme_mod( 'neve_checkout_page_layout', 'standard' ) === 'stepped';
	}

	/**
	 * Sanitize regular json.
	 *
	 * @param mixed $input Input.
	 *
	 * @return array
	 */
	public function sanitize_spacing_array( $input ) {
		if ( is_array( $input ) ) {
			return $input;
		}

		return array();
	}

	/**
	 * Sanitize the cart style control
	 *
	 * @param string $value control value.
	 *
	 * @return string
	 */
	public function sanitize_checkout_style( $value ) {
		$allowed = array( 'normal', 'boxed' );

		if ( ! in_array( $value, $allowed, true ) ) {
			return 'normal';
		}

		return $value;
	}

	/**
	 * Sanitize the checkout page layout value
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_checkout_layout( $value ) {
		$allowed_values = array( 'standard', 'vertical', 'stepped' );
		if ( ! in_array( $value, $allowed_values, true ) ) {
			return 'standard';
		}

		return sanitize_text_field( $value );
	}

}
