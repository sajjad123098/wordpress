<?php
/**
 * Sanitize functions.
 *
 * @package Neve_Pro
 */
namespace Neve_Pro\Traits;

use Neve_Pro\Modules\Blog_Pro\Customizer\Defaults\Single_Post;

/**
 * Trait Sanitize_Functions
 *
 * @package Neve_Pro\Modules\Blog_Pro\Customizer
 */
trait Sanitize_Functions {
	use Single_Post;

	/**
	 * Sanitize sharing order.
	 *
	 * @param string $value Value from the control.
	 *
	 * @return string
	 */
	public function sanitize_sharing_icons_repeater( $value ) {
		$default_value = apply_filters( 'neve_sharing_icons_default_value', $this->social_icons_default() );
		$fields        = array(
			'social_network',
			'visibility',
		);

		$valid = $this->sanitize_repeater_json( $value, $fields );

		if ( $valid === false ) {
			return wp_json_encode( $default_value );
		}

		return $value;
	}

	/**
	 * Sanitize the social sharing tag control.
	 *
	 * @param string $value Control value.
	 *
	 * @return string
	 */
	public function sanitize_sharing_icons_tag( $value ) {
		$allowed_tags = [ 'p', 'span', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ];
		if ( ! in_array( $value, $allowed_tags, true ) ) {
			return 'span';
		}
		return $value;
	}

	/**
	 * Sanitize related posts title tag.
	 *
	 * @param string $value Control value.
	 *
	 * @return string
	 */
	public function sanitize_title_html_tag( $value ) {
		$allowed_tags = [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ];
		if ( ! in_array( $value, $allowed_tags, true ) ) {
			return 'h2';
		}

		return $value;
	}

	/**
	 * Sanitize the repeater control.
	 *
	 * @param string $value json value.
	 * @param array  $must_have_fields array of must have fields for repeater.
	 *
	 * @return bool
	 */
	public function sanitize_repeater_json( $value, $must_have_fields = array( 'visibility' ) ) {
		$decoded = json_decode( $value, true );

		if ( ! is_array( $decoded ) ) {
			return false;
		}
		foreach ( $decoded as $item ) {
			if ( ! is_array( $item ) ) {
				return false;
			}

			foreach ( $must_have_fields as $field_key ) {
				if ( ! array_key_exists( $field_key, $item ) ) {
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Sanitize image hover control.
	 *
	 * @param string $value value from the control.
	 *
	 * @return string
	 */
	public function sanitize_image_hover( $value ) {
		$allowed_values = array(
			'none',
			'next',
			'swipe',
			'zoom',
			'blur',
			'fadein',
			'fadeout',
			'glow',
			'colorize',
			'grayscale',
		);
		if ( ! in_array( $value, $allowed_values, true ) ) {
			return 'none';
		}

		return $value;
	}

	/**
	 * Sanitize the repeater control for payment icons.
	 *
	 * @param string $value The value to sanitize.
	 *
	 * @return false|string
	 */
	public function sanitize_payment_icons_repeater( $value ) {

		$sanitized_value = [];

		$allowed_slugs = apply_filters(
			'neve_payment_options',
			array(
				'visa',
				'visa-electron',
				'paypal',
				'stripe',
				'mastercard',
				'cash-on-delivery',
				'amazon',
				'american-express',
				'apple-pay',
				'bank-transfer',
				'google-pay',
				'google-wallet',
				'maestro',
				'pay-u',
				'western-union',
			)
		);


		$allowed_properties = [
			'slug',
			'title',
			'visibility',
			'blocked',
			'svg',
		];

		if ( empty( $value ) ) {
			return wp_json_encode( $sanitized_value );
		}

		$decoded = json_decode( $value, true );

		foreach ( $decoded as $val ) {
			if ( isset( $val['slug'] ) && ! in_array( $val['slug'], $allowed_slugs, true ) ) {
				return wp_json_encode( $sanitized_value );
			}

			foreach ( $val as $property => $property_value ) {
				if ( ! in_array( $property, $allowed_properties, true ) ) {
					return wp_json_encode( $sanitized_value );
				}
				$val[ $property ] = $property === 'svg' ? $this->sanitize_svg_input( $property_value ) : wp_kses_post( $property_value );
			}

			$sanitized_value[] = $val;
		}

		return wp_json_encode( $sanitized_value );
	}

	/**
	 * Sanitize SVG input.
	 *
	 * @param string $content The input to sanitize.
	 *
	 * @return string
	 */
	public function sanitize_svg_input( $content ) {
		// Remove comments
		$content = preg_replace( '/<!--(.*)-->/Uis', '', $content );

		// Load the SVG content into a DOMDocument
		$dom = new \DOMDocument();
		// phpcs:ignore
		@$dom->loadXML( $content );

		// List of allowed SVG elements
		$allowed_tags = [
			'svg',
			'circle',
			'ellipse',
			'line',
			'polygon',
			'polyline',
			'rect',
			'path',
			'g',
			'defs',
			'use',
			'style',
			'text',
			'symbol',
			'desc',
			'title',
		];

		// List of allowed SVG attributes (again, expand as needed)
		$allowed_attrs = [
			'id',
			'class',
			'viewBox',
			'width',
			'height',
			'fill',
			'stroke',
			'stroke-width',
			'stroke-linecap',
			'stroke-linejoin',
			'd',
			'transform',
			'x',
			'y',
			'r',
			'cx',
			'cy',
		];

		// Remove disallowed elements
		foreach ( $dom->getElementsByTagName( '*' ) as $node ) {
			// phpcs:disable
			if ( ! in_array( $node->tagName, $allowed_tags ) ) {
				$node->parentNode->removeChild( $node );
				// phpcs:enable
			} else {
				// For allowed elements, remove any disallowed attributes
				for ( $i = $node->attributes->length - 1; $i >= 0; $i-- ) {
					// @phpstan-ignore-next-line
					$attr_name = $node->attributes->item( $i )->name;

					if ( ! in_array( $attr_name, $allowed_attrs ) ) {
						$node->removeAttribute( $attr_name );
					}
				}
			}
		}

		// Save the cleaned SVG content
		$sanitized_svg = $dom->saveXML();

		return preg_replace( '/<\?xml.*\?>\n/', '', $sanitized_svg );
	}
}
