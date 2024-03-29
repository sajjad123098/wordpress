<?php
/**
 * Single post customizer controls.
 *
 * @package Neve_Pro\Modules\Blog_Pro\Customizer
 */

namespace Neve_Pro\Modules\Blog_Pro\Customizer;

use Neve\Core\Settings\Mods;
use Neve\Customizer\Base_Customizer;
use Neve\Customizer\Types\Control;
use Neve_Pro\Core\Loader;
use Neve_Pro\Modules\Blog_Pro\Dynamic_Style;
use Neve_Pro\Traits\Utils;
use Neve_Pro\Traits\Sanitize_Functions;


/**
 * Class Single_Post
 *
 * @package Neve_Pro\Modules\Blog_Pro\Customizer
 */
class Single_Post extends Base_Customizer {
	use Sanitize_Functions;
	use Utils;

	/**
	 * Add customizer controls.
	 */
	public function add_controls() {
		$this->headings();
		$this->comments();
		$this->sharing();
		$this->related_posts();
		$this->post_navigation();
		$this->author_box();
	}

	/**
	 * Add headings controls.
	 */
	private function headings() {
		$related_no_controls = method_exists( $this, 'add_boxed_layout_controls' ) ? 15 : 7;
		$sharing_no_controls = 13;

		$headings = [
			'sharing'       => [
				'label'            => esc_html__( 'Sharing icons', 'neve' ),
				'priority'         => 205,
				'expanded'         => false,
				'controls_to_wrap' => $sharing_no_controls,
				'active_callback'  => function () {
					return $this->element_is_enabled( 'sharing-icons' );
				},
			],
			'related_posts' => [
				'label'            => esc_html__( 'Related Posts', 'neve' ),
				'priority'         => 285,
				'expanded'         => false,
				'controls_to_wrap' => $related_no_controls,
				'active_callback'  => function () {
					return $this->element_is_enabled( 'related-posts' );
				},
			],
			'post_nav'      => [
				'label'            => esc_html__( 'Post Navigation', 'neve' ),
				'priority'         => 350,
				'expanded'         => false,
				'controls_to_wrap' => 1,
				'active_callback'  => function () {
					return $this->element_is_enabled( 'post-navigation' );
				},
			],
		];

		$headings['author_box'] = [
			'label'            => esc_html__( 'Author Box', 'neve' ),
			'priority'         => 230,
			'expanded'         => false,
			'controls_to_wrap' => method_exists( $this, 'add_boxed_layout_controls' ) ? 10 : 6,
			'active_callback'  => function () {
				return $this->element_is_enabled( 'author-biography' );
			},
		];

		foreach ( $headings as $heading_id => $heading_data ) {
			$this->add_control(
				new Control(
					'neve_post_' . $heading_id . '_heading',
					[
						'sanitize_callback' => 'sanitize_text_field',
					],
					[
						'label'            => $heading_data['label'],
						'section'          => 'neve_single_post_layout',
						'priority'         => $heading_data['priority'],
						'class'            => $heading_id . '-accordion',
						'expanded'         => $heading_data['expanded'],
						'accordion'        => true,
						'controls_to_wrap' => $heading_data['controls_to_wrap'],
						'active_callback'  => $heading_data['active_callback'],
					],
					'Neve\Customizer\Controls\Heading'
				)
			);
		}
	}

	/**
	 * Add comments customizer controls.
	 */
	private function comments() {
		/**
		 * Heading for Related posts options
		 */
		$this->add_control(
			new Control(
				'neve_comments_heading',
				[
					'sanitize_callback' => 'sanitize_text_field',
				],
				[
					'label'            => esc_html__( 'Comments', 'neve' ),
					'section'          => 'neve_single_post_layout',
					'priority'         => 140,
					'class'            => 'comments-accordion',
					'accordion'        => true,
					'expanded'         => false,
					'controls_to_wrap' => 1,
					'active_callback'  => function () {
						return $this->element_is_enabled( 'comments' );
					},
				],
				'Neve\Customizer\Controls\Heading'
			)
		);

		$this->add_control(
			new Control(
				'neve_comment_section_style',
				[
					'default'           => 'always',
					'sanitize_callback' => 'wp_filter_nohtml_kses',
				],
				[
					'label'           => esc_html__( 'Comment Section Style', 'neve' ),
					'section'         => 'neve_single_post_layout',
					'priority'        => 145,
					'type'            => 'select',
					'choices'         => [
						'always' => esc_html__( 'Always Show', 'neve' ),
						'toggle' => esc_html__( 'Show/Hide mechanism', 'neve' ),
					],
					'active_callback' => function () {
						return $this->element_is_enabled( 'comments' );
					},
				]
			)
		);
	}

	/**
	 * Add single post sharing controls.
	 */
	public function sharing() {
		// Content
		$this->add_control(
			new Control(
				'neve_sharing_icons',
				[
					'sanitize_callback' => [ $this, 'sanitize_sharing_icons_repeater' ],
					'default'           => wp_json_encode( $this->social_icons_default() ),
				],
				[
					'label'           => esc_html__( 'Choose your social icons', 'neve' ),
					'section'         => 'neve_single_post_layout',
					'fields'          => [
						'title'            => [
							'type'  => 'text',
							'label' => esc_html__( 'Title', 'neve' ),
						],
						'social_network'   => [
							'type'    => 'select',
							'label'   => __( 'Social Network', 'neve' ),
							'choices' => [
								'facebook'  => 'Facebook',
								'twitter'   => 'X',
								'email'     => 'Email',
								'pinterest' => 'Pinterest',
								'linkedin'  => 'LinkedIn',
								'tumblr'    => 'Tumblr',
								'reddit'    => 'Reddit',
								'whatsapp'  => 'WhatsApp',
								'sms'       => 'SMS',
								'vk'        => 'VKontakte',
							],
						],
						'icon_color'       => array(
							'type'  => 'color',
							'label' => esc_html__( 'Icon Color', 'neve' ),
						),
						'background_color' => array(
							'type'     => 'color',
							'label'    => esc_html__( 'Background Color', 'neve' ),
							'gradient' => true,
						),
						'display_desktop'  => [
							'type'  => 'checkbox',
							'label' => esc_html__( 'Show on Desktop', 'neve' ),
						],
						'display_mobile'   => [
							'type'  => 'checkbox',
							'label' => esc_html__( 'Show on Mobile', 'neve' ),
						],
					],
					'priority'        => 225,
					'active_callback' => function () {
						return $this->element_is_enabled( 'sharing-icons' );
					},
				],
				Loader::has_compatibility( 'repeater_control' ) ? '\Neve\Customizer\Controls\React\Repeater' : 'Neve_Pro\Customizer\Controls\Repeater'
			)
		);

		$this->add_control(
			new Control(
				'neve_sharing_icon_style',
				[
					'default'           => 'round',
					'sanitize_callback' => 'wp_filter_nohtml_kses',
				],
				[
					'label'           => esc_html__( 'Icon style', 'neve' ),
					'section'         => 'neve_single_post_layout',
					'priority'        => 210,
					'type'            => 'select',
					'choices'         => [
						'plain' => esc_html__( 'Plain', 'neve' ),
						'round' => esc_html__( 'Round', 'neve' ),
					],
					'active_callback' => function () {
						return $this->element_is_enabled( 'sharing-icons' );
					},
				]
			)
		);

		$this->add_control(
			new Control(
				'neve_sharing_enable_custom_color',
				[
					'sanitize_callback' => 'neve_sanitize_checkbox',
					'default'           => false,
				],
				[
					'label'           => esc_html__( 'Use custom icon color', 'neve' ),
					'section'         => 'neve_single_post_layout',
					'type'            => 'neve_toggle_control',
					'priority'        => 215,
					'active_callback' => function () {
						return $this->element_is_enabled( 'sharing-icons' );
					},
				]
			)
		);

		$default_old_sharing_custom_color = Mods::get( 'neve_sharing_custom_color', 'var(--nv-primary-accent)' );
		// Icon Color
		$this->add_control(
			new Control(
				'neve_sharing_icon_color',
				[
					'sanitize_callback' => 'neve_sanitize_colors',
					'transport'         => $this->selective_refresh,
					'default'           => Mods::get( 'neve_sharing_icon_style', 'round' ) === 'plain' ? $default_old_sharing_custom_color : '#fff',
				],
				[
					'label'                 => esc_html__( 'Icon color', 'neve' ),
					'section'               => 'neve_single_post_layout',
					'priority'              => 220,
					'input_attrs'           => [
						'allow_gradient' => false,
					],
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar'   => [
							'vars'     => '--hex',
							'selector' => '.nv-social-icon a',
						],
						'template' => '
							.nv-post-share.round-style .nv-social-icon svg,
							.nv-post-share.round-style .nv-social-icon a svg  {
								fill: {{value}};
							}
							.nv-post-share:not(.nv-is-boxed).custom-color .nv-social-icon svg,
							.nv-post-share:not(.nv-is-boxed).custom-color .nv-social-icon a svg  {
								fill: {{value}};
							}',
					],
					'active_callback'       => function () {
						return $this->element_is_enabled( 'sharing-icons' ) && Mods::get( 'neve_sharing_enable_custom_color', false );
					},
				],
				'Neve\Customizer\Controls\React\Color'
			)
		);

		// Background Color / Previously Custom Color
		$this->add_control(
			new Control(
				'neve_sharing_custom_color',
				[
					'sanitize_callback' => 'neve_sanitize_colors',
					'transport'         => $this->selective_refresh,
					'default'           => 'var(--nv-primary-accent)',
				],
				[
					'label'                 => esc_html__( 'Custom icon color', 'neve' ),
					'section'               => 'neve_single_post_layout',
					'priority'              => 220,
					'input_attrs'           => [
						'allow_gradient' => Loader::has_compatibility( 'gradient_picker' ),
					],
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar'   => [
							'vars'     => '--bgsocial',
							'selector' => '.nv-social-icon a',
						],
						'template' => '
							.nv-post-share:not(.nv-is-boxed).custom-color .nv-social-icon svg,
							.nv-post-share:not(.nv-is-boxed).custom-color .nv-social-icon a svg  {
								fill: {{value}};
							}
							.nv-post-share.nv-is-boxed.custom-color .social-share,
							.nv-post-share.nv-is-boxed.custom-color .nv-social-icon a  {
								background: {{value}};
							}',
					],
					'active_callback'       => function () {
						return $this->element_is_enabled( 'sharing-icons' ) &&
							Mods::get( 'neve_sharing_enable_custom_color', false ) &&
							Mods::get( 'neve_sharing_icon_style', 'round' ) === 'round';
					},
				],
				'Neve\Customizer\Controls\React\Color'
			)
		);

		// Icon Size
		$this->add_control(
			new Control(
				'neve_sharing_icon_size',
				[
					'sanitize_callback' => 'neve_sanitize_range_value',
					'transport'         => $this->selective_refresh,
					'default'           => '{"desktop":20,"tablet":20,"mobile":20}',
				],
				[
					'label'                 => esc_html__( 'Icon size', 'neve' ),
					'section'               => 'neve_single_post_layout',
					'type'                  => 'neve_responsive_range_control',
					'input_attrs'           => [
						'max'        => 100,
						'units'      => [ 'px' ],
						'defaultVal' => [
							'mobile'  => 20,
							'tablet'  => 20,
							'desktop' => 20,
							'suffix'  => [
								'mobile'  => 'px',
								'tablet'  => 'px',
								'desktop' => 'px',
							],
						],
					],
					'priority'              => 220,
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar' => [
							'responsive' => true,
							'vars'       => '--iconsizesocial',
							'selector'   => '.nv-social-icon a',
							'suffix'     => 'px',
						],
					],
					'active_callback'       => function () {
						return $this->element_is_enabled( 'sharing-icons' );
					},
				],
				'\Neve\Customizer\Controls\React\Responsive_Range'
			)
		);

		// Icon Padding
		$this->add_control(
			new Control(
				'neve_sharing_icon_padding',
				[
					'sanitize_callback' => 'neve_sanitize_range_value',
					'transport'         => $this->selective_refresh,
					'default'           => '{"desktop":15,"tablet":15,"mobile":15}',
				],
				[
					'label'                 => esc_html__( 'Padding', 'neve' ),
					'section'               => 'neve_single_post_layout',
					'type'                  => 'neve_responsive_range_control',
					'input_attrs'           => [
						'max'        => 100,
						'units'      => [ 'px' ],
						'defaultVal' => [
							'mobile'  => 15,
							'tablet'  => 15,
							'desktop' => 15,
							'suffix'  => [
								'mobile'  => 'px',
								'tablet'  => 'px',
								'desktop' => 'px',
							],
						],
					],
					'priority'              => 220,
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar' => [
							'responsive' => true,
							'vars'       => '--iconpaddingsocial',
							'selector'   => '.nv-social-icon a',
							'suffix'     => 'px',
						],
					],
					'active_callback'       => function () {
						return $this->element_is_enabled( 'sharing-icons' ) &&
							Mods::get( 'neve_sharing_icon_style', 'round' ) === 'round';
					},
				],
				'\Neve\Customizer\Controls\React\Responsive_Range'
			)
		);

		// Enable Text Label
		$this->add_control(
			new Control(
				'neve_sharing_enable_text_label',
				[
					'sanitize_callback' => 'neve_sanitize_checkbox',
					'default'           => false,
				],
				[
					'label'           => esc_html__( 'Add text label', 'neve' ),
					'section'         => 'neve_single_post_layout',
					'type'            => 'neve_toggle_control',
					'priority'        => 220,
					'active_callback' => function () {
						return $this->element_is_enabled( 'sharing-icons' );
					},
				]
			)
		);

		// Text label
		$this->add_control(
			new Control(
				'neve_sharing_label',
				[
					'sanitize_callback' => 'sanitize_text_field',
					'default'           => esc_html__( 'Share this post on social!', 'neve' ),
				],
				[
					'priority'        => 220,
					'section'         => 'neve_single_post_layout',
					'label'           => esc_html__( 'Label', 'neve' ),
					'type'            => 'text',
					'active_callback' => function () {
						return $this->element_is_enabled( 'sharing-icons' ) && Mods::get( 'neve_sharing_enable_text_label', false );
					},
				]
			)
		);

		// Label tag
		$this->add_control(
			new Control(
				'neve_sharing_label_tag',
				[
					'sanitize_callback' => [ $this, 'sanitize_sharing_icons_tag' ],
					'default'           => 'span',
				],
				[
					'label'           => esc_html__( 'Label HTML tag', 'neve' ),
					'section'         => 'neve_single_post_layout',
					'priority'        => 220,
					'type'            => 'select',
					'choices'         => [
						'span' => 'span',
						'p'    => 'p',
						'h1'   => 'H1',
						'h2'   => 'H2',
						'h3'   => 'H3',
						'h4'   => 'H4',
						'h5'   => 'H5',
						'h6'   => 'H6',
					],
					'active_callback' => function () {
						return $this->element_is_enabled( 'sharing-icons' ) && Mods::get( 'neve_sharing_enable_text_label', false );
					},
				]
			)
		);

		// Text position
		$this->add_control(
			new Control(
				'neve_sharing_label_position',
				[
					'default'           => 'before',
					'sanitize_callback' => 'wp_filter_nohtml_kses',
				],
				[
					'label'           => esc_html__( 'Label position', 'neve' ),
					'section'         => 'neve_single_post_layout',
					'priority'        => 220,
					'type'            => 'select',
					'choices'         => [
						'before' => esc_html__( 'Before icons', 'neve' ),
						'after'  => esc_html__( 'After icons', 'neve' ),
						'above'  => esc_html__( 'Above icons', 'neve' ),
						'below'  => esc_html__( 'Below icons', 'neve' ),
					],
					'active_callback' => function () {
						return $this->element_is_enabled( 'sharing-icons' ) && Mods::get( 'neve_sharing_enable_text_label', false );
					},
				]
			)
		);

		// Icons Alignment
		$this->add_control(
			new Control(
				'neve_sharing_icons_alignment',
				[
					'sanitize_callback' => 'neve_sanitize_alignment',
					'transport'         => $this->selective_refresh,
					'default'           => 'left',
				],
				[
					'label'                 => esc_html__( 'Icons alignment', 'neve' ),
					'section'               => 'neve_single_post_layout',
					'priority'              => 220,
					'choices'               => [
						'left'   => [
							'tooltip' => esc_html__( 'Left', 'neve' ),
							'icon'    => 'align-left',
						],
						'center' => [
							'tooltip' => esc_html__( 'Center', 'neve' ),
							'icon'    => 'align-center',
						],
						'right'  => [
							'tooltip' => esc_html__( 'Right', 'neve' ),
							'icon'    => 'align-right',
						],
					],
					'show_labels'           => true,
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar' => [
							'vars'       => '--iconalignsocial',
							'responsive' => true,
							'selector'   => '.nv-post-share',
						],
					],
					'active_callback'       => function () {
						return $this->element_is_enabled( 'sharing-icons' );
					},
				],
				'\Neve\Customizer\Controls\React\Responsive_Radio_Buttons'
			)
		);

		// Icons Spacing
		$this->add_control(
			new Control(
				'neve_sharing_icon_spacing',
				[
					'sanitize_callback' => 'neve_sanitize_range_value',
					'transport'         => $this->selective_refresh,
					'default'           => '{"desktop":10,"tablet":10,"mobile":10}',
				],
				[
					'label'                 => esc_html__( 'Space between', 'neve' ),
					'section'               => 'neve_single_post_layout',
					'type'                  => 'neve_responsive_range_control',
					'input_attrs'           => [
						'max'        => 100,
						'units'      => [ 'px' ],
						'defaultVal' => [
							'mobile'  => 10,
							'tablet'  => 10,
							'desktop' => 10,
							'suffix'  => [
								'mobile'  => 'px',
								'tablet'  => 'px',
								'desktop' => 'px',
							],
						],
					],
					'priority'              => 220,
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar' => [
							'responsive' => true,
							'vars'       => '--icongapsocial',
							'selector'   => '.nv-post-share',
							'suffix'     => 'px',
						],
					],
					'active_callback'       => function () {
						return $this->element_is_enabled( 'sharing-icons' );
					},
				],
				'\Neve\Customizer\Controls\React\Responsive_Range'
			)
		);
	}

	/**
	 * Add author box settings.
	 */
	public function author_box() {

		$this->add_control(
			new Control(
				'neve_author_box_enable_avatar',
				[
					'sanitize_callback' => 'neve_sanitize_checkbox',
					'default'           => true,
				],
				[
					'label'           => esc_html__( 'Show author image', 'neve' ),
					'section'         => 'neve_single_post_layout',
					'type'            => 'neve_toggle_control',
					'priority'        => 235,
					'active_callback' => function () {
						return $this->element_is_enabled( 'author-biography' );
					},
				]
			)
		);

		$this->add_control(
			new Control(
				'neve_author_box_avatar_size',
				[
					'sanitize_callback' => 'neve_sanitize_range_value',
					'transport'         => $this->selective_refresh,
					'default'           => '{ "mobile": 100, "tablet": 100, "desktop": 100 }',
				],
				[
					'label'                 => esc_html__( 'Image Size', 'neve' ),
					'section'               => 'neve_single_post_layout',
					'type'                  => 'neve_responsive_range_control',
					'input_attrs'           => [
						'min'        => 0,
						'max'        => 100,
						'defaultVal' => [
							'mobile'  => 100,
							'tablet'  => 100,
							'desktop' => 100,
							'suffix'  => [
								'mobile'  => 'px',
								'tablet'  => 'px',
								'desktop' => 'px',
							],
						],
						'units'      => [ 'px' ],
					],
					'priority'              => 240,
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar'     => [
							'selector'   => '.nv-author-biography',
							'vars'       => '--avatarsize',
							'suffix'     => 'px',
							'responsive' => true,
						],
						'responsive' => true,
						'prop'       => 'width',
						'template'   => '.nv-author-bio-image {
							width: {{value}}px;
						}',
					],
					'active_callback'       => function () {
						return $this->element_is_enabled( 'author-biography' ) && get_theme_mod( 'neve_author_box_enable_avatar', true );
					},
				],
				'\Neve\Customizer\Controls\React\Responsive_Range'
			)
		);

		$this->add_control(
			new Control(
				'neve_author_box_avatar_position',
				[
					'sanitize_callback' => 'wp_filter_nohtml_kses',
					'transport'         => 'refresh',
					'default'           => 'left',
				],
				[
					'label'                 => esc_html__( 'Image position', 'neve' ),
					'section'               => 'neve_single_post_layout',
					'priority'              => 245,
					'choices'               => [
						'left'   => [
							'tooltip' => esc_html__( 'Left', 'neve' ),
							'icon'    => 'align-left',
						],
						'center' => [
							'tooltip' => esc_html__( 'Center', 'neve' ),
							'icon'    => 'align-center',
						],
						'right'  => [
							'tooltip' => esc_html__( 'Right', 'neve' ),
							'icon'    => 'align-right',
						],
					],
					'show_labels'           => true,
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'template' =>
							'.nv-author-biography .nv-author-elements-wrapper {
							    flex-direction: {{value}}!important;
					    	}',
					],
					'active_callback'       => function () {
						return $this->element_is_enabled( 'author-biography' ) && get_theme_mod( 'neve_author_box_enable_avatar' ) === true;
					},
				],
				'\Neve\Customizer\Controls\React\Radio_Buttons'
			)
		);

		$this->add_control(
			new Control(
				'neve_author_box_avatar_border_radius',
				[
					'sanitize_callback' => 'neve_sanitize_range_value',
					'transport'         => 'postMessage',
					'default'           => 0,
				],
				[
					'label'                 => esc_html__( 'Border Radius', 'neve' ),
					'section'               => 'neve_single_post_layout',
					'type'                  => 'neve_range_control',
					'input_attrs'           => [
						'min'        => 0,
						'max'        => 50,
						'defaultVal' => 0,
					],
					'priority'              => 250,
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar'   => [
							'vars'     => '--borderradius',
							'suffix'   => '%',
							'selector' => '.nv-author-biography',
						],
						'fallback' => 0,
						'template' =>
							'.nv-author-bio-image {
							    border-radius: {{value}}px;
					    	}',
					],
					'active_callback'       => function () {
						return $this->element_is_enabled( 'author-biography' ) && get_theme_mod( 'neve_author_box_enable_avatar' ) === true;
					},
				],
				'\Neve\Customizer\Controls\React\Range'
			)
		);

		$this->add_control(
			new Control(
				'neve_author_box_enable_archive_link',
				[
					'sanitize_callback' => 'neve_sanitize_checkbox',
					'default'           => false,
				],
				[
					'label'           => esc_html__( 'Show archive link', 'neve' ),
					'section'         => 'neve_single_post_layout',
					'type'            => 'neve_toggle_control',
					'priority'        => 255,
					'active_callback' => function () {
						return $this->element_is_enabled( 'author-biography' );
					},
				]
			)
		);

		$this->add_control(
			new Control(
				'neve_author_box_content_alignment',
				[
					'sanitize_callback' => 'neve_sanitize_alignment',
					'transport'         => $this->selective_refresh,
					'default'           => 'left',
				],
				[
					'label'                 => esc_html__( 'Content alignment', 'neve' ),
					'section'               => 'neve_single_post_layout',
					'priority'              => 260,
					'choices'               => [
						'left'   => [
							'tooltip' => esc_html__( 'Left', 'neve' ),
							'icon'    => 'editor-alignleft',
						],
						'center' => [
							'tooltip' => esc_html__( 'Center', 'neve' ),
							'icon'    => 'editor-aligncenter',
						],
						'right'  => [
							'tooltip' => esc_html__( 'Right', 'neve' ),
							'icon'    => 'editor-alignright',
						],
					],
					'show_labels'           => true,
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar'     => [
							'vars'       => [
								'--authorcontentalign',
							],
							'responsive' => true,
							'selector'   => '.nv-author-biography',
						],
						'responsive' => true,
					],
					'active_callback'       => function () {
						return $this->element_is_enabled( 'author-biography' );
					},
				],
				'\Neve\Customizer\Controls\React\Responsive_Radio_Buttons'
			)
		);

		if ( method_exists( $this, 'add_boxed_layout_controls' ) ) {
			$this->add_boxed_layout_controls(
				'author_box',
				[
					'priority'                => 265,
					'section'                 => 'neve_single_post_layout',
					'padding_default'         => $this->responsive_padding_default(),
					'background_default'      => 'var(--nv-light-bg)',
					'color_default'           => 'var(--nv-text-color)',
					'boxed_selector'          => '.nv-author-biography.nv-is-boxed',
					'text_color_css_selector' => '.nv-author-biography.nv-is-boxed, .nv-author-biography.nv-is-boxed a',
					'toggle_active_callback'  => function () {
						return $this->element_is_enabled( 'author-biography' );
					},
					'active_callback'         => function () {
						return $this->element_is_enabled( 'author-biography' ) && get_theme_mod( 'neve_author_box_boxed_layout', false );
					},
				]
			);
		}
	}

	/**
	 * Add post navigation related controls.
	 */
	public function post_navigation() {
		$this->add_control(
			new Control(
				'neve_post_nav_infinite',
				[
					'sanitize_callback' => 'neve_sanitize_checkbox',
					'default'           => false,
				],
				[
					'label'           => esc_html__( 'Enable infinite scroll', 'neve' ),
					'description'     => apply_filters( 'neve_external_link', 'https://bit.ly/nv-sp-inf', __( 'View more details about this', 'neve' ) ),
					'section'         => 'neve_single_post_layout',
					'type'            => 'neve_toggle_control',
					'priority'        => 360,
					'active_callback' => function () {
						return $this->element_is_enabled( 'post-navigation' );
					},
				]
			)
		);
	}

	/**
	 * Related Posts customizer controls
	 */
	public function related_posts() {

		$this->add_control(
			new Control(
				'neve_related_posts_title',
				[
					'sanitize_callback' => 'wp_kses_post',
					'default'           => esc_html__( 'Related Posts', 'neve' ),
				],
				[
					'priority'        => 290,
					'section'         => 'neve_single_post_layout',
					'label'           => esc_html__( 'Title', 'neve' ),
					'type'            => 'text',
					'active_callback' => function () {
						return $this->element_is_enabled( 'related-posts' );
					},
				]
			)
		);

		$this->add_control(
			new Control(
				'neve_related_posts_title_tag',
				[
					'sanitize_callback' => [ $this, 'sanitize_title_html_tag' ],
					'default'           => 'h2',
				],
				[
					'priority'        => 295,
					'section'         => 'neve_single_post_layout',
					'label'           => esc_html__( 'Title HTML tag', 'neve' ),
					'type'            => 'select',
					'choices'         => [
						'h1' => 'H1',
						'h2' => 'H2',
						'h3' => 'H3',
						'h4' => 'H4',
						'h5' => 'H5',
						'h6' => 'H6',
					],
					'active_callback' => function () {
						return $this->element_is_enabled( 'related-posts' );
					},
				]
			)
		);

		$this->add_control(
			new Control(
				'neve_related_posts_taxonomy',
				[
					'default'           => 'category',
					'sanitize_callback' => 'wp_filter_nohtml_kses',
				],
				[
					'label'           => esc_html__( 'Related Posts By', 'neve' ),
					'section'         => 'neve_single_post_layout',
					'priority'        => 300,
					'type'            => 'select',
					'choices'         => [
						'category' => esc_html__( 'Categories', 'neve' ),
						'post_tag' => esc_html__( 'Tags', 'neve' ),
					],
					'active_callback' => function () {
						return $this->element_is_enabled( 'related-posts' );
					},
				]
			)
		);

		$default_related_posts_nb = $this->responsive_related_posts_nb( 'neve_related_posts_columns' );
		$this->add_control(
			new Control(
				'neve_related_posts_col_nb',
				array(
					'sanitize_callback' => 'neve_sanitize_range_value',
					'default'           => wp_json_encode( $default_related_posts_nb ),
				),
				array(
					'label'           => esc_html__( 'Columns', 'neve' ),
					'section'         => 'neve_single_post_layout',
					'units'           => array(
						'items',
					),
					'input_attrs'     => [
						'min'        => 1,
						'max'        => 6,
						'defaultVal' => $default_related_posts_nb,
					],
					'priority'        => 305,
					'active_callback' => function () {
						return $this->element_is_enabled( 'related-posts' );
					},
				),
				'Neve\Customizer\Controls\React\Responsive_Range'
			)
		);

		$this->add_control(
			new Control(
				'neve_related_posts_number',
				[
					'sanitize_callback' => 'absint',
					'default'           => 3,
				],
				[
					'label'           => esc_html__( 'Number of Related Posts', 'neve' ),
					'section'         => 'neve_single_post_layout',
					'input_attrs'     => array(
						'min'  => 1,
						'max'  => 50,
						'step' => 1,
					),
					'priority'        => 310,
					'type'            => 'number',
					'active_callback' => function () {
						return $this->element_is_enabled( 'related-posts' );
					},
				]
			)
		);

		$this->add_control(
			new Control(
				'neve_related_posts_content_alignment',
				[
					'sanitize_callback' => 'neve_sanitize_alignment',
					'transport'         => $this->selective_refresh,
					'default'           => 'left',
				],
				[
					'label'                 => esc_html__( 'Content alignment', 'neve' ),
					'section'               => 'neve_single_post_layout',
					'priority'              => 315,
					'choices'               => [
						'left'   => [
							'tooltip' => esc_html__( 'Left', 'neve' ),
							'icon'    => 'editor-alignleft',
						],
						'center' => [
							'tooltip' => esc_html__( 'Center', 'neve' ),
							'icon'    => 'editor-aligncenter',
						],
						'right'  => [
							'tooltip' => esc_html__( 'Right', 'neve' ),
							'icon'    => 'editor-alignright',
						],
					],
					'show_labels'           => true,
					'live_refresh_selector' => true,
					'live_refresh_css_prop' => [
						'cssVar'     => [
							'vars'       => [
								'--relatedContentAlign',
							],
							'responsive' => true,
							'selector'   => '.nv-related-posts',
						],
						'responsive' => true,
					],
					'active_callback'       => function () {
						return $this->element_is_enabled( 'related-posts' );
					},
				],
				'\Neve\Customizer\Controls\React\Responsive_Radio_Buttons'
			)
		);

		$this->add_control(
			new Control(
				'neve_related_posts_excerpt_length',
				[
					'sanitize_callback' => 'neve_sanitize_range_value',
					'default'           => 25,
				],
				[
					'label'           => esc_html__( 'Excerpt Length', 'neve' ),
					'section'         => 'neve_single_post_layout',
					'step'            => 5,
					'input_attr'      => [
						'min'     => 5,
						'max'     => 300,
						'default' => 25,
					],
					'input_attrs'     => [
						'min'     => 5,
						'max'     => 300,
						'default' => 25,
					],
					'priority'        => 320,
					'active_callback' => function () {
						return $this->element_is_enabled( 'related-posts' );
					},
				],
				class_exists( 'Neve\Customizer\Controls\React\Range' ) ? 'Neve\Customizer\Controls\React\Range' : 'Neve\Customizer\Controls\Range'
			)
		);

		if ( method_exists( $this, 'add_boxed_layout_controls' ) ) {
			$this->add_boxed_layout_controls(
				'related_posts',
				[
					'priority'                  => 330,
					'section'                   => 'neve_single_post_layout',
					'padding_default'           => $this->responsive_padding_default(),
					'background_default'        => 'var(--nv-light-bg)',
					'color_default'             => 'var(--nv-text-color)',
					'boxed_selector'            => '.nv-related-posts.nv-is-boxed',
					'border_color_css_selector' => '.nv-related-posts.nv-is-boxed .posts-wrapper .related-post .content',
					'text_color_css_selector'   => '.nv-related-posts.nv-is-boxed, .nv-related-posts.nv-is-boxed a',
					'toggle_active_callback'    => function () {
						return $this->element_is_enabled( 'related-posts' );
					},
					'active_callback'           => function () {
						return $this->element_is_enabled( 'related-posts' ) && get_theme_mod( 'neve_related_posts_boxed_layout', false );
					},
				]
			);
		}

		$this->add_control(
			new Control(
				'neve_related_posts_box_layout_width',
				[
					'default'           => 'same-as-content',
					'sanitize_callback' => 'wp_filter_nohtml_kses',
				],
				[
					'label'           => esc_html__( 'Section width', 'neve' ),
					'section'         => 'neve_single_post_layout',
					'priority'        => 335,
					'type'            => 'select',
					'choices'         => [
						'same-as-content' => esc_html__( 'Same as content', 'neve' ),
						'wide'            => esc_html__( 'Wide', 'neve' ),
						'full'            => esc_html__( 'Full width', 'neve' ),
					],
					'active_callback' => function () {
						return (
							$this->element_is_enabled( 'related-posts' ) &&
							get_theme_mod( 'neve_single_post_sidebar_layout', 'right' ) === 'full-width' &&
							get_theme_mod( 'neve_related_posts_boxed_layout', false ) === true
						);
					},
				]
			)
		);

		$related_posts_card_components = apply_filters(
			'neve_related_posts_card_filter',
			[
				'featured_image' => __( 'Featured Image', 'neve' ),
				'post_title'     => __( 'Post title', 'neve' ),
				'post_meta'      => __( 'Post meta', 'neve' ),
				'post_excerpt'   => __( 'Post excerpt', 'neve' ),
			]
		);

		$this->add_control(
			new Control(
				'neve_related_posts_card',
				[
					'sanitize_callback' => [ $this, 'sanitize_related_posts_card_value' ],
					'default'           => self::get_related_posts_card_default(),
				],
				[
					'label'            => esc_html__( 'Related posts card', 'neve' ),
					'section'          => 'neve_single_post_layout',
					'fields'           => [
						'margin_bottom' => [
							'type'    => 'range',
							'default' => '0',
							'label'   => __( 'Margin Bottom', 'neve' ) . ' (px)',
							'min'     => 0,
							'max'     => 50,
						],
					],
					'components'       => $related_posts_card_components,
					'allow_new_fields' => 'no',
					'priority'         => 340,
					'active_callback'  => function () {
						return $this->element_is_enabled( 'related-posts' );
					},
				],
				'\Neve\Customizer\Controls\React\Repeater'
			)
		);

		$related_posts_meta_components = apply_filters(
			'neve_related_posts_meta_filter',
			[
				'author'   => __( 'Author', 'neve' ),
				'category' => __( 'Category', 'neve' ),
				'date'     => __( 'Date', 'neve' ),
				'comments' => __( 'Comments', 'neve' ),
				'reading'  => __( 'Estimated reading time', 'neve' ),
			]
		);
		$default                       = wp_json_encode( [ 'author', 'category', 'date', 'comments' ] );
		$default_value                 = $this->get_default_meta_value( 'neve_post_meta_ordering', $default );
		$default_value                 = get_theme_mod( 'neve_blog_post_meta_fields', wp_json_encode( $default_value ) );
		$default_value                 = get_theme_mod( 'neve_related_posts_post_meta_ordering', $default_value );

		$this->add_control(
			new Control(
				'neve_related_posts_post_meta_ordering',
				[
					'sanitize_callback' => 'neve_sanitize_meta_repeater',
					'default'           => $default_value,
				],
				[
					'label'            => esc_html__( 'Related Posts Meta Order', 'neve' ),
					'section'          => 'neve_single_post_layout',
					'fields'           => [
						'hide_on_mobile' => [
							'type'  => 'checkbox',
							'label' => __( 'Hide on mobile', 'neve' ),
						],
					],
					'components'       => $related_posts_meta_components,
					'allow_new_fields' => 'no',
					'priority'         => 345,
					'active_callback'  => function () {
						return $this->element_is_enabled( 'related-posts' );
					},
				],
				'\Neve\Customizer\Controls\React\Repeater'
			)
		);

		$this->add_control(
			new Control(
				'neve_related_typography_shortcut',
				array(
					'sanitize_callback' => 'neve_sanitize_text_field',
				),
				array(
					'button_class'     => 'nv-top-bar-menu-shortcut',
					'text_before'      => __( 'Customize Typography for the Related Posts section', 'neve' ),
					'text_after'       => '.',
					'button_text'      => __( 'here', 'neve' ),
					'is_button'        => false,
					'control_to_focus' => 'neve_related_posts_typography_section_title_accordion_wrap',
					'shortcut'         => true,
					'section'          => 'neve_single_post_layout',
					'priority'         => 346,
					'active_callback'  => function () {
						return $this->element_is_enabled( 'related-posts' );
					},
				),
				'\Neve\Customizer\Controls\Button'
			)
		);

		$this->add_related_posts_typography_controls();

	}

	/**
	 * Related posts card default.
	 */
	public static function get_related_posts_card_default() {
		$featured_image_is_enabled = get_theme_mod( 'neve_related_posts_enable_featured_image', true );

		return wp_json_encode(
			[
				[
					'slug'          => 'featured_image',
					'title'         => __( 'Featured Image', 'neve' ),
					'margin_bottom' => 0,
					'blocked'       => 'yes',
					'visibility'    => $featured_image_is_enabled ? 'yes' : 'no',
				],
				[
					'slug'          => 'post_title',
					'title'         => __( 'Post title', 'neve' ),
					'margin_bottom' => 20,
					'blocked'       => 'yes',
					'visibility'    => 'yes',
				],
				[
					'slug'          => 'post_meta',
					'title'         => __( 'Post meta', 'neve' ),
					'margin_bottom' => 20,
					'blocked'       => 'yes',
					'visibility'    => 'yes',
				],
				[
					'slug'          => 'post_excerpt',
					'title'         => __( 'Post excerpt', 'neve' ),
					'margin_bottom' => 0,
					'blocked'       => 'yes',
					'visibility'    => 'yes',
				],
			]
		);
	}

	/**
	 * Active callback for sharing controls.
	 *
	 * @param string $element Post page element.
	 *
	 * @return bool
	 */
	public function element_is_enabled( $element ) {
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

	/**
	 * Add related posts typography options.
	 *
	 * @return void
	 */
	private function add_related_posts_typography_controls() {
		$controls = array(
			Dynamic_Style::RELATED_POSTS_SECTION_TITLE_TYPOGRAPHY => array(
				'label'                 => __( 'Section title', 'neve' ),
				'title'                 => __( 'Related Posts', 'neve' ),
				'priority'              => 500,
				'live_refresh_selector' => '.nv-related-posts .section-title > *',
				'font_family_control'   => 'neve_headings_font_family',
			),
			Dynamic_Style::RELATED_POSTS_POST_TITLE_TYPOGRAPHY    => array(
				'label'                 => __( 'Post title', 'neve' ),
				'priority'              => 510,
				'live_refresh_selector' => '.nv-related-posts .title',
				'font_family_control'   => 'neve_headings_font_family',
			),
			Dynamic_Style::RELATED_POSTS_POST_META_TYPOGRAPHY     => array(
				'label'                 => __( 'Post meta', 'neve' ),
				'priority'              => 520,
				'live_refresh_selector' => '.nv-related-posts .nv-meta-list',
				'font_family_control'   => 'neve_body_font_family',
			),
			Dynamic_Style::RELATED_POSTS_POST_EXCERPT_TYPOGRAPHY  => array(
				'label'                 => __( 'Post excerpt', 'neve' ),
				'priority'              => 530,
				'live_refresh_selector' => '.nv-related-posts .excerpt-wrap',
				'font_family_control'   => 'neve_body_font_family',
			),
		);

		foreach ( $controls as $id => $args ) {
			$this->add_control(
				new Control(
					$id . '_accordion_wrap',
					array(
						'sanitize_callback' => 'sanitize_text_field',
						'transport'         => $this->selective_refresh,
					),
					[
						'category_label'   => isset( $args['title'] ) ? $args['title'] : '',
						'label'            => $args['label'],
						'section'          => 'neve_typography_blog',
						'priority'         => $args['priority'],
						'class'            => esc_attr( 'typography-related-' . $id ),
						'accordion'        => true,
						'controls_to_wrap' => 1,
						'expanded'         => false,
						'active_callback'  => function () {
							return $this->element_is_enabled( 'related-posts' );
						},
					],
					'Neve\Customizer\Controls\Heading'
				)
			);

			$css_vars = [
				'--texttransform' => 'textTransform',
				'--fontweight'    => 'fontWeight',
				'--fontsize'      => [
					'key'        => 'fontSize',
					'responsive' => true,
				],
				'--lineheight'    => [
					'key'        => 'lineHeight',
					'responsive' => true,
				],
				'--letterspacing' => [
					'key'        => 'letterSpacing',
					'suffix'     => 'px',
					'responsive' => true,
				],
			];

			if ( $id === Dynamic_Style::RELATED_POSTS_SECTION_TITLE_TYPOGRAPHY ) {
				$keys = array_keys( $css_vars );
				$keys = array_map(
					function ( $key ) {
						return '--h2' . ltrim( $key, '-' );
					},
					$keys
				);

				$css_vars = array_combine( $keys, array_values( $css_vars ) );
			}


			$this->add_control(
				new Control(
					$id,
					[
						'transport' => $this->selective_refresh,
					],
					[
						'priority'              => $args['priority'] += 1,
						'section'               => 'neve_typography_blog',
						'type'                  => 'neve_typeface_control',
						'active_callback'       => function () {
							return $this->element_is_enabled( 'related-posts' );
						},
						'font_family_control'   => $args['font_family_control'],
						'live_refresh_selector' => true,
						'live_refresh_css_prop' => [
							'cssVar' => [
								'vars'     => $css_vars,
								'selector' => $args['live_refresh_selector'],
							],
						],
						'refresh_on_reset'      => true,
						'input_attrs'           => array(
							'default_is_empty'       => true,
							'size_units'             => [ 'em', 'px', 'rem' ],
							'weight_default'         => 'none',
							'size_default'           => array(
								'suffix'  => array(
									'mobile'  => 'px',
									'tablet'  => 'px',
									'desktop' => 'px',
								),
								'mobile'  => '',
								'tablet'  => '',
								'desktop' => '',
							),
							'line_height_default'    => array(
								'mobile'  => '',
								'tablet'  => '',
								'desktop' => '',
							),
							'letter_spacing_default' => array(
								'mobile'  => '',
								'tablet'  => '',
								'desktop' => '',
							),
						),
					],
					'\Neve\Customizer\Controls\React\Typography'
				)
			);
		}
	}

	/**
	 * Sanitize related posts card value.
	 *
	 * @param string $value the value from the control.
	 *
	 * @return string
	 */
	public function sanitize_related_posts_card_value( $value ) {
		$allowed_slugs = [
			'featured_image',
			'post_title',
			'post_meta',
			'post_excerpt',
		];

		$allowed_keys = [
			'slug',
			'title',
			'visibility',
			'blocked',
			'margin_bottom',
		];

		$value = json_decode( $value, true );

		$value = array_filter(
			$value,
			function ( $item ) use ( $allowed_slugs ) {
				return in_array( $item['slug'], $allowed_slugs );
			}
		);

		$value = array_map(
			function ( $item ) use ( $allowed_keys ) {
				return array_intersect_key( $item, array_flip( $allowed_keys ) );
			},
			$value
		);

		$value = array_map(
			function ( $item ) {
				if ( isset( $item['margin_bottom'] ) ) {
					$item['margin_bottom'] = absint( $item['margin_bottom'] );
				}

				return $item;
			},
			$value
		);

		return wp_json_encode( $value );
	}
}
