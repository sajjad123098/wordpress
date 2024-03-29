<?php
/**
 * Add inline style for shop page.
 *
 * @package Neve_Pro\Modules\Woocommerce_Booster\Inline
 */

namespace Neve_Pro\Modules\Woocommerce_Booster\Inline;

use Neve\Views\Inline\Base_Inline;

/**
 * Class Shop_Page
 *
 * @package Neve_Pro\Modules\Woocommerce_Booster\Inline
 */
class Shop_Page extends Base_Inline {

	/**
	 * Init function.
	 *
	 * @return mixed|void
	 */
	public function init() {
		$this->same_image_height();
		$this->sale_tag();
	}

	/**
	 * Add style for force same image height.
	 */
	private function same_image_height() {
		$same_image_height = get_theme_mod( 'neve_force_same_image_height' );
		if ( $same_image_height === false ) {
			return;
		}

		$image_height    = get_theme_mod( 'neve_image_height', 230 );
		$image_selectors = '.woocommerce ul.products li.product .sp-product-image.sp-same-image-height';
		$this->add_style(
			array(
				array(
					'css_prop' => 'height',
					'value'    => $image_height,
					'suffix'   => 'px',
				),
			),
			$image_selectors
		);
	}


	/**
	 * Add style for sale tag.
	 */
	private function sale_tag() {
		$color      = get_theme_mod( 'neve_sale_tag_color' );
		$text_color = get_theme_mod( 'neve_sale_tag_text_color' );
		$selector   = '.woocommerce span.onsale';

		$this->add_style(
			array(
				array(
					'css_prop' => 'background-color',
					'value'    => $color,
				),
			),
			$selector
		);

		$this->add_style(
			array(
				array(
					'css_prop' => 'color',
					'value'    => $text_color,
				),
			),
			$selector
		);

		$radius = get_theme_mod( 'neve_sale_tag_radius' );
		if ( empty( $radius ) ) {
			return;
		}
		$this->add_style(
			array(
				array(
					'css_prop' => 'border-radius',
					'value'    => $radius,
					'suffix'   => '%',
				),
			),
			$selector
		);
	}

}
