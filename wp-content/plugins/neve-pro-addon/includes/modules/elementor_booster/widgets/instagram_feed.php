<?php
/**
 * Elementor Instagram Feed Widget.
 *
 * @package Neve_Pro\Modules\Elementor_Booster\Widgets
 */

namespace Neve_Pro\Modules\Elementor_Booster\Widgets;

use Elementor\Controls_Manager;
use Elementor\Plugin;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Core\Schemes\Typography;
use Neve_Pro\Modules\Elementor_Booster\Module;

/**
 * Class Instagram_Feed
 *
 * @package Neve_Pro\Modules\Elementor_Booster\Widgets
 */
class Instagram_Feed extends Elementor_Booster_Base {

	/**
	 * Holds our normalized Instagram Feed Widget Settings
	 *
	 * @var array $instagram_feed_settings Holds an a custom created array of Instagram Feed settings pulled from the elementor widget.
	 */
	private $instagram_feed_settings = array();

	/**
	 * Holds our widget ID of the current widget being rendered.
	 *
	 * Useful if user is rendering different IG widgets on the same page.
	 *
	 * @var string $widget_id Holds the widget ID of the current IG widget being rendered.
	 */
	private $widget_id;

	/**
	 * Widget slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'neve_instagram_feed';
	}

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return esc_html__( 'Instagram Feed', 'neve' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string Widget icon.
	 * @since 1.0.0
	 * @access public
	 */
	public function get_icon() {
		return 'fab fa-instagram-square';
	}

	/**
	 * Retrieve the list of styles the team member widget depended on.
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_style_depends() {
		return [ 'font-awesome-5-all' ];
	}


	/**
	 * Get widget keywords
	 *
	 * @return array
	 */
	public function get_keywords() {
		return [ 'carousel', 'instagram', 'slider' ];
	}

	/**
	 * Retrieve the list of scripts the instagram widget depended on.
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return [ 'neb-instagram-feed' ];
	}

	/**
	 * Register content related controls
	 */
	protected function register_content_controls() {

		$this->start_controls_section(
			'instagram_feed_api_settings',
			[
				'label' => esc_html__( 'API Settings', 'neve' ),
			]
		);

		$this->add_control(
			'access_token',
			[
				/* translators: %s link markup */
				'description' => sprintf(
					'Learn how to find your API Key %s',
					/* translators: %s link text */
					sprintf( '<a href="https://bit.ly/nv-eiw" target="_blank" rel="external noreferrer noopener">%s</a>', esc_html__( 'here', 'neve' ) )
				),
				'label'       => esc_html__( 'Instagram Access Token (API Key)', 'neve' ),
				'label_block' => true,
				'title'       => esc_html__( 'Instagram Access Token (API Key)', 'neve' ),
				'type'        => Controls_Manager::TEXTAREA,
			]
		);

		$this->add_control(
			'save_instagram_media_to_library',
			[
				'description'  => esc_html__( 'Save the images to the media library to reduce calls to the Instagram API.', 'neve' ),
				'label'        => esc_html__( 'Save Instagram Media to Library', 'neve' ),
				'label_on'     => esc_html__( 'Yes', 'neve' ),
				'label_off'    => esc_html__( 'No', 'neve' ),
				'return_value' => 'true', // This has to be a string for Elementor to respect setting state.
				'title'        => esc_html__( 'Save Instagram Images Data', 'neve' ),
				'type'         => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'cron_schedules',
			[
				'condition' => [
					'save_instagram_media_to_library' => 'true',
				],
				'label'     => esc_html__( 'Refresh Images', 'neve' ),
				'default'   => 'daily',
				'options'   => [
					'hourly'     => esc_html__( 'Hourly', 'neve' ),
					'daily'      => esc_html__( 'Once a Day', 'neve' ),
					'twicedaily' => esc_html__( 'Twice a Day', 'neve' ),
					'weekly'     => esc_html__( 'Once a Week', 'neve' ),
				],
				'title'     => esc_html__( 'Reload Data', 'neve' ),
				'type'      => Controls_Manager::SELECT,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'instagram_feed_settings',
			[
				'label' => esc_html__( 'General Settings', 'neve' ),
			]
		);

		$this->add_control(
			'display_type',
			[
				'label'              => esc_html__( 'Display Type', 'neve' ),
				'default'            => 'grid',
				'frontend_available' => true,
				'options'            => [
					'grid'     => esc_html__( 'Grid', 'neve' ),
					'carousel' => esc_html__( 'Carousel', 'neve' ),
				],
				'title'              => esc_html__( 'Display Type', 'neve' ),
				'type'               => Controls_Manager::SELECT,
			]
		);

		$this->add_control(
			'number_of_images_to_show',
			[
				'label'   => esc_html__( 'Number of images', 'neve' ),
				'default' => '12',
				// 'frontend_available' => true,
				'min'     => 1,
				'max'     => apply_filters( 'neb_instagram_feed_widget_num_images_control_max', 100 ),
				'title'   => esc_html__( 'Number of images to show', 'neve' ),
				'type'    => Controls_Manager::NUMBER,
			]
		);

		$this->add_control(
			'include_video_thumbnails',
			[
				'label'        => esc_html__( 'Include Video Thumbnails?', 'neve' ),
				'description'  => esc_html__( 'Turning this option on will include the thumbnail for videos on your account. Leaving it off will strip the video thumbnails from the results before the feed is displayed.', 'neve' ),
				'default'      => 'true',
				'label_on'     => esc_html__( 'Yes', 'neve' ),
				'label_off'    => esc_html__( 'No', 'neve' ),
				// 'frontend_available' => true,
				'return_value' => 'true', // This has to be a string for Elementor to respect setting state.
				'title'        => esc_html__( 'Include Video Thumbnails', 'neve' ),
				'type'         => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'reverse_media_order',
			[
				'label'        => esc_html__( 'Reverse Image Order', 'neve' ),
				'label_on'     => esc_html__( 'Yes', 'neve' ),
				'label_off'    => esc_html__( 'No', 'neve' ),
				// 'frontend_available' => true,
				'return_value' => 'true', // This has to be a string for Elementor to respect setting state.
				'title'        => esc_html__( 'Reverse Image Order', 'neve' ),
				'type'         => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'include_link',
			[
				'label'        => esc_html__( 'Link Images to Instagram', 'neve' ),
				'label_on'     => esc_html__( 'Yes', 'neve' ),
				'label_off'    => esc_html__( 'No', 'neve' ),
				'default'      => 'true',
				// 'frontend_available' => true,
				'return_value' => 'true', // This has to be a string for Elementor to respect setting state.
				'title'        => esc_html__( 'Include Link', 'neve' ),
				'type'         => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'link_behavior',
			[
				'condition' => [
					'include_link' => 'true',
				],
				'label'     => esc_html__( 'Link Behavior', 'neve' ),
				'default'   => 'go_to_instagram',
				// 'frontend_available' => true,
				'options'   => [
					'go_to_instagram' => esc_html__( 'Go to Instagram', 'neve' ),
					'lightbox'        => esc_html__( 'Lightbox Image', 'neve' ),
				],
				'title'     => esc_html__( 'Link Behavior', 'neve' ),
				'type'      => Controls_Manager::SELECT,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'instagram_feed_carousel_settings',
			[
				'condition' => [
					'display_type' => 'carousel',
				],
				'label'     => esc_html__( 'Carousel Settings', 'neve' ),
			]
		);

		$this->add_control(
			'number_of_carousel_images_to_show',
			[
				'condition'          => [
					'display_type' => 'carousel',
				],
				'label'              => esc_html__( 'Number of images to show at once', 'neve' ),
				'default'            => 4,
				'frontend_available' => true,
				'min'                => 1,
				'max'                => 6,
				'title'              => esc_html__( 'Number of images to show at once', 'neve' ),
				'type'               => Controls_Manager::NUMBER,
			]
		);

		$this->add_control(
			'autoplay_carousel',
			[
				'label'              => esc_html__( 'Autoplay carousel', 'neve' ),
				'label_on'           => esc_html__( 'Yes', 'neve' ),
				'label_off'          => esc_html__( 'No', 'neve' ),
				'default'            => 'true',
				'frontend_available' => true,
				'return_value'       => 'true', // This has to be a string for Elementor to respect setting state.
				'title'              => esc_html__( 'Autoplay carousel', 'neve' ),
				'type'               => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'autoplay_carousel_timeout',
			[
				'label'              => esc_html__( 'Autoplay timeout (in seconds)', 'neve' ),
				'default'            => 5,
				'frontend_available' => true,
				'min'                => 1,
				'step'               => .5,
				'title'              => esc_html__( 'Autoplay timeout in seconds', 'neve' ),
				'type'               => Controls_Manager::NUMBER,
			]
		);

		$this->add_control(
			'loop_type',
			[
				'label'              => esc_html__( 'Loop Type', 'neve' ),
				'default'            => 'forward',
				'frontend_available' => true,
				'options'            => [
					'forward'  => esc_html__( 'Forward', 'neve' ),
					'reverse'  => esc_html__( 'Reverse', 'neve' ),
					'disabled' => esc_html__( 'Disabled', 'neve' ),
				],
				'title'              => esc_html__( 'Loop Type', 'neve' ),
				'type'               => Controls_Manager::SELECT,
			]
		);

		$this->add_control(
			'slide_by',
			[
				'label'              => esc_html__( 'Slide By', 'neve' ),
				'default'            => 1,
				'frontend_available' => true,
				'min'                => 1,
				'max'                => 6, // @see number_of_carousel_images_to_show
				'title'              => esc_html__( 'Slide By', 'neve' ),
				'type'               => Controls_Manager::NUMBER,
			]
		);

		$this->add_control(
			'carousel_pagination',
			[
				'label'        => esc_html__( 'Show Pagination', 'neve' ),
				'default'      => 'true',
				'label_on'     => esc_html__( 'Yes', 'neve' ),
				'label_off'    => esc_html__( 'No', 'neve' ),
				'return_value' => 'true', // This has to be a string for Elementor to respect setting state.
				'title'        => esc_html__( 'Pagination', 'neve' ),
				'type'         => Controls_Manager::SWITCHER,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'follow_btn_settings',
			[
				'label' => esc_html__( 'Follow Button Settings', 'neve' ),
			]
		);

		$this->add_control(
			'show_follow_btn',
			[
				'label'        => esc_html__( 'Show Follow Button', 'neve' ),
				'label_on'     => esc_html__( 'Yes', 'neve' ),
				'label_off'    => esc_html__( 'No', 'neve' ),
				'return_value' => 'true', // This has to be a string for Elementor to respect setting state.
				'title'        => esc_html__( 'Show Follow Button', 'neve' ),
				'type'         => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'follow_username',
			[
				'condition'   => [
					'show_follow_btn' => 'true',
				],
				'label'       => esc_html__( 'Profile Name', 'neve' ),
				// 'frontend_available' => true,
				'placeholder' => '@username',
				'title'       => esc_html__( 'Profile Name', 'neve' ),
				'type'        => Controls_Manager::TEXT,
			]
		);

		$this->add_control(
			'follow_btn_text',
			[
				'condition' => [
					'show_follow_btn' => 'true',
				],
				'label'     => esc_html__( 'Button Text', 'neve' ),
				'default'   => 'Follow on Instagram',
				// 'frontend_available' => true,
				'type'      => Controls_Manager::TEXT,
			]
		);

		$this->add_control(
			'show_follow_btn_icon',
			[
				'condition'    => [
					'show_follow_btn' => 'true',
				],
				'label'        => esc_html__( 'Show Icon', 'neve' ),
				'label_on'     => esc_html__( 'Yes', 'neve' ),
				'label_off'    => esc_html__( 'No', 'neve' ),
				'default'      => 'true',
				// 'frontend_available' => true,
				'return_value' => 'true', // This has to be a string for Elementor to respect setting state.
				'title'        => esc_html__( 'Show Icon', 'neve' ),
				'type'         => Controls_Manager::SWITCHER,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'caption_settings',
			[
				'label' => esc_html__( 'Caption Settings', 'neve' ),
			]
		);

		$this->add_control(
			'display_caption',
			[
				'label'        => esc_html__( 'Display Caption', 'neve' ),
				'label_on'     => esc_html__( 'Yes', 'neve' ),
				'label_off'    => esc_html__( 'No', 'neve' ),
				'default'      => 'true',
				// 'frontend_available' => true,
				'return_value' => 'true', // This has to be a string for Elementor to respect setting state.
				'title'        => esc_html__( 'Display Caption', 'neve' ),
				'type'         => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'caption_style',
			[
				'condition' => [
					'display_caption' => 'true',
				],
				'label'     => esc_html__( 'Caption Style', 'neve' ),
				'default'   => 'bottom',
				// 'frontend_available' => true,
				'options'   => [
					'bottom'  => esc_html__( 'Bottom', 'neve' ),
					'overlay' => esc_html__( 'Overlay', 'neve' ),
				],
				'title'     => esc_html__( 'Caption Style', 'neve' ),
				'type'      => Controls_Manager::SELECT,
			]
		);

		$this->add_control(
			'caption_length',
			[
				'condition' => [
					'display_caption' => 'true',
				],
				'label'     => esc_html__( 'Max Caption Length', 'neve' ),
				'default'   => 50,
				// 'frontend_available' => true,
				'min'       => 1,
				'max'       => 237,
				'title'     => esc_html__( 'Max Caption Length', 'neve' ),
				'type'      => Controls_Manager::NUMBER,
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Register styles related controls
	 */
	protected function register_style_controls() {

		$this->start_controls_section(
			'instagram_feed_grid_style_settings',
			[
				'condition' => [
					'display_type' => 'grid',
				],
				'label'     => esc_html__( 'Grid', 'neve' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'grid_columns',
			[
				'condition'          => [
					'display_type' => 'grid',
				],
				'label'              => esc_html__( 'Number of Columns', 'neve' ),
				'frontend_available' => true,
				'range'              => [
					'px' => [
						'min'  => 0,
						'max'  => 6,
						'step' => 1,
					],
				],
				'devices'            => [ 'desktop', 'tablet', 'mobile' ],
				'desktop_default'    => [
					'size' => 4,
				],
				'tablet_default'     => [
					'size' => 2,
				],
				'mobile_default'     => [
					'size' => 1,
				],
				'type'               => Controls_Manager::SLIDER,
				'selectors'          => [
					'{{WRAPPER}} .neb-ig-grid-container' => 'grid-template-columns: repeat({{SIZE}}, 1fr);',
				],
			]
		);

		$this->add_responsive_control(
			'grid_column_gap',
			[
				'condition'  => [
					'display_type' => 'grid',
				],
				'label'      => esc_html__( 'Grid Column Gap', 'neve' ),
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 500,
						'step' => .5,
					],
				],
				'devices'    => [ 'desktop', 'tablet', 'mobile' ],
				'type'       => Controls_Manager::SLIDER,
				'selectors'  => [
					'{{WRAPPER}} .neb-ig-grid-container' => 'column-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'grid_row_gap',
			[
				'condition'  => [
					'display_type' => 'grid',
				],
				'label'      => esc_html__( 'Grid Row Gap', 'neve' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 500,
						'step' => .5,
					],
				],
				'devices'    => [ 'desktop', 'tablet', 'mobile' ],
				'selectors'  => [
					'{{WRAPPER}} .neb-ig-grid-container' => 'row-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'instagram_feed_image_style',
			[
				'label' => esc_html__( 'Image', 'neve' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'ig_image_height',
			[
				'label'              => esc_html__( 'Height', 'neve' ),
				'frontend_available' => true,
				'type'               => Controls_Manager::SLIDER,
				'range'              => [
					'px' => [
						'min'  => 200,
						'max'  => 800,
						'step' => 1,
					],
				],
				'size_units'         => [ 'px' ],
				'devices'            => [ 'desktop', 'tablet', 'mobile' ],
				'desktop_default'    => [
					'unit' => 'px',
					'size' => 270,
				],
				'tablet_default'     => [
					'unit' => 'px',
					'size' => 270,
				],
				'mobile_default'     => [
					'unit' => 'px',
					'size' => 110,
				],
				'selectors'          => [
					'{{WRAPPER}} .neb-ig-feed-image-item' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .neb-ig-feed-caption-overlay' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'space_between',
			[
				'condition'          => [
					'display_type' => 'carousel',
				],
				'label'              => esc_html__( 'Space Between', 'neve' ),
				'frontend_available' => true,
				'type'               => Controls_Manager::SLIDER,
				'range'              => [
					'px' => [
						'min'  => 5,
						'max'  => 100,
						'step' => 1,
					],
				],
				'size_units'         => [ 'px' ],
				'devices'            => [ 'desktop', 'tablet', 'mobile' ],
				'desktop_default'    => [
					'unit' => 'px',
					'size' => 10,
				],
				'tablet_default'     => [
					'unit' => 'px',
					'size' => 10,
				],
				'mobile_default'     => [
					'unit' => 'px',
					'size' => 10,
				],
			]
		);

		$this->add_control(
			'image_background_color',
			[
				'default'   => '#FFF',
				'label'     => esc_html__( 'Image Background Color', 'neve' ),
				'title'     => esc_html__( 'Image Background Color', 'neve' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .neb-ig-feed-image-item' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_padding',
			[
				'label'      => esc_html__( 'Padding', 'neve' ),
				'devices'    => [ 'desktop', 'tablet', 'mobile' ],
				'size_units' => [ 'px', '%', 'em' ],
				'type'       => Controls_Manager::DIMENSIONS,
				'selectors'  => [
					'{{WRAPPER}} .neb-ig-feed-image-item img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'neve' ),
				'devices'    => [ 'desktop', 'tablet', 'mobile' ],
				'size_units' => [ 'px', '%', 'em' ],
				'type'       => Controls_Manager::DIMENSIONS,
				'selectors'  => [
					'{{WRAPPER}} .neb-ig-feed-image-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .neb-ig-feed-caption-overlay' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',

				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'instagram_feed_caption_style',
			[
				'condition' => [
					'display_caption' => 'true',
				],
				'label'     => esc_html__( 'Caption', 'neve' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'caption_on_mobile',
			[
				'label'              => esc_html__( 'Hide on Mobile', 'neve' ),
				'label_on'           => esc_html__( 'Yes', 'neve' ),
				'label_off'          => esc_html__( 'No', 'neve' ),
				'default'            => 'none',
				'frontend_available' => true,
				'return_value'       => 'none', // This has to be a string for Elementor to respect setting state.
				'title'              => esc_html__( 'Show Caption on Mobile', 'neve' ),
				'type'               => Controls_Manager::SWITCHER,
				'selectors'          => [
					'(mobile) {{WRAPPER}} .neb-ig-feed-caption' => 'display: {{VALUE}};',
					'(mobile) {{WRAPPER}} .neb-ig-feed-caption-overlay' => 'display: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'caption_typography',
				'scheme'   => Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .neb-ig-feed-caption-overlay p, {{WRAPPER}} .neb-ig-feed-caption p',
			]
		);

		$this->add_responsive_control(
			'caption_text_align',
			[
				'label'     => esc_html__( 'Alignment', 'neve' ),
				'default'   => 'left',
				'options'   => [
					'left'   => [
						'title' => esc_html__( 'Left', 'neve' ),
						'icon'  => 'fa fa-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'neve' ),
						'icon'  => 'fa fa-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'neve' ),
						'icon'  => 'fa fa-align-right',
					],
				],
				'type'      => Controls_Manager::CHOOSE,
				'toggle'    => true,
				'selectors' => [
					'{{WRAPPER}} .neb-ig-feed-caption p' => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .neb-ig-feed-caption-overlay p' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'      => 'text_shadow',
				'label'     => esc_html__( 'Text Shadow', 'neve' ),
				'selectors' => [
					'{{WRAPPER}} .neb-ig-feed-caption p',
					'{{WRAPPER}} .neb-ig-feed-caption-overlay p',
				],
			]
		);

		$this->add_control(
			'caption_text_color',
			[
				'condition' => [
					'display_caption' => 'true',
				],
				'label'     => esc_html__( 'Caption Text Color', 'neve' ),
				'default'   => '#000',
				'title'     => esc_html__( 'Caption Text color', 'neve' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .neb-ig-feed-caption p' => 'color: {{VALUE}};',
					'{{WRAPPER}} .neb-ig-feed-caption-overlay p' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'caption_background_color',
			[
				'condition' => [
					'display_caption' => 'true',
				],
				'label'     => esc_html__( 'Caption Background Color', 'neve' ),
				'default'   => '#FFF',
				'title'     => esc_html__( 'Caption Background Color', 'neve' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .neb-ig-feed-caption' => 'background: {{VALUE}};',
					'{{WRAPPER}} .neb-ig-feed-caption-overlay' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'caption_min_height',
			[
				'condition'          => [
					'caption_style' => 'bottom',
				],
				'label'              => esc_html__( 'Min height', 'neve' ),
				'frontend_available' => true,
				'range'              => [
					'px' => [
						'min'  => 0,
						'max'  => 500,
						'step' => 1,
					],
				],
				'size_units'         => [ 'px', 'em', '%' ],
				'devices'            => [ 'desktop', 'tablet', 'mobile' ],
				'desktop_default'    => [
					'unit' => 'px',
					'size' => 100,
				],
				'tablet_default'     => [
					'unit' => 'px',
					'size' => 50,
				],
				'mobile_default'     => [
					'unit' => 'px',
					'size' => 25,
				],
				'type'               => Controls_Manager::SLIDER,
				'selectors'          => [
					'{{WRAPPER}} .neb-ig-feed-caption' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'caption_padding',
			[
				'label'      => esc_html__( 'Padding', 'neve' ),
				'size_units' => [ 'px', '%', 'em' ],
				'devices'    => [ 'desktop', 'tablet', 'mobile' ],
				'type'       => Controls_Manager::DIMENSIONS,
				'selectors'  => [
					'{{WRAPPER}} .neb-ig-feed-caption' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .neb-ig-feed-caption-overlay' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'caption_border_radius',
			[
				'condition'  => [
					'caption_style' => 'bottom',
				],
				'label'      => esc_html__( 'Border Radius', 'neve' ),
				'size_units' => [ 'px', '%', 'em' ],
				'devices'    => [ 'desktop', 'tablet', 'mobile' ],
				'type'       => Controls_Manager::DIMENSIONS,
				'selectors'  => [
					'{{WRAPPER}} .neb-ig-feed-caption' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'instagram_feed_follow_btn_style',
			[
				'condition' => [
					'show_follow_btn' => 'true',
				],
				'label'     => esc_html__( 'Follow Button', 'neve' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'follow_btn_typography',
				'scheme'   => Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} #neb-instagram-feed-follow-btn p',
			]
		);

		$this->add_control(
			'follow_btn_bg_color',
			[
				'default'   => '#000',
				'label'     => esc_html__( 'Button Color', 'neve' ),
				'title'     => esc_html__( 'Button Color', 'neve' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #neb-instagram-feed-follow-btn p' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'follow_btn_icon_color',
			[
				'condition' => [
					'show_follow_btn_icon' => 'true',
				],
				'default'   => '#FFF',
				'label'     => esc_html__( 'Icon Color', 'neve' ),
				'title'     => esc_html__( 'Icon Color', 'neve' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #neb-instagram-feed-follow-btn i.fab.fa-instagram' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'follow_btn_text_color',
			[
				'default'   => '#FFF',
				'label'     => esc_html__( 'Text Color', 'neve' ),
				'title'     => esc_html__( 'Text Color', 'neve' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #neb-instagram-feed-follow-btn p' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'follow_btn_margin',
			[
				'label'      => esc_html__( 'Margin', 'neve' ),
				'devices'    => [ 'desktop', 'tablet', 'mobile' ],
				'size_units' => [ 'px', '%', 'em' ],
				'type'       => Controls_Manager::DIMENSIONS,
				'selectors'  => [
					'{{WRAPPER}} #neb-instagram-feed-follow-btn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'follow_btn_padding',
			[
				'label'      => esc_html__( 'Padding', 'neve' ),
				'devices'    => [ 'desktop', 'tablet', 'mobile' ],
				'size_units' => [ 'px', '%', 'em' ],
				'type'       => Controls_Manager::DIMENSIONS,
				'selectors'  => [
					'{{WRAPPER}} #neb-instagram-feed-follow-btn p' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'follow_btn_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'neve' ),
				'devices'    => [ 'desktop', 'tablet', 'mobile' ],
				'size_units' => [ 'px', '%', 'em' ],
				'type'       => Controls_Manager::DIMENSIONS,
				'selectors'  => [
					'{{WRAPPER}} #neb-instagram-feed-follow-btn p' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'follow_btn_width',
			[
				'label'              => esc_html__( 'Button Width', 'neve' ),
				'frontend_available' => true,
				'range'              => [
					'px' => [
						'min'  => 0,
						'max'  => 500,
						'step' => 1,
					],
				],
				'size_units'         => [ 'px', '%' ],
				'devices'            => [ 'desktop', 'tablet', 'mobile' ],
				'desktop_default'    => [
					'unit' => 'px',
					'size' => 200,
				],
				'tablet_default'     => [
					'unit' => 'px',
					'size' => 200,
				],
				'mobile_default'     => [
					'unit' => 'px',
					'size' => 100,
				],
				'type'               => Controls_Manager::SLIDER,
				'selectors'          => [
					'{{WRAPPER}} #neb-instagram-feed-follow-btn a' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} #neb-instagram-feed-follow-btn p' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'hover_animation',
			[
				'label' => esc_html__( 'Hover Animation', 'neve' ),
				'type'  => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'instagram_pagination_style',
			[
				'condition' => [
					'display_type' => 'carousel',
				],
				'label'     => esc_html__( 'Pagination', 'neve' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'pagination_dot_color',
			[
				'label'     => esc_html__( 'Pagination Dot Color', 'neve' ),
				'default'   => '#000',
				'title'     => esc_html__( 'Pagination Dot Color', 'neve' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet' => 'background: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

	}

	/**
	 * Creates a basic HTML output in elementor editor view with an error message.
	 *
	 * Used to wrap various API related error messages.
	 *
	 * @param string $error_message The error message to wrap inside the HTML div.
	 *
	 * @return string $html The HTML to output to the admin editing the elementor page.
	 */
	private function do_error_markup( $error_message ) {

		/**
		 * Instance of the plugin class.
		 *
		 * @var object $instance \Elementor\Plugin
		 */
		$instance = Plugin::$instance;

		/**
		 * Only show these errors in Elementor's Editor mode.
		 */
		if ( ! $instance->editor->is_edit_mode() ) {
			return '';
		}

		$error_message = esc_html( $error_message );

		$html = <<<IGERROR
		<div style="background: #fff; padding: 20px; border: 2px dashed #000; text-align: center;">
		<p style="color: #000; margin-bottom: 0">
		<strong>
		$error_message
		</strong>
		</p>
		</div>
IGERROR;

		return $html;

	}

	/**
	 * Setup widget settings.
	 *
	 * Normalizes the widget settings with the settings we need throughout the widget code.
	 *
	 * @return array $instagram_feed_settings Array of widget settings.
	 */
	private function setup_widget_settings() {

		$settings = $this->get_settings();

		$access_token             = $settings['access_token'] ?? '';
		$include_video_thumbnails = ( $settings['include_video_thumbnails'] ?? '' );
		$number_of_images_to_show = ( $settings['number_of_images_to_show'] ?? 12 );

		$instagram_feed_settings = array(
			'api'          => array(
				'access_token'                    => $access_token,
				'save_instagram_media_to_library' => (bool) ( $settings['save_instagram_media_to_library'] ?? '' ),
				'number_of_images_to_show'        => (int) $number_of_images_to_show,
			),
			'follow_btn'   => array(
				'show'            => (bool) ( $settings['show_follow_btn'] ?? '' ),
				'username'        => $settings['follow_username'] ? sanitize_text_field( $settings['follow_username'] ) : '',
				'hover_animation' => $settings['hover_animation'],
				'icon'            => (bool) ( $settings['show_follow_btn_icon'] ?? '' ),
				'text'            => ( ! empty( trim( $settings['follow_btn_text'] ) ) ) ? trim( sanitize_text_field( $settings['follow_btn_text'] ) ) : esc_html__( 'Follow on Instagram', 'neve' ),
			),
			'display_type' => $settings['display_type'],
			'carousel'     => array(
				'carousel_pagination' => $settings['carousel_pagination'],
			),
			'grid'         => array(
				'',
			),
			'image_card'   => array(
				'display_caption'            => (bool) ( $settings['display_caption'] ?? '' ),
				'caption_background_color'   => $settings['caption_background_color'] ?? '',
				'caption_length'             => (int) ( $settings['caption_length'] ?? 50 ),
				'caption_on_mobile'          => (bool) ( $settings['caption_on_mobile'] ?? '' ),
				'caption_style'              => $settings['caption_style'] ?? 'bottom',
				'caption_alignment'          => $settings['caption_text_align'] ?? 'left',
				'caption_text_color'         => $settings['caption_text_color'] ?? '',
				'container_background_color' => $settings['container_background_color'] ?? '',
				'include_link'               => (bool) ( $settings['include_link'] ?? '' ),
				'include_video_thumbnails'   => (bool) $include_video_thumbnails,
				'link_behavior'              => $settings['link_behavior'] ?? 'go_to_instagram',
				'reverse_media_order'        => (bool) ( $settings['reverse_media_order'] ?? '' ),

			),
		);

		return $instagram_feed_settings;
	}

	/**
	 * Get Instagram media from Instagram API.
	 *
	 * @param array $instagram_feed_settings Normalized widget settings.
	 *
	 * @return array Array of images from Instagram API.
	 */
	private function get_instagram_media_from_api( $instagram_feed_settings ) {
		return ( new Module() )->instagram_feed__get_media_from_api( $instagram_feed_settings );
	}

	/**
	 * Get the image by it's title.
	 *
	 * The get_page_by_title() function is open to false positives which we do not want here.
	 * WordPress might save a post with '-1' at the end even if there's no post which exists with that name.
	 *
	 * @see https://vertis.d.pr/IwJdtc
	 *
	 * @param string $post_title The attachment post title.
	 *
	 * @return object|false
	 */
	private function get_image_by_title( $post_title ) {

		global $wpdb;
		$cache_key = $post_title . '_key';
		$image     = wp_cache_get( $cache_key );
		// phpcs:disable
		if ( $image === false ) {
			$image = $wpdb->get_row(
				$wpdb->prepare(
					"SELECT `ID`, `guid` FROM {$wpdb->posts} WHERE `post_title` LIKE %s;",
					$wpdb->esc_like( $post_title ) . '%'
				)
			);
			wp_cache_set( $cache_key, $image );
		}
		// phpcs:enable
		return $image;

	}

	/**
	 * Get saved Instagram media from site library.
	 *
	 * @return mixed $images Array of images with normalized keys for displaying on frontend. Error on failure.
	 */
	private function get_instagram_media_from_library_cache() {

		$instagram_feed_settings = $this->instagram_feed_settings;
		$access_token            = $instagram_feed_settings['api']['access_token'];
		$widget_id               = $this->get_id();
		$image_ids_for_page      = '';
		$post_id                 = get_the_ID();

		if ( empty( $access_token ) ) {
			return;
		}

		$cached_image_ids = get_option( 'neb_instagram_feed_cached_image_ids', array() );

		/**
		 * If we have no cached images yet, show the user images from the API.
		 */
		$all_widgets = array_column( $cached_image_ids, 'widget_id' );

		if ( ! in_array( $widget_id, $all_widgets ) ) {
			return $this->get_instagram_media_from_api( $instagram_feed_settings );
		}

		foreach ( $cached_image_ids as $key => $data ) {

			if ( $data['widget_id'] === $widget_id && $data['post_id'] === $post_id ) {
				$image_ids_for_page = $cached_image_ids[ $key ];
				break;
			}
		}

		if ( empty( $image_ids_for_page ) ) {
			return $this->get_instagram_media_from_api( $instagram_feed_settings );
		}

		$cached_image_ids = $image_ids_for_page['widget_images'];
		$images           = array();

		foreach ( $cached_image_ids as $image_name ) {

			$image_obj = $this->get_image_by_title( $image_name );

			if ( empty( $image_obj ) ) {
				continue;
			}

			$image_id  = $image_obj->ID;
			$image_url = $image_obj->guid;

			$image_meta = get_post_meta( $image_id, 'neb_instagram_feed_media_meta', true );

			$caption       = $image_meta['caption'] ?? '';
			$instagram_url = $image_meta['permalink'] ?? '#';
			$media_type    = $image_meta['media_type'] ?? '';


			$images[] = array(
				'media_id'      => $image_id,
				'media_type'    => $media_type,
				'media_url'     => $image_url,
				'thumbnail_url' => $image_url,
				'caption'       => $caption,
				'permalink'     => $instagram_url,
			);

		}

		if ( empty( $images ) ) {
			return __METHOD__ . ' Line ' . __LINE__ . ': ' . esc_html__( 'Cached image objects returned empty. Please reach out to support with a screenshot of this error message.', 'neve' );
		}

		$images = array_slice( $images, 0, $instagram_feed_settings['api']['number_of_images_to_show'] );

		return $images;

	}

	/**
	 * Get saved Instagram media from transient.
	 *
	 * @return array $media Array of images with normalized keys for displaying on frontend.
	 */
	private function get_instagram_media_from_transient_cache() {

		$post_id = get_the_ID();
		$media   = get_transient( 'neb_instagram_api_media_data_' . $post_id . '_' . $this->widget_id );

		/*
		 * If our transient is empty lets get a fresh set of images from the API.
		 */
		if ( empty( $media ) ) {

			$media = $this->get_instagram_media_from_api( $this->instagram_feed_settings );

			/*
			 * Set our transient with fresh set of images.
			 *
			 * It is possible that the url to the images changes or gets invalidated by Instagram
			 * So Transients are set for 12 hours to grab new details.
			 */
			if ( is_array( $media ) ) {
				set_transient( 'neb_instagram_api_media_data_' . $post_id . '_' . $this->widget_id, $media, 43200 );
			}
		}

		return $media;

	}

	/**
	 * Handle Lightbox logic.
	 *
	 * @param array $instagram_media Array of Instagram images for displaying.
	 *
	 * @return mixed $instagram_media Altered Instagram media array or error message.
	 */
	private function handle_lightbox( $instagram_media ) {

		/**
		 * Instance of the plugin class.
		 *
		 * @var object $instance \Elementor\Plugin
		 */
		$instance = Plugin::$instance;

		$kit = $instance->kits_manager->get_active_kit();

		if ( ! empty( $kit->get_settings( 'global_image_lightbox' ) ) ) {
			/**
			 * Replace the link to the Instagram image post with the direct link to the actual image.
			 */
				array_walk(
					$instagram_media,
					function( &$item, $key ) {
						if ( $item['media_type'] !== 'VIDEO' ) {
							$item['permalink'] = $item['media_url'];
						} else {
							$item['permalink'] = $item['thumbnail_url'];
						}
					}
				);

		} else {
			$error_message = esc_html__( 'Lightbox setting needs to be turned on in Elementor "Site Settings" for lightbox feature to work.', 'neve' );
			return $error_message;
		}

		return $instagram_media;
	}

	/**
	 * Checks the media_type of the passed in image data.
	 *
	 * Possible values are IMAGE, VIDEO, CAROUSEL_ALBUM.
	 *
	 * @param array $instagram_media Instagram media array item.
	 *
	 * @return string $media_type The media type.
	 */
	private function get_instagram_post_media_type( array $instagram_media ) {
		$media_type = $instagram_media['media_type'] ?? '';
		return $media_type;
	}

	/**
	 * Prepare the caption based on the max character length and add ellipses.
	 *
	 * @param string $caption The caption to do work on.
	 *
	 * @return string $altered_caption The altered caption or the default caption if PHP mbstring not enabled/installed on server.
	 */
	private function prepare_caption( $caption ) {

		$ellipses               = apply_filters( 'neb_instagram_feed_widget_caption_ellipses', '...', $caption, $this->instagram_feed_settings );
		$desired_caption_length = apply_filters( 'neb_instagram_feed_widget_caption_length', $this->instagram_feed_settings['image_card']['caption_length'], $caption, $this->instagram_feed_settings );

		if ( strlen( $caption ) < $desired_caption_length ) {
			return $caption;
		}

		/**
		 * This function might be disabled on some hosts.
		 *
		 * But we need it so that we don't break emojis inside captions during the slicing operation.
		 */
		if ( ! function_exists( 'mb_substr' ) ) {
			return $caption;
		}

		$altered_caption = mb_substr( $caption, 0, $desired_caption_length );
		$altered_caption = trim( $altered_caption ) . $ellipses;

		// TODO-UV add link to ellipses
		return $altered_caption;
	}

	/**
	 * Create the Instagram follow button.
	 *
	 * @param array $instagram_feed_settings Normalized Instagram settings from Elementor.
	 *
	 * @return void Button markup.
	 */
	private function do_follow_button( $instagram_feed_settings ) {

		$show = $instagram_feed_settings['follow_btn']['show'];

		if ( empty( $show ) ) {
			return;
		}

		$username = $instagram_feed_settings['follow_btn']['username'];
		$username = str_replace( '@', '', $username );

		$link = 'https://instagram.com/' . trim( $username );

		$icon            = $instagram_feed_settings['follow_btn']['icon'] ? '<i class="fab fa-instagram"></i>' : '';
		$text            = $instagram_feed_settings['follow_btn']['text'];
		$hover_animation = $instagram_feed_settings['follow_btn']['hover_animation'];

		echo wp_kses_post(
			"
			<div id='neb-instagram-feed-follow-btn'>
			<a href='$link' target='_blank'>
			<p class='elementor-animation-$hover_animation'> $icon $text </p>
			</a>
			</div>
			"
		);

	}

	/**
	 * Grid Layout.
	 *
	 * @param array $instagram_media Array of Instagram images to display.
	 */
	private function create_grid_display( array $instagram_media ) {

		$instagram_feed_settings = $this->instagram_feed_settings;

		/**
		 * If lightbox feature is turned on, check if the lightbox option is activated in Elementor Site settings.
		 *
		 * Then replace $instagram_media variable with altered array if it is.
		 */
		if ( $instagram_feed_settings['image_card']['link_behavior'] === 'lightbox' ) {

			$lightbox_instagram_media = $this->handle_lightbox( $instagram_media );

			if ( is_array( $lightbox_instagram_media ) ) {
				$instagram_media = $lightbox_instagram_media;
			} else {
				// Output already escaped in do_error_markup()
				echo $this->do_error_markup( $lightbox_instagram_media ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}

		?>

		<div class="neb-ig-grid-container">
			<?php

			$include_video_thumbnails = $instagram_feed_settings['image_card']['include_video_thumbnails'];

			foreach ( $instagram_media as $image ) :

				$media_type = $this->get_instagram_post_media_type( $image );

				if ( $media_type === 'VIDEO' && empty( $include_video_thumbnails ) ) {
					continue;
				}

				$caption = $image['caption'] ?? '';
				$caption = ( ! empty( $caption ) ) ? $this->prepare_caption( $caption ) : '';

				/**
				 *  If this is an Instagram video post then replace the media_url with the thumbnail_url.
				 *
				 *  The thumbnail_url key only exists for video posts.
				 *  https://developers.facebook.com/docs/instagram-basic-display-api/reference/media/#fields
				 *
				 *  The media URL for Instagram video posts is a direct link to the Instagram video, what we want to display is the link to an image.
				 */
				if ( $media_type === 'VIDEO' ) {
					$image['media_url'] = $image['thumbnail_url'];
				}
				?>
				<div class="neb-ig-grid-item">

					<div class="neb-ig-feed-image-item">

						<?php if ( $instagram_feed_settings['image_card']['include_link'] ) : ?>
							<a href="<?php echo esc_url( $image['permalink'] ); ?>" target="_blank">
						<?php endif; ?>

						<img class="neb-ig-feed-image" src="<?php echo esc_url( $image['media_url'] ); ?>" />

						<?php if ( $instagram_feed_settings['image_card']['caption_style'] === 'overlay' && ! empty( $caption ) ) : ?>
							<div class="neb-ig-feed-caption-overlay"><p><?php echo wp_kses( $caption, array() ); ?></p></div>
						<?php endif; ?>

						<?php if ( $instagram_feed_settings['image_card']['include_link'] ) : ?>
							</a>
						<?php endif; ?>

					</div>

					<?php if ( $instagram_feed_settings['image_card']['display_caption'] ) : ?>
						<div class="neb-ig-caption">
							<?php if ( $instagram_feed_settings['image_card']['caption_style'] === 'bottom' && ! empty( $caption ) ) : ?>
								<div class="neb-ig-feed-caption"><p><?php echo wp_kses( $caption, array() ); ?></p></div>
							<?php endif; ?>
						</div>
					<?php endif; ?>

				</div>
			<?php endforeach; ?>
		</div>

		<?php
		$this->do_follow_button( $instagram_feed_settings );
	}

	/**
	 * Carousel Layout.
	 *
	 *  @param array $instagram_media Array of Instagram images to display.
	 */
	private function create_carousel_display( array $instagram_media ) {

		$instagram_feed_settings = $this->instagram_feed_settings;

		/**
		 * If lightbox feature is turned on, check if the lightbox option is activated in Elementor Site settings.
		 *
		 * Then replace $instagram_media variable with altered array if it is.
		 */
		if ( $instagram_feed_settings['image_card']['link_behavior'] === 'lightbox' ) {

			$lightbox_instagram_media = $this->handle_lightbox( $instagram_media );

			if ( is_array( $lightbox_instagram_media ) ) {
				$instagram_media = $lightbox_instagram_media;
			} else {
				// Output already escaped in do_error_markup()
				echo $this->do_error_markup( $lightbox_instagram_media ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}

		?>

		<!-- Carousel main container -->
		<div class="neb swiper-container">
			<!-- Additional required wrapper -->
			<div class="neb swiper-wrapper">
				<!-- Slides -->
				<?php

				$include_video_thumbnails = $instagram_feed_settings['image_card']['include_video_thumbnails'];

				foreach ( $instagram_media as $image ) :

					$media_type = $this->get_instagram_post_media_type( $image );

					if ( $media_type === 'VIDEO' && empty( $include_video_thumbnails ) ) {
						continue;
					}

					$caption = $image['caption'] ?? '';
					$caption = ( ! empty( $caption ) ) ? $this->prepare_caption( $caption ) : '';

					/**
					 *  If this is an Instagram video post then replace the media_url with the thumbnail_url.
					 *
					 *  The thumbnail_url key only exists for video posts.
					 *  https://developers.facebook.com/docs/instagram-basic-display-api/reference/media/#fields
					 *
					 *  The media URL for Instagram video posts is a direct link to the Instagram video, what we want to display is the link to an image.
					 */
					if ( $media_type === 'VIDEO' ) {
						$image['media_url'] = $image['thumbnail_url'];
					}
					?>
					<div class="swiper-slide">

						<?php if ( $instagram_feed_settings['image_card']['include_link'] ) : ?>
							<a href="<?php echo esc_url( $image['permalink'] ); ?>" target="_blank">
						<?php endif; ?>

						<div class="neb-ig-feed-image-item">
							<img class="neb-ig-feed-image" src="<?php echo esc_url( $image['media_url'] ); ?>" />
							<?php if ( $instagram_feed_settings['image_card']['caption_style'] === 'overlay' && ! empty( $caption ) ) : ?>
								<div class="neb-ig-feed-caption-overlay"><p><?php echo wp_kses( $caption, array() ); ?></p></div>
							<?php endif; ?>
						</div>

						<?php if ( $instagram_feed_settings['image_card']['include_link'] ) : ?>
							</a>
						<?php endif; ?>

						<?php if ( $instagram_feed_settings['image_card']['display_caption'] ) : ?>
							<div class="neb-ig-caption">
								<?php if ( $instagram_feed_settings['image_card']['caption_style'] === 'bottom' && ! empty( $caption ) ) : ?>
									<div class="neb-ig-feed-caption"><p><?php echo wp_kses( $caption, array() ); ?></p></div>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
			<?php if ( $instagram_feed_settings['carousel']['carousel_pagination'] ) : ?>
				<div class="neb-swiper-pagination swiper-pagination"></div>
			<?php endif ?>
		</div>
		<?php
		$this->do_follow_button( $instagram_feed_settings );
	}

	/**
	 * Determine if currently in Editor view, Preview, or Ajax running while in Editor View.
	 *
	 * The is_editor_view() is not reliable enough to truly determine the state of the page.
	 * We need to do additional checks to determine the state.
	 * This method determines whether someone is currently doing any sort of elementor page building and previewing.
	 *
	 * @return bool Whether or not the user is doing some kind of elementor page building
	 */
	private function is_page_building() {

		/**
		 * Instance of the plugin class.
		 *
		 * @var object $instance \Elementor\Plugin
		 */
		$instance = Plugin::$instance;

		// Editor Mode
		if ( $instance->editor->is_edit_mode( get_the_ID() ) ) {
			return true;
		}

		// Preview Mode
		if ( isset( $_GET['preview_id'] ) ) {
			return true;
		}

		// Ajax actions while in editor mode
		$ajax_action = '';
		if ( ! empty( $instance->common ) ) {
			$ajax_action = $instance->common->get_component( 'ajax' )->get_current_action_data();
			$ajax_action = ( ! empty( $ajax_action ) ) ? $ajax_action : '';
		}

		if ( is_array( $ajax_action ) && $ajax_action['action'] === 'render_widget' ) {
			return true;
		}

		if ( is_array( $ajax_action ) && $ajax_action['action'] === 'save_builder' ) {
			return true;
		}

		return false;

	}

	/**
	 * Render widget.
	 */
	protected function render() {

		/**
		* Widget settings saved by the user.
		*
		* We only need to do this on render, no need for construct method.
		*/
		$this->instagram_feed_settings = $this->setup_widget_settings();
		$this->widget_id               = $this->get_id();

		$instagram_feed_settings = $this->instagram_feed_settings;

		$save_to_library = $instagram_feed_settings['api']['save_instagram_media_to_library'];
		$reverse_order   = $instagram_feed_settings['image_card']['reverse_media_order'];
		$display_type    = $instagram_feed_settings['display_type'];

		/**
		 * When visiting page on frontend and 'Save Instagram Media to Library' is active, load media from media library.
		 *
		 * When visiting page on frontend and 'Save Instagram Media to Library' is not active, load media from transient.
		 *
		 * When in Editor mode lets always load live media from the API so users can see their changes in realtime.
		 */
		$instagram_media = '';
		if ( $this->is_page_building() === false && $save_to_library === true ) {
			$instagram_media = $this->get_instagram_media_from_library_cache();
		} elseif ( $this->is_page_building() === false && $save_to_library === false ) {
			$instagram_media = $this->get_instagram_media_from_transient_cache();
		} else {
			$instagram_media = $this->get_instagram_media_from_api( $instagram_feed_settings );
		}

		if ( ! is_array( $instagram_media ) ) {

			$output_error_message = $this->do_error_markup( $instagram_media );
			// Output already escaped in do_error_markup()
			echo $output_error_message; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			return;
		}

		if ( $reverse_order ) {
			$instagram_media = array_reverse( $instagram_media );
		}

		// Debug
		// echo "<p style='background: #000; font-size: 30px; text-align: center; color: #fff'>{$this->get_id()}</p>";

		if ( $display_type === 'grid' ) {
			$this->create_grid_display( $instagram_media );
		} else {
			$this->create_carousel_display( $instagram_media );
		}

	}

}
