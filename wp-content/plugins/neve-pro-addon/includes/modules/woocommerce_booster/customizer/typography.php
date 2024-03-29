<?php
/**
 * Typography controls for WooCommerce.
 *
 * @package WooCommerce Booster
 */

namespace Neve_Pro\Modules\Woocommerce_Booster\Customizer;

use Neve\Customizer\Base_Customizer;
use Neve\Customizer\Types\Control;
use Neve\Customizer\Types\Section;

/**
 * Class Typography
 *
 * @package Neve_Pro\Modules\Woocommerce_Booster\Customizer
 */
class Typography extends Base_Customizer {

	/**
	 * Typography settings.
	 *
	 * @var array
	 */
	private $controls_to_register = array();

	/**
	 * Typography constructor.
	 */
	public function __construct() {
		$this->controls_to_register = $this->get_typography_controls();
	}

	/**
	 * Get shop typography controls
	 *
	 * @return array
	 */
	private function get_typography_controls() {
		return array(
			'neve_shop_archive_typography_product_title' => array(
				'label'                 => __( 'Product title', 'neve' ),
				'category_label'        => __( 'Product Archive', 'neve' ),
				'priority'              => 10,
				'font_family_control'   => 'neve_headings_font_family',
				'live_refresh_selector' => '.woocommerce ul.products li.product h2.woocommerce-loop-product__title',
			),
			'neve_shop_archive_typography_product_price' => array(
				'label'                 => __( 'Product price', 'neve' ),
				'priority'              => 20,
				'font_family_control'   => 'neve_body_font_family',
				'live_refresh_selector' => '.woocommerce ul.products li.product .price, .woocommerce ul.products li.product span.price del, .woocommerce ul.products li.product span.price ins',
			),
			'neve_single_product_typography_title'       => array(
				'label'                 => __( 'Product title', 'neve' ),
				'category_label'        => __( 'Single Product', 'neve' ),
				'priority'              => 30,
				'font_family_control'   => 'neve_headings_font_family',
				'live_refresh_selector' => '.woocommerce.single .product_title',
				'vars'                  => [
					'--h1texttransform' => 'textTransform',
					'--h1fontweight'    => 'fontWeight',
					'--h1fontsize'      => [
						'key'        => 'fontSize',
						'responsive' => true,
					],
					'--h1lineheight'    => [
						'key'        => 'lineHeight',
						'responsive' => true,
					],
					'--h1letterspacing' => [
						'key'        => 'letterSpacing',
						'suffix'     => 'px',
						'responsive' => true,
					],
				],
			),
			'neve_single_product_typography_price'       => array(
				'label'                 => __( 'Product price', 'neve' ),
				'priority'              => 40,
				'font_family_control'   => 'neve_body_font_family',
				'live_refresh_selector' => '.woocommerce div.product p.price, .woocommerce div.product p.price del, .woocommerce div.product p.price ins',
			),
			'neve_single_product_typography_meta'        => array(
				'label'                 => __( 'Product meta', 'neve' ),
				'priority'              => 50,
				'font_family_control'   => 'neve_body_font_family',
				'live_refresh_selector' => '.product_meta, .woocommerce div.product .woocommerce-product-rating',
			),
			'neve_single_product_typography_short_description' => array(
				'label'                 => __( 'Product short description', 'neve' ),
				'priority'              => 60,
				'font_family_control'   => 'neve_body_font_family',
				'live_refresh_selector' => '.single-product .entry-summary .woocommerce-product-details__short-description',
			),
			'neve_single_product_typography_tab_titles'  => array(
				'label'                 => __( 'Tab titles', 'neve' ),
				'priority'              => 70,
				'font_family_control'   => 'neve_body_font_family',
				'live_refresh_selector' => '.woocommerce div.product .woocommerce-tabs ul.tabs li a',
			),
			'neve_shop_typography_alert_notice'          => array(
				'label'                 => __( 'Alert notice', 'neve' ),
				'category_label'        => __( 'Other', 'neve' ),
				'priority'              => 80,
				'font_family_control'   => 'neve_body_font_family',
				'live_refresh_selector' => '.woocommerce div.woocommerce-message, .woocommerce-page div.woocommerce-message, .woocommerce ul.woocommerce-error, .woocommerce-page ul.woocommerce-error',
			),
			'neve_shop_typography_sale_tag'              => array(
				'label'                 => __( 'Sale tag', 'neve' ),
				'priority'              => 90,
				'font_family_control'   => 'neve_body_font_family',
				'live_refresh_selector' => '.woocommerce span.onsale',
			),
		);
	}

	/**
	 * Add customizer controls
	 *
	 * @return bool
	 */
	public function add_controls() {
		$this->typography_section();
		$this->controls_typography_shop();
		return true;
	}

	/**
	 * Add shop typography section in customizer.
	 */
	private function typography_section() {
		$this->add_section(
			new Section(
				'neve_typography_shop',
				array(
					'title'    => __( 'WooCommerce', 'neve' ),
					'panel'    => 'neve_typography',
					'priority' => 55,
				)
			)
		);
	}

	/**
	 * Add controls for shop typography.
	 *
	 * @return bool
	 */
	private function controls_typography_shop() {
		if ( empty( $this->controls_to_register ) ) {
			return false;
		}

		$vars = [
			'--texttransform' => 'textTransform',
			'--fontweight'    => 'fontWeight',
			'--fontsize'      => [
				'key'        => 'fontSize',
				'responsive' => true,
			],
			'--lineheight'    => [
				'key'        => 'lineHeight',
				'responsive' => true,
			],
			'--letterspacing' => [
				'key'        => 'letterSpacing',
				'suffix'     => 'px',
				'responsive' => true,
			],
		];

		foreach ( $this->controls_to_register as $control_id => $control_settings ) {
			$settings = array(
				'label'            => $control_settings['label'],
				'section'          => 'neve_typography_shop',
				'priority'         => $control_settings['priority'],
				'class'            => esc_attr( 'typography-blog-' . $control_id ),
				'accordion'        => true,
				'controls_to_wrap' => 1,
				'expanded'         => false,
			);
			if ( array_key_exists( 'category_label', $control_settings ) ) {
				$settings['category_label'] = $control_settings['category_label'];
			}

			$this->add_control(
				new Control(
					$control_id . '_accordion_wrap',
					array(
						'sanitize_callback' => 'sanitize_text_field',
						'transport'         => $this->selective_refresh,
					),
					$settings,
					'Neve\Customizer\Controls\Heading'
				)
			);

			$this->add_control(
				new Control(
					$control_id,
					[
						'transport' => $this->selective_refresh,
					],
					[
						'priority'              => $control_settings['priority'] += 1,
						'section'               => 'neve_typography_shop',
						'type'                  => 'neve_typeface_control',
						'font_family_control'   => $control_settings['font_family_control'],
						'live_refresh_selector' => $control_settings['live_refresh_selector'],
						'live_refresh_css_prop' => [
							'cssVar' => [
								'vars'     => isset( $control_settings['vars'] ) ? $control_settings['vars'] : $vars,
								'selector' => $control_settings['live_refresh_selector'],
							],
						],
						'refresh_on_reset'      => true,
						'input_attrs'           => array(
							'default_is_empty'       => true,
							'size_units'             => [ 'em', 'px', 'rem' ],
							'weight_default'         => 'none',
							'size_default'           => array(
								'suffix'  => array(
									'mobile'  => 'px',
									'tablet'  => 'px',
									'desktop' => 'px',
								),
								'mobile'  => '',
								'tablet'  => '',
								'desktop' => '',
							),
							'line_height_default'    => array(
								'mobile'  => '',
								'tablet'  => '',
								'desktop' => '',
							),
							'letter_spacing_default' => array(
								'mobile'  => '',
								'tablet'  => '',
								'desktop' => '',
							),
						),
					],
					'\Neve\Customizer\Controls\React\Typography'
				)
			);
		}

		return true;
	}
}
