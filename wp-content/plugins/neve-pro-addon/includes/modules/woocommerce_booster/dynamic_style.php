<?php
/**
 * File that handle dynamic css for Woo pro integration.
 *
 * @package Neve_Pro\Modules\Woocommerce_Booster
 */

namespace Neve_Pro\Modules\Woocommerce_Booster;

use Neve\Core\Settings\Config;
use Neve\Core\Settings\Mods;
use Neve\Core\Styles\Dynamic_Selector;
use Neve_Pro\Core\Generic_Style;
use Neve_Pro\Modules\Woocommerce_Booster\Customizer\Checkout_Page;

/**
 * Class Dynamic_Style
 *
 * @package Neve_Pro\Modules\Woocommerce_Booster
 */
class Dynamic_Style extends Generic_Style {
	const SAME_IMAGE_HEIGHT      = 'neve_force_same_image_height';
	const IMAGE_HEIGHT           = 'neve_image_height';
	const SALE_TAG_COLOR         = 'neve_sale_tag_color';
	const SALE_TAG_TEXT_COLOR    = 'neve_sale_tag_text_color';
	const SALE_TAG_RADIUS        = 'neve_sale_tag_radius';
	const BOX_SHADOW_INTENTISITY = 'neve_box_shadow_intensity';
	const THUMBNAIL_WIDTH        = 'woocommerce_thumbnail_image_width';

	// Sticky add to cart options
	const STICKY_ADD_TO_CART_BACKGROUND_COLOR = 'neve_sticky_add_to_cart_background_color';
	const STICKY_ADD_TO_CART_COLOR            = 'neve_sticky_add_to_cart_color';

	// Typography options
	const MODS_TYPEFACE_ARCHIVE_PRODUCT_TITLE      = 'neve_shop_archive_typography_product_title';
	const MODS_TYPEFACE_ARCHIVE_PRODUCT_PRICE      = 'neve_shop_archive_typography_product_price';
	const MODS_TYPEFACE_SINGLE_PRODUCT_TITLE       = 'neve_single_product_typography_title';
	const MODS_TYPEFACE_SINGLE_PRODUCT_PRICE       = 'neve_single_product_typography_price';
	const MODS_TYPEFACE_SINGLE_PRODUCT_META        = 'neve_single_product_typography_meta';
	const MODS_TYPEFACE_SINGLE_PRODUCT_DESCRIPTION = 'neve_single_product_typography_short_description';
	const MODS_TYPEFACE_SINGLE_PRODUCT_TABS        = 'neve_single_product_typography_tab_titles';
	const MODS_TYPEFACE_SHOP_NOTICE                = 'neve_shop_typography_alert_notice';
	const MODS_TYPEFACE_SHOP_SALE_TAG              = 'neve_shop_typography_sale_tag';

	// Checkout options
	const MODS_CHECKOUT_PAGE_LAYOUT           = 'neve_checkout_page_layout';
	const MODS_CHECKOUT_BOX_WIDTH             = 'neve_checkout_box_width';
	const MODS_CHECKOUT_BOXED_LAYOUT          = 'neve_checkout_boxed_layout';
	const MODS_CHECKOUT_PAGE_BACKGROUND_COLOR = 'neve_checkout_page_background_color';
	const MODS_CHECKOUT_BOX_BACKGROUND_COLOR  = 'neve_checkout_box_background_color';
	const MODS_CHECKOUT_BOX_PADDING           = 'neve_checkout_box_padding';

	// Products per row
	const MODS_SHOP_GRID_PRODUCTS_PER_ROW = 'neve_products_per_row';
	const MODS_SHOP_SINGLE_RELATED_COLUMN = 'neve_single_product_related_columns';


	/**
	 * Register Subscribe Groups
	 *
	 * @return array
	 */
	public function register_subscribers() {
		return [
			[
				'subscribers'       => [ $this, 'single_product_catalog_subscribers' ],
				// TODO: in next versions: update the return value to catch if current post contains products widget
				'activate_callback' => '__return_true',
			],
			[
				'subscribers'       => [ $this, 'sticky_add_to_cart_subscribers' ],
				'activate_callback' => 'is_product',
			],
			[
				'subscribers'       => [ $this, 'checkout_page_subscribers' ],
				'activate_callback' => 'is_checkout',
			],
		];
	}

	/**
	 * Add dynamic style subscribers.
	 *
	 * @param array $subscribers Css subscribers.
	 *
	 * @return array
	 */
	public function add_subscribers( $subscribers = [] ) {
		$dynamic_styles = $this->register_subscribers();

		// filter subscribers according to the activate status and call functions.
		foreach ( $dynamic_styles as $dynamic_style ) {
			if ( ! isset( $dynamic_style['activate_callback'] ) || ! isset( $dynamic_style['subscribers'] ) || ! call_user_func( $dynamic_style['activate_callback'] ) ) {
				continue;
			}

			$subscribers = call_user_func( $dynamic_style['subscribers'], $subscribers );
		}

		return $subscribers;
	}

	/**
	 * Dynamic style for the checkout page.
	 *
	 * @param array $subscribers Current subscribers array.
	 *
	 * @return array
	 */
	private function checkout_page_subscribers( $subscribers ) {

		$is_boxed = Mods::get( self::MODS_CHECKOUT_BOXED_LAYOUT, Checkout_Page::get_checkout_boxed_layout_default() );

		if ( ! $is_boxed ) {
			return $subscribers;
		}

		$is_standard_layout          = Mods::get( self::MODS_CHECKOUT_PAGE_LAYOUT, 'standard' ) === 'standard';
		$checkout_background_default = 'var(--nv-site-bg)';

		if ( ! $is_standard_layout ) {
			$subscribers[] = [
				Dynamic_Selector::KEY_SELECTOR => '.nv-checkout-boxed-style',
				Dynamic_Selector::KEY_RULES    => [
					'--maxwidth' => [
						Dynamic_Selector::META_KEY    => self::MODS_CHECKOUT_BOX_WIDTH,
						Dynamic_Selector::META_IS_RESPONSIVE => true,
						Dynamic_Selector::META_SUFFIX => '%',
					],
				],
			];
		}

		$box_padding_default    = Checkout_Page::get_box_padding_default_value();
		$box_background_default = 'var(--nv-light-bg)';

		$subscribers[] = [
			Dynamic_Selector::KEY_SELECTOR => '.nv-checkout-boxed-style',
			Dynamic_Selector::KEY_RULES    => [
				'--bgcolor'    => [
					Dynamic_Selector::META_KEY     => self::MODS_CHECKOUT_PAGE_BACKGROUND_COLOR,
					Dynamic_Selector::META_DEFAULT => $checkout_background_default,
				],
				'--boxbgcolor' => [
					Dynamic_Selector::META_KEY     => self::MODS_CHECKOUT_BOX_BACKGROUND_COLOR,
					Dynamic_Selector::META_DEFAULT => $box_background_default,
				],
				'--boxpadding' => [
					Dynamic_Selector::META_KEY           => self::MODS_CHECKOUT_BOX_PADDING,
					Dynamic_Selector::META_IS_RESPONSIVE => true,
					Dynamic_Selector::META_DEFAULT       => $box_padding_default,
					Dynamic_Selector::META_SUFFIX        => 'responsive_unit',
					'directional-prop'                   => Config::CSS_PROP_PADDING,
				],
			],
		];

		return $subscribers;
	}

	/**
	 * Dynamic style for single product and catalog page.
	 *
	 * @param array $subscribers That current subscribers.
	 *
	 * @return array
	 */
	public function single_product_catalog_subscribers( $subscribers ) {

		$subscribers[] = [
			'selectors' => '.products.related .products',
			'rules'     => [
				'--shopcoltemplate' => [
					Dynamic_Selector::META_DEFAULT     => 4,
					Dynamic_Selector::META_DEVICE_ONLY => 'desktop',
					Dynamic_Selector::META_KEY         => self::MODS_SHOP_SINGLE_RELATED_COLUMN,
				],
			],
		];


		$rules = [
			'--shopcoltemplate' => [
				Dynamic_Selector::META_KEY           => self::MODS_SHOP_GRID_PRODUCTS_PER_ROW,
				Dynamic_Selector::META_IS_RESPONSIVE => true,
				Dynamic_Selector::META_DEFAULT       => '{"desktop":3,"tablet":2,"mobile":2}',
			],
		];

		$same_image_height = Mods::get( self::SAME_IMAGE_HEIGHT );
		if ( $same_image_height === true ) {
			$rules['--sameimageheight'] = [
				Dynamic_Selector::META_KEY    => self::IMAGE_HEIGHT,
				Dynamic_Selector::META_SUFFIX => 'px',
			];
		}

		$subscribers[] = [
			'selectors' => ':root',
			'rules'     => $rules,
		];

		$subscribers[] = [
			'selectors' => '.product_title.entry-title',
			'rules'     => [
				'--h1texttransform' => [
					Dynamic_Selector::META_KEY => self::MODS_TYPEFACE_SINGLE_PRODUCT_TITLE . '.textTransform',
				],
				'--h1fontweight'    => [
					Dynamic_Selector::META_KEY => self::MODS_TYPEFACE_SINGLE_PRODUCT_TITLE . '.fontWeight',
					'font'                     => 'mods_' . Config::MODS_FONT_HEADINGS,
				],
				'--h1fontsize'      => [
					Dynamic_Selector::META_KEY           => self::MODS_TYPEFACE_SINGLE_PRODUCT_TITLE . '.fontSize',
					Dynamic_Selector::META_IS_RESPONSIVE => true,
					Dynamic_Selector::META_SUFFIX        => 'px',
				],
				'--h1lineheight'    => [
					Dynamic_Selector::META_KEY           => self::MODS_TYPEFACE_SINGLE_PRODUCT_TITLE . '.lineHeight',
					Dynamic_Selector::META_IS_RESPONSIVE => true,
					Dynamic_Selector::META_SUFFIX        => '',
				],
				'--h1letterspacing' => [
					Dynamic_Selector::META_KEY           => self::MODS_TYPEFACE_SINGLE_PRODUCT_TITLE . '.letterSpacing',
					Dynamic_Selector::META_IS_RESPONSIVE => true,
					Dynamic_Selector::META_SUFFIX        => 'px',
				],
			],
		];

		$shop_typography = [
			self::MODS_TYPEFACE_ARCHIVE_PRODUCT_TITLE      => '.woocommerce-loop-product__title',
			self::MODS_TYPEFACE_ARCHIVE_PRODUCT_PRICE      => 'ul.products .price',
			self::MODS_TYPEFACE_SINGLE_PRODUCT_PRICE       => '.summary .price',
			self::MODS_TYPEFACE_SINGLE_PRODUCT_META        => '.product_meta, .woocommerce-product-rating',
			self::MODS_TYPEFACE_SINGLE_PRODUCT_DESCRIPTION => '.single-product .entry-summary .woocommerce-product-details__short-description',
			self::MODS_TYPEFACE_SINGLE_PRODUCT_TABS        => '.woocommerce-tabs a',
			self::MODS_TYPEFACE_SHOP_NOTICE                => '.woocommerce-message,.woocommerce-error, .woocommerce-info',
			self::MODS_TYPEFACE_SHOP_SALE_TAG              => 'span.onsale',
		];

		foreach ( $shop_typography as $mod => $selector ) {
			$font          = $mod == self::MODS_TYPEFACE_ARCHIVE_PRODUCT_TITLE ? 'mods_' . Config::MODS_FONT_HEADINGS : 'mods_' . Config::MODS_FONT_GENERAL;
			$subscribers[] = [
				'selectors' => $selector,
				'rules'     => [
					'--texttransform' => [
						Dynamic_Selector::META_KEY => $mod . '.textTransform',
					],
					'--fontweight'    => [
						Dynamic_Selector::META_KEY => $mod . '.fontWeight',
						'font'                     => $font,
					],
					'--fontsize'      => [
						Dynamic_Selector::META_KEY    => $mod . '.fontSize',
						Dynamic_Selector::META_IS_RESPONSIVE => true,
						Dynamic_Selector::META_SUFFIX => 'px',
					],
					'--lineheight'    => [
						Dynamic_Selector::META_KEY    => $mod . '.lineHeight',
						Dynamic_Selector::META_IS_RESPONSIVE => true,
						Dynamic_Selector::META_SUFFIX => '',
					],
					'--letterspacing' => [
						Dynamic_Selector::META_KEY    => $mod . '.letterSpacing',
						Dynamic_Selector::META_IS_RESPONSIVE => true,
						Dynamic_Selector::META_SUFFIX => 'px',
					],
				],
			];
		}

		$subscribers[] = [
			'selectors' => '.woocommerce span.onsale',
			'rules'     => [
				Config::CSS_PROP_BACKGROUND_COLOR => self::SALE_TAG_COLOR,
				Config::CSS_PROP_COLOR            => self::SALE_TAG_TEXT_COLOR,
				Config::CSS_PROP_BORDER_RADIUS    => [
					Dynamic_Selector::META_KEY    => self::SALE_TAG_RADIUS,
					Dynamic_Selector::META_SUFFIX => '%',
				],
			],
		];

		return $subscribers;
	}

	/**
	 * Dynamic style for sticky add to cart
	 *
	 * @param array $subscribers That current subscribers.
	 *
	 * @return array
	 */
	public function sticky_add_to_cart_subscribers( $subscribers ) {

		$subscribers[] = [
			Dynamic_Selector::KEY_SELECTOR => '.sticky-add-to-cart--active',
			Dynamic_Selector::KEY_RULES    => [
				'--bgcolor' => self::STICKY_ADD_TO_CART_BACKGROUND_COLOR,
				'--color'   => self::STICKY_ADD_TO_CART_COLOR,
			],
		];

		return $subscribers;
	}
}
