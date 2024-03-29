<?php
/**
 * Author:          Stefan Cotitosu <stefan@themeisle.com>
 * Created on:      2019-02-28
 *
 * @package Neve Pro
 */

namespace Neve_Pro\Modules\Blog_Pro\Inline;

use Neve\Views\Inline\Base_Inline;

/**
 * Class Blog_Pro - handles inline style for this module
 *
 * @package Neve_Pro\Modules\Blog_Pro\Inline
 */
class Blog_Pro extends Base_Inline {

	/**
	 * Call inline handlers
	 *
	 * @return void
	 */
	public function init() {
		$this->author_avatar_size();
	}

	/**
	 * Set avatar width and height based on the customizer option
	 */
	private function author_avatar_size() {
		$default_author_sizes = wp_json_encode(
			array(
				'desktop' => 20,
				'tablet'  => 20,
				'mobile'  => 20,
			)
		);
		$avatar_size          = get_theme_mod( 'neve_author_avatar_size', $default_author_sizes );
		$avatar_size          = json_decode( $avatar_size, true );

		$settings = array(
			array(
				'css_prop' => 'width',
				'value'    => $avatar_size,
				'suffix'   => 'px',
			),
			array(
				'css_prop' => 'height',
				'value'    => $avatar_size,
				'suffix'   => 'px',
			),
		);

		$this->add_responsive_style( $settings, '.nv-meta-list .meta.author .photo' );
		add_filter(
			'neve_gravatar_args',
			function ( $args_array ) use ( $avatar_size ) {
				if ( ! isset( $args_array['size'] ) ) {
					return $args_array;
				}
				if ( ! is_array( $avatar_size ) ) {
					return $args_array;
				}
				$args_array['size'] = max( $avatar_size );

				return $args_array;
			}
		);
	}
}
