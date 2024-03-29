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
use Neve_Pro\Core\Loader;
use Neve_Pro\Traits\Shop;

/**
 * Class Single_Product
 *
 * @package Neve_Pro\Modules\Woocommerce_Booster\Customizer
 */
class Single_Product extends Base_Customizer {

	use Shop;

	/**
	 * Add customizer controls
	 */
	public function add_controls() {
		$this->add_order_control();
		$this->add_gallery_layout_control();
		$this->add_checkboxes();
		$this->add_related_count();
		$this->add_related_accordion();
		$this->add_sticky_add_to_cart_accordion();
		$this->add_sticky_add_to_cart_controls();
		$this->add_section_description();
	}

	/**
	 * Add component ordering.
	 */
	private function add_order_control() {
		$order_default_components = array(
			'title',
			'price',
			'description',
			'add_to_cart',
			'meta',
		);

		$components = array(
			'title'       => __( 'Title', 'neve' ),
			'reviews'     => __( 'Reviews', 'neve' ),
			'price'       => __( 'Price', 'neve' ),
			'description' => __( 'Short Description', 'neve' ),
			'add_to_cart' => __( 'Add to cart', 'neve' ),
			'meta'        => __( 'Meta', 'neve' ),
		);

		$this->add_control(
			new Control(
				'neve_single_product_elements_order',
				array(
					'sanitize_callback' => array( $this, 'sanitize_elements_ordering' ),
					'default'           => wp_json_encode( $order_default_components ),
				),
				array(
					'label'      => esc_html__( 'Elements Order', 'neve' ),
					'section'    => 'neve_single_product_layout',
					'components' => $components,
					'priority'   => 10,
				),
				'Neve\Customizer\Controls\React\Ordering'
			)
		);

		$default_value = get_theme_mod( 'neve_shop_price_template', '{price}' );
		$this->add_control(
			new Control(
				'neve_product_price_template',
				array(
					'sanitize_callback' => 'sanitize_text_field',
					'default'           => $default_value,
				),
				array(
					'priority'        => 11,
					'section'         => 'neve_single_product_layout',
					'label'           => esc_html__( 'Price template', 'neve' ),
					'type'            => 'text',
					'description'     => __( 'To show the price of the product, use {price} in your template.', 'neve' ),
					'active_callback' => function () {
						$elements_order = $this->get_product_elements_order( 'product' );
						return in_array( 'price', $elements_order, true );
					},
				)
			)
		);
	}

	/**
	 * Add gallery layout control.
	 */
	private function add_gallery_layout_control() {
		$this->add_control(
			new Control(
				'neve_single_product_gallery_layout',
				array(
					'default'           => 'normal',
					'sanitize_callback' => array( $this, 'sanitize_gallery_layout' ),
				),
				array(
					'label'    => esc_html__( 'Gallery Layout', 'neve' ),
					'section'  => 'neve_single_product_layout',
					'priority' => 15,
					'choices'  => array(
						'normal' => array(
							'name' => __( 'Normal', 'neve' ),
							'url'  => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAABqCAMAAABpj1iyAAAACVBMVEUAyv/V1dX////o4eoDAAAAcUlEQVR4Ae3ZMQoAMQhFQd37H3pJbxOIIDKv/NW0Ynwjw1rPisZ2sbCwsLCwsLCwsLCwsLCwsLCwsLCwsLCwsLCwsLCmhOWzf/Ysut37WFhYWFhYWFhYWFhYXTnIsLLo5Y6FhYWFhYWFhYWF5U7EmtYP2ZZKOeVUSPIAAAAASUVORK5CYII=',
						),
						'left'   => array(
							'name' => __( 'Left', 'neve' ),
							'url'  => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAABqCAMAAABpj1iyAAAACVBMVEUAyv/V1dX////o4eoDAAAAc0lEQVR42u3bMQoAIAwEwej/H21hG5AQEJHZ0mqqwBXGfDKs71mRtN/7YWFhYWFhYWFhYWH1WNWwsLBus0YSFhYWFhYWFhYWFtaBZWJgYWG5W1hYWFhYWFhYWFhWNRYWlruFhYWFhYWFhYWFZVX7+4pVaAGXVUBZRr/2PwAAAABJRU5ErkJggg==',
						),
					),
				),
				'\Neve\Customizer\Controls\React\Radio_Image'
			)
		);
	}

	/**
	 * Add checkbox controls.
	 *
	 * - image zoom toggle
	 * - breadcrumbs toggle
	 * - product tabs toggle
	 * - related products toggle
	 * - up-sells toggle
	 * - related products slider toggle
	 * - related viewed products box toggle
	 */
	private function add_checkboxes() {
		$checkboxes = array(
			'neve_enable_product_gallery_thumbnails_slider' => array(
				'default'  => false,
				'priority' => 20,
				'label'    => __( 'Enable Gallery Thumbnails Slider', 'neve' ),
			),
			'neve_enable_product_image_zoom_effect' => array(
				'default'  => true,
				'priority' => 25,
				'label'    => __( 'Enable Image Zoom Effect', 'neve' ),
			),
			'neve_enable_product_breadcrumbs'       => array(
				'default'  => true,
				'priority' => 30,
				'label'    => __( 'Show Breadcrumbs', 'neve' ),
			),
			'neve_enable_product_tabs'              => array(
				'default'  => true,
				'priority' => 35,
				'label'    => __( 'Show Product Tabs', 'neve' ),
			),
			'neve_enable_seamless_add_to_cart'      => array(
				'default'  => false,
				'priority' => 36,
				'label'    => __( 'Enable Seamless Add to Cart', 'neve' ),
			),
			'neve_enable_product_upsells'           => array(
				'default'  => true,
				'priority' => 45,
				'label'    => __( 'Show Upsell Products', 'neve' ),
			),
			'neve_enable_related_viewed'            => array(
				'default'  => false,
				'priority' => 50,
				'label'    => __( 'Show Recently Viewed Products', 'neve' ),
			),
			'neve_enable_product_navigation'        => array(
				'default'  => false,
				'priority' => 55,
				'label'    => __( 'Enable Product Navigation', 'neve' ),
			),
			'neve_enable_product_related'           => array(
				'default'  => true,
				'priority' => 57,
				'label'    => __( 'Show Related Products', 'neve' ),
			),
			'neve_enable_product_related_slider'    => array(
				'default'         => false,
				'priority'        => 58,
				'active_callback' => array( $this, 'hide_if_related_disabled' ),
				'label'           => __( 'Enable Related Products Slider', 'neve' ),
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
						'section'         => 'neve_single_product_layout',
						'type'            => 'neve_toggle_control',
						'priority'        => $args['priority'],
						'active_callback' => isset( $args['active_callback'] ) ? $args['active_callback'] : '__return_true',
					)
				)
			);
		}
	}

	/**
	 * Add related products number control.
	 */
	private function add_related_count() {
		$this->add_control(
			new Control(
				'neve_single_product_related_count',
				array(
					'sanitize_callback' => 'absint',
				),
				array(
					'label'           => esc_html__( 'Number of Related Products', 'neve' ),
					'section'         => 'neve_single_product_layout',
					'default'         => 4,
					'type'            => 'number',
					'input_attrs'     => array(
						'min' => 1,
						'max' => 20,
					),
					'priority'        => 59,
					'active_callback' => array( $this, 'hide_if_related_disabled' ),
				)
			)
		);
		$this->add_control(
			new Control(
				'neve_single_product_related_columns',
				array(
					'sanitize_callback' => 'absint',
				),
				array(
					'label'           => esc_html__( 'Number of Columns', 'neve' ),
					'section'         => 'neve_single_product_layout',
					'default'         => 4,
					'type'            => 'number',
					'input_attrs'     => array(
						'min' => 1,
						'max' => 6,
					),
					'priority'        => 60,
					'active_callback' => array( $this, 'hide_if_related_disabled' ),
				)
			)
		);
	}

	/**
	 * Adds related products settings accordion
	 */
	private function add_related_accordion() {
		$this->add_control(
			new Control(
				'neve_related_products_heading',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'label'            => esc_html__( 'Related Products', 'neve' ),
					'section'          => 'neve_single_product_layout',
					'priority'         => 56,
					'class'            => 'related-products-accordion',
					'accordion'        => true,
					'expanded'         => false,
					'controls_to_wrap' => 4,
				),
				'Neve\Customizer\Controls\Heading'
			)
		);
	}

	/**
	 * Add sticky add to cart accordion
	 */
	private function add_sticky_add_to_cart_accordion() {
		$this->add_control(
			new Control(
				'neve_sticky_add_to_cart_heading',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'label'            => esc_html__( 'Sticky Add to Cart', 'neve' ),
					'section'          => 'neve_single_product_layout',
					'priority'         => 70,
					'class'            => 'sticky-add-to-cart-accordion',
					'accordion'        => true,
					'expanded'         => false,
					'controls_to_wrap' => 5,
				),
				'Neve\Customizer\Controls\Heading'
			)
		);
	}

	/**
	 * Add sticky add to cart controls
	 */
	private function add_sticky_add_to_cart_controls() {
		$this->add_control(
			new Control(
				'neve_enable_sticky_add_to_cart',
				array(
					'sanitize_callback' => array( $this, 'sanitize_responsive_checkbox' ),
				),
				array(
					'label'            => __( 'Enable Sticky Add to Cart', 'neve' ),
					'excluded_devices' => [ 'tablet' ],
					'section'          => 'neve_single_product_layout',
					'priority'         => 75,
				),
				'Neve\Customizer\Controls\React\Responsive_Toggle'
			)
		);

		$this->add_control(
			new Control(
				'neve_enable_sticky_add_to_cart_tabs',
				array(
					'sanitize_callback' => 'neve_sanitize_checkbox',
					'default'           => true,
				),
				array(
					'label'           => __( 'Enable Tabs in the Sticky Add to Cart', 'neve' ),
					'type'            => 'neve_toggle_control',
					'section'         => 'neve_single_product_layout',
					'priority'        => 76,
					'active_callback' => array( $this, 'is_sticky_add_to_cart_enabled_callback' ),
				)
			)
		);

		$this->add_control(
			new Control(
				'neve_sticky_add_to_cart_position',
				array(
					'default'           => 'bottom',
					'sanitize_callback' => array( $this, 'sanitize_sticky_add_to_cart_position' ),
				),
				array(
					'label'           => esc_html__( 'Position', 'neve' ),
					'section'         => 'neve_single_product_layout',
					'priority'        => 80,
					'type'            => 'select',
					'choices'         => array(
						'top'    => esc_html__( 'Top', 'neve' ),
						'bottom' => esc_html__( 'Bottom', 'neve' ),
					),
					'active_callback' => array( $this, 'is_sticky_add_to_cart_enabled_callback' ),
				)
			)
		);

		$this->add_control(
			new Control(
				'neve_sticky_add_to_cart_background_color',
				array(
					'sanitize_callback' => 'neve_sanitize_colors',
					'transport'         => $this->selective_refresh,
					'default'           => 'var(--nv-site-bg)',
				),
				array(
					'label'                 => __( 'Background Color', 'neve' ),
					'section'               => 'neve_single_product_layout',
					'priority'              => 85,
					'input_attrs'           => [
						'allow_gradient' => Loader::has_compatibility( 'gradient_picker' ),
					],
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => array(
						'cssVar'   => [
							'vars'     => '--bgcolor',
							'selector' => '.sticky-add-to-cart--active',
						],
						'template' => '
							div.sticky-add-to-cart--active {
								background: {{value}};
							}',
					),
					'active_callback'       => array( $this, 'is_sticky_add_to_cart_enabled_callback' ),
				),
				'Neve\Customizer\Controls\React\Color'
			)
		);

		$this->add_control(
			new Control(
				'neve_sticky_add_to_cart_color',
				array(
					'sanitize_callback' => 'neve_sanitize_colors',
					'transport'         => $this->selective_refresh,
				),
				array(
					'label'                 => __( 'Color', 'neve' ),
					'section'               => 'neve_single_product_layout',
					'priority'              => 90,
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => array(
						'cssVar'   => [
							'vars'     => '--color',
							'selector' => '.sticky-add-to-cart--active',
						],
						'template' => '
							div.sticky-add-to-cart--active {
								color: {{value}};
							}',
					),
					'active_callback'       => array( $this, 'is_sticky_add_to_cart_enabled_callback' ),
				),
				'Neve\Customizer\Controls\React\Color'
			)
		);
	}

	/**
	 * Add description for single products.
	 */
	private function add_section_description() {
		$checkout_section               = $this->wpc->get_section( 'neve_single_product_layout' );
		$checkout_section->description .= __( 'Extend single product pages with more features to boost sales.', 'neve' ) . ' ' . apply_filters( 'neve_external_link', 'https://bit.ly/neve-woo-sgl', __( 'Learn more', 'neve' ) );
		$this->wpc->add_section( $checkout_section );
	}

	/**
	 * Active callback for when related is disabled.
	 *
	 * @return mixed|string
	 */
	public function hide_if_related_disabled() {
		return get_theme_mod( 'neve_enable_product_related', true );
	}

	/**
	 * Sanitize components ordering
	 *
	 * @param string $value json encoded array.
	 *
	 * @return string
	 */
	public function sanitize_elements_ordering( $value ) {

		$allowed = array(
			'title',
			'reviews',
			'price',
			'description',
			'add_to_cart',
			'meta',
		);

		if ( empty( $value ) ) {
			return wp_json_encode( $allowed );
		}

		$decoded = json_decode( $value, true );
		if ( ! is_array( $decoded ) ) {
			return wp_json_encode( $allowed );
		}

		foreach ( $decoded as $val ) {
			if ( ! in_array( $val, $allowed, true ) ) {
				return wp_json_encode( $allowed );
			}
		}

		return $value;
	}

	/**
	 * Sanitize gallery layout control.
	 *
	 * @param string $value the value.
	 *
	 * @return string
	 */
	public function sanitize_gallery_layout( $value ) {
		$allowed_values = array( 'normal', 'left' );
		if ( ! in_array( $value, $allowed_values, true ) ) {
			return 'normal';
		}

		return esc_html( $value );
	}

	/**
	 * Active callback for controls for sticky add to cart.
	 *
	 * @return bool
	 */
	public function is_sticky_add_to_cart_enabled_callback() {
		$enabled_sticky = get_theme_mod( 'neve_enable_sticky_add_to_cart' );
		if ( empty( $enabled_sticky ) || ( $enabled_sticky['mobile'] === false && $enabled_sticky['desktop'] === false ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Sanitize the sticky header responsive value.
	 *
	 * @param array $arr array from theme mod.
	 *
	 * @return array
	 */
	public function sanitize_responsive_checkbox( $arr ) {
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
	 * Sanitize the pagination type
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_sticky_add_to_cart_position( $value ) {
		$allowed_values = array( 'top', 'bottom' );
		if ( ! in_array( $value, $allowed_values, true ) ) {
			return 'bottom';
		}

		return esc_html( $value );
	}
}
