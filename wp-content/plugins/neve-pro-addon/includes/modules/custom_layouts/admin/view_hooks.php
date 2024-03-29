<?php
/**
 * Class that handle the show/hide hooks.
 *
 * @package Neve_Pro\Modules\Custom_Layouts\Admin
 */

namespace Neve_Pro\Modules\Custom_Layouts\Admin;

use Neve\Core\Dynamic_Css;
use WP_Admin_Bar;

/**
 * Class View_Hooks
 *
 * @package Neve_Pro\Modules\Custom_Layouts\Admin
 */
class View_Hooks {

	/**
	 * Initialize function.
	 */
	public function init() {
		if ( ! $this->should_load() ) {
			return;
		}
		add_action( 'admin_bar_menu', array( $this, 'admin_bar_menu' ), 99 );
		add_action( 'wp', array( $this, 'render_hook_placeholder' ) );
	}

	/**
	 * Check user role before allowing the class to run
	 *
	 * @return bool
	 */
	private function should_load() {
		return current_user_can( 'administrator' );
	}

	/**
	 * Admin Bar Menu
	 *
	 * @param WP_Admin_Bar $wp_admin_bar Admin bar menus.
	 */
	function admin_bar_menu( $wp_admin_bar ) {
		if ( is_admin() ) {
			return;
		}
		$title = __( 'Show Hooks', 'neve' );

		$href = add_query_arg( 'neve_preview_hook', 'show' );
		if ( isset( $_GET['neve_preview_hook'] ) && 'show' === $_GET['neve_preview_hook'] ) {
			$title = __( 'Hide Hooks', 'neve' );
			$href  = remove_query_arg( 'neve_preview_hook' );
		}

		$wp_admin_bar->add_menu(
			array(
				'title'  => $title,
				'id'     => 'neve_preview_hook',
				'parent' => false,
				'href'   => $href,
			)
		);
	}

	/**
	 * Beautify hook names.
	 *
	 * @param string $hook Hook name.
	 *
	 * @return string
	 */
	public static function beautify_hook( $hook ) {
		$hook_label = str_replace( '_', ' ', $hook );
		$hook_label = str_replace( 'neve', ' ', $hook_label );
		$hook_label = str_replace( 'nv', ' ', $hook_label );
		$hook_label = str_replace( 'woocommerce', ' ', $hook_label );
		$hook_label = ucwords( $hook_label );
		return $hook_label;
	}

	/**
	 * Render hook placeholder.
	 */
	public function render_hook_placeholder() {
		if ( ! isset( $_GET['neve_preview_hook'] ) || 'show' !== $_GET['neve_preview_hook'] ) {
			return;
		}
		$hooks = neve_hooks();
		$css   = '
		.nv-hook-wrapper {
			text-align: center; width: 100%;
		}
		.nv-hook-placeholder {
			display: flex; 
			width: 98%; 
			justify-content: center;
			align-items: center;
			margin: 10px auto; 
			border: 2px dashed #0065A6;
			font-size: 14px; 
			padding: 6px 10px; 
			text-align: left; 
			word-break: break-word;
		}
		.nv-hook-placeholder a {
			display: flex;
			align-items: center;
			justify-content: center;
			color: #0065A6;
			min-width: 250px;
			width: 100%;
			font-size: 16px;
			min-height: 32px;
			text-decoration: none;
		}
		.nv-hook-placeholder a:hover, .nv-hook-placeholder a:focus {
			text-decoration: none;
		}
		.nv-hook-placeholder a:hover .nv-hook-icon, .nv-hook-placeholder a:focus .nv-hook-icon {
			box-shadow: inset 0 0 0 1px  #0065A6;
			color: #0065A6;
			opacity: 1;
			display: block;
		 }
		.nv-hook-placeholder a .nv-hook-icon {
			box-shadow: inset 0 0 0 1px #0065A6;
			border-radius: 50%;
			width: 20px;
			height: 20px;
			font-size: 16px;
			padding: 3px 2px;
			margin-left: -2px;
			opacity: 0;
			transform:rotate(360deg);
			transition: all 300ms cubic-bezier(0.4, 0, 0.2, 1);
			position: absolute;
		}
		.nv-hook-placeholder a .nv-hook-name {
			transition: all 300ms cubic-bezier(0.4, 0, 0.2, 1);
			font-size: 14px;
			opacity: 1;
		}
		.nv-hook-placeholder a:hover .nv-hook-name, .nv-hook-placeholder a:focus .nv-hook-name {
			opacity: 0;
		';

		echo '<style type="text/css">';
		echo esc_attr( Dynamic_Css::minify_css( $css ) );
		echo '</style>';

		// These hooks have to be removed as to not have nested links that will break the layout.
		// We don't need them when the hooks are displayed, as the action will be replaced by the displayed hook action.
		remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );

		foreach ( $hooks as $hooks_in_category ) {
			foreach ( $hooks_in_category as $hook_value ) {
				$hook_label = self::beautify_hook( $hook_value );
				add_action(
					$hook_value,
					function () use ( $hook_label, $hook_value ) {
						$hook_new_url = admin_url( 'post-new.php?post_type=neve_custom_layouts&layout=hooks&hook=' . $hook_value );
						echo '<div class="nv-hook-wrapper">';
						echo '<div class="nv-hook-placeholder">';
						echo '<a href="' . esc_url( $hook_new_url ) . '" title="' . esc_attr__( 'Add Custom Layout on Hook', 'neve' ) . '">';
						echo '<span class="nv-hook-icon dashicons dashicons-plus"></span>';
						echo '<span class="nv-hook-name">' . esc_html( $hook_label ) . '</span>';
						echo '</a>';
						echo '</div>';
						echo '</div>';
					}
				);
			}
		}
	}


}
