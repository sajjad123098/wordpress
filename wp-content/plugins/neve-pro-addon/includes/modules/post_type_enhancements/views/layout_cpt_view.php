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

use Neve\Core\Settings\Config;
use Neve\Core\Settings\Mods;
use Neve\Customizer\Defaults\Single_Post;
use Neve\Views\Base_View;
use Neve_Pro\Core\Loader;
use Neve_Pro\Traits\Utils;
use Neve_Pro\Modules\Post_Type_Enhancements\Model\CPT_Model;
use WP_Post_Type;

/**
 * Class Layout_CPT_View
 *
 * @since   3.1.0
 * @package Neve Pro Addon
 */
class Layout_CPT_View extends Base_View {
	use Single_Post;
	use Utils;

	/**
	 * Holds the current model.
	 *
	 * @var CPT_Model $model
	 */
	private $model;

	/**
	 * Holds the current post type or false.
	 *
	 * @var string $current_post_type The current post type;
	 */
	private $current_post_type = 'post';

	/**
	 * Is Blog Pro active.
	 *
	 * @var bool
	 */
	private $is_blog_pro_enabled = false;

	/**
	 * Constructor for the class.
	 *
	 * @since 3.1.0
	 * @param CPT_Model $cpt_model The Custom Post Type Model.
	 */
	public function __construct( $cpt_model ) {
		$this->model               = $cpt_model;
		$this->is_blog_pro_enabled = get_option( 'nv_pro_blog_pro_status' );
		$this->init();
	}

	/**
	 * Initialize the module.
	 *
	 * @since 3.1.0
	 * @return void
	 */
	public function init() {
		add_action( 'neve_do_single_post', [ $this, 'render_post' ], 9, 1 );
		add_action( 'neve_after_header_wrapper_hook', [ $this, 'render_cover_header' ], 9 );

		add_filter( 'neve_allowed_custom_post_types', [ $this, 'allowed_custom_post_types_filter' ] );

		add_filter( 'neve_customize_preview_localization', [ $this, 'add_dynamic_content_listeners' ] );
		add_filter( 'neve_single_container_style_filter', [ $this, 'custom_post_container_styles' ] );
		add_filter( 'neve_related_post_type_filter', [ $this, 'related_post_type_context' ] );

		add_action( 'wp_loaded', [ $this, 'register_dynamic_theme_mods' ] );
		add_filter(
			'neve_context_filter',
			function ( $context ) {
				if ( ! $this->model->is_custom_layout_enabled() && ! in_array( $context, [ 'page', 'product' ] ) ) {
					return 'post';
				}
				return $context;
			}
		);
	}

	/**
	 * Register filters for dynamic mods.
	 *
	 * @since 3.1.0
	 * @return void
	 */
	final public function register_dynamic_theme_mods() {
		add_filter(
			'theme_mod_' . Config::MODS_OTHERS_CONTENT_WIDTH,
			function ( $value ) {
				if ( is_singular( $this->model->get_type() ) ) {
					return Mods::get( 'neve_single_' . $this->model->get_type() . '_content_width', 70 );
				}
				return $value;
			}
		);

		if ( ! $this->model->is_custom_layout_enabled() ) {
			return;
		}

		// We only need to filter these inside when in admin context
		// this is used only to alter the metabox defaults.
		add_filter(
			'theme_mod_neve_layout_single_post_elements_order',
			function ( $value ) {
				if ( is_admin() ) {
					$screen = get_current_screen();
					if ( $screen->id == $this->model->get_type() ) {
						return Mods::get( 'neve_layout_single_' . $this->model->get_type() . '_elements_order', $value );
					}
				}
				if ( is_singular( $this->model->get_type() ) ) {
					return Mods::get( 'neve_layout_single_' . $this->model->get_type() . '_elements_order', $value );
				}
				return $value;
			}
		);

		// Same here so that the correct layout is used and not the post one.
		// this is used only to alter the metabox defaults.
		add_filter(
			'theme_mod_neve_post_header_layout',
			function ( $value ) {
				if ( is_admin() ) {
					$screen = get_current_screen();
					if ( $screen->id == $this->model->get_type() ) {
						return Mods::get( 'neve_' . $this->model->get_type() . '_header_layout', $value );
					}
				}
				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_post_title_alignment',
			function ( $value ) {
				if ( is_singular( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_type() . '_title_alignment', $value );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_post_cover_height',
			function ( $value ) {
				if ( is_singular( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_type() . '_cover_height', $value );
				}

				return $value;
			}
		);

		add_filter(
			'neve_metadata_separator_filter',
			function ( $value ) {
				$separator = Mods::get( 'neve_' . $this->model->get_type() . '_archive_metadata_separator', $value );
				if ( is_post_type_archive( $this->model->get_type() ) && $this->model->is_custom_layout_archive_enabled() ) {
					return $separator;
				}
				if ( is_singular( $this->model->get_type() ) && $this->model->is_custom_layout_enabled() ) {
					return Mods::get( 'neve_single_' . $this->model->get_type() . '_metadata_separator', $separator );
				}

				return $value;
			}
		);


		$has_custom_fields = Loader::has_compatibility( 'meta_custom_fields' );
		$filter            = $has_custom_fields ? 'theme_mod_neve_single_post_meta_fields' : 'theme_mod_neve_single_post_meta_ordering';
		$theme_mod         = $has_custom_fields ? 'neve_single_' . $this->model->get_type() . '_meta_fields' : 'neve_single_' . $this->model->get_type() . '_meta_ordering';

		add_filter(
			$filter,
			function ( $value ) use ( $theme_mod ) {
				if ( is_singular( $this->model->get_type() ) ) {
					return Mods::get( $theme_mod, $value );
				}
				return $value;
			}
		);

		add_filter(
			'neve_display_author_avatar',
			function ( $value ) {
				$show_avatar = Mods::get( 'neve_' . $this->model->get_type() . '_archive_author_avatar', $value );
				if ( is_post_type_archive( $this->model->get_type() ) && $this->model->is_custom_layout_archive_enabled() ) {
					return $show_avatar;
				}
				if ( is_singular( $this->model->get_type() ) && $this->model->is_custom_layout_archive_enabled() ) {
					return Mods::get( 'neve_single_' . $this->model->get_type() . '_author_avatar', $show_avatar );
				}

				return $value;
			},
			16
		);

		add_filter(
			'theme_mod_neve_single_post_author_avatar',
			function ( $value ) {
				if ( is_singular( $this->model->get_type() ) ) {
					return Mods::get( 'neve_single_' . $this->model->get_type() . '_author_avatar', $value );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_single_post_avatar_size',
			function ( $value ) {
				$avatar_size = Mods::get( 'neve_' . $this->model->get_type() . '_archive_author_avatar_size', $value );
				if ( is_post_type_archive( $this->model->get_type() ) ) {
					return $avatar_size;
				}
				if ( is_singular( $this->model->get_type() ) ) {
					return Mods::get( 'neve_single_' . $this->model->get_type() . '_avatar_size', $avatar_size );
				}

				return $value;
			}
		);

		add_filter(
			'neve_author_avatar_size_filter',
			function ( $value ) {
				$avatar_size = Mods::get( 'neve_' . $this->model->get_type() . '_archive_author_avatar_size', wp_json_encode( $value ) );
				if ( is_post_type_archive( $this->model->get_type() ) ) {
					return json_decode( $avatar_size );
				}
				if ( is_singular( $this->model->get_type() ) ) {
					return json_decode( Mods::get( 'neve_single_' . $this->model->get_type() . '_avatar_size', $avatar_size ) );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_show_last_updated_date',
			function ( $value ) {
				if ( is_singular( $this->model->get_type() ) ) {
					return Mods::get( 'neve_single_' . $this->model->get_type() . '_show_last_updated_date', $value );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_single_post_elements_spacing',
			function ( $value ) {
				if ( is_singular( $this->model->get_type() ) ) {
					return Mods::get( 'neve_single_' . $this->model->get_type() . '_elements_spacing', $value );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_post_comment_section_title',
			function ( $value ) {
				if ( is_singular( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_type() . '_comment_section_title', $value );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_comments_boxed_layout',
			function ( $value ) {
				if ( is_singular( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_type() . '_comments_boxed_layout', $value );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_comments_form_boxed_layout',
			function ( $value ) {
				if ( is_singular( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_type() . '_comments_form_boxed_layout', $value );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_comments_boxed_padding',
			function ( $value ) {
				if ( is_singular( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_type() . '_comments_boxed_padding', $value );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_comments_boxed_background_color',
			function ( $value ) {
				if ( is_singular( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_type() . '_comments_boxed_background_color', $value );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_comments_boxed_text_color',
			function ( $value ) {
				if ( is_singular( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_type() . '_comments_boxed_text_color', $value );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_post_comment_form_title',
			function ( $value ) {
				if ( is_singular( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_type() . '_comment_form_title', $value );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_post_comment_form_button_style',
			function ( $value ) {
				if ( is_singular( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_type() . '_comment_form_button_style', $value );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_post_comment_form_button_text',
			function ( $value ) {
				if ( is_singular( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_type() . '_comment_form_button_text', $value );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_comments_form_boxed_padding',
			function ( $value ) {
				if ( is_singular( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_type() . '_comments_form_boxed_padding', $value );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_comments_form_boxed_background_color',
			function ( $value ) {
				if ( is_singular( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_type() . '_comments_form_boxed_background_color', $value );
				}

				return $value;
			}
		);

		add_filter(
			'theme_mod_neve_comments_form_boxed_text_color',
			function ( $value ) {
				if ( is_singular( $this->model->get_type() ) ) {
					return Mods::get( 'neve_' . $this->model->get_type() . '_comments_form_boxed_text_color', $value );
				}

				return $value;
			}
		);

		if ( $this->is_blog_pro_enabled ) {
			add_filter(
				'theme_mod_neve_comment_section_style',
				function ( $value ) {
					if ( is_singular( $this->model->get_type() ) ) {
						return Mods::get( 'neve_' . $this->model->get_type() . '_comment_section_style', $value );
					}

					return $value;
				}
			);

			add_filter(
				'theme_mod_neve_author_box_enable_avatar',
				function ( $value ) {
					if ( is_singular( $this->model->get_type() ) ) {
						return Mods::get( 'neve_author_' . $this->model->get_type() . '_box_enable_avatar', $value );
					}

					return $value;
				}
			);

			add_filter(
				'theme_mod_neve_author_box_avatar_size',
				function ( $value ) {
					if ( is_singular( $this->model->get_type() ) ) {
						return Mods::get( 'neve_author_' . $this->model->get_type() . '_box_avatar_size', $value );
					}

					return $value;
				}
			);

			add_filter(
				'theme_mod_neve_author_box_avatar_position',
				function ( $value ) {
					if ( is_singular( $this->model->get_type() ) ) {
						return Mods::get( 'neve_author_' . $this->model->get_type() . '_box_avatar_position', $value );
					}

					return $value;
				}
			);

			add_filter(
				'theme_mod_neve_author_box_avatar_border_radius',
				function ( $value ) {
					if ( is_singular( $this->model->get_type() ) ) {
						return Mods::get( 'neve_author_' . $this->model->get_type() . '_box_avatar_border_radius', $value );
					}

					return $value;
				}
			);

			add_filter(
				'theme_mod_neve_author_box_enable_archive_link',
				function ( $value ) {
					if ( is_singular( $this->model->get_type() ) ) {
						return Mods::get( 'neve_author_' . $this->model->get_type() . '_box_enable_archive_link', $value );
					}

					return $value;
				}
			);

			add_filter(
				'theme_mod_neve_author_box_content_alignment',
				function ( $value ) {
					if ( is_singular( $this->model->get_type() ) ) {
						return Mods::get( 'neve_author_' . $this->model->get_type() . '_box_content_alignment', $value );
					}

					return $value;
				}
			);

			add_filter(
				'theme_mod_neve_author_box_boxed_layout',
				function ( $value ) {
					if ( is_singular( $this->model->get_type() ) ) {
						return Mods::get( 'neve_author_' . $this->model->get_type() . '_box_boxed_layout', $value );
					}

					return $value;
				}
			);

			add_filter(
				'theme_mod_neve_author_box_boxed_padding',
				function ( $value ) {
					if ( is_singular( $this->model->get_type() ) ) {
						return Mods::get( 'neve_author_' . $this->model->get_type() . '_box_boxed_padding', $value );
					}

					return $value;
				}
			);

			add_filter(
				'theme_mod_neve_author_box_boxed_background_color',
				function ( $value ) {
					if ( is_singular( $this->model->get_type() ) ) {
						return Mods::get( 'neve_author_' . $this->model->get_type() . '_box_boxed_background_color', $value );
					}

					return $value;
				}
			);

			add_filter(
				'theme_mod_neve_author_box_boxed_text_color',
				function ( $value ) {
					if ( is_singular( $this->model->get_type() ) ) {
						return Mods::get( 'neve_author_' . $this->model->get_type() . '_box_boxed_text_color', $value );
					}

					return $value;
				}
			);

			add_filter(
				'theme_mod_neve_related_posts_title',
				function ( $value ) {
					if ( is_singular( $this->model->get_type() ) ) {
						return Mods::get( 'neve_related_' . $this->model->get_type() . '_title', $value );
					}

					return $value;
				}
			);

			add_filter(
				'theme_mod_neve_related_posts_title_tag',
				function ( $value ) {
					if ( is_singular( $this->model->get_type() ) ) {
						return Mods::get( 'neve_related_' . $this->model->get_type() . '_title_tag', $value );
					}

					return $value;
				}
			);

			add_filter(
				'theme_mod_neve_related_posts_taxonomy',
				function ( $value ) {
					if ( is_singular( $this->model->get_type() ) ) {
						return Mods::get( 'neve_related_' . $this->model->get_type() . '_taxonomy', $value );
					}

					return $value;
				}
			);

			add_filter(
				'theme_mod_neve_related_posts_number',
				function ( $value ) {
					if ( is_singular( $this->model->get_type() ) ) {
						return Mods::get( 'neve_related_' . $this->model->get_type() . '_number', $value );
					}

					return $value;
				}
			);

			add_filter(
				'theme_mod_neve_related_posts_excerpt_length',
				function ( $value ) {
					if ( is_singular( $this->model->get_type() ) ) {
						return Mods::get( 'neve_related_' . $this->model->get_type() . '_excerpt_length', $value );
					}

					return $value;
				}
			);

			add_filter(
				'theme_mod_neve_related_posts_col_nb',
				function ( $value ) {
					if ( is_singular( $this->model->get_type() ) ) {
						return Mods::get( 'neve_related_' . $this->model->get_type() . '_col_nb', $value );
					}

					return $value;
				}
			);

			add_filter(
				'theme_mod_neve_related_posts_enable_featured_image',
				function ( $value ) {
					if ( is_singular( $this->model->get_type() ) ) {
						return Mods::get( 'neve_related_' . $this->model->get_type() . '_enable_featured_image', $value );
					}

					return $value;
				}
			);

			add_filter(
				'theme_mod_neve_related_posts_boxed_layout',
				function ( $value ) {
					if ( is_singular( $this->model->get_type() ) ) {
						return Mods::get( 'neve_related_' . $this->model->get_type() . '_boxed_layout', $value );
					}

					return $value;
				}
			);

			add_filter(
				'theme_mod_neve_related_posts_boxed_padding',
				function ( $value ) {
					if ( is_singular( $this->model->get_type() ) ) {
						return Mods::get( 'neve_related_' . $this->model->get_type() . '_boxed_padding', $value );
					}

					return $value;
				}
			);

			add_filter(
				'theme_mod_neve_related_posts_boxed_background_color',
				function ( $value ) {
					if ( is_singular( $this->model->get_type() ) ) {
						return Mods::get( 'neve_related_' . $this->model->get_type() . '_boxed_background_color', $value );
					}

					return $value;
				}
			);

			add_filter(
				'theme_mod_neve_related_posts_boxed_text_color',
				function ( $value ) {
					if ( is_singular( $this->model->get_type() ) ) {
						return Mods::get( 'neve_related_' . $this->model->get_type() . '_boxed_text_color', $value );
					}

					return $value;
				}
			);

			add_filter(
				'theme_mod_neve_sharing_icons',
				function ( $value ) {
					if ( is_singular( $this->model->get_type() ) ) {
						return Mods::get( 'neve_' . $this->model->get_type() . '_sharing_icons', $value );
					}

					return $value;
				}
			);

			add_filter(
				'theme_mod_neve_sharing_icon_style',
				function ( $value ) {
					if ( is_singular( $this->model->get_type() ) ) {
						return Mods::get( 'neve_' . $this->model->get_type() . '_sharing_icon_style', $value );
					}

					return $value;
				}
			);

			add_filter(
				'theme_mod_neve_sharing_enable_custom_color',
				function ( $value ) {
					if ( is_singular( $this->model->get_type() ) ) {
						return Mods::get( 'neve_' . $this->model->get_type() . '_sharing_enable_custom_color', $value );
					}

					return $value;
				}
			);

			add_filter(
				'theme_mod_neve_sharing_icon_color',
				function ( $value ) {
					if ( is_singular( $this->model->get_type() ) ) {
						return Mods::get( 'neve_' . $this->model->get_type() . '_sharing_icon_color', $value );
					}

					return $value;
				}
			);

			add_filter(
				'theme_mod_neve_sharing_custom_color',
				function ( $value ) {
					if ( is_singular( $this->model->get_type() ) ) {
						return Mods::get( 'neve_' . $this->model->get_type() . '_sharing_custom_color', $value );
					}

					return $value;
				}
			);

			add_filter(
				'theme_mod_neve_sharing_icon_size',
				function ( $value ) {
					if ( is_singular( $this->model->get_type() ) ) {
						return Mods::get( 'neve_' . $this->model->get_type() . '_sharing_icon_size', $value );
					}

					return $value;
				}
			);

			add_filter(
				'theme_mod_neve_sharing_icon_padding',
				function ( $value ) {
					if ( is_singular( $this->model->get_type() ) ) {
						return Mods::get( 'neve_' . $this->model->get_type() . '_sharing_icon_padding', $value );
					}

					return $value;
				}
			);

			add_filter(
				'theme_mod_neve_sharing_enable_text_label',
				function ( $value ) {
					if ( is_singular( $this->model->get_type() ) ) {
						return Mods::get( 'neve_' . $this->model->get_type() . '_sharing_enable_text_label', $value );
					}

					return $value;
				}
			);

			add_filter(
				'theme_mod_neve_sharing_label',
				function ( $value ) {
					if ( is_singular( $this->model->get_type() ) ) {
						return Mods::get( 'neve_' . $this->model->get_type() . '_sharing_label', $value );
					}

					return $value;
				}
			);

			add_filter(
				'theme_mod_neve_sharing_label_tag',
				function ( $value ) {
					if ( is_singular( $this->model->get_type() ) ) {
						return Mods::get( 'neve_' . $this->model->get_type() . '_sharing_label_tag', $value );
					}

					return $value;
				}
			);

			add_filter(
				'theme_mod_neve_sharing_label_position',
				function ( $value ) {
					if ( is_singular( $this->model->get_type() ) ) {
						return Mods::get( 'neve_' . $this->model->get_type() . '_sharing_label_position', $value );
					}

					return $value;
				}
			);

			add_filter(
				'theme_mod_neve_sharing_icons_alignment',
				function ( $value ) {
					if ( is_singular( $this->model->get_type() ) ) {
						return Mods::get( 'neve_' . $this->model->get_type() . '_sharing_icons_alignment', $value );
					}

					return $value;
				}
			);

			add_filter(
				'theme_mod_neve_sharing_icon_spacing',
				function ( $value ) {
					if ( is_singular( $this->model->get_type() ) ) {
						return Mods::get( 'neve_' . $this->model->get_type() . '_sharing_icon_spacing', $value );
					}

					return $value;
				}
			);

			add_filter(
				'theme_mod_neve_post_nav_infinite',
				function ( $value ) {
					if ( is_singular( $this->model->get_type() ) ) {
						return Mods::get( 'neve_' . $this->model->get_type() . '_nav_infinite', $value );
					}

					return $value;
				}
			);

			add_filter(
				'theme_mod_neve_post_inherit_vspacing',
				function ( $value ) {

					if ( is_singular( $this->model->get_type() ) ) {
						return Mods::get( 'neve_' . $this->model->get_type() . '_inherit_vspacing', $value );
					}

					return $value;
				}
			);

			add_filter(
				'theme_mod_neve_post_content_vspacing',
				function ( $value ) {
					if ( is_singular( $this->model->get_type() ) ) {
						return Mods::get( 'neve_' . $this->model->get_type() . '_content_vspacing', $value );
					}

					return $value;
				}
			);
		}
	}

	/**
	 * Get default values for ordering control
	 *
	 * @since 3.1.0
	 * @return array
	 */
	public function default_post_ordering() {
		$default_components = $this->post_ordering();
		if ( ! $this->model->is_custom_layout_enabled() ) {
			return $default_components;
		}

		$default_components = [
			'title-meta',
			'thumbnail',
			'content',
			'tags',
			'comments',
		];

		if ( $this->model->is_cover_layout() ) {
			$default_components = [
				'content',
				'tags',
				'comments',
			];
		}

		return $default_components;
	}

	/**
	 * Get elements order.
	 *
	 * @since 3.1.0
	 * @return array
	 */
	private function get_content_order() {
		$default_order = $this->default_post_ordering();

		$content_order = Mods::get( 'neve_layout_single_' . $this->current_post_type . '_elements_order', wp_json_encode( $default_order ) );
		if ( ! is_string( $content_order ) ) {
			$content_order = wp_json_encode( $default_order );
		}

		$content_order = json_decode( $content_order, true );
		if ( apply_filters( 'neve_filter_toggle_content_parts', true, 'title' ) !== true ) {
			$title_key = array_search( 'title-meta', $content_order, true );
			if ( $title_key !== false ) {
				unset( $content_order[ $title_key ] );
			}
		}

		if ( apply_filters( 'neve_filter_toggle_content_parts', true, 'featured-image' ) !== true || ! has_post_thumbnail() ) {
			$thumb_index = array_search( 'thumbnail', $content_order, true );
			if ( $thumb_index !== false ) {
				unset( $content_order[ $thumb_index ] );
			}
		}

		return apply_filters( 'neve_layout_single_post_elements_order', $content_order );
	}

	/**
	 * Render the post header.
	 *
	 * @since 3.1.0
	 * @param string $context the context provided in do_action.
	 *
	 * @return void
	 */
	public function render_post( $context ) {
		if ( $context !== 'single-post' ) {
			return;
		}

		$this->current_post_type = get_post_type();
		if ( empty( $this->current_post_type ) || $this->current_post_type === 'post' || $this->current_post_type !== $this->model->get_type() ) {
			return;
		}

		if ( ! $this->model->is_custom_layout_enabled() ) {
			return;
		}

		$content_order = $this->get_content_order();
		if ( empty( $content_order ) ) {
			return;
		}

		remove_all_actions( 'neve_do_single_post' );

		$should_add_skip_lazy = apply_filters( 'neve_skip_lazy', true );
		$skip_lazy_class      = '';
		if ( $should_add_skip_lazy ) {
			$thumbnail_index = array_search( 'thumbnail', $content_order );
			$content_index   = array_search( 'content', $content_order );
			if ( $thumbnail_index < $content_index ) {
				$skip_lazy_class = 'skip-lazy';
			}
		}

		$content_order_length = count( $content_order );

		foreach ( $content_order as $index => $item ) {
			switch ( $item ) {
				case 'title-meta':
					if ( $this->model->is_cover_layout() ) {
						break;
					}
					$this->render_entry_header();
					break;
				case 'thumbnail':
					if ( $this->model->is_cover_layout() ) {
						break;
					}
					echo '<div class="nv-thumb-wrap">';
					echo get_the_post_thumbnail(
						null,
						'neve-blog',
						array( 'class' => $skip_lazy_class )
					);
					echo '</div>';
					break;
				case 'content':
					do_action( 'neve_before_content', 'single-post' );
					echo '<div class="nv-content-wrap entry-content">';
					the_content();
					echo '</div>';
					do_action( 'neve_do_pagination', 'single' );
					do_action( 'neve_after_content', 'single-post' );
					break;
				case 'post-navigation':
					do_action( 'neve_post_navigation' );
					break;
				case 'tags':
					do_action( 'neve_do_tags' );
					break;
				case 'title':
					if ( $this->model->is_cover_layout() ) {
						break;
					}
					if ( $index !== $content_order_length - 1 && $content_order[ $index + 1 ] === 'meta' ) {
						$this->render_entry_header();
						break;
					}
					$this->render_entry_header( false );
					break;
				case 'meta':
					if ( $this->model->is_cover_layout() ) {
						break;
					}
					if ( $index !== 0 && $content_order[ $index - 1 ] === 'title' ) {
						break;
					}
					$this->render_post_meta();
					break;
				case 'author-biography':
					do_action( 'neve_layout_single_post_author_biography' );
					break;
				case 'related-posts':
					do_action( 'neve_do_related_posts' );
					break;
				case 'sharing-icons':
					do_action( 'neve_do_sharing' );
					break;
				case 'comments':
					comments_template();
					break;
				default:
					break;
			}
		}
	}

	/**
	 * Render post header
	 *
	 * @since 3.1.0
	 * @param bool $render_meta Render meta flag.
	 *
	 * @return void
	 */
	private function render_entry_header( $render_meta = true ) {
		$normal_style = apply_filters( 'neve_title_alignment_style', '', 'normal' );
		if ( ! empty( $normal_style ) ) {
			$normal_style = 'style="' . $normal_style . '"';
		}
		echo '<div class="entry-header" ' . wp_kses_post( $normal_style ) . '>';
		echo '<div class="nv-title-meta-wrap">';
		do_action( 'neve_before_post_title' );
		echo '<h1 class="title entry-title">' . wp_kses_post( get_the_title() ) . '</h1>';
		if ( $render_meta ) {
			$this->render_post_meta();
		}
		echo '</div>';
		echo '</div>';
	}

	/**
	 * Render the post meta.
	 *
	 * @since 3.1.0
	 * @param bool $is_list Flag to render meta as a list or as a text.
	 *
	 * @return bool
	 */
	public function render_post_meta( $is_list = true ) {
		if ( ! get_post() ) {
			return false;
		}
		$default_meta_order = Mods::get(
			'neve_' . $this->current_post_type . '_archive_meta_ordering',
			wp_json_encode(
				array(
					'author',
					'date',
					'comments',
				)
			)
		);

		$has_custom_fields  = Loader::has_compatibility( 'meta_custom_fields' );
		$theme_mod          = $has_custom_fields ? 'neve_single_' . $this->model->get_type() . '_meta_fields' : 'neve_single_' . $this->model->get_type() . '_meta_ordering';
		$default_meta_order = $has_custom_fields ? $this->get_default_meta_value( 'neve_single_' . $this->model->get_type() . '_meta_ordering', Mods::get( 'neve_single_post_meta_ordering', $default_meta_order ) ) : $default_meta_order;
		$default_meta_order = $has_custom_fields ? Mods::get( 'neve_single_post_meta_fields', $default_meta_order ) : $default_meta_order;
		$meta_order         = Mods::get( $theme_mod, $default_meta_order );

		$meta_order = is_string( $meta_order ) ? json_decode( $meta_order ) : $meta_order;
		$meta_order = apply_filters( 'neve_post_meta_ordering_filter', $meta_order );

		do_action( 'neve_post_meta_single', $meta_order, $is_list );

		return true;
	}

	/**
	 * Check that the custom post type is supported
	 *
	 * @since 3.1.0
	 * @return bool
	 */
	private function is_supported_post_type() {
		$this->current_post_type = get_post_type();
		if ( empty( $this->current_post_type ) ) {
			return false;
		}
		if ( in_array( $this->current_post_type, [ 'post', 'page' ] ) || ! is_singular( $this->current_post_type ) ) {
			return false;
		}

		if ( ! $this->model->is_custom_layout_enabled() ) {
			return false;
		}

		return in_array( $this->current_post_type, apply_filters( 'neve_post_type_supported_list', [], 'block_editor' ) );
	}

	/**
	 * Render the cover layout on single post.
	 *
	 * @since 3.1.0
	 * @return void
	 */
	public function render_cover_header() {
		if ( ! $this->is_supported_post_type() ) {
			return;
		}

		if ( $this->current_post_type !== $this->model->get_type() ) {
			return;
		}

		$default_layout = Mods::get( 'neve_post_header_layout', 'normal' );
		$header_layout  = Mods::get( 'neve_' . $this->current_post_type . '_header_layout', $default_layout );
		if ( $header_layout !== 'cover' ) {
			if ( Mods::get( 'neve_post_header_layout', 'normal' ) === 'cover' ) {
				remove_all_actions( 'neve_after_header_wrapper_hook' );
			}
			return;
		}
		remove_all_actions( 'neve_after_header_wrapper_hook' );

		$hide_thumbnail = Mods::get( 'neve_' . $this->current_post_type . '_cover_hide_thumbnail', false );
		$post_thumbnail = get_the_post_thumbnail_url();
		$cover_style    = '';
		if ( $hide_thumbnail === false && ! empty( $post_thumbnail ) ) {
			$cover_style = 'background-image:url(' . esc_url( $post_thumbnail ) . ');';
		}

		$container_class = [ 'container' ];

		$container_mode = Mods::get( 'neve_' . $this->current_post_type . '_cover_container', 'contained' );

		$title_meta_wrap_classes = [
			'nv-title-meta-wrap',
		];
		$title_mode              = Mods::get( 'neve_' . $this->current_post_type . '_cover_title_boxed_layout', false );
		if ( $title_mode ) {
			$title_meta_wrap_classes[] = 'nv-is-boxed';
		}

		/**
		 * Filters the post title styles to override specific styles.
		 *
		 * @param string $style The styles for the title.
		 * @param string $context The context of the layout (e.g. 'cover', 'normal'). Default is 'normal'.
		 *
		 * @since 3.1.0
		 */
		$cover_style = apply_filters( 'neve_title_alignment_style', $cover_style, 'cover' );
		if ( ! empty( $cover_style ) ) {
			$cover_style = 'style="' . $cover_style . '"';
		}

		$meta_before = Mods::get( 'neve_' . $this->current_post_type . '_cover_meta_before_title', false );

		echo '<div class="nv-post-cover" ' . wp_kses_post( $cover_style ) . '>';
		echo '<div class="nv-overlay"></div>';
		echo $container_mode === 'contained' ? '<div class="' . esc_attr( implode( ' ', $container_class ) ) . '">' : '';

		echo '<div class="' . esc_attr( implode( ' ', $title_meta_wrap_classes ) ) . '">';
		if ( $meta_before ) {
			$this->render_post_meta();
		}
		do_action( 'neve_before_post_title' );
		echo '<h1 class="title entry-title">' . wp_kses_post( get_the_title() ) . '</h1>';
		if ( ! $meta_before ) {
			$this->render_post_meta();
		}
		echo '</div>';

		echo $container_mode === 'contained' ? '</div>' : '';
		echo '</div>';
	}

	/**
	 * Filters the post types that are allowed to use the cover feature.
	 *
	 * @since 3.1.0
	 * @param array $allowed_context Allowed context array [ 'post', 'page' ].
	 *
	 * @return array|false[]|WP_Post_Type[]
	 */
	public function allowed_custom_post_types_filter( $allowed_context ) {
		if ( ! $this->is_supported_post_type() ) {
			return $allowed_context;
		}

		return array_merge( $allowed_context, [ $this->current_post_type ] );
	}

	/**
	 * Add additional listeners for customizer reactive controls.
	 *
	 * @since 3.1.0
	 * @param array $array neveCustomizePreview object.
	 *
	 * @return array
	 */
	final public function add_dynamic_content_listeners( $array ) {
		$dynamic_content_widths = [];
		if ( isset( $array['dynamicContentWidths'] ) && is_array( $array['dynamicContentWidths'] ) ) {
			$dynamic_content_widths = $array['dynamicContentWidths'];
		}

		$dynamic_content_containers = [];
		if ( isset( $array['dynamicContentContainers'] ) && is_array( $array['dynamicContentContainers'] ) ) {
			$dynamic_content_containers = $array['dynamicContentContainers'];
		}

		$dynamic_content_widths = array_merge(
			$dynamic_content_widths,
			[
				'neve_single_' . $this->model->get_type() . '_content_width'  => [
					'content' => '.single-post-container .nv-single-post-wrap',
					'sidebar' => '.single-post-container .nv-sidebar-wrap',
				],
				'neve_' . $this->model->get_type() . '_archive_content_width' => [
					'content' => '.archive-container .nv-index-posts',
					'sidebar' => '.archive-container .nv-sidebar-wrap',
				],
			]
		);

		$dynamic_content_containers[ 'neve_single_' . $this->model->get_type() . '_container_style' ]  = '.single-post-container';
		$dynamic_content_containers[ 'neve_' . $this->model->get_type() . '_archive_container_style' ] = '.archive-container';

		$array['dynamicContentWidths']     = $dynamic_content_widths;
		$array['dynamicContentContainers'] = $dynamic_content_containers;

		return $array;
	}

	/**
	 * Filter container style for supported custom post types.
	 *
	 * @since 3.1.0
	 * @param string $style Class of the container.
	 *
	 * @return string
	 */
	final public function custom_post_container_styles( $style ) {
		if ( ! $this->is_supported_post_type() ) {
			return $style;
		}
		$theme_mod      = 'neve_single_' . $this->current_post_type . '_container_style';
		$container_type = Mods::get( $theme_mod, 'contained' );
		if ( $container_type === 'contained' ) {
			return 'container';
		}

		return 'container-fluid';
	}

	/**
	 * Filter related post type context.
	 *
	 * @since 3.1.0
	 * @param string $post_type The post type to return related posts.
	 *
	 * @return string
	 */
	final public function related_post_type_context( $post_type = 'post' ) {
		$model_post_type = $this->model->get_type();
		if ( get_post_type() === $model_post_type && $model_post_type !== 'post' ) {
			return $this->model->get_type();
		}
		return $post_type;
	}
}
