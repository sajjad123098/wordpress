<?php
/**
 * Author:          Stefan Cotitosu <stefan@themeisle.com>
 * Created on:      2019-02-07
 *
 * @package Neve Pro Addon
 */

namespace Neve_Pro\Modules\Scroll_To_Top;

use Neve_Pro\Core\Abstract_Module;
use Neve_Pro\Modules\Scroll_To_Top\Icons;

/**
 * Class Module
 *
 * @package Neve_Pro\Modules\Scroll_To_Top
 */
class Module extends Abstract_Module {

	/**
	 * Define module properties.
	 *
	 * @access  public
	 * @return void
	 *
	 * @version 1.0.0
	 */
	public function define_module_properties() {
		$this->slug              = 'scroll_to_top';
		$this->name              = __( 'Scroll To Top', 'neve' );
		$this->description       = __( 'Simple but effective module to help you navigate back to the top of the really long pages.', 'neve' );
		$this->documentation     = array(
			'url'   => 'https://docs.themeisle.com/article/1060-scroll-to-top-module-documentation',
			'label' => __( 'Learn more', 'neve' ),
		);
		$this->order             = 5;
		$this->has_dynamic_style = true;
	}

	/**
	 * Check if module should load.
	 *
	 * @return bool
	 */
	public function should_load() {
		return true;
	}

	/**
	 * Run Scroll to Top Module
	 */
	public function run_module() {
		add_action( 'neve_after_primary', array( $this, 'render_button' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'neve_pro_filter_customizer_modules', array( $this, 'add_customizer_classes' ) );
		add_filter( 'neve_dynamic_style_output', array( $this, 'module_css' ), 99, 2 );
		add_action( 'neve_before_header_hook', array( $this, 'scroll_to_top_amp' ) );
	}

	/**
	 * Scroll to top amp observer.
	 */
	public function scroll_to_top_amp() {
		if ( ! $this->is_active() ) {
			return false;
		}

		if ( ! neve_is_amp() ) {
			return false;
		}

		echo '<amp-position-observer on="enter:hideAnim.start; exit:showAnim.start" layout="nodisplay"></amp-position-observer>';

		// We use 2 `amp-animation` elements to trigger the visibility of the button. The first one is for making the button visible
		echo '
		<amp-animation id="showAnim" layout="nodisplay">
		<script type="application/json">
		  {
		    "duration": "200ms",
		    "fill": "both",
		    "iterations": "1",
		    "direction": "alternate",
		    "animations": [
		      {
		        "selector": "#scroll-to-top",
		        "keyframes": [
		         { "opacity": "1", "visibility": "visible" }
		        ]
		      }
		    ]
		  }
		</script>
		</amp-animation>';

		echo '
		<!-- ... and the second one is for adding the button.-->
		<amp-animation id="hideAnim" layout="nodisplay">
		  <script type="application/json">
		    {
		      "duration": "200ms",
		      "fill": "both",
		      "iterations": "1",
		      "direction": "alternate",
		      "animations": [
		        {
		          "selector": "#scroll-to-top",
		          "keyframes": [
		            { "opacity": "0", "visibility": "hidden" }
		          ]
		        }
		      ]
		     }
		    </script>
		  </amp-animation>
		';
		return true;
	}

	/**
	 * Add module css.
	 *
	 * @param string $css Current CSS style.
	 * @param string $context Current context.
	 *
	 * @return string Altered CSS.
	 */
	public function module_css( $css, $context = 'frontend' ) {
		if ( ! $this->is_active() ) {
			return $css;
		}
		if ( $context !== 'frontend' ) {
			return $css;
		}
		$scroll_to_top_css = '.scroll-to-top {' . ( is_rtl() ? 'left: 20px;' : 'right: 20px;' ) . '
			border: none;
			position: fixed;
			bottom: 30px;
			display: none;
			opacity: 0;
			visibility: hidden;
			transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out;
			align-items: center;
			justify-content: center;
			z-index: 999;
		}
		@supports (-webkit-overflow-scrolling: touch) {
			.scroll-to-top {
				bottom: 74px;
			}
		}
		.scroll-to-top.image {
			background-position: center;
		}
		.scroll-to-top .scroll-to-top-image {
			width: 100%;
		    height: 100%;
		}
		.scroll-to-top .scroll-to-top-label {
			margin: 0;
			padding: 5px;
		}
		.scroll-to-top:hover {
			text-decoration: none;
		}
		.scroll-to-top.scroll-to-top-left {' . ( is_rtl() ? 'right: 20px; left: unset;' : 'left: 20px; right: unset;' ) . '}
		.scroll-to-top.scroll-show-mobile {
		  display: flex;
		}
		@media (min-width: 960px) {
			.scroll-to-top {
				display: flex;
			}
		}';

		$scroll_to_top_css .= '.scroll-to-top {
			color: var(--color);
			padding: var(--padding);
			border-radius: var(--borderradius);
			background: var(--bgcolor);  
		}

		.scroll-to-top:hover, .scroll-to-top:focus {
			color: var(--hovercolor);
			background: var(--hoverbgcolor);
		}

		.scroll-to-top-icon, .scroll-to-top.image .scroll-to-top-image {
			width: var(--size);
			height: var(--size);
		}

		.scroll-to-top-image {
			background-image: var(--bgimage);
			background-size: cover;
		}';

		return $css . $scroll_to_top_css;
	}

	/**
	 * Add customizer classes.
	 *
	 * @param array $classes loaded classes.
	 *
	 * @return array
	 */
	public function add_customizer_classes( $classes ) {
		$classes[] = 'Modules\Scroll_To_Top\Customizer\Scroll_To_Top';

		return $classes;
	}

	/**
	 * Enqueue module scripts
	 */
	public function enqueue_scripts() {
		if ( ! $this->is_active() ) {
			return;
		}

		if ( neve_is_amp() ) {
			return;
		}

		wp_register_script( 'neve-pro-scroll-to-top', NEVE_PRO_INCLUDES_URL . 'modules/scroll_to_top/assets/js/build/script.js', array(), NEVE_PRO_VERSION, true );

		wp_enqueue_script( 'neve-pro-scroll-to-top' );

		wp_script_add_data( 'neve-pro-scroll-to-top', 'async', true );

		wp_localize_script( 'neve-pro-scroll-to-top', 'neveScrollOffset', $this->localize_scroll() );
	}

	/**
	 * Send offset to the JS object
	 *
	 * @return array
	 */
	private function localize_scroll() {
		return array(
			'offset' => get_theme_mod( 'neve_scroll_to_top_offset', 0 ),
		);
	}

	/**
	 * Display scroll to top button
	 */
	public function render_button() {
		if ( ! $this->is_active() ) {
			return false;
		}

		$position       = get_theme_mod( 'neve_scroll_to_top_side', 'right' );
		$hide_on_mobile = get_theme_mod( 'neve_scroll_to_top_on_mobile', false );
		$type           = get_theme_mod( 'neve_scroll_to_top_type', 'icon' );
		$label          = get_theme_mod( 'neve_scroll_to_top_label' );
		$image          = get_theme_mod( 'neve_scroll_to_top_image' );
		$icon           = get_theme_mod( 'neve_scroll_to_top_icon', 'stt-icon-style-1' );

		$extra_class  = sprintf( 'scroll-to-top-%s %s', $position, ( ( ! $hide_on_mobile ) ? ' scroll-show-mobile ' : '' ) );
		$extra_class .= $type;

		$amp = neve_is_amp() ? 'on="tap:neve_body.scrollTo(duration=200)"' : '';

		echo '<button tabindex="0"' . esc_attr( $amp ) . ' id="scroll-to-top" class="scroll-to-top ' . esc_attr( $extra_class ) . '" aria-label="' . esc_attr__( 'Scroll to Top', 'neve' ) . '">';
		if ( $type === 'icon' ) {
			echo ( new Icons() )->get_icon_svg( $icon ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		if ( $type === 'image' && ! empty( $image ) ) {
			echo '<div class="scroll-to-top-image"></div>';
		}
		if ( ! empty( $label ) ) {
			echo '<p class="scroll-to-top-label">' . wp_kses_post( $label ) . '</p>';
		}
		if ( neve_is_amp() ) {
			echo '<amp-position-observer on="enter:hideAnim.start; exit:showAnim.start" layout="nodisplay"></amp-position-observer>';
		}
		echo '</button>';

		return true;
	}
}
