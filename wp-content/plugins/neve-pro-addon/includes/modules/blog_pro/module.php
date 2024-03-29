<?php
/**
 * Author:          Stefan Cotitosu <stefan@themeisle.com>
 * Created on:      2019-02-27
 *
 * @package Neve Pro
 */

namespace Neve_Pro\Modules\Blog_Pro;

use Neve_Pro\Core\Abstract_Module;
use Neve_Pro\Core\Loader;
use Neve_Pro\Modules\Blog_Pro\Customizer\Single_Post;
use Neve_Pro\Modules\Header_Footer_Grid\Components\Icons;
use Neve_Pro\Traits\Utils;
use Neve_Pro\Traits\Inline_Styles;

/**
 * Class Module  - main class for the module
 * Enqueue scripts, style
 * Render functions
 *
 * @package Neve_Pro\Modules\Blog_Pro
 */
class Module extends Abstract_Module {
	use Utils;
	use Inline_Styles;

	/**
	 * Define module properties.
	 *
	 * @access  public
	 * @return void
	 *
	 * @version 1.0.0
	 */
	public function define_module_properties() {
		$this->slug              = 'blog_pro';
		$this->name              = __( 'Blog Booster', 'neve' );
		$this->description       = __( 'Give a huge boost to your entire blogging experience with features specially designed for increased user experience.', 'neve' );
		$this->documentation     = array(
			'url'   => 'https://docs.themeisle.com/article/1059-blog-booster-documentation',
			'label' => __( 'Learn more', 'neve' ),
		);
		$this->order             = 4;
		$this->has_dynamic_style = true;
	}

	/**
	 * Check if module should load.
	 *
	 * @return bool
	 */
	public function should_load() {
		return $this->is_active();
	}

	/**
	 * Run Blog Pro Module
	 */
	public function run_module() {
		add_filter( 'kses_allowed_protocols', array( $this, 'custom_allowed_protocols' ), 1000 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_style' ) );
		add_filter( 'neve_read_more_args', array( $this, 'get_read_more_args' ) );
		add_action( 'neve_layout_single_post_author_biography', array( $this, 'author_biography_render' ) );
		add_action( 'neve_do_related_posts', array( $this, 'related_posts_render' ) );

		add_action( 'pre_get_posts', array( $this, 'order_posts' ), 1 );
		add_filter( 'get_next_post_where', array( $this, 'adjacent_post_where' ) );
		add_filter( 'get_previous_post_where', array( $this, 'adjacent_post_where' ) );
		add_filter( 'get_next_post_sort', array( $this, 'adjacent_post_sort' ) );
		add_filter( 'get_previous_post_sort', array( $this, 'adjacent_post_sort' ) );


		add_action( 'neve_do_sharing', array( $this, 'render_sharing_icons' ) );
		add_action( 'init', array( $this, 'count_post_words' ) );
		add_action( 'save_post', array( $this, 'update_number_of_words' ) );
		add_action( 'neve_do_comment_area', array( $this, 'before_comment_area' ), 0 );
		add_action( 'neve_do_comment_area', array( $this, 'after_comment_area' ), PHP_INT_MAX );
		add_filter( 'neve_pro_filter_customizer_modules', array( $this, 'add_customizer_classes' ) );
		add_filter( 'neve_meta_sidebar_localize_filter', array( $this, 'add_pro_features' ) );
		add_filter( 'neve_sidebar_meta_controls', array( $this, 'add_reading_time_meta_block_editor' ) );
		add_filter( 'neve_meta_filter', array( $this, 'add_reading_time_meta' ) );
		add_filter( 'neve_do_read_time', array( $this, 'render_read_time_meta' ) );
		add_filter( 'neve_magic_tags_config', array( $this, 'add_read_time_magic_tag' ) );
		add_filter( 'neve_post_meta_ordering_filter', array( $this, 'reading_time_meta_action' ) );
		add_action( 'wp', array( $this, 'add_post_infinite_scroll' ) );

		if ( ! self::has_single_compatibility() ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'meta_custom_separator' ) );
		}


		add_filter(
			'neve_featured_has_post_thumbnail',
			function ( $class, $post_id ) {
				$featured = get_theme_mod( 'neve_enable_featured_post', false );
				if ( ! $featured ) {
					return $class;
				}

				$layout   = get_theme_mod( 'neve_blog_archive_layout', 'grid' );
				$position = get_theme_mod( 'neve_blog_list_image_position', 'left' );
				if ( $layout === 'default' && $position === 'no' ) {
					return $class;
				}

				return has_post_thumbnail( $post_id ) ? $class . ' with-thumb' : '';
			},
			10,
			2
		);

		if ( Loader::has_compatibility( 'meta_custom_fields' ) ) {
			add_filter( 'neve_do_custom_meta', array( $this, 'render_custom_meta' ), 10, 4 );
		}

		if ( Loader::has_compatibility( 'blog_hover_effects' ) ) {
			add_action( 'enqueue_block_editor_assets', array( $this, 'load_metabox_script' ) );
			add_action( 'init', array( $this, 'register_meta_second_thumbnail' ) );
			add_action( 'wp', array( $this, 'blog_post_image_effect' ) );
		}
	}

	/**
	 * Do we have the single compatibility enabled?
	 *
	 * @return bool Compatibility status.
	 */
	public static function has_single_compatibility() {
		return Loader::has_compatibility( 'single_customizer' );
	}

	/**
	 * Add customizer classes.
	 *
	 * @param array $classes loaded classes.
	 *
	 * @return array
	 */
	public function add_customizer_classes( $classes ) {
		$classes[] = 'Modules\Blog_Pro\Customizer\Blog_Pro';
		$classes[] = 'Modules\Blog_Pro\Customizer\Single_Post';

		return $classes;
	}

	/**
	 * Enqueue module styles
	 */
	public function enqueue_style() {
		$file = 'style';

		$this->rtl_enqueue_style( 'neve-blog-pro', NEVE_PRO_INCLUDES_URL . 'modules/blog_pro/assets/' . $file . '.min.css', array(), NEVE_PRO_VERSION );
	}

	/**
	 * Get read more text options from customizer
	 *
	 * @return bool|array - text and style
	 */
	public function get_read_more_args() {

		$read_more_text = get_theme_mod( 'neve_read_more_text', esc_html__( 'Read More', 'neve' ) . ' &raquo;' );
		if ( empty( $read_more_text ) ) {
			return false;
		}
		$read_more_side = get_theme_mod( 'neve_read_more_style', 'text' );

		$read_more_classes = '';
		if ( $read_more_side === 'primary_button' ) {
			$read_more_classes = 'button button-primary';
		}
		if ( $read_more_side === 'secondary_button' ) {
			$read_more_classes = 'button button-secondary';
		}

		$args = array(
			'text'    => $read_more_text,
			'classes' => $read_more_classes,
		);

		return $args;
	}

	/**
	 * Change metadata separator according to the customizer setting
	 */
	public function meta_custom_separator() {

		$separator = get_theme_mod( 'neve_metadata_separator', esc_html( '/' ) );

		$custom_css  = '';
		$custom_css .= '.nv-meta-list li:not(:last-child):after,.nv-meta-list span:not(:last-child):after { content:"' . esc_html( $separator ) . '" }';

		wp_add_inline_style( 'neve-style', $custom_css );
	}


	/**
	 * Render author biography
	 */
	public function author_biography_render() {

		$avatar_markup       = '';
		$archive_link_markup = '';
		$container_class     = [ 'nv-author-biography' ];

		$first_name         = get_the_author_meta( 'user_firstname' );
		$last_name          = get_the_author_meta( 'user_lastname' );
		$is_author_name_set = ! empty( $first_name ) || ! empty( $last_name );
		$author_name        = esc_html( $first_name ) . ' ' . esc_html( $last_name );

		$show_avatar = get_theme_mod( 'neve_author_box_enable_avatar', true );
		if ( $show_avatar ) {
			$author_email  = get_the_author_meta( 'user_email' );
			$avatar_url    = get_avatar_url( $author_email );
			$avatar_markup = '<img src="' . esc_url( $avatar_url ) . '" alt="' . ( $is_author_name_set ? $author_name : __( 'Author', 'neve' ) ) . '" class="nv-author-bio-image">';
		} else {
			$container_class[] = 'no-avatar';
		}

		$boxed_layout = get_theme_mod( 'neve_author_box_boxed_layout', false );
		if ( $boxed_layout ) {
			$container_class[] = 'nv-is-boxed';
		}

		$archive_link = get_theme_mod( 'neve_author_box_enable_archive_link', false );
		if ( $archive_link ) {
			$author_id           = (int) get_the_author_meta( 'ID' );
			$author_url          = get_author_posts_url( $author_id );
			$archive_link_markup = '<a class="nv-author-bio-link" href="' . esc_url( $author_url ) . '">' . esc_html__( 'View Author posts', 'neve' ) . '</a>';
		}

		$avatar_position = get_theme_mod( 'neve_author_box_avatar_position', 'left' );

		$author_description = wp_kses_post( get_the_author_meta( 'description' ) );

		$section_markup  = '<div class="' . esc_attr( apply_filters( 'neve_author_biography_class', implode( ' ', $container_class ) ) ) . '">';
		$section_markup .= '<div class="nv-author-elements-wrapper ' . esc_attr( $avatar_position ) . '">';
		$section_markup .= $avatar_markup;


		$description_classes = [ 'nv-author-bio-text-wrapper' ];
		$section_markup     .= '<div class="' . esc_attr( implode( ' ', $description_classes ) ) . '">';

		if ( $is_author_name_set ) {
			$section_markup .= '<h4 class="nv-author-bio-name">' . ( $author_name ) . '</h4>';
		}

		if ( ! empty( $author_description ) ) {
			$section_markup .= '<p class="nv-author-bio-desc">' . $author_description . $archive_link_markup . '</p>';
		}
		$section_markup .= '</div>';
		$section_markup .= '</div>';
		$section_markup .= '</div>';

		echo $section_markup; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, already escaped.
	}

	/**
	 * Render the related post meta.
	 *
	 * @param bool $is_list Flag to render meta as a list or as a text.
	 *
	 * @return bool
	 */
	private function render_related_posts_meta( $is_list = true ) {

		$default_meta_order = $this->get_default_meta_value(
			'neve_post_meta_ordering',
			wp_json_encode(
				[
					'author',
					'category',
					'date',
					'comments',
				]
			)
		);

		$meta_order = get_theme_mod( 'neve_related_posts_post_meta_ordering', $default_meta_order );
		$meta_order = is_string( $meta_order ) ? json_decode( $meta_order ) : $meta_order;
		$meta_order = apply_filters( 'neve_related_posts_meta_ordering_filter', $meta_order );

		do_action( 'neve_post_meta_single', $meta_order, $is_list );

		return true;
	}

	/**
	 * Render related posts
	 */
	public function related_posts_render() {

		global $post;

		$default_title    = esc_html__( 'Related Posts', 'neve' );
		$section_title    = get_theme_mod( 'neve_related_posts_title', $default_title );
		$section_tag      = get_theme_mod( 'neve_related_posts_title_tag', 'h2' );
		$box_layout_width = get_theme_mod( 'neve_related_posts_box_layout_width', 'same-as-content' );

		$taxonomy        = get_theme_mod( 'neve_related_posts_taxonomy', 'category' );
		$number_of_posts = get_theme_mod( 'neve_related_posts_number', 3 );

		$current_taxonomy_ids = wp_get_object_terms(
			$post->ID,
			$taxonomy,
			array(
				'fields' => 'ids',
			)
		);

		$post_type = apply_filters( 'neve_related_post_type_filter', 'post' );
		$args      = array(
			'post_type'           => $post_type,
			'post_status'         => 'publish',
			'posts_per_page'      => $number_of_posts,
			'orderby'             => 'date',
			'ignore_sticky_posts' => true,
			'post__not_in'        => array( $post->ID ),
		);

		if ( $taxonomy === 'post_tag' ) {
			$args['tag__in'] = $current_taxonomy_ids;
		} else {
			$args['cat'] = $current_taxonomy_ids;
		}

		$loop = new \WP_Query( $args );

		$container_class = [ 'nv-related-posts' ];
		$wrapper_classes = [ 'posts-wrapper' ];
		$post_classes    = [ 'related-post' ];

		$is_boxed = get_theme_mod( 'neve_related_posts_boxed_layout', false );
		if ( $is_boxed ) {
			$container_class[] = 'nv-is-boxed';

			switch ( $box_layout_width ) {
				case 'wide':
					$container_class[] = 'alignwide';
					break;
				case 'full':
					$container_class[] = 'alignfull';
					break;
				default:
					break;
			}
		}

		$card_order = get_theme_mod( 'neve_related_posts_card', Single_Post::get_related_posts_card_default() );
		$card_order = json_decode( $card_order, true );
		$card_order = array_filter(
			$card_order,
			function ( $item ) {
				return isset( $item['visibility'] ) && $item['visibility'] === 'yes';
			}
		);


		if ( $loop->have_posts() ) { ?>
			<div class="<?php echo esc_attr( apply_filters( 'neve_related_posts_class', join( ' ', $container_class ) ) ); ?>">
				<div class="section-title">
					<?php echo '<' . esc_attr( $section_tag ) . '>'; ?>
						<?php echo esc_html( $section_title ); ?>
					<?php echo '</' . esc_attr( $section_tag ) . '>'; ?>
				</div>
				<div class="<?php echo esc_attr( join( ' ', $wrapper_classes ) ); ?>">
					<?php
					// @phpstan-ignore-next-line - impure function.
					while ( $loop->have_posts() ) {
						$loop->the_post();
						?>
						<div class="<?php echo esc_attr( join( ' ', $post_classes ) ); ?>">
							<div class="content">
								<?php

								$link = get_permalink();

								foreach ( $card_order as $item ) {
									$mb = isset( $item['margin_bottom'] ) ? '--mb: ' . $item['margin_bottom'] . 'px;' : '';

									if ( ! isset( $item['slug'] ) ) {
										continue;
									}

									switch ( $item['slug'] ) {
										case 'post_title':
											$title = get_the_title();
											if ( empty( $title ) ) {
												break;
											}
											?>
											<h3 class="title entry-title" style="<?php echo esc_attr( $mb ); ?>">
												<a href="<?php echo esc_url( $link ); ?>">
													<?php echo esc_html( $title ); ?>
												</a>
											</h3>
											<?php
											break;
										case 'post_meta':
											echo '<div style="' . esc_attr( $mb ) . '">';
											$this->render_related_posts_meta();
											echo '</div>';
											break;

										case 'featured_image':
											if ( ! has_post_thumbnail() ) {
												break;
											}
											?>
											<a class="th-wrap" href="<?php echo esc_url( $link ); ?>" style="<?php echo esc_attr( $mb ); ?>">
												<?php the_post_thumbnail(); ?>
											</a>
											<?php
											break;

										case 'post_excerpt':
											$excerpt = get_the_excerpt();
											if ( empty( $excerpt ) ) {
												break;
											}

											?>
											<div class="description excerpt-wrap" style="<?php echo esc_attr( $mb ); ?>">
												<?php
												add_filter(
													'excerpt_length',
													array(
														$this,
														'related_posts_excerpt_length',
													),
													10
												);
												the_excerpt();
												remove_filter(
													'excerpt_length',
													array(
														$this,
														'related_posts_excerpt_length',
													),
													10
												);
												?>
											</div>
											<?php
											break;
									}
								}
								?>
							</div>
						</div>
						<?php
					} // @phpstan-ignore-line - code is reachable.
					?>
				</div>
			</div>
			<?php
		}
		wp_reset_postdata();
	}

	/**
	 * Custom excerpt length for related posts
	 */
	public function related_posts_excerpt_length() {
		$excerpt_length = get_theme_mod( 'neve_related_posts_excerpt_length', 25 );
		$excerpt_length = round( $excerpt_length );

		return absint( $excerpt_length );
	}

	/**
	 * Order posts by date ( asc / desc ) or by last edited.
	 *
	 * @param \WP_Query $query Main Query.
	 */
	public function order_posts( $query ) {
		$order = get_theme_mod( 'neve_posts_order', 'date_posted_desc' );
		if ( ! is_admin() && $query->is_main_query() && $query->is_home() ) {
			if ( $order === 'date_updated' ) {
				$query->set( 'orderby', 'modified' );
			}
			if ( $order === 'date_posted_asc' ) {
				$query->set( 'order', 'asc' );
			}
		}
	}

	/**
	 * Change the query WHERE part for adjacent posts based on neve_posts_order option.
	 *
	 * @param string $sql Current sql query.
	 *
	 * @return string
	 */
	public function adjacent_post_where( $sql ) {
		if ( ! is_main_query() || ! is_singular() ) {
			return $sql;
		}

		$order          = get_theme_mod( 'neve_posts_order', 'date_posted_desc' );
		$allowed_values = array( 'date_posted_desc', 'date_posted_asc', 'date_updated' );

		if ( ! in_array( $order, $allowed_values ) ) {
			return $sql;
		}

		if ( $order === 'date_posted_desc' ) {
			return $sql;
		}

		if ( $order === 'date_posted_asc' ) {
			$pattern     = '/</';
			$replacement = '>';
			if ( 'get_next_post_where' === current_filter() ) {
				$pattern     = '/>/';
				$replacement = '<';
			}

			return preg_replace( $pattern, $replacement, $sql );
		}

		$the_post = get_post( get_the_ID() );

		$patterns   = array();
		$patterns[] = '/post_date/';
		$patterns[] = '/\'[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}\'/';

		$replacements   = array();
		$replacements[] = 'post_modified';
		$replacements[] = '\'' . $the_post->post_modified . '\'';

		return preg_replace( $patterns, $replacements, $sql );
	}

	/**
	 * Change the query SORT part for adjacent posts based on neve_posts_order option.
	 *
	 * @param string $sql Current sql query.
	 *
	 * @return string
	 */
	public function adjacent_post_sort( $sql ) {
		if ( ! is_main_query() || ! is_singular() ) {
			return $sql;
		}

		$order          = get_theme_mod( 'neve_posts_order', 'date_posted_desc' );
		$allowed_values = array( 'date_posted_desc', 'date_posted_asc', 'date_updated' );

		if ( ! in_array( $order, $allowed_values ) ) {
			return $sql;
		}

		if ( $order === 'date_posted_desc' ) {
			return $sql;
		}

		if ( $order === 'date_posted_asc' ) {
			$pattern     = '/ASC/';
			$replacement = 'DESC';
			if ( 'get_previous_post_sort' === current_filter() ) {
				$pattern     = '/DESC/';
				$replacement = 'ASC';
			}

			return preg_replace( $pattern, $replacement, $sql );
		}

		$pattern     = '/post_date/';
		$replacement = 'post_modified';

		return preg_replace( $pattern, $replacement, $sql );
	}

	/**
	 * Render sharing icons.
	 */
	public function render_sharing_icons() {
		$post_categories = wp_strip_all_tags( get_the_category_list( ',' ) );
		$post_title      = get_the_title();
		$post_link       = urlencode( get_the_permalink() );
		$email_title     = str_replace( '&', '%26', $post_title );
		$icon_size       = 100;

		$link_map = array(
			'facebook'  => array(
				'link' => add_query_arg(
					array(
						'u' => $post_link,
					),
					'https://www.facebook.com/sharer.php'
				),
				'icon' => Icons::get_instance()->get_single_icon( 'facebook', $icon_size ),

			),
			'twitter'   => array(
				'link' => add_query_arg(
					array(
						'url'      => $post_link,
						'text'     => rawurlencode( html_entity_decode( wp_strip_all_tags( $post_title ), ENT_COMPAT, 'UTF-8' ) ),
						'hashtags' => $post_categories,
					),
					'http://x.com/share'
				),
				'icon' => Icons::get_instance()->get_single_icon( 'twitter-x', $icon_size ),
			),
			'email'     => array(
				'link'   => add_query_arg(
					array(
						'subject' => wp_strip_all_tags( $email_title ),
						'body'    => $post_link,
					),
					'mailto:'
				),
				'icon'   => Icons::get_instance()->get_single_icon( 'envelope', $icon_size ),
				'target' => '0',
			),
			'pinterest' => array(
				'link' => 'https://pinterest.com/pin/create/bookmarklet/?media=' . get_the_post_thumbnail_url() . '&url=' . $post_link . '&description=' . $post_title,
				'icon' => Icons::get_instance()->get_single_icon( 'pinterest', $icon_size ),
			),
			'linkedin'  => array(
				'link' => 'https://www.linkedin.com/shareArticle?mini=true&url=' . $post_link . '&title=' . urlencode( $post_title ) . '&source=' . urlencode( get_bloginfo( 'name' ) ),
				'icon' => Icons::get_instance()->get_single_icon( 'linkedin', $icon_size ),
			),
			'tumblr'    => array(
				'link' => 'http://www.tumblr.com/share/link?url=' . $post_link . '&title=' . $post_title,
				'icon' => Icons::get_instance()->get_single_icon( 'tumblr', $icon_size ),
			),
			'reddit'    => array(
				'link' => 'https://reddit.com/submit?url=' . $post_link . '&title=' . $post_title,
				'icon' => Icons::get_instance()->get_single_icon( 'reddit', $icon_size ),
			),
			'whatsapp'  => array(
				'link'   => 'https://wa.me/?text=' . $post_link,
				'icon'   => Icons::get_instance()->get_single_icon( 'whatsapp', $icon_size ),
				'target' => '0',
			),
			'sms'       => array(
				'link'   => 'sms://?&body=' . $post_title . ' - ' . $post_link,
				'icon'   => Icons::get_instance()->get_single_icon( 'comments', $icon_size ),
				'target' => '0',
			),
			'vk'        => array(
				'link' => 'http://vk.com/share.php?url=' . urlencode( $post_link ),
				'icon' => Icons::get_instance()->get_single_icon( 'vk', $icon_size ),
			),
		);

		$default_value = apply_filters(
			'neve_sharing_icons_default_value',
			array(
				array(
					'social_network'  => 'facebook',
					'title'           => 'Facebook',
					'visibility'      => 'yes',
					'display_desktop' => true,
					'display_mobile'  => true,
				),
				array(
					'social_network'  => 'twitter',
					'title'           => 'X',
					'visibility'      => 'yes',
					'display_desktop' => true,
					'display_mobile'  => true,
				),
				array(
					'social_network'  => 'email',
					'title'           => 'Email',
					'visibility'      => 'yes',
					'display_desktop' => true,
					'display_mobile'  => true,
				),
			)
		);
		$sharing_icons = get_theme_mod( 'neve_sharing_icons', wp_json_encode( $default_value ) );
		$sharing_icons = json_decode( $sharing_icons, true );
		if ( empty( $sharing_icons ) ) {
			return;
		}


		$label_position = get_theme_mod( 'neve_sharing_label_position', 'before' );
		$sharing_class  = [ 'nv-post-share', $label_position ];

		$style           = get_theme_mod( 'neve_sharing_icon_style', 'round' );
		$sharing_class[] = $style . '-style';

		$custom_color = get_theme_mod( 'neve_sharing_enable_custom_color', false );
		if ( $custom_color ) {
			$sharing_class[] = 'custom-color';
		}


		echo '<div class="' . esc_attr( apply_filters( 'neve_post_share_class', implode( ' ', $sharing_class ) ) ) . '">';

		$has_label  = get_theme_mod( 'neve_sharing_enable_text_label', false );
		$label_text = get_theme_mod( 'neve_sharing_label', esc_html__( 'Share this post on social!', 'neve' ) );
		$label_tag  = get_theme_mod( 'neve_sharing_label_tag', 'span' );
		if ( $has_label && in_array( $label_position, [ 'before', 'above' ], true ) && ! empty( $label_text ) ) {
			echo '<' . esc_html( $label_tag ) . ' class="nv-social-icons-label">' . esc_html( $label_text ) . '</' . esc_html( $label_tag ) . '>';
		}
		echo '<ul>';

		foreach ( $sharing_icons as $icon => $values ) {
			$is_visible = array_key_exists( 'visibility', $values ) ? $values['visibility'] : 'no';
			if ( ! $is_visible ) {
				continue;
			}

			$social_network = array_key_exists( 'social_network', $values ) ? $values['social_network'] : '';
			if ( empty( $social_network ) ) {
				continue;
			}

			$classes     = '';
			$hide_mobile = isset( $values['display_mobile'] ) && $values['display_mobile'] === false;
			if ( $hide_mobile ) {
				$classes .= ' hide-mobile ';
			}

			$hide_desktop = isset( $values['display_desktop'] ) && $values['display_desktop'] === false;
			if ( $hide_desktop ) {
				$classes .= ' hide-desktop ';
			}

			$link_map_item = $link_map[ $social_network ];
			$is_blank      = ! ( isset( $link_map_item['target'] ) && (int) $link_map_item['target'] === 0 );

			$title = array_key_exists( 'title', $values ) ? $values['title'] : '';
			$style = 'style=' .
					( array_key_exists( 'icon_color', $values ) && ! empty( $values['icon_color'] ) ? '--hex:' . esc_attr( $values['icon_color'] ) . ';' : '' ) .
					( array_key_exists( 'background_color', $values ) && ! empty( $values['background_color'] ) ? '--bgsocial:' . esc_attr( $values['background_color'] ) . ';' : '' );


			echo '<li class="nv-social-icon social-' . esc_attr( $social_network ) . esc_attr( $classes ) . '">';
			echo '<a rel="noopener"' .
				( $is_blank ? ' target="_blank"' : '' ) .
				( ! empty( $title ) ? ' title="' . esc_attr( $title ) . '"' : '' ) .
				' href="' . esc_url( $link_map_item['link'] ) . '" class="' . esc_attr( $social_network ) . '" ' . esc_attr( $style ) . '>';
			echo $link_map_item['icon']; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped, already escaped
			echo '</a>';
			echo '</li>';
		}

		echo '</ul>';
		if ( $has_label && in_array( $label_position, [ 'after', 'below' ], true ) && ! empty( $label_text ) ) {
			echo '<' . esc_html( $label_tag ) . ' class="nv-social-icons-label">' . esc_html( $label_text ) . '</' . esc_html( $label_tag ) . '>';
		}
		echo '</div>';
	}

	/**
	 * Count words for every post and store the number in a meta field.
	 * This actions happens only once for posts that already exists.
	 */
	public function count_post_words() {
		$posts_have_nb_of_words = get_option( 'posts_have_nb_of_words', 'no' );
		if ( $posts_have_nb_of_words === 'yes' ) {
			return;
		}
		$args       = array(
			'post_type'      => 'post',
			'posts_per_page' => - 1,
		);
		$post_query = new \WP_Query( $args );
		if ( ! $post_query->have_posts() ) {
			return;
		}

		// @phpstan-ignore-next-line - impure function.
		while ( $post_query->have_posts() ) {
			$post_query->the_post();

			$post_id    = get_the_ID();
			$word_count = $this->get_number_of_words( $post_id );
			update_post_meta( $post_id, 'nb_of_words', $word_count );
		}
		// @phpstan-ignore-next-line - code is reachable.
		update_option( 'posts_have_nb_of_words', 'yes' );
	}


	/**
	 * Get number of words for a post.
	 *
	 * @param int $pid Post id.
	 *
	 * @return int
	 */
	private function get_number_of_words( $pid ) {
		$words_per_minute = apply_filters( 'neve_words_per_minute', 200 );
		$content          = get_post_field( 'post_content', $pid );
		$number_of_images = substr_count( strtolower( $content ), '<img ' );
		$content          = strip_shortcodes( $content );
		$content          = wp_strip_all_tags( $content );
		$word_count       = count( preg_split( '/\s+/', $content ) );
		if ( $number_of_images !== 0 ) {
			$additional_words_for_images = $this->calculate_images( $number_of_images, $words_per_minute );
			$word_count                 += $additional_words_for_images;
		}

		return $word_count;
	}

	/**
	 * Adds additional reading time for images
	 *
	 * Calculate additional reading time added by images in posts. Based on calculations by Medium.
	 * https://blog.medium.com/read-time-and-you-bc2048ab620c
	 *
	 * @param int   $total_images number of images in post.
	 * @param array $wpm words per minute.
	 *
	 * @return int  Additional time added to the reading time by images.
	 */
	public function calculate_images( $total_images, $wpm ) {
		$additional_time = 0;
		// For the first image add 12 seconds, second image add 11, ..., for image 10+ add 3 seconds.
		for ( $i = 1; $i <= $total_images; $i ++ ) {
			if ( $i >= 10 ) {
				$additional_time += 3 * (int) $wpm / 60;
			} else {
				$additional_time += ( 12 - ( $i - 1 ) ) * (int) $wpm / 60;
			}
		}

		return $additional_time;
	}

	/**
	 * Update number of words on post save.
	 *
	 * @param int $post_id Post id.
	 */
	public function update_number_of_words( $post_id ) {
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		$post_type       = get_post_type( $post_id );
		$allowed_context = apply_filters( 'neve_post_type_supported_list', [ 'post' ], 'block_editor' );
		if ( ! in_array( $post_type, $allowed_context, true ) ) {
			return;
		}

		$word_count = $this->get_number_of_words( $post_id );
		update_post_meta( $post_id, 'nb_of_words', $word_count );
	}

	/**
	 * Render toggle button and wrap comment area content.
	 */
	public function before_comment_area() {
		$post_id      = get_the_ID();
		$comment_area = get_theme_mod( 'neve_comment_section_style', 'always' );
		if ( $comment_area !== 'toggle' || neve_is_amp() ) {
			return;
		}

		$text_show = apply_filters( 'neve_show_comments_button_text', __( 'Show comments', 'neve' ) );
		$text_hide = apply_filters( 'neve_hide_comments_button_text', __( 'Hide comments', 'neve' ) );
		?>
		<script type="text/javascript">
			function toggleCommentArea(el, selector) {
				const wrapper = document.getElementById(selector);
				const buttonText = wrapper.classList.contains('nv-comments-hidden') ?
					"<?php echo esc_html( $text_hide ); ?>" :
					"<?php echo esc_html( $text_show ); ?>";

				wrapper.classList.toggle('nv-comments-hidden');
				el.textContent = buttonText;
			}
		</script>
		<?php
		$wrap_class      = isset( $_GET['replytocom'] ) ? '' : 'nv-comments-hidden';
		$button_text     = isset( $_GET['replytocom'] ) ? $text_hide : $text_show;
		$area_wrapper_id = get_theme_mod( 'neve_post_nav_infinite', false ) ? 'comment-area-wrapper-' . intval( $post_id ) : 'comment-area-wrapper';
		echo '<button onclick="toggleCommentArea(this,\'' . esc_attr( $area_wrapper_id ) . '\')" id="toggle-comment-area" class="button button-primary">' . esc_html( $button_text ) . '</button>';
		echo '<div id="' . esc_attr( $area_wrapper_id ) . '" class="' . esc_attr( $wrap_class ) . '">';
	}

	/**
	 * Close content area wrapper.
	 */
	public function after_comment_area() {
		$comment_area = get_theme_mod( 'neve_comment_section_style', 'always' );
		if ( $comment_area !== 'toggle' || neve_is_amp() ) {
			return;
		}
		echo '</div>';
	}

	/**
	 * Add extra protocols to list of allowed protocols.
	 *
	 * @param array $protocols List of protocols from core.
	 *
	 * @return array Updated list including extra protocols added.
	 */
	public function custom_allowed_protocols( $protocols ) {
		$protocols[] = 'whatsapp';
		$protocols[] = 'sms';

		return $protocols;
	}

	/**
	 * Add pro features in Neve meta sidebar.
	 *
	 * @param array $localized_data Localized data.
	 *
	 * @return mixed
	 */
	public function add_pro_features( $localized_data ) {
		$localized_data['enable_pro']      = true;
		$localized_data['supported_types'] = apply_filters( 'neve_post_type_supported_list', [ 'post' ], 'block_editor' );

		return $localized_data;
	}

	/**
	 * Add Reading time control in Neve Meta Sidebar.
	 *
	 * @param array $controls Meta controls.
	 *
	 * @return array
	 */
	public function add_reading_time_meta_block_editor( $controls ) {
		$controls[] = [
			'id'   => 'neve_meta_reading_time',
			'type' => 'checkbox',
		];

		return $controls;
	}

	/**
	 * Add estimated reading time in meta fields.
	 *
	 * @param array $meta_fields Meta fields.
	 *
	 * @return mixed
	 */
	public function add_reading_time_meta( $meta_fields ) {
		$meta_fields['reading'] = __( 'Estimated reading time', 'neve' );

		return $meta_fields;
	}

	/**
	 * Output function for post read time.
	 *
	 * @param int | null $post_id Post id.
	 *
	 * @return string
	 */
	public function render_read_time_meta( $post_id = null ) {
		if ( empty( $post_id ) ) {
			$post_id = get_the_ID();
		}

		$word_count = get_post_meta( $post_id, 'nb_of_words', true );
		if ( empty( $word_count ) && $word_count !== 0 ) {
			return '';
		}
		$words_per_minute = apply_filters( 'neve_words_per_minute', 200 );
		$reading_time     = ceil( $word_count / $words_per_minute );
		if ( $reading_time < 1 ) {
			$value = __( 'Less than 1 min read', 'neve' );

			return $value;
		}

		/* translators: %s - reading time */

		return sprintf( __( '%s min read', 'neve' ), $reading_time );
	}

	/**
	 * Filter magic tags array.
	 *
	 * @param array $magic_tag_settings Magic tags options.
	 *
	 * @return array
	 */
	public function add_read_time_magic_tag( $magic_tag_settings ) {
		$magic_tag_settings[0]['controls']['meta_time_to_read'] = [
			'label' => __( 'Time to read meta', 'neve' ),
			'type'  => 'string',
		];

		return $magic_tag_settings;
	}

	/**
	 * Control the reading time from post meta.
	 *
	 * @param array $post_components Post components.
	 *
	 * @return array
	 */
	public function reading_time_meta_action( $post_components ) {
		global $post;
		if ( empty( $post ) ) {
			return $post_components;
		}

		$post_id       = apply_filters( 'neve_post_meta_filters_post_id', $post->ID );
		$option_status = get_post_meta( $post_id, 'neve_meta_reading_time', true );

		// if reading time meta is not overridden on post level.
		if ( empty( $option_status ) ) {
			return $post_components;
		}

		foreach ( $post_components as $component ) {
			// against string items in $post_components.
			if ( ! is_object( $component ) ) {
				return $post_components;
			}

			if ( $component->slug === 'reading' ) {
				$component->visibility = ( 'on' === $option_status ? 'yes' : 'no' );
			}
		}

		return $post_components;
	}

	/**
	 * Method that handles the infinite scroll on single post.
	 */
	public function add_post_infinite_scroll() {

		$is_infinite_scroll = get_theme_mod( 'neve_post_nav_infinite', false );
		if ( ! $is_infinite_scroll ) {
			return false;
		}

		$current_post_type = get_post_type();
		if ( ! in_array( $current_post_type, apply_filters( 'neve_post_type_supported_list', [ 'post' ], 'block_editor' ), true ) ) {
			return false;
		}
		if ( ! is_singular( $current_post_type ) ) {
			return false;
		}

		$previous = function_exists( 'wpcom_vip_get_adjacent_post' ) ?
			wpcom_vip_get_adjacent_post( false, array(), true ) :
			get_adjacent_post( false, '', true ); //phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.get_adjacent_post_get_adjacent_post
		$previous = apply_filters( 'allowed_previous_post', $previous );

		add_action(
			'neve_before_post_content',
			function () use ( $previous ) {
				$permalink     = get_the_permalink();
				$previous_link = is_a( $previous, 'WP_Post' ) ? get_permalink( $previous->ID ) : null;
				echo '<div class="nv-single-post-wrap nv-infinite-post" data-url="' . esc_url( $permalink ) . '" ';
				if ( $previous_link ) {
					echo 'data-prev-url="' . esc_url( $previous_link ) . '"';
				}
				echo '>';
			},
			1
		);

		add_action(
			'neve_after_post_content',
			function () {
				echo '</div>';
				echo '<div class="add-more-posts"></div>';
			},
			100
		);

		add_filter(
			'neve_post_navigation_class',
			function ( $classes ) {
				return $classes . ' nv-infinite';
			}
		);

		add_action(
			'wp_enqueue_scripts',
			function () use ( $previous ) {
				wp_register_script( 'neve-pro-addon-blog-pro', NEVE_PRO_INCLUDES_URL . 'modules/blog_pro/assets/js/build/script.js', array(), NEVE_PRO_VERSION, true );
				wp_localize_script(
					'neve-pro-addon-blog-pro',
					'neveInfinitePost',
					[
						'infiniteNext' => is_a( $previous, 'WP_Post' ) ? get_permalink( $previous->ID ) : null,
						'pid'          => get_the_ID(),
					]
				);
				wp_enqueue_script( 'neve-pro-addon-blog-pro' );
			}
		);
	}

	/**
	 * Render custom meta data.
	 *
	 * @param string $filter Default value.
	 * @param object $meta Meta data.
	 * @param string $tag Markup tag.
	 * @param int    $pid Post id.
	 *
	 * @return string|bool;
	 */
	public function render_custom_meta( $filter, $meta, $tag, $pid ) {
		if ( ! isset( $meta->meta_type ) ) {
			return false;
		}

		$meta_value   = isset( $meta->fallback ) ? $meta->fallback : '';
		$meta_field   = isset( $meta->field ) ? $meta->field : '';
		$meta_by_type = '';
		switch ( $meta->meta_type ) {
			case 'raw':
			case 'toolset':
			case 'metabox':
				$meta_by_type = get_post_meta( $pid, $meta_field );
				break;
			case 'custom_tax':
				$terms = wp_get_post_terms( $pid, $meta_field );
				if ( is_array( $terms ) && ! empty( $terms ) ) {
					$term_names = array();
					foreach ( $terms as $term ) {
						if ( property_exists( $term, 'name' ) ) {
							$term_names[] = $term->name;
						}
					}
					$meta_by_type = implode( ', ', $term_names );
				}
				break;
			case 'acf':
				$meta_by_type = $this->get_acf_value( $meta_field, $pid );
				break;
			case 'pods':
				if ( function_exists( 'pods_field_display' ) ) {
					$meta_by_type = pods_field_display( $meta_field );
				}
				break;
		}

		if ( is_array( $meta_by_type ) && isset( $meta_by_type[0] ) && is_string( $meta_by_type[0] ) ) {
			$meta_by_type = $meta_by_type[0];
		}

		if ( is_string( $meta_by_type ) && ! empty( $meta_by_type ) ) {
			$meta_value = $meta_by_type;
		}

		if ( ! isset( $meta_value ) || $meta_value === '' ) {
			return false;
		}

		$format       = ! empty( $meta->format ) ? $meta->format : '{meta}';
		$meta_content = str_replace( '{meta}', $meta_value, $format );
		$meta_value   = '<span class="custom">' . $meta_content . '</span>';

		return wp_kses_post( $meta_value );
	}

	/**
	 * Get ACF value based on the field type.
	 *
	 * Edit this function to add support for more field types.
	 *
	 * @param string $meta_field Field name.
	 * @param int    $pid Post id.
	 *
	 * @return string
	 */
	private function get_acf_value( $meta_field, $pid ) {
		if ( ! function_exists( 'get_field_object' ) ) {
			return '';
		}

		if ( ! function_exists( 'get_field' ) ) {
			return '';
		}

		$field_data = get_field_object( $meta_field, $pid );

		$type = $field_data['type'] ?? '';
		if ( empty( $type ) ) {
			return '';
		}

		$value = $field_data['value'] ?? '';
		if ( empty( $value ) ) {
			return '';
		}

		switch ( $type ) {
			case 'taxonomy':
				$field_type = $field_data['field_type'] ?? '';
				if ( is_array( $value ) && in_array( $field_type, [ 'checkbox', 'multi_select' ] ) ) {
					$data = [];
					foreach ( $value as $value_item ) {
						if ( is_int( $value_item ) ) {
							$data[] = get_cat_name( $value_item );
							continue;
						}
						if ( ! isset( $value_item->name ) ) {
							continue;
						}
						$data[] = $value_item->name;
					}
					return implode( ', ', $data );
				}
				if ( is_int( $value ) ) {
					return get_cat_name( $value );
				}
				return $value->name ?? '';
			default:
				return get_field( $meta_field, $pid );
		}
	}

	/**
	 * Decide if the blog image effects functions should run.
	 *
	 * @param bool $check_second_img Flag to tell if we need to check for adding a second image.
	 * @return bool
	 */
	public function should_add_blog_image_effects( $check_second_img = false ) {
		$supported_post_types = apply_filters( 'neve_post_type_supported_list', [ 'post' ], 'block_editor' );
		$post_type            = get_post_type();

		// The image effects should only apply on supported post types and on the search page.
		if ( ! in_array( $post_type, $supported_post_types, true ) && ! is_search() ) {
			return false;
		}

		if ( ! post_type_supports( $post_type, 'thumbnail' ) ) {
			return false;
		}

		$option_name   = $post_type === 'post' ? 'neve_blog_image_hover' : 'neve_' . $post_type . '_archive_image_hover';
		$default_value = $post_type === 'post' ? 'none' : get_theme_mod( 'neve_blog_image_hover', 'none' );
		$image_effect  = get_theme_mod( $option_name, $default_value );
		if ( $image_effect === 'none' ) {
			return false;
		}

		if ( $check_second_img && $image_effect !== 'swipe' && $image_effect !== 'next' ) {
			return false;
		}

		return true;
	}

	/**
	 * Load the script that adds the second featured image control.
	 */
	public function load_metabox_script() {
		if ( ! $this->should_add_blog_image_effects( true ) ) {
			return;
		}

		$relative_path = 'assets/apps/metabox/';
		$dependencies  = include NEVE_PRO_PATH . $relative_path . '/build/app.asset.php';

		wp_enqueue_script(
			'neve-pro-addon-featured-metabox',
			NEVE_PRO_URL . $relative_path . 'build/app.js',
			$dependencies['dependencies'],
			$dependencies['version'],
			true
		);
	}

	/**
	 * Register post meta for posts and for other custom post types.
	 */
	public function register_meta_second_thumbnail() {

		$blog_image_hover = get_theme_mod( 'neve_blog_image_hover', 'none' );
		if ( $blog_image_hover !== 'swipe' && $blog_image_hover !== 'next' ) {
			return false;
		}

		// Register second thumbnail for posts
		register_meta(
			'post',
			'neve_second_thumbnail',
			array(
				'type'              => 'integer',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'absint',
			)
		);

		// Do not register second thumbnail for other post types if post_type_enhancements module is deactivated
		if ( ! get_option( 'nv_pro_post_type_enhancements_status', true ) ) {
			return;
		}

		// Register second thumbnail meta for other available custom post types.
		$supported_post_types = apply_filters( 'neve_post_type_supported_list', [], 'block_editor' );
		foreach ( $supported_post_types as $post_type ) {
			if ( ! post_type_supports( $post_type, 'thumbnail' ) ) {
				continue;
			}

			register_meta(
				$post_type,
				'neve_second_thumbnail',
				array(
					'type'              => 'integer',
					'single'            => true,
					'show_in_rest'      => true,
					'sanitize_callback' => 'absint',
				)
			);
		}
	}

	/**
	 * Handle blog post image effect.
	 */
	public function blog_post_image_effect() {

		if ( ! $this->should_add_blog_image_effects() ) {
			return;
		}

		// Add effect class on the image wrapper
		add_filter(
			'neve_post_wrap_classes',
			function ( $classes ) {
				$blog_image_hover = get_theme_mod( 'neve_blog_image_hover', 'none' );
				if ( $blog_image_hover === 'none' ) {
					return $classes;
				}
				$classes[] = $blog_image_hover;
				return $classes;
			}
		);

		// Add class on posts wrapper
		add_filter(
			'neve_posts_wrapper_class',
			function ( $classes ) {
				$classes[] = 'nv-has-effect';
				return $classes;
			}
		);

		// Render second thumbnail image
		add_filter( 'post_thumbnail_html', array( $this, 'render_second_thumbnail' ), 10, 5 );


		// Add inline style for image effects
		add_action( 'wp_enqueue_scripts', array( $this, 'load_blog_post_image_effect_style' ) );
	}

	/**
	 * Render function for the second thumbnail.
	 *
	 * @param string $html Post thumbnail HTML.
	 * @param int    $post_id Post id.
	 * @param int    $post_thumbnail_id Primary thumbnail id.
	 * @param string $size Image size.
	 * @param array  $attr Image attributes.
	 */
	public function render_second_thumbnail( $html, $post_id, $post_thumbnail_id, $size, $attr ) {
		$blog_image_hover = get_theme_mod( 'neve_blog_image_hover', 'none' );
		if ( $blog_image_hover !== 'swipe' && $blog_image_hover !== 'next' ) {
			return $html;
		}

		if ( is_singular() ) {
			return $html;
		}

		$scd_ft_img_id = get_post_meta( $post_id, 'neve_second_thumbnail', true );
		if ( empty( $scd_ft_img_id ) ) {
			return $html;
		}

		$attr['class'] = $attr['class'] . ' wp-post-image';
		$second_image  = wp_get_attachment_image( $scd_ft_img_id, $size, false, $attr );
		if ( empty( $second_image ) ) {
			return $html;
		}

		return $html . $second_image;
	}

	/**
	 * Load blog post image effect style.
	 */
	public function load_blog_post_image_effect_style() {
		if ( class_exists( 'WooCommerce', false ) && is_shop() ) {
			return;
		}

		if ( ! is_home() && ! is_archive() && ! is_search() ) {
			return;
		}
		$blog_image_hover = get_theme_mod( 'neve_blog_image_hover', 'none' );
		wp_add_inline_style( 'neve-style', $this->get_thumbnail_effect_style( $blog_image_hover ) );
	}

}
