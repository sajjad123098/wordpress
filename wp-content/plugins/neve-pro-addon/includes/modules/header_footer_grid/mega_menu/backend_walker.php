<?php
/**
 * Custom Walker for nav menu editor.
 *
 * @package Neve Pro
 */

namespace Neve_Pro\Modules\Header_Footer_Grid\Mega_Menu;

/**
 * Class Backend Walker.
 */
class Backend_Walker extends \Walker_Nav_Menu_Edit {

	/**
	 * Start the element output.
	 *
	 * @param string    $output Passed by reference. Used to append additional content.
	 * @param \WP_Post  $item Menu item data object.
	 * @param int       $depth Depth of menu item. Used for padding.
	 * @param \stdClass $args Menu item args.
	 * @param int       $id Nav menu ID.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		parent::start_el( $output, $item, $depth, $args, $id );

		$icon = get_post_meta( $item->ID, 'nv_mm_icon', true );

		$output .= sprintf( '<div class="mm-values" data-icon="%s"></div>', $icon );
		$output .= $this->maybe_add_obfx_compat( $item );
	}

	/**
	 * Add orbit-fox compatibility.
	 */
	private function maybe_add_obfx_compat( $item ) {
		if ( ! defined( 'OBX_PATH' ) ) {
			return '';
		}

		$icon = get_post_meta( $item->ID, 'obfx_menu_icon', true );
		return sprintf( '<input type="hidden" name="menu-item-icon[%d]" id="menu-item-icon-%d" value="%s">', $item->ID, $item->ID, $icon );
	}
}
