<?php
/**
 * Inline styles traits, shared with other classes.
 *
 * @package Neve_Pro
 */

namespace Neve_Pro\Traits;

use Neve\Core\Dynamic_Css;

/**
 * Trait Core
 *
 * @package Neve_Pro\Traits
 */
trait Inline_Styles {

	/**
	 * Get css code for specific thumbnail style.
	 *
	 * @param string $effect Current effect.
	 *
	 * @return string
	 */
	public function get_thumbnail_effect_style( $effect ) {
		$css = '
		.nv-has-effect .img-wrap a:hover {
            opacity: 1;
        }
        .nv-has-effect .img-wrap{
            overflow: hidden;
        }
        .nv-has-effect img {
            transition: all 0.2s ease;
        }
        ';

		switch ( $effect ) {
			case 'next':
				$css .= '
                    .nv-has-effect .has-post-thumbnail:not(.layout-covers) .next{
                      position: relative;
                    }
                    .nv-has-effect .has-post-thumbnail .next img:nth-of-type(2) {
                      opacity: 0;
                      position: absolute;
                      top: 0;
                    }
                    .nv-has-effect .has-post-thumbnail:hover .next img:nth-of-type(2) {
                       opacity: 1;
                    }
                ';
				break;
			case 'swipe':
				$css .= '
                    .nv-has-effect .has-post-thumbnail:not(.layout-covers) .swipe{
                        position: relative;
                    }
                    .nv-has-effect .has-post-thumbnail .swipe img:nth-of-type(2) {
                      right: 100%;
                      top: 0;
                      position: absolute;
                    }
                    .nv-has-effect .has-post-thumbnail:hover .swipe img:nth-of-type(2) {
                        right: 0;
                    }
                ';
				break;
			case 'zoom':
				$css .= '
                .nv-has-effect .has-post-thumbnail:hover .zoom img {
                    transform: scale(1.1);
                 }
                ';
				break;
			case 'blur':
				$css .= '
                .nv-has-effect .has-post-thumbnail:hover .blur img {
                    filter: blur(5px);
                 }
                ';
				break;
			case 'fadein':
				$css .= '
                .nv-has-effect .has-post-thumbnail .fadein img {
                    opacity: .7;
                 }
                 .nv-has-effect .has-post-thumbnail:hover .fadein img {
                    opacity: 1;
                 }
                ';
				break;
			case 'fadeout':
				$css .= '
                .nv-has-effect .has-post-thumbnail:hover .fadeout img {
                   opacity: .7;
                }
                ';
				break;
			case 'glow':
				$css .= '
                .nv-has-effect .has-post-thumbnail:hover .glow img {
                   filter: brightness(1.1);
                }
                ';
				break;
			case 'grayscale':
				$css .= '
                .nv-has-effect .has-post-thumbnail:hover .grayscale img {
                   filter: grayscale(100%);
                }
                ';
				break;
			case 'colorize':
				$css .= '
                .nv-has-effect .has-post-thumbnail .colorize img {
                    filter: grayscale(100%);
                 }
                 .nv-has-effect .has-post-thumbnail:hover .colorize img {
                    filter: grayscale(0%);
                 }
                ';
				break;
		}

		return Dynamic_Css::minify_css( $css );
	}
}
