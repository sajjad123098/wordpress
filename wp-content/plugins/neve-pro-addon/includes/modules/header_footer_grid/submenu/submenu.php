<?php
/**
 * Class for submenu styling.
 *
 * @package Neve Pro Addon
 */

namespace Neve_Pro\Modules\Header_Footer_Grid\Submenu;

use Neve\Core\Settings\Config;
use Neve\Core\Settings\Mods;
use Neve\Core\Styles\Dynamic_Selector;
use Neve_Pro\Modules\Header_Footer_Grid\Submenu\Customizer\Component_Settings;
use Neve_Pro\Modules\Scroll_To_Top\Icons;

/**
 * Class Sub_Menu_Style
 *
 * @package Neve_Pro\Modules\Header_Footer_Grid\Submenu
 */
class Submenu {

	/**
	 * Component id.
	 *
	 * @var string
	 */
	private $component_id;

	/**
	 * Array of flagsto check if style was loaded by other instance.
	 *
	 * @var array
	 */
	private static $style_added = [];

	/**
	 * Sub_Menu_Style constructor.
	 *
	 * @param string $component_id Current component id.
	 */
	public function __construct( $component_id ) {
		$this->component_id = $component_id;
		$this->init();
	}

	/**
	 * Init function.
	 */
	public function init() {
		// Apply submenu icon settings
		add_filter( 'neve_submenu_icon_settings', [ $this, 'sm_icon_settings' ], 10, 2 );


		add_action(
			'neve_before_render_nav',
			function ( $component_id ) {
				if ( $this->component_id !== $component_id ) {
					return;
				}
				// Add animation class before menu render
				add_filter( 'neve_additional_menu_class', [ $this, 'filter_additional_menu_class' ] );

				// Add hover skin class for submenus
				add_filter( 'neve_additional_menu_container_class', [ $this, 'filter_additional_menu_container_class' ] );
			}
		);

		// Remove animation class after menu render to avoid affecting other instances
		add_action(
			'neve_after_render_nav',
			function ( $component_id ) {
				if ( $this->component_id !== $component_id ) {
					return;
				}
				// Remove animation class after menu render to avoid affecting other instances
				remove_filter( 'neve_additional_menu_class', [ $this, 'filter_additional_menu_class' ] );

				// Remove hover skin class for submenus
				remove_filter( 'neve_additional_menu_container_class', [ $this, 'filter_additional_menu_container_class' ] );
			}
		);

		// Load menu scripts
		add_action( 'wp_enqueue_scripts', [ $this, 'load_scripts' ] );

		// Add style subscribers
		add_filter( 'neve_nav_filter_css', [ $this, 'add_subscribers' ], 10, 2 );
	}

	/**
	 * Implement sumbenu icon settings
	 *
	 * @param array  $settings Default settings for the icon.
	 * @param string $component_id Component id.
	 * @return array
	 */
	public function sm_icon_settings( $settings, $component_id ) {
		if ( $this->component_id !== $component_id ) {
			return $settings;
		}

		$default_side     = is_rtl() ? 'left' : 'right';
		$settings['side'] = Mods::get( $this->component_id . '_sm_icon_side', $default_side );

		$icon_type             = Mods::get( $this->component_id . '_sm_icon_type', 'icon' );
		$settings['icon_type'] = $icon_type;
		if ( $icon_type === 'icon' ) {
			$icon             = Mods::get( $this->component_id . '_sm_icon', 'mc-icon-style-1' );
			$settings['icon'] = ( new Icons() )->get_icon_svg( $icon );
		}
		if ( $icon_type === 'image' ) {
			$settings['image'] = Mods::get( $this->component_id . '_sm_image' );
		}
		return $settings;
	}

	/**
	 * Add additional class for the menu animation.
	 *
	 * @param string $classes Menu classes.
	 */
	public function filter_additional_menu_class( $classes ) {
		$animation_type = Mods::get( $this->component_id . '_' . Component_Settings::SM_ANIMATION );
		if ( empty( $animation_type ) ) {
			return $classes;
		}

		return $classes . ' nv-menu-animation-' . $animation_type;
	}

	/**
	 * Filter submenu container classes to add skin mode.
	 *
	 * @param array $classes Submenu array classes.
	 */
	public function filter_additional_menu_container_class( $classes ) {
		$menu_style     = Mods::get( $this->component_id . '_style', 'style-plain' );
		$sub_menu_style = Mods::get( $this->component_id . '_' . Component_Settings::SM_HOVER_SKIN, $menu_style );
		if ( empty( $sub_menu_style ) ) {
			return $classes;
		}
		if ( $sub_menu_style === 'style-plain' ) {
			$classes[] = 'submenu-' . $sub_menu_style;
			return $classes;
		}

		$classes[] = 'sm-style';
		$classes[] = 'sm-' . $sub_menu_style;

		return $classes;
	}

	/**
	 * Load submenu scripts.
	 */
	public function load_scripts() {
		wp_add_inline_style( 'neve-style', $this->get_inline_style() );
	}

	/**
	 * Get submenus inline style.
	 *
	 * @return string
	 */
	private function get_inline_style() {
		$css  = $this->get_submenu_icon_style();
		$css .= $this->get_animation_style();
		$css .= $this->get_submenu_style();
		return $css;
	}

	/**
	 * Get submenu icon style.
	 *
	 * @return string
	 */
	private function get_submenu_icon_style() {
		$css  = '';
		$type = Mods::get( $this->component_id . '_sm_icon_type', 'icon' );
		if ( $type === 'icon' ) {
			$color_css = '';
			$color     = Mods::get( $this->component_id . '_sm_icon_color' );
			if ( $this->should_add_property( 'sm_icon_color', $color ) ) {
				$color_css .= 'color:var(--smiconcolor);';
			}
		}
		if ( ! empty( $color_css ) ) {
			$css .= '.nav-ul .caret {' . $color_css . '}';
		}

		$size     = Mods::get( $this->component_id . '_sm_icon_size', Component_Settings::SM_ICON_DEFAULT_SIZE );
		$icon_css = '';
		if ( $this->should_add_property( 'sm_icon_size', $size ) ) {
			$icon_css .= 'width:var(--smiconsize, 0.5em);height:var(--smiconsize, 0.5em);';
		}

		if ( ! empty( $icon_css ) ) {
			$css .= '.nav-ul li .caret svg, .nav-ul li .caret img{' . $icon_css . '}';
		}

		return $css;
	}

	/**
	 * Get submenu animation style.
	 *
	 * @return string
	 */
	private function get_animation_style() {
		$css            = '';
		$animation_type = Mods::get( $this->component_id . '_' . Component_Settings::SM_ANIMATION );
		if ( ! empty( $animation_type ) && $this->is_style_added( $animation_type ) ) {
			return $css;
		}

		switch ( $animation_type ) {
			case 'slide-up':
				$css .= '
                .nv-menu-animation-slide-up:not(.menu-mobile) > .menu-item > .sub-menu,
                .nv-menu-animation-slide-up:not(.menu-mobile) > .menu-item > .sub-menu .sub-menu {
                      opacity: 0;
                      visibility: hidden;
                      transform: translateY(0.5em);
                      transition: visibility .2s ease, transform .2s ease;
                }
                .nv-menu-animation-slide-up:not(.menu-mobile) > .menu-item:focus > .sub-menu, .nv-menu-animation-slide-up:not(.menu-mobile) > .menu-item:hover > .sub-menu, .nv-menu-animation-slide-up:not(.menu-mobile) > .menu-item .menu-item:focus > .sub-menu, .nv-menu-animation-slide-up:not(.menu-mobile) > .menu-item .menu-item:hover > .sub-menu {
                      opacity: 1;
                      visibility: visible;
                      transform: translateY(0);
                      transition: opacity .2s ease, visibility .2s ease, transform .2s ease;
                }';
				break;
			case 'slide-down':
				$css .= '
                .nv-menu-animation-slide-down:not(.menu-mobile) > .menu-item > .sub-menu,
                .nv-menu-animation-slide-down:not(.menu-mobile) > .menu-item > .sub-menu .sub-menu {
                      opacity: 0;
                      visibility: hidden;
                      transform: translateY(-0.5em);
                      transition: visibility .2s ease, transform .2s ease; 
                }
                
                .nv-menu-animation-slide-down:not(.menu-mobile) > .menu-item:focus > .sub-menu, .nv-menu-animation-slide-down:not(.menu-mobile) > .menu-item:hover > .sub-menu, .nv-menu-animation-slide-down:not(.menu-mobile) > .menu-item .menu-item:focus > .sub-menu, .nv-menu-animation-slide-down:not(.menu-mobile) > .menu-item .menu-item:hover > .sub-menu {
                      opacity: 1;
                      visibility: visible;
                      transform: translateY(0);
                      transition: opacity .2s ease, visibility .2s ease, transform .2s ease; 
                }';
				break;
			case 'fade':
				$css .= '
                .nv-menu-animation-fade:not(.menu-mobile) > .menu-item > .sub-menu,
                .nv-menu-animation-fade:not(.menu-mobile) > .menu-item > .sub-menu .sub-menu {
                      opacity: 0;
                      visibility: hidden;
                      transition: opacity ease-in-out .3s;
                 }
                
                .nv-menu-animation-fade:not(.menu-mobile) > .menu-item:focus > .sub-menu, .nv-menu-animation-fade:not(.menu-mobile) > .menu-item:hover > .sub-menu, .nv-menu-animation-fade:not(.menu-mobile) > .menu-item .menu-item:focus > .sub-menu, .nv-menu-animation-fade:not(.menu-mobile) > .menu-item .menu-item:hover > .sub-menu {
                      opacity: 1;
                      visibility: visible;
                      transition: opacity ease-in-out .3s;
                }';
				break;
			default:
				break;
		}

		self::$style_added[ $animation_type ] = true;
		return $css;
	}

	/**
	 * Get submenu style.
	 *
	 * @return string
	 */
	private function get_submenu_style() {

		$css = '';

		// Submenu selector
		$submenu_css = '';

		// -> Container alignment
		$container_alignment = Mods::get( $this->component_id . '_' . Component_Settings::SM_CONTAINER_ALIGNMENT );
		if ( $this->should_add_property( Component_Settings::SM_CONTAINER_ALIGNMENT, $container_alignment ) ) {
			$submenu_css .= 'right: var(--alignment);';
		}

		// Border style
		$container_border_style = Mods::get( $this->component_id . '_' . Component_Settings::SM_BORDER_STYLE, 'none' );
		if ( $container_border_style !== 'none' && $this->should_add_property( Component_Settings::SM_BORDER_STYLE, $container_border_style ) ) {
			$submenu_css .= 'border-style: var(--bstyle);';
		}

		// -> Border radius
		$container_border_radius = Mods::get( $this->component_id . '_' . Component_Settings::SM_BORDER_RADIUS );
		if ( $this->should_add_property( Component_Settings::SM_BORDER_RADIUS, $container_border_radius ) ) {
			$submenu_css .= 'border-radius: var(--bradius, 0);';
		}

		if ( $container_border_style !== 'none' ) {
			// -> Border width
			$container_border_width = Mods::get(
				$this->component_id . '_' . Component_Settings::SM_BORDER_WIDTH,
				[
					'mobile'  => 0,
					'tablet'  => 0,
					'desktop' => 0,
					'suffix'  => [
						'mobile'  => 'px',
						'tablet'  => 'px',
						'desktop' => 'px',
					],
				]
			);
			if ( $this->should_add_property( Component_Settings::SM_BORDER_WIDTH, $container_border_width ) ) {
				$submenu_css .= 'border-width: var(--bwidth, 0);';
			}

			// -> Border color
			$border_color = Mods::get( $this->component_id . '_' . Component_Settings::SM_BORDER_COLOR );
			if ( $this->should_add_property( Component_Settings::SM_BORDER_COLOR, $border_color ) ) {
				$submenu_css .= 'border-color: var(--bcolor);';
			}
		}

		// -> Box shadow
		$container_box_shadow = Mods::get( $this->component_id . '_' . Component_Settings::SM_BOX_SHADOW );
		if ( ! empty( $container_box_shadow ) && $this->should_add_property( Component_Settings::SM_BOX_SHADOW, $container_box_shadow ) ) {
			$submenu_css .= 'box-shadow: var(--boxshadow, rgb(149 157 165 / 20%) 0 8px 24px )!important;';
		}

		// -> Background color
		$background_color = Mods::get( $this->component_id . '_' . Component_Settings::SM_BG_COLOR );
		if ( $this->should_add_property( Component_Settings::SM_BG_COLOR, $background_color ) ) {
			$submenu_css .= 'background: var(--bgcolor )!important;';
		}

		// -> Submenu font family
		$font_family = Mods::get( $this->component_id . '_' . Component_Settings::SM_FONT_FAMILY );
		if ( $this->should_add_property( Component_Settings::SM_FONT_FAMILY, $font_family ) ) {
			$submenu_css .= 'font-family: var(--fontfamily );';
		}

		// -> Text transform
		$text_transform = Mods::get( $this->component_id . '_' . Component_Settings::SM_TYPEFACE . '.textTransform' );
		if ( $this->should_add_property( Component_Settings::SM_TYPEFACE . '.textTransform', $text_transform ) ) {
			$submenu_css .= 'text-transform: var(--texttransform );';
		}

		// -> Font weight
		$font_weight = Mods::get( $this->component_id . '_' . Component_Settings::SM_TYPEFACE . '.fontWeight' );
		if ( $this->should_add_property( Component_Settings::SM_TYPEFACE . '.fontWeight', $font_weight ) ) {
			$submenu_css .= 'font-weight: var(--fontweight );';
		}

		// -> Font size
		$font_size = Mods::get( $this->component_id . '_' . Component_Settings::SM_TYPEFACE . '.fontSize' );
		if ( $this->should_add_property( Component_Settings::SM_TYPEFACE . '.fontSize', $font_size ) ) {
			$submenu_css .= 'font-size: var(--fontsize );';
		}

		// -> Line height
		$line_height = Mods::get( $this->component_id . '_' . Component_Settings::SM_TYPEFACE . '.lineHeight' );
		if ( $this->should_add_property( Component_Settings::SM_TYPEFACE . '.lineHeight', $line_height ) ) {
			$submenu_css .= 'line-height: var(--lineheight );';
		}

		// -> Letter spacing
		$letter_spacing = Mods::get( $this->component_id . '_' . Component_Settings::SM_TYPEFACE . '.letterSpacing' );
		if ( $this->should_add_property( Component_Settings::SM_TYPEFACE . '.letterSpacing', $letter_spacing ) ) {
			$submenu_css .= 'letter-spacing: var(--letterspacing );';
		}

		if ( ! empty( $submenu_css ) ) {
			$css .= '.nav-ul .sub-menu {' . $submenu_css . '}';
		}

		/*______________________________________________________________________________*/

		// Submenu li selector
		$submenu_li_css = '';

		// Border style
		$item_border_style = Mods::get( $this->component_id . '_' . Component_Settings::SM_ITEM_BORDER_STYLE, 'none' );
		if ( $this->should_add_property( Component_Settings::SM_ITEM_BORDER_STYLE, $item_border_style ) ) {
			$submenu_li_css .= 'border-style: var(--itembstyle);';
		}

		// Border radius
		$item_border_radius = Mods::get( $this->component_id . '_' . Component_Settings::SM_ITEM_BORDER_RADIUS );
		if ( $this->should_add_property( Component_Settings::SM_ITEM_BORDER_RADIUS, $item_border_radius ) ) {
			$submenu_li_css .= 'border-radius: var(--itembradius, 0);';
		}

		if ( $item_border_style !== 'none' ) {
			// -> Border width
			$item_border_width = Mods::get( $this->component_id . '_' . Component_Settings::SM_ITEM_BORDER_WIDTH );
			if ( $this->should_add_property( Component_Settings::SM_ITEM_BORDER_WIDTH, $item_border_width ) ) {
				$submenu_li_css .= 'border-width: var(--itembwidth, 0);';
			}

			// -> Border color
			$item_border_color = Mods::get( $this->component_id . '_' . Component_Settings::SM_ITEM_BORDER_COLOR );
			if ( $this->should_add_property( Component_Settings::SM_ITEM_BORDER_COLOR, $item_border_color ) ) {
				$submenu_li_css .= 'border-color: var(--itembcolor);';
			}
		}

		// Item background color
		$item_background_color = Mods::get( $this->component_id . '_' . Component_Settings::SM_ITEM_BG_COLOR );
		if ( $this->should_add_property( Component_Settings::SM_ITEM_BG_COLOR, $item_background_color ) ) {
			$submenu_li_css .= 'background: var(--itembgcolor);';
		}

		if ( ! empty( $submenu_li_css ) ) {
			$css .= '.nav-ul .sub-menu li {' . $submenu_li_css . '}';
		}

		/*______________________________________________________________________________*/

		// Submenu li hover selector
		$submenu_li_hover_css = '';

		if ( $item_border_style !== 'none' ) {
			// -> Border hover color
			$item_border_hover_color = Mods::get( $this->component_id . '_' . Component_Settings::SM_ITEM_BORDER_COLOR_HOVER );
			if ( $this->should_add_property( Component_Settings::SM_ITEM_BORDER_COLOR_HOVER, $item_border_hover_color ) ) {
				$submenu_li_hover_css .= 'border-color: var(--itembcolorhover, var(--itembcolor));';
			}
		}

		// Item background color hover
		$item_background_color_hover = Mods::get( $this->component_id . '_' . Component_Settings::SM_ITEM_BG_COLOR_HOVER );
		if ( $this->should_add_property( Component_Settings::SM_ITEM_BG_COLOR_HOVER, $item_background_color_hover ) ) {
			$submenu_li_hover_css .= 'background: var(--itembgcolorhover);';
		}

		if ( ! empty( $submenu_li_hover_css ) ) {
			$css .= '.nav-ul.menu-mobile .sub-menu li:not(.neve-mm-col):hover:not(:has(li:hover)) > .wrap, 
			.nav-ul:not(.menu-mobile) .sub-menu li:not(.neve-mm-col):hover > .wrap {' . $submenu_li_hover_css . '}';
		}

		/*______________________________________________________________________________*/

		// Submenu li not last selector
		$submenu_li_not_last_css = '';

		// -> Space between submenu elements
		$space_between = Mods::get( $this->component_id . '_' . Component_Settings::SM_SPACING );
		if ( $this->should_add_property( Component_Settings::SM_SPACING, $space_between ) ) {
			$submenu_li_not_last_css .= 'margin-bottom: var(--spacebetween );';
		}

		if ( ! empty( $submenu_li_not_last_css ) ) {
			$css .= '.nav-ul .sub-menu li:not(:last-child) {' . $submenu_li_not_last_css . '}';
		}

		return $css;
	}

	/**
	 * Helper function to determin if a property should be added via inline style.
	 *
	 * @param string $property_id Property id.
	 * @param mixed  $value Theme mod value.
	 * @return bool
	 */
	private function should_add_property( $property_id, $value ) {
		if ( ! $this->is_style_added( $property_id ) && ( ! empty( $value ) || is_customize_preview() ) ) {
			self::$style_added[ $property_id ] = true;
			return true;
		}
		return false;
	}

	/**
	 * Add variable values
	 *
	 * @param array $css_array Subscribers array.
	 *
	 * @return array
	 */
	public function add_subscribers( $css_array, $component_id ) {

		$menu_selector    = '.builder-item--' . $component_id;
		$submenu_selector = $menu_selector . ' .sub-menu';

		$css_array[] = [
			Dynamic_Selector::KEY_SELECTOR => $menu_selector,
			Dynamic_Selector::KEY_RULES    => [
				'--smiconcolor' => [
					Dynamic_Selector::META_KEY => $component_id . '_' . Component_Settings::SM_ICON_COLOR,
				],
				'--smiconsize'  => [
					Dynamic_Selector::META_KEY           => $component_id . '_' . Component_Settings::SM_ICON_SIZE,
					Dynamic_Selector::META_IS_RESPONSIVE => true,
					Dynamic_Selector::META_SUFFIX        => 'responsive_suffix',
				],
			],
		];

		$css_array[] = [
			Dynamic_Selector::KEY_SELECTOR => $submenu_selector,
			Dynamic_Selector::KEY_RULES    => [
				'--alignment'        => [
					Dynamic_Selector::META_KEY => $component_id . '_' . Component_Settings::SM_CONTAINER_ALIGNMENT,
				],
				'--justify'          => [
					Dynamic_Selector::META_KEY           => $component_id . '_' . Component_Settings::SM_CONTENT_ALIGNMENT,
					Dynamic_Selector::META_IS_RESPONSIVE => true,
				],
				'--spacebetween'     => [
					Dynamic_Selector::META_KEY           => $component_id . '_' . Component_Settings::SM_SPACING,
					Dynamic_Selector::META_IS_RESPONSIVE => true,
					Dynamic_Selector::META_SUFFIX        => 'responsive_suffix',
				],
				'--bstyle'           => [
					Dynamic_Selector::META_KEY     => $component_id . '_' . Component_Settings::SM_BORDER_STYLE,
					Dynamic_Selector::META_DEFAULT => 'none',
				],
				'--bwidth'           => [
					Dynamic_Selector::META_KEY           => $component_id . '_' . Component_Settings::SM_BORDER_WIDTH,
					Dynamic_Selector::META_IS_RESPONSIVE => true,
					Dynamic_Selector::META_SUFFIX        => 'px',
					'directional-prop'                   => Config::CSS_PROP_BORDER_WIDTH,
				],
				'--bradius'          => [
					Dynamic_Selector::META_KEY           => $component_id . '_' . Component_Settings::SM_BORDER_RADIUS,
					Dynamic_Selector::META_IS_RESPONSIVE => true,
					Dynamic_Selector::META_SUFFIX        => 'responsive_suffix',
				],
				'--bcolor'           => [
					Dynamic_Selector::META_KEY => $component_id . '_' . Component_Settings::SM_BORDER_COLOR,
				],
				'--boxshadow'        => [
					Dynamic_Selector::META_KEY           => $component_id . '_' . Component_Settings::SM_BOX_SHADOW,
					Dynamic_Selector::META_IS_RESPONSIVE => true,
					Dynamic_Selector::META_FILTER        => function ( $css_prop, $value, $meta, $device ) {
						$blur    = $value * 4;
						$opacity = 0.1 + $value / 10;
						return sprintf( '%s:0 0 %spx 0 rgba(0,0,0,%s);', $css_prop, $blur, $opacity );
					},
				],
				'--bgcolor'          => [
					Dynamic_Selector::META_KEY => $component_id . '_' . Component_Settings::SM_BG_COLOR,
				],
				'--itembstyle'       => [
					Dynamic_Selector::META_KEY     => $component_id . '_' . Component_Settings::SM_ITEM_BORDER_STYLE,
					Dynamic_Selector::META_DEFAULT => 'none',
				],
				'--itembwidth'       => [
					Dynamic_Selector::META_KEY           => $component_id . '_' . Component_Settings::SM_ITEM_BORDER_WIDTH,
					Dynamic_Selector::META_IS_RESPONSIVE => true,
					Dynamic_Selector::META_SUFFIX        => 'px',
					'directional-prop'                   => Config::CSS_PROP_BORDER_WIDTH,
				],
				'--itembradius'      => [
					Dynamic_Selector::META_KEY           => $component_id . '_' . Component_Settings::SM_ITEM_BORDER_RADIUS,
					Dynamic_Selector::META_IS_RESPONSIVE => true,
					Dynamic_Selector::META_SUFFIX        => 'responsive_suffix',
				],
				'--itembcolor'       => [
					Dynamic_Selector::META_KEY => $component_id . '_' . Component_Settings::SM_ITEM_BORDER_COLOR,
				],
				'--itembcolorhover'  => [
					Dynamic_Selector::META_KEY => $component_id . '_' . Component_Settings::SM_ITEM_BORDER_COLOR_HOVER,
				],
				'--itembgcolor'      => [
					Dynamic_Selector::META_KEY => $component_id . '_' . Component_Settings::SM_ITEM_BG_COLOR,
				],
				'--itembgcolorhover' => [
					Dynamic_Selector::META_KEY => $component_id . '_' . Component_Settings::SM_ITEM_BG_COLOR_HOVER,
				],
				'--color'            => [
					Dynamic_Selector::META_KEY => $component_id . '_' . Component_Settings::SM_ITEM_COLOR,
				],
				'--hovercolor'       => [
					Dynamic_Selector::META_KEY => $component_id . '_' . Component_Settings::SM_ITEM_COLOR_HOVER,
				],
				'--hovertextcolor'   => [
					Dynamic_Selector::META_KEY => $component_id . '_' . Component_Settings::SM_HOVER_TEXT_COLOR,
				],
				'--fontfamily'       => [
					Dynamic_Selector::META_KEY => $component_id . '_' . Component_Settings::SM_FONT_FAMILY,
				],
				'--texttransform'    => [
					Dynamic_Selector::META_KEY => $component_id . '_' . Component_Settings::SM_TYPEFACE . '.textTransform',

				],
				'--fontweight'       => [
					Dynamic_Selector::META_KEY => $component_id . '_' . Component_Settings::SM_TYPEFACE . '.fontWeight',
					'font'                     => 'mods_' . $component_id . '_' . Component_Settings::SM_FONT_FAMILY,
				],
				'--fontsize'         => [
					Dynamic_Selector::META_KEY           => $component_id . '_' . Component_Settings::SM_TYPEFACE . '.fontSize',
					Dynamic_Selector::META_IS_RESPONSIVE => true,
					Dynamic_Selector::META_SUFFIX        => 'em',
				],
				'--lineheight'       => [
					Dynamic_Selector::META_KEY           => $component_id . '_' . Component_Settings::SM_TYPEFACE . '.lineHeight',
					Dynamic_Selector::META_IS_RESPONSIVE => true,
				],
				'--letterspacing'    => [
					Dynamic_Selector::META_KEY           => $component_id . '_' . Component_Settings::SM_TYPEFACE . '.letterSpacing',
					Dynamic_Selector::META_IS_RESPONSIVE => true,
					Dynamic_Selector::META_SUFFIX        => 'px',
				],
			],
		];

		return $css_array;
	}

	/**
	 * Helper function to check is a piece of style was already added.
	 *
	 * @param string $id Specific style id.
	 *
	 * @return bool
	 */
	private function is_style_added( $id ) {
		return array_key_exists( $id, self::$style_added ) && self::$style_added[ $id ] === true;
	}





























}
