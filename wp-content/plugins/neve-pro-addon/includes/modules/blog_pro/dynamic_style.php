<?php
/**
 * File that handle dynamic css for Blog pro integration.
 *
 * @package Neve_Pro\Modules\Blog_Pro
 */

namespace Neve_Pro\Modules\Blog_Pro;

use Neve\Core\Settings\Config;
use Neve\Core\Settings\Mods;
use Neve\Core\Styles\Css_Prop;
use Neve_Pro\Core\Generic_Style;
use Neve_Pro\Core\Loader;
use Neve_Pro\Modules\Blog_Pro\Customizer\Defaults\Single_Post;

/**
 * Class Dynamic_Style
 *
 * @package Neve_Pro\Modules\Blog_Pro
 */
class Dynamic_Style extends Generic_Style {
	use Single_Post;

	const AVATAR_SIZE           = 'neve_author_avatar_size';
	const BLOG_LAYOUT           = 'neve_blog_archive_layout';
	const COVER_OVERLAY         = 'neve_blog_covers_overlay_color';
	const SHOW_CONTENT_ON_HOVER = 'neve_blog_show_on_hover';
	const CONTENT_PADDING       = 'neve_blog_content_padding';
	const COVER_MIN_HEIGHT      = 'neve_blog_covers_min_height';
	const CONTENT_ALIGNMENT     = 'neve_blog_content_alignment';
	const VERTICAL_ALIGNMENT    = 'neve_blog_content_vertical_alignment';
	const BORDER_RADIUS         = 'neve_blog_items_border_radius';
	const GRID_SPACING          = 'neve_blog_grid_spacing';
	const LIST_SPACING          = 'neve_blog_list_spacing';
	const IMAGE_POSITION        = 'neve_blog_list_image_position';
	const ALTERNATIVE_LAYOUT    = 'neve_blog_list_alternative_layout';
	const IMAGE_WIDTH           = 'neve_blog_list_image_width';
	const GRID_CARD_BG          = 'neve_blog_grid_card_bg_color';
	const GRID_TEXT_COLOR       = 'neve_blog_grid_text_color';
	const CARD_STYLE            = 'neve_enable_card_style';
	const SEPARATOR             = 'neve_blog_separator';
	const SEPARATOR_WIDTH       = 'neve_blog_separator_width';
	const SEPARATOR_COLOR       = 'neve_blog_separator_color';
	const CARD_SHADOW           = 'neve_blog_card_shadow';
	const SHARING_COLOR         = 'neve_sharing_custom_color';
	const SHARING_ICON_COLOR    = 'neve_sharing_icon_color';
	const SHARING_ICON_SIZE     = 'neve_sharing_icon_size';
	const SHARING_ICON_PADDING  = 'neve_sharing_icon_padding';
	const SHARING_ALIGNMENT     = 'neve_sharing_icons_alignment';
	const SHARING_SPACING       = 'neve_sharing_icon_spacing';

	const ENABLE_FEATURED_POST         = 'neve_enable_featured_post';
	const FEATURED_POST_IMAGE_POSITION = 'neve_featured_post_image_position';
	const FEATURED_POST_IMAGE_ALIGN    = 'neve_featured_post_image_align';
	const FEATURED_POST_CONTENT_ALIGN  = 'neve_featured_post_content_align';
	const FEATURED_POST_BACKGROUND     = 'neve_featured_post_background';
	const FEATURED_POST_PADDING        = 'neve_featured_post_padding';
	const FEATURED_POST_MIN_HEIGHT     = 'neve_featured_post_min_height';

	const AUTHOR_BOX_AVATAR_SIZE          = 'neve_author_box_avatar_size';
	const AUTHOR_BOX_AVATAR_BORDER_RADIUS = 'neve_author_box_avatar_border_radius';
	const AUTHOR_BOX_PADDING              = 'neve_author_box_boxed_padding';
	const AUTHOR_BOX_BACKGROUND_COLOR     = 'neve_author_box_boxed_background_color';
	const AUTHOR_BOX_TEXT_COLOR           = 'neve_author_box_boxed_text_color';
	const AUTHOR_BOX_CONTENT_ALIGNMENT    = 'neve_author_box_content_alignment';

	const RELATED_POSTS_BOXED_STATUS      = 'neve_related_posts_boxed_layout';
	const RELATED_POSTS_COLUMNS           = 'neve_related_posts_col_nb';
	const RELATED_POSTS_PADDING           = 'neve_related_posts_boxed_padding';
	const RELATED_POSTS_BACKGROUND_COLOR  = 'neve_related_posts_boxed_background_color';
	const RELATED_POSTS_TEXT_COLOR        = 'neve_related_posts_boxed_text_color';
	const RELATED_POSTS_CONTENT_ALIGNMENT = 'neve_related_posts_content_alignment';

	const RELATED_POSTS_SECTION_TITLE_TYPOGRAPHY = 'neve_related_posts_typography_section_title';
	const RELATED_POSTS_POST_TITLE_TYPOGRAPHY    = 'neve_related_posts_typography_post_title';
	const RELATED_POSTS_POST_META_TYPOGRAPHY     = 'neve_related_posts_typography_post_meta';
	const RELATED_POSTS_POST_EXCERPT_TYPOGRAPHY  = 'neve_related_posts_typography_post_excerpt';


	/**
	 * Register extra hooks.
	 */
	public function register_hooks() {
		parent::register_hooks();
		add_filter(
			'post_class',
			[ $this, 'add_hover_class' ]
		);
	}

	/**
	 * Add dynamic style subscribers.
	 *
	 * @param array $subscribers Css subscribers.
	 *
	 * @return array|mixed
	 */
	public function add_subscribers( $subscribers = [] ) {

		// Sharing icons
		if ( $this->section_is_enabled( 'sharing-icons' ) ) {
			$custom_sharing_icons_color = get_theme_mod( 'neve_sharing_enable_custom_color', false );
			if ( $custom_sharing_icons_color ) {
				$subscribers[] = [
					'selectors' => '.nv-post-share a',
					'rules'     => [
						'--hex'      => [
							'key'     => self::SHARING_ICON_COLOR,
							'default' => '#fff',
						],
						'--bgsocial' => [
							'key'     => self::SHARING_COLOR,
							'default' => 'var(--nv-primary-accent)',
						],
					],
				];
			}
			$subscribers[] = [
				'selectors' => '.nv-post-share a',
				'rules'     => [
					'--iconsizesocial'    => [
						'key'           => self::SHARING_ICON_SIZE,
						'default'       => '{"desktop":20,"tablet":20,"mobile":20}',
						'suffix'        => 'px',
						'is_responsive' => true,
					],
					'--iconpaddingsocial' => [
						'key'           => self::SHARING_ICON_PADDING,
						'default'       => '{"desktop":15,"tablet":15,"mobile":15}',
						'suffix'        => 'px',
						'is_responsive' => true,
					],
				],
			];
			$subscribers[] = [
				'selectors' => '.nv-post-share',
				'rules'     => [
					'--iconalignsocial' => [
						'key'           => self::SHARING_ALIGNMENT,
						'default'       => '{ "mobile": "left", "tablet": "left", "desktop": "left" }',
						'is_responsive' => true,
					],
					'--icongapsocial'   => [
						'key'           => self::SHARING_SPACING,
						'default'       => '{"desktop":10,"tablet":10,"mobile":10}',
						'suffix'        => 'px',
						'is_responsive' => true,
					],
				],
			];
		}

		// Author box
		if ( $this->section_is_enabled( 'author-biography' ) ) {
			$author_rules = [];
			$show_avatar  = get_theme_mod( 'neve_author_box_enable_avatar', true );
			if ( $show_avatar ) {
				$author_rules['--avatarsize']   = [
					'key'           => self::AUTHOR_BOX_AVATAR_SIZE,
					'default'       => '{ "mobile": 96, "tablet": 96, "desktop": 96 }',
					'suffix'        => 'px',
					'is_responsive' => true,
				];
				$author_rules['--borderradius'] = [
					'key'     => self::AUTHOR_BOX_AVATAR_BORDER_RADIUS,
					'suffix'  => '%',
					'default' => 0,
				];
			}

			$author_rules['--authorcontentalign'] = [
				'key'           => self::AUTHOR_BOX_CONTENT_ALIGNMENT,
				'default'       => '{ "mobile": "left", "tablet": "left", "desktop": "left" }',
				'is_responsive' => true,
			];

			$is_boxed = get_theme_mod( 'neve_author_box_boxed_layout', false );

			if ( $is_boxed ) {
				$author_rules['--padding'] = [
					'key'              => self::AUTHOR_BOX_PADDING,
					'is_responsive'    => true,
					'directional-prop' => 'padding',
					'suffix'           => 'responsive_unit',
					'default'          => $this->responsive_padding_default(),
				];

				$author_rules['--bgcolor'] = [
					'key'     => self::AUTHOR_BOX_BACKGROUND_COLOR,
					'default' => 'var(--nv-light-bg)',
				];

				$author_rules['--color'] = [
					'key'     => self::AUTHOR_BOX_TEXT_COLOR,
					'default' => 'var(--nv-text-color)',
				];
			}

			$subscribers[] = [
				'selectors' => '.nv-author-biography',
				'rules'     => $author_rules,
			];
		}

		if ( $this->section_is_enabled( 'related-posts' ) ) {
			// Related Posts
			$related_rules = [
				'--relatedcolumns' => [
					'key'           => self::RELATED_POSTS_COLUMNS,
					'is_responsive' => true,
					'default'       => $this->responsive_related_posts_nb( 'neve_related_posts_columns' ),
				],
			];

			$is_related_boxed = Mods::get( self::RELATED_POSTS_BOXED_STATUS, false );
			if ( $is_related_boxed ) {
				$related_rules = array_merge(
					$related_rules,
					[
						'--bgcolor' => [
							'key'     => self::RELATED_POSTS_BACKGROUND_COLOR,
							'default' => 'var(--nv-light-bg)',
						],
						'--color'   => [
							'key'     => self::RELATED_POSTS_TEXT_COLOR,
							'default' => 'var(--nv-text-color)',
						],
						'--padding' => [
							'key'              => self::RELATED_POSTS_PADDING,
							'is_responsive'    => true,
							'directional-prop' => 'padding',
							'suffix'           => 'responsive_unit',
							'default'          => $this->responsive_padding_default(),
						],
					]
				);
			}

			$related_rules['--relatedContentAlign'] = [
				'key'           => self::RELATED_POSTS_CONTENT_ALIGNMENT,
				'default'       => '{ "mobile": "left", "tablet": "left", "desktop": "left" }',
				'is_responsive' => true,
			];

			$subscribers[] = [
				'selectors' => '.nv-related-posts',
				'rules'     => $related_rules,
			];



			$related_typography = [
				self::RELATED_POSTS_SECTION_TITLE_TYPOGRAPHY => [
					'selector' => '.nv-related-posts .section-title > h2',
					'font'     => Config::MODS_FONT_HEADINGS,
				],
				self::RELATED_POSTS_POST_TITLE_TYPOGRAPHY => [
					'selector' => '.nv-related-posts .title',
					'font'     => Config::MODS_FONT_HEADINGS,
				],
				self::RELATED_POSTS_POST_META_TYPOGRAPHY  => [
					'selector' => '.nv-related-posts .nv-meta-list',
					'font'     => Config::MODS_FONT_GENERAL,
				],
				self::RELATED_POSTS_POST_EXCERPT_TYPOGRAPHY => [
					'selector' => '.nv-related-posts .excerpt-wrap',
					'font'     => Config::MODS_FONT_GENERAL,
				],
			];

			foreach ( $related_typography as $mod => $args ) {
				$prefix = $mod === self::RELATED_POSTS_SECTION_TITLE_TYPOGRAPHY ? '--h2' : '--';

				$subscribers[ $args['selector'] ] = [
					$prefix . 'fontsize'      => [
						'key'           => $mod . '.fontSize',
						'is_responsive' => true,
						'suffix'        => 'px',
					],
					$prefix . 'lineheight'    => [
						'key'           => $mod . '.lineHeight',
						'is_responsive' => true,
						'suffix'        => '',
					],
					$prefix . 'letterspacing' => [
						'key'           => $mod . '.letterSpacing',
						'is_responsive' => true,
						'suffix'        => 'px',
					],
					$prefix . 'fontweight'    => [
						'key'  => $mod . '.fontWeight',
						'font' => 'mods_' . $args['font'],
					],
					$prefix . 'texttransform' => $mod . '.textTransform',
				];
			}
		}

		// Blog archive
		$layout        = Mods::get( self::BLOG_LAYOUT, 'grid' );
		$image_pos     = Mods::get( self::IMAGE_POSITION, 'left' );
		$alt_layout    = Mods::get( self::ALTERNATIVE_LAYOUT, false );
		$has_separator = Mods::get( self::SEPARATOR, false ) === true;

		$rules                = [];
		$rules['--padding']   = [
			'key'              => self::CONTENT_PADDING,
			'is_responsive'    => true,
			'suffix'           => 'responsive_unit',
			'directional-prop' => 'padding',
		];
		$rules['--alignment'] = self::CONTENT_ALIGNMENT;

		if ( $layout === 'default' ) {
			$rules['--spacing'] = [
				'key'           => self::LIST_SPACING,
				'suffix'        => 'responsive_suffix',
				'is_responsive' => true,
			];

			$rules['--postcoltemplate'] = [
				'key'         => self::IMAGE_WIDTH,
				'filter'      => function ( $css_prop, $value, $meta, $device ) use ( $image_pos ) {
					if ( $image_pos === 'no' ) {
						add_filter( 'neve_blog_post_thumbnail_markup', '__return_empty_string', 0 );

						return sprintf( '%s:1fr;', $css_prop );
					}

					$content = 100 - absint( $value );
					if ( $image_pos === 'left' ) {
						return sprintf( '%s:%sfr %sfr;', $css_prop, $value, $content );
					}

					return sprintf( '%s:%sfr %sfr;', $css_prop, $content, $value );
				},
				'default'     => 35,
				'device_only' => 'desktop',
			];

			if ( $image_pos === 'right' ) {
				$rules['--thumbgridcolumn'] = [
					'key'         => self::IMAGE_POSITION,
					'override'    => 2,
					'default'     => 35,
					'device_only' => 'desktop',
				];
			}

			// Alt layout and image.
			if ( $alt_layout && $image_pos !== 'no' ) {
				$subscribers[] = [
					'selectors' => '.layout-alternative:nth-child(even)',
					'rules'     => [
						'--postcoltemplate' => [
							'key'         => self::IMAGE_WIDTH,
							'filter'      => function ( $css_prop, $value, $meta, $device ) use ( $image_pos ) {
								$content = 100 - absint( $value );
								if ( $image_pos === 'right' ) {
									return sprintf( '%s:%sfr %sfr;', $css_prop, $value, $content );
								}

								return sprintf( '%s:%sfr %sfr;', $css_prop, $content, $value );
							},
							'default'     => 35,
							'device_only' => 'desktop',
						],
					],
				];
				if ( $image_pos === 'right' ) {
					$subscribers[] = [
						'selectors'   => '.posts-wrapper > article.has-post-thumbnail.layout-alternative:nth-child(even)',
						'rules'       => [
							'--thumbgridcolumn' => [
								'key'      => self::IMAGE_WIDTH,
								'default'  => 35,
								'override' => 1,
							],
						],
						'device_only' => 'desktop',
					];
				}
			}
		}

		if ( $layout !== 'default' ) {
			$rules['--gridspacing'] = [
				'key'           => self::GRID_SPACING,
				'is_responsive' => true,
				'suffix'        => 'responsive_suffix',
			];

			$rules['--borderradius'] = [
				'key'    => self::BORDER_RADIUS,
				'suffix' => 'px',
			];
		}

		if ( $layout === 'covers' ) {
			$rules['--overlay'] = self::COVER_OVERLAY;

			$rules['--coverheight'] = [
				'key'           => self::COVER_MIN_HEIGHT,
				'is_responsive' => true,
				'suffix'        => 'px',
			];

			$vertical_alignment = Mods::get( self::BLOG_LAYOUT );
			if ( ! empty( $vertical_alignment ) ) {
				$rules['--justify'] = self::VERTICAL_ALIGNMENT;
			}
		}

		if ( $has_separator && $layout !== 'covers' ) {
			$rules['--bordercolor'] = [
				'key'     => self::SEPARATOR_COLOR,
				'default' => 'var(--nv-light-bg)',
			];
			$rules['--borderwidth'] = [
				'key'           => self::SEPARATOR_WIDTH,
				'default'       => '{ "mobile": 1, "tablet": 1, "desktop": 1 }',
				'suffix'        => 'px',
				'is_responsive' => true,
			];
		}

		if ( $layout === 'grid' ) {
			// Make sure image goes to edges
			$subscribers[] = [
				'selectors'     => '.layout-grid .nv-post-thumbnail-wrap',
				'rules'         => [
					'margin' => [
						'key'           => self::CONTENT_PADDING,
						'is_responsive' => true,
						'filter'        => function ( $css_prop, $value, $meta, $device ) {
							$output = '';
							$unit   = Css_Prop::get_unit_responsive( $meta, $device );
							if ( isset( $value['right'] ) && ! empty( $value['right'] ) ) {
								$output .= sprintf( 'margin-right:-%1$s%2$s;', $value['right'], $unit );
							}
							if ( isset( $value['left'] ) && ! empty( $value['left'] ) ) {
								$output .= sprintf( 'margin-left:-%1$s%2$s;', $value['left'], $unit );
							}

							return $output;
						},
					],
				],
				'is_responsive' => true,
			];
		}

		$has_card_style = Mods::get( self::CARD_STYLE, false ) === true;
		if ( $has_card_style ) {
			$rules['--cardboxshadow'] = [
				'key'    => self::CARD_SHADOW,
				'filter' => function ( $css_prop, $value, $meta, $device ) {
					$blur    = $value * 4;
					$opacity = 0.1 + $value / 10;

					return sprintf( '%s:0 0 %spx 0 rgba(0,0,0,%s);', $css_prop, $blur, $opacity );
				},
			];
		}

		if ( $layout !== 'covers' && $has_card_style ) {
			$rules['--cardbgcolor'] = [
				'key'     => self::GRID_CARD_BG,
				'default' => '#333333',
			];
			$rules['--cardcolor']   = [
				'key'     => self::GRID_TEXT_COLOR,
				'default' => '#ffffff',
			];
		}

		if ( $layout === 'default' ) {

			// Make sure image goes to edges
			$subscribers[] = [
				'selectors'     => '.layout-default .nv-post-thumbnail-wrap',
				'rules'         => [
					'margin' => [
						'key'           => self::CONTENT_PADDING,
						'is_responsive' => true,
						'filter'        => function ( $css_prop, $value, $meta, $device ) {
							$output = '';
							$unit   = Css_Prop::get_unit_responsive( $meta, $device );

							if ( isset( $value['right'] ) && ! empty( $value['right'] ) ) {
								$output .= sprintf( 'margin-right:-%1$s%2$s;', $value['right'], $unit );
							}

							return $output;
						},
					],
				],
				'is_responsive' => true,
			];

		}

		$subscribers[] = [
			'selectors' => '.nv-index-posts',
			'rules'     => $rules,
		];

		if ( Loader::has_compatibility( 'featured_post' ) ) {
			$has_featured_post = Mods::get( self::ENABLE_FEATURED_POST, false );

			if ( $has_featured_post ) {
				$featured_post_rules['--overlay']        = self::COVER_OVERLAY;
				$featured_post_rules['--ftposttemplate'] = [
					'key'     => self::FEATURED_POST_IMAGE_POSITION,
					'filter'  => function ( $css_prop, $value ) {
						if ( $value === 'right' ) {
							return $css_prop . ':1.25fr 1fr;';
						}

						if ( $value === 'left' ) {
							return $css_prop . ':1fr 1.25fr;';
						}

						return $css_prop . ':1fr;';
					},
					'default' => 'top',
				];

				$featured_post_rules['--ftpostimgorder'] = [
					'key'     => self::FEATURED_POST_IMAGE_POSITION,
					'filter'  => function ( $css_prop, $value ) {
						if ( $value === 'right' ) {
							return $css_prop . ':1;';
						}

						if ( $value === 'left' ) {
							return $css_prop . ':0;';
						}
					},
					'default' => 'top',
				];

				$featured_post_rules['--ftpostcontentorder'] = [
					'key'     => self::FEATURED_POST_IMAGE_POSITION,
					'filter'  => function ( $css_prop, $value ) {
						if ( $value === 'right' ) {
							return $css_prop . ':0;';
						}

						if ( $value === 'left' ) {
							return $css_prop . ':1;';
						}
					},
					'default' => 'top',
				];

				$featured_post_rules['--ftpostimgalign'] = [
					'key'     => self::FEATURED_POST_IMAGE_ALIGN,
					'default' => 'center',
				];

				$featured_post_rules['--ftpostcontentalign'] = [
					'key'     => self::FEATURED_POST_CONTENT_ALIGN,
					'default' => 'center',
				];

				$featured_post_rules['--fpbackground'] = [
					'key'     => self::FEATURED_POST_BACKGROUND,
					'default' => 'var(--nv-light-bg)',
				];

				$featured_post_rules['--fppadding'] = [
					'key'              => self::FEATURED_POST_PADDING,
					'is_responsive'    => true,
					'directional-prop' => 'padding',
					'suffix'           => 'responsive_unit',
					'default'          => $this->responsive_padding_default(),
				];

				$featured_post_rules['--fpminheight'] = [
					'key'              => self::FEATURED_POST_MIN_HEIGHT,
					'is_responsive'    => true,
					'directional-prop' => 'min-height',
					'suffix'           => 'responsive_suffix',
					'default'          => '{ "mobile": 300, "tablet": 300, "desktop": 300 }',
				];

				$featured_post_rules['--borderradius'] = [
					'key'    => self::BORDER_RADIUS,
					'suffix' => 'px',
				];

				if ( $has_card_style ) {
					$featured_post_rules['--cardboxshadow'] = [
						'key'    => self::CARD_SHADOW,
						'filter' => function ( $css_prop, $value, $meta, $device ) {
							$blur    = $value * 4;
							$opacity = 0.1 + $value / 10;

							return sprintf( '%s:0 0 %spx 0 rgba(0,0,0,%s);', $css_prop, $blur, $opacity );
						},
					];
				}

				$subscribers[] = [
					'selectors' => '.nv-ft-post',
					'rules'     => $featured_post_rules,
				];
			}
		}


		return $subscribers;
	}

	/**
	 * Add class to posts to only show on hover.
	 *
	 * @param string[] $classes post classes.
	 *
	 * @return array
	 */
	public function add_hover_class( $classes ) {
		if ( Mods::get( self::SHOW_CONTENT_ON_HOVER, false ) === false ) {
			return $classes;
		}
		$classes[] = 'show-hover';

		return $classes;
	}

	/**
	 * Is single post section enabled.
	 *
	 * @param string $element Post page section.
	 *
	 * @return bool
	 */
	private function section_is_enabled( $element ) {
		$default_order = apply_filters(
			'neve_single_post_elements_default_order',
			array(
				'title-meta',
				'thumbnail',
				'content',
				'tags',
				'comments',
			)
		);

		$content_order = get_theme_mod( 'neve_layout_single_post_elements_order', wp_json_encode( $default_order ) );
		$content_order = json_decode( $content_order, true );
		if ( ! in_array( $element, $content_order, true ) ) {
			return false;
		}

		return true;
	}
}
