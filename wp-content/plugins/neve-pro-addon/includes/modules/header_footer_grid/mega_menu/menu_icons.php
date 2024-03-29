<?php
/**
 * Icons.
 *
 * @package Neve Pro
 */

namespace Neve_Pro\Modules\Header_Footer_Grid\Mega_Menu;

/**
 * Menu Icons trait.
 */
trait Menu_Icons {
	/**
	 * Themeisle icons
	 *
	 * @var array
	 */
	public static $ti = null;
	/**
	 * WordPress icons
	 *
	 * @var array
	 */
	public static $dashicons = null;
	/**
	 * FontAwesome icons
	 *
	 * @var array
	 */
	public static $fa = null;

	/**
	 * Get icon set array.
	 *
	 * @param string $slug ["ti"|"wp"|"fa"] icon set slug.
	 *
	 * @return array
	 */
	public function get_icon_set( $slug ) {
		if ( self::$$slug === null ) {
			self::$$slug = $this->get_from_file( $slug );
		}

		return self::$$slug;
	}

	/**
	 * Parse icon.
	 *
	 * @param string $key icon key.
	 */
	public function parse_svg_json( $key ) {
		if ( strpos( $key, 'ti-' ) !== false ) {
			return $this->parse_single_icon( $key, 'ti' );
		}
		if ( strpos( $key, 'dashicons-' ) !== false ) {
			return $this->parse_single_icon( $key, 'dashicons' );
		}
		if ( strpos( $key, 'fa-' ) !== false ) {
			return $this->parse_single_icon( $key, 'fa' );
		}

		return '';
	}

	/**
	 * Get json icons from file.
	 *
	 * @param string $file file name, without extension.
	 *
	 * @return array | null
	 */
	private function get_from_file( $file ) {
		$path = NEVE_PRO_SPL_ROOT . 'modules/header_footer_grid/mega_menu/icons/' . $file . '.json';

		require_once ABSPATH . '/wp-admin/includes/file.php';
		global $wp_filesystem;
		WP_Filesystem();

		$json  = $wp_filesystem->get_contents( $path );
		$icons = json_decode( $json, true );

		if ( ! is_array( $icons ) ) {
			return null;
		}

		return $icons;
	}

	/**
	 * Parse singular icon, receiving key and set.
	 *
	 * @param string $key the icon key.
	 * @param string $set the icon set - corresponds to one of the sets described in this class.
	 *
	 * @return string
	 */
	private function parse_single_icon( $key, $set ) {
		$icons = self::$$set;

		if ( $icons === null ) {
			$icons = $this->get_icon_set( $set );
		}

		if ( ! isset( $icons[ $key ] ) ) {
			return '';
		}

		return $this->json_to_svg( $icons[ $key ] );
	}

	/**
	 * Transform JSON to SVG markup.
	 *
	 * Runs recursively for its children.
	 *
	 * @param array $icon_array the icon array.
	 *
	 * @return string
	 */
	private function json_to_svg( $icon_array, $size = 20 ) {
		$output = '';

		if ( ! isset( $icon_array['tag'] ) ) {
			return '';
		}

		$output .= '<' . esc_html( $icon_array['tag'] );
		if ( isset( $icon_array['atr'] ) ) {
			foreach ( $icon_array['atr'] as $attribute => $value ) {
				$attribute = $attribute === 'vb' ? 'viewBox' : $attribute;

				$output .= ' ' . esc_html( $attribute ) . '="' . esc_attr( $value ) . '"';
			}
		}

		if ( $icon_array['tag'] === 'svg' ) {
			$output .= ' width="' . esc_attr( $size ) . 'px" height="' . esc_attr( $size ) . 'px"';
		}

		$output .= '>';

		if ( isset( $icon_array['ch'] ) && is_array( $icon_array['ch'] ) ) {
			foreach ( $icon_array['ch'] as $child ) {
				$output .= $this->json_to_svg( $child );
			}
		}

		$output .= '</' . esc_html( $icon_array['tag'] ) . '>';

		return $output;
	}
}
