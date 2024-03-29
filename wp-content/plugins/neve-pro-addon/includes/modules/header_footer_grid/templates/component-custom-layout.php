<?php
/**
 * Template used for component rendering wrapper.
 *
 * Name:    Header Footer Grid
 *
 * @since 1.2.8
 * @package HFG
 */

namespace Neve_Pro\Modules\Header_Footer_Grid\Templates;

use Neve_Pro\Modules\Header_Footer_Grid\Components\Custom_Layout;

$current_layout = \HFG\component_setting( Custom_Layout::POST_ID );

if ( is_customize_preview() && $current_layout === 'none' ) {
	echo '<p style="margin-bottom:0">';
	echo sprintf(
		/* translators: %1$s - Custom Layouts, %2$s: Individual Custom Layout */
		esc_html__( 'You have to activate the %1$s module from the theme options page and create an %2$s.', 'neve' ),
		'<strong>' . __( 'Custom Layouts', 'neve' ) . '</strong>', //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		'<strong>' . esc_html( __( 'Individual Custom Layout', 'neve' ) ) . '</strong>'
	);
	echo '</p>'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
} else {
	if ( $current_layout !== 'none' ) {
		do_action( 'neve_do_individual', $current_layout );
	}
}
