<?php
/**
 * Handles the layout display for a custom post type.
 *
 * Author:      Bogdan Preda <bogdan.preda@themeisle.com>
 * Created on:  15-12-{2021}
 *
 * @package Neve Pro Addon
 */

namespace Neve_Pro\Modules\Post_Type_Enhancements\Views;

use Neve\Core\Settings\Mods;
use Neve_Pro\Core\Loader;
use Neve_Pro\Modules\Post_Type_Enhancements\Model\CPT_Model;

/**
 * Class Layout_CPT_Archive_View
 *
 * @since   3.1.0
 * @package Neve Pro Addon
 */
class Layout_CPT_Archive_View {
	/**
	 * Holds the current model.
	 *
	 * @var CPT_Model $model
	 */
	private $model;

	/**
	 * Constructor for the class.
	 *
	 * @since 3.1.0
	 * @param CPT_Model $cpt_model The Custom Post Type Model.
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
	 * Register filters for dynamic mods.
	 *
	 * @since 3.1.0
	 * @return void
	 */
	final public function register_dynamic_theme_mods() {
		if ( $this->model->has_archive() ) {
			add_filter(
				'theme_mod_neve_blog_archive_content_width',
				function ( $value ) {
					if ( is_post_type_archive( $this->model->get_type() ) ) {
						return MODS::get( 'neve_' . $this->model->get_archive_type() . '_content_width', $value );
					}

					return $value;
				}
			);
		}

		if ( ! $this->model->is_custom_layout_archive_enabled() ) {
			return;
		}

		add_filter(
			'theme_mod_neve_blog_archive_layout',
			function ( $value ) {
				if ( is_post_type_archive( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_archive_type() . '_layout', $value );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_grid_layout',
			function ( $value ) {
				if ( is_post_type_archive( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_archive_type() . '_grid_layout', $value );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_blog_covers_text_color',
			function ( $value ) {
				if ( is_post_type_archive( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_archive_type() . '_covers_text_color', $value );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_blog_list_alternative_layout',
			function ( $value ) {
				if ( is_post_type_archive( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_archive_type() . '_list_alternative_layout', $value );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_enable_masonry',
			function ( $value ) {
				if ( is_post_type_archive( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_archive_type() . '_enable_masonry', $value );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_archive_hide_title',
			function ( $value ) {
				if ( is_post_type_archive( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_archive_type() . '_hide_title', $value );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_pagination_type',
			function ( $value ) {
				if ( is_post_type_archive( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_archive_type() . '_pagination_type', $value );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_post_content_ordering',
			function ( $value ) {
				if ( is_post_type_archive( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_archive_type() . '_content_ordering', $value );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_post_excerpt_length',
			function ( $value ) {
				if ( is_post_type_archive( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_archive_type() . '_excerpt_length', $value );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_post_thumbnail_box_shadow',
			function ( $value ) {
				if ( is_post_type_archive( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_archive_type() . '_thumbnail_box_shadow', $value );
				}

				return $value;
			}
		);

		add_filter(
			'neve_thumbnail_box_shadow_meta_filter',
			function ( $meta_name ) {
				if ( is_post_type_archive( $this->model->get_type() ) ) {
					return 'neve_' . $this->model->get_archive_type() . '_thumbnail_box_shadow';
				}
				return $meta_name;
			}
		);

		$has_custom_fields = Loader::has_compatibility( 'meta_custom_fields' );
		$filter            = $has_custom_fields ? 'theme_mod_neve_blog_post_meta_fields' : 'theme_mod_neve_post_meta_ordering';
		$theme_mod         = $has_custom_fields ? 'neve_' . $this->model->get_archive_type() . '_post_meta_fields' : 'neve_' . $this->model->get_archive_type() . '_post_meta_ordering';

		add_filter(
			$filter,
			function ( $value ) use ( $theme_mod ) {
				if ( is_post_type_archive( $this->model->get_type() ) ) {
					return Mods::get( $theme_mod, $value );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_metadata_separator',
			function ( $value ) {
				if ( is_post_type_archive( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_archive_type() . '_metadata_separator', $value );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_author_avatar',
			function ( $value ) {
				if ( is_post_type_archive( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_archive_type() . '_author_avatar', $value );
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

		add_filter(
			'theme_mod_neve_show_last_updated_date',
			function ( $value ) {
				if ( is_post_type_archive( $this->model->get_type() ) ) {
					return mods::get( 'neve_' . $this->model->get_archive_type() . '_show_last_updated_date', $value );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_blog_typography_shortcut',
			function ( $value ) {
				if ( is_post_type_archive( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_archive_type() . '_typography_shortcut', $value );
				}

				return $value;
			}
		);
	}
}
