<?php
/**
 * Handles the layout display for blog pro archive custom post type.
 *
 * Author:      Bogdan Preda <bogdan.preda@themeisle.com>
 * Created on:  15-12-{2021}
 *
 * @package Neve Pro Addon
 */

namespace Neve_Pro\Modules\Post_Type_Enhancements\Views;

use Neve\Core\Settings\Mods;
use Neve_Pro\Modules\Post_Type_Enhancements\Model\CPT_Model;

/**
 * Class Layout_CPT_Archive_View
 *
 * @since   3.1.0
 * @package Neve Pro Addon
 */
class Blog_Pro_CPT_View {
	/**
	 * Holds the current model.
	 *
	 * @var CPT_Model $model
	 */
	private $model;

	/**
	 * Constructor for the class.
	 *
	 * @param CPT_Model $cpt_model The Custom Post Type Model.
	 *
	 * @since 3.1.0
	 */
	public function __construct( $cpt_model ) {
		$this->model = $cpt_model;
		$this->init();
	}

	/**
	 * Initialize the module.
	 *
	 * @since 3.1.0
	 * @return void
	 */
	public function init() {
		add_action( 'wp_loaded', array( $this, 'register_dynamic_theme_mods' ) );
	}

	/**
	 * Register dynamic filters for passed custom post type.
	 *
	 * @since 3.1.0
	 * @return void
	 */
	final public function register_dynamic_theme_mods( $custom_post_type ) {
		if ( ! $this->model->is_custom_layout_archive_enabled() ) {
			return;
		}
		add_filter(
			'theme_mod_neve_blog_grid_spacing',
			function ( $value ) {
				if ( is_post_type_archive( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_archive_type() . '_grid_spacing', $value );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_blog_list_spacing',
			function ( $value ) {
				if ( is_post_type_archive( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_archive_type() . '_list_spacing', $value );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_blog_covers_min_height',
			function ( $value ) {
				if ( is_post_type_archive( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_archive_type() . '_covers_min_height', $value );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_blog_items_border_radius',
			function ( $value ) {
				if ( is_post_type_archive( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_archive_type() . '_items_border_radius', $value );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_blog_covers_overlay_color',
			function ( $value ) {
				if ( is_post_type_archive( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_archive_type() . '_covers_overlay_color', $value );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_blog_content_padding',
			function ( $value ) {
				if ( is_post_type_archive( $this->model->get_type() ) || is_singular( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_archive_type() . '_content_padding', $value );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_blog_image_hover',
			function ( $value ) {
				if ( is_post_type_archive( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_archive_type() . '_image_hover', $value );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_blog_show_on_hover',
			function ( $value ) {
				if ( is_post_type_archive( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_archive_type() . '_show_on_hover', $value );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_blog_list_image_position',
			function ( $value ) {
				if ( is_post_type_archive( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_archive_type() . '_list_image_position', $value );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_blog_list_image_width',
			function ( $value ) {
				if ( is_post_type_archive( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_archive_type() . '_list_image_width', $value );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_blog_separator',
			function ( $value ) {
				if ( is_post_type_archive( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_archive_type() . '_separator', $value );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_blog_separator_width',
			function ( $value ) {
				if ( is_post_type_archive( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_archive_type() . '_separator_width', $value );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_blog_separator_color',
			function ( $value ) {
				if ( is_post_type_archive( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_archive_type() . '_separator_color', $value );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_enable_card_style',
			function ( $value ) {
				if ( is_post_type_archive( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_archive_type() . '_enable_card_style', $value );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_blog_grid_card_bg_color',
			function ( $value ) {
				if ( is_post_type_archive( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_archive_type() . '_grid_card_bg_color', $value );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_blog_grid_text_color',
			function ( $value ) {
				if ( is_post_type_archive( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_archive_type() . '_grid_text_color', $value );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_blog_card_shadow',
			function ( $value ) {
				if ( is_post_type_archive( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_archive_type() . '_card_shadow', $value );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_blog_content_alignment',
			function ( $value ) {
				if ( is_post_type_archive( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_archive_type() . '_content_alignment', $value );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_blog_content_vertical_alignment',
			function ( $value ) {
				if ( is_post_type_archive( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_archive_type() . '_content_vertical_alignment', $value );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_read_more_options',
			function ( $value ) {
				if ( is_post_type_archive( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_archive_type() . '_read_more_options', $value );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_read_more_text',
			function ( $value ) {
				if ( is_post_type_archive( $this->model->get_type() ) || is_singular( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_archive_type() . '_read_more_text', $value );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_read_more_style',
			function ( $value ) {
				if ( is_post_type_archive( $this->model->get_type() ) || is_singular( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_archive_type() . '_read_more_style', $value );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_author_avatar_size',
			function ( $value ) {
				if ( is_post_type_archive( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_archive_type() . '_author_avatar_size', $value );
				}

				return $value;
			}
		);

	}
}
