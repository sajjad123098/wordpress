<?php
/**
 * Handle metabox controls that need to be added to already existing metabox in the theme.
 *
 * Author:          Andrei Baicus <andrei@themeisle.com>
 * Created on:      2018-12-03
 *
 * @package Neve Pro Addon
 */

namespace Neve_Pro\Admin\Metabox;

use Neve_Pro\Traits\Core;

/**
 * Class Injector
 *
 * @package Neve Pro Addon
 */
class Injector {
	use Core;
	/**
	 * Initialize the injector and hook in the new classes.
	 */
	public function init() {
		add_filter( 'neve_filter_metabox_controls', array( $this, 'inject_control_classes' ) );

		add_filter( 'neve_post_type_supported_list', array( $this, 'add_gutenberg_meta_box_support_for_custom_post_types' ), 10, 2 );
	}

	/**
	 * Inject front end style classes from this plugin.
	 *
	 * @see \Neve\Admin\Metabox\Manager
	 *
	 * @param array $control_classes control handlers from Neve.
	 *
	 * @return array
	 */
	public function inject_control_classes( $control_classes ) {
		if ( apply_filters( 'nv_pro_woocommerce_booster_status', false ) ) {
			$control_classes[] = '\\Neve_Pro\\Modules\\Woocommerce_Booster\\Admin\\Product_Metabox';
		}

		return $control_classes;
	}

	/**
	 * Get supported post types for the 'block_editor' context.
	 *
	 * @param array $supported_post_types that currently supported post types.
	 *
	 * @return array
	 */
	public function add_gutenberg_meta_box_support_for_custom_post_types( $supported_post_types, $context ) {
		if ( $context !== 'block_editor' ) {
			return $supported_post_types;
		}

		$extra_post_types = array_values(
			array_intersect(
				get_post_types(
					[
						'public'       => true,
						'_builtin'     => false,
						'show_in_rest' => true,
					]
				),
				get_post_types_by_support( [ 'editor', 'custom-fields' ] )
			)
		);

		$all_post_types = array_merge( $supported_post_types, $extra_post_types );

		$exclude_list = array(
			'sfwd-certificates',
			'courses',
			'e-landing-page',
			'piotnetforms-book',
			'piotnetforms',
			'course',
			'piotnetforms-data',
			'jet-menu',
			'jet-popup',
			'adsforwp-groups',
			'pgc_simply_gallery',
			'lesson',
			'editor-story',
			'pafe-form-booking',
			'sfwd-assignment',
			'sfwd-essays',
			'pafe-formabandonment',
			'frm_display',
			'sfwd-transactions',
			'jet-engine',
			'jet-theme-core',
			'product',
			'reply',
			'jet_options_preset',
			'tutor_assignments',
			'brizy_template',
			'jet-smart-filters',
			'pafe-fonts',
			'pafe-form-database',
			'ct_content_block',
			'adsforwp',
			'iamport_payment',
			'tribe_events',
			'mec_esb',
			'elementor_library',
			'testimonial',
			'zion_template',
			'popup',
			'jet-engine-booking',
			'tutor_quiz',
			'piotnetforms-aban',
			'forum',
			'topic',
			'sfwd-quiz',
			'mec-events',
			'jet-woo-builder',
			'neve_custom_layouts',
			'feedzy_imports',
			'neve_cart_notices',
			'visualizer',
			'neve_product_tabs',
		);

		return array_values( array_diff( $all_post_types, $exclude_list ) );
	}
}
