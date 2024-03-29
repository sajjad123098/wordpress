<?php
/**
 * Template used for component rendering wrapper.
 *
 * Name:    Header Footer Grid
 *
 * @package HFG
 */

namespace Neve_Pro\Modules\Header_Footer_Grid\Templates;

use Neve_Pro\Modules\Header_Footer_Grid\Components\Icons;
use Neve_Pro\Modules\Header_Footer_Grid\Components\My_Account;

$my_account_page = get_option( 'woocommerce_myaccount_page_id' );
if ( empty( $my_account_page ) && current_user_can( 'manage_options' ) ) {
	echo sprintf(
		/* translators: %s  is WooCommerce page link settings */
		esc_attr( __( 'You need to create the "My Account Page" in %s', 'neve' ) ),
		sprintf(
			/* translators: %s is WooCommerce page label */
			'<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=advanced' ) ) . '">%s</a>',
			esc_attr( __( 'WooCommerce page settings.', 'neve' ) )
		)
	);
}

$enable_registration = \HFG\component_setting( My_Account::ENABLE_REGISTER, 0 );
if ( ! empty( $my_account_page ) && ( (bool) $enable_registration === true || is_user_logged_in() ) ) {

	$icon_size   = \HFG\component_setting( My_Account::ICON_SIZE_ID, 15 );
	$icon        = \HFG\component_setting( My_Account::ICON_SELECTOR, 'user_avatar' );
	$icon_custom = neve_kses_svg( \HFG\component_setting( My_Account::ICON_CUSTOM, '' ) );
	$user_id     = get_current_user_id();

	$icon_code = get_avatar( $user_id, $icon_size );
	if ( $icon !== 'user_avatar' ) {
		if ( $icon === 'custom' ) {
			// remove existing width and height in favor of $icon_size
			$icon_code = preg_replace( '/( width| height)=("?[0-9]+"?)/i', '', $icon_custom );
			// if svg has fill set to none, add fill-opacity 0 to make sure it will display as it should
			$icon_code = preg_replace( '/fill="?none"?/i', 'fill=none fill-opacity=0', $icon_code );

			if ( strpos( $icon_code, '<svg' ) !== false ) {
				$offset    = strpos( $icon_code, '<svg' ) + strlen( '<svg ' );
				$icon_code = substr( $icon_code, 0, $offset ) . 'width=' . $icon_size . ' height=' . $icon_size . ' ' . substr( $icon_code, $offset, strlen( $icon_code ) - 1 );
			}
		} else {
			$icon_instance = new Icons();
			$icon_code     = $icon_instance->get_single_icon( $icon, $icon_size );
		}
	}

	$label              = \HFG\parse_dynamic_tags( \HFG\component_setting( My_Account::LABEL_TEXT ) );
	$show_register_icon = false;

	if ( ! is_user_logged_in() ) {
		$show_register_icon = \HFG\component_setting( My_Account::ENABLE_REGISTER_ICON, 0 );
		$label              = \HFG\component_setting( My_Account::REGISTER_TEXT, __( 'Register', 'neve' ) );
	}

	$button_link = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) );
	$dropdown    = \HFG\component_setting( My_Account::ENABLE_DROPDOWN, 0 );

	echo '<div class="component-wrap my-account-component ' . ( (bool) $dropdown === true ? 'my-account-has-dropdown' : '' ) . '">';
	echo '<div class="my-account-container">';
	echo '<a href="' . esc_url( $button_link ) . '" class="my-account-wrapper ' . ( ! empty( $label ) ? 'has-label' : '' ) . '">';
	if ( ( is_user_logged_in() || $show_register_icon ) && ! empty( $icon_code ) ) {
		echo '<span class="my-account-icon">' . $icon_code . '</span>'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	echo '<span class="my-account-label inherit-ff">' . esc_html( $label ) . '</span>';
	echo '</a>';
	if ( (bool) $dropdown === true && is_user_logged_in() ) {
		echo '<ul class="sub-menu inherit-ff">';
		echo My_Account::get_account_links(); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</ul>';
	}
	echo '</div>';
	echo '</div>';
}

