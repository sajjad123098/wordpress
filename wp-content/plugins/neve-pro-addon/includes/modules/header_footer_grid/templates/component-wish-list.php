<?php
/**
 * Template used for component rendering wrapper.
 *
 * Name:    Header Footer Grid
 *
 * @version 1.0.0
 * @package HFG
 */

namespace Neve_Pro\Modules\Header_Footer_Grid\Templates;

$wish_list_module = sparks()->module( 'wish_list' );

if ( ! ( $wish_list_module instanceof \Codeinwp\Sparks\Modules\Base_Module ) ) {
	return;
}

$wish_list_position = sparks()->module( 'wish_list' )->get_button_position();

if ( is_customize_preview() && $wish_list_position === 'none' ) {
	echo sprintf(
		/* translators: %s - path to wish list control */
		esc_html__( 'Activate your wish list from %s', 'neve' ),
		sprintf(
			'<strong>%s</strong>',
			esc_html__( 'WP Admin -> Settings -> Sparks -> Wish List', 'neve' )
		)
	);
	return;
}

if ( $wish_list_position !== 'none' ) {
	$settings = array(
		'tag'   => 'div',
		'class' => 'wish-list-component',
	);

	do_action( 'sparks_wish_list_icon', $settings );
}
