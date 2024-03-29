<?php
/**
 * Page Header class for Header Footer Grid.
 *
 * Name:    Header Footer Grid
 * Author:  Bogdan Preda <bogdan.preda@themeisle.com>
 *
 * @version 1.0.0
 * @package HFG
 */

namespace Neve_Pro\Modules\Header_Footer_Grid\Builder;

use HFG\Core\Builder\Abstract_Builder;
use HFG\Main;
use Neve\Customizer\Controls\React\Heading;
use Neve_Pro\Core\Loader;
use WP_Customize_Manager;

/**
 * Class Page_Header
 *
 * @package Neve_Pro\Modules\Header_Footer_Grid\Builder
 */
class Page_Header extends Abstract_Builder {
	/**
	 * Builder name.
	 */
	const BUILDER_NAME            = 'page_header';
	const DISPLAY_LOCATIONS       = 'neve_pro_page_header_display_locations';
	const GLOBAL_SETTINGS_SECTION = 'neve_pro_global_page_header_settings';

	const DISPLAY_ARCHIVE_TITLE = 'neve_pro_page_header_show_archives_title';
	const DISPLAY_SINGLES_TITLE = 'neve_pro_page_header_show_singles_title';

	/**
	 * Can hold additional inline CSS for customizer controls layout
	 *
	 * @var string
	 */
	private $customizer_style = '';

	/**
	 * Header init.compo
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function init() {
		if ( ! self::is_module_activated() ) {
			return;
		}

		$this->set_property( 'title', __( 'Page Header', 'neve' ) );
		$this->set_property(
			'description',
			sprintf(
				/* translators: %s link to documentation */
				esc_html__( 'Design your %1$s by dragging, dropping and resizing all the elements in real-time. %2$s.', 'neve' ),
				/* translators: %s builder type */
				$this->get_property( 'title' ),
				/* translators: %s link text */
				sprintf(
					'<br/><a target="_blank" href="https://docs.themeisle.com/article/1057-header-booster-documentation">%s</a>',
					esc_html__( 'Read full documentation', 'neve' )
				)
			)
		);
		$this->set_property(
			'instructions_array',
			array(
				'description' => sprintf(
					/* translators: 1: builder, 2: builder symbol */
					esc_attr__( 'Welcome to the %1$s builder! Click the %2$s button to add a new component or follow the Quick Links.', 'neve' ),
					$this->get_property( 'title' ),
					'+'
				),
				'quickLinks'  => array(
					'hfg_page_header_layout_top_background' => array(
						'label' => esc_html__( 'Change Top Row Color', 'neve' ),
						'icon'  => 'dashicons-admin-appearance',
					),
				),
			)
		);
		$this->devices           = array(
			'desktop' => __( 'Desktop', 'neve' ),
		);
		$this->devices['mobile'] = __( 'Mobile', 'neve' );
		add_filter( 'hfg_template_locations', array( $this, 'register_template_location' ) );
		add_action( 'neve_after_header_hook', array( $this, 'render_on_neve_page_header' ), 1, 1 );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_neve_page_header_scripts' ) );
	}

	/**
	 * Enqueue inline style to hide previous section that the builder hooked into.
	 */
	public function enqueue_neve_page_header_scripts() {
		$style = '#accordion-section-hfg_page_header_layout_section {display: none !important;}' . $this->customizer_style;
		wp_add_inline_style( 'neve-components', $style );
	}

	/**
	 * Check that the module is enabled.
	 *
	 * @return boolean
	 */
	public static function is_module_activated() {
		return get_option( 'nv_pro_enable_page_header', true );
	}

	/**
	 * Checks if the option is toggled.
	 *
	 * @param string  $name The name of the post type.
	 * @param boolean $default The default value. Used for setting legacy defaults.
	 *
	 * @return boolean
	 */
	private function is_post_type_enabled( $name, $default = false ) {
		return get_theme_mod( 'neve_pro_page_header_' . $name . '_enabled', $default );
	}

	/**
	 * Checks if the archive option is toggled.
	 *
	 * @param string  $name The name of the post type.
	 * @param boolean $default The default value. Used for setting legacy defaults.
	 *
	 * @return boolean
	 */
	private function is_archive_post_type_enabled( $name, $default = false ) {
		return get_theme_mod( 'neve_pro_page_header_' . $name . '_archive_enabled', $default );
	}

	/**
	 * Set defaults based on legacy display value.
	 *
	 * @param string   $name The name of the post type.
	 * @param string[] $legacy_display_locations The legacy value.
	 *
	 * @return bool
	 */
	private function get_legacy_defaults( $name, $legacy_display_locations ) {
		if ( empty( $legacy_display_locations ) ) {
			return false;
		}
		if ( ! in_array( $name, $legacy_display_locations, true ) ) {
			return false;
		}
		return true;
	}

	/**
	 * Invoke page header render on neve hook.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function render_on_neve_page_header() {
		if ( is_page_template() ) {
			return;
		}

		$legacy_display_location = get_theme_mod( self::DISPLAY_LOCATIONS, [ 'post', 'page' ] );
		$default                 = $this->get_legacy_defaults( 'post', $legacy_display_location );
		$will_render             = false;
		if ( $this->is_archive_post_type_enabled( 'post', $default ) && is_home() ) {
			$will_render = true;
		}

		$current_post_type = get_post_type();
		$default           = $this->get_legacy_defaults( $current_post_type, $legacy_display_location );
		if ( $this->is_post_type_enabled( $current_post_type, $default ) && is_singular( $current_post_type ) ) {
			$will_render = true;
		}

		if ( $this->is_archive_post_type_enabled( $current_post_type, $default ) && is_archive() ) {
			$will_render = true;
		}

		if ( $will_render ) {
			do_action( 'hfg_' . self::BUILDER_NAME . '_render' );
		}
	}

	/**
	 * Register a new template location for pro.
	 *
	 * @param array $template_locations An array with places to look for templates.
	 *
	 * @return mixed
	 * @since   1.0.0
	 * @access  public
	 */
	public function register_template_location( $template_locations ) {
		array_push( $template_locations, NEVE_PRO_SPL_ROOT . 'modules/header_footer_grid/templates/' );

		return $template_locations;
	}

	/**
	 * Method called via hook.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function load_template() {
		Main::get_instance()->load( 'page-header-wrapper' );
	}

	/**
	 * Get builder id.
	 *
	 * @return string Builder id.
	 */
	public function get_id() {
		return self::BUILDER_NAME;
	}

	/**
	 * Render builder row.
	 *
	 * @param string $device_id The device id.
	 * @param string $row_id The row id.
	 * @param array  $row_details Row data.
	 */
	public function render_row( $device_id, $row_id, $row_details ) {
		Main::get_instance()->load( 'row-page-wrapper', $row_id );
	}

	/**
	 * Return  the builder rows.
	 *
	 * @return array
	 * @since   1.0.0
	 * @updated 1.0.1
	 * @access  protected
	 */
	protected function get_rows() {
		return array(
			'top'    => array(
				'title'       => esc_html__( 'Page Header Top', 'neve' ),
				'description' => $this->get_property( 'description' ),
			),
			'bottom' => array(
				'title'       => esc_html__( 'Page Header Bottom', 'neve' ),
				'description' => $this->get_property( 'description' ),
			),
		);
	}

	/**
	 * Adds the controls for archives.
	 *
	 * @param WP_Customize_Manager $wp_customize The WP Customize object.
	 * @param string[]             $post_types The available post_type.
	 *
	 * @return WP_Customize_Manager
	 */
	private function add_archives_controls( WP_Customize_Manager $wp_customize, $post_types ) {
		$controls_wrap = ( empty( $post_types ) ) ? 1 : count( $post_types );

		$legacy_display_location = get_theme_mod( self::DISPLAY_LOCATIONS, [ 'post', 'page' ] );

		// Display archive title setting
		$wp_customize->add_setting(
			self::DISPLAY_ARCHIVE_TITLE,
			[
				'transport'         => 'refresh',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => esc_html__( 'Show on Archives', 'neve' ),
			]
		);

		// Display archive title control
		$heading = new Heading(
			$wp_customize,
			self::DISPLAY_ARCHIVE_TITLE,
			[
				'type'             => 'neve_customizer_heading',
				'section'          => self::GLOBAL_SETTINGS_SECTION,
				'label'            => esc_html__( 'Show on Archives', 'neve' ),
				'description'      => esc_html__( 'Select taxonomies', 'neve' ),
				'accordion'        => true,
				'expanded'         => true,
				'controls_to_wrap' => $controls_wrap,
				'class'            => self::DISPLAY_ARCHIVE_TITLE . '-accordion',
			]
		);
		$wp_customize->add_control( $heading );
		$this->customizer_style .= '.' . self::DISPLAY_ARCHIVE_TITLE . '-accordion.expanded {margin-bottom: 0;}';

		$count = 0;
		foreach ( $post_types as $post_type ) {
			$post_type_object = get_post_type_object( $post_type );

			$post_type_archive_control_id = 'neve_pro_page_header_' . $post_type_object->name . '_archive_enabled';
			$wp_customize->add_setting(
				$post_type_archive_control_id,
				// @phpstan-ignore-next-line - default accepts mixed type.
				[
					'transport'         => 'refresh',
					'sanitize_callback' => 'neve_sanitize_checkbox',
					'default'           => ! empty( $legacy_display_location ) && in_array( $post_type_object->name, $legacy_display_location ),
				]
			);

			$wp_customize->add_control(
				$post_type_archive_control_id,
				[
					'type'    => 'neve_toggle_control',
					'label'   => $post_type_object->label,
					'section' => self::GLOBAL_SETTINGS_SECTION,
				]
			);

			if ( $count < ( $controls_wrap - 1 ) ) {
				$this->customizer_style .= '#customize-control-' . $post_type_archive_control_id . ' {margin-bottom: 0;}';
			}
			$count++;
		}

		return $wp_customize;
	}

	/**
	 * Adds the controls for singles post types.
	 *
	 * @param WP_Customize_Manager $wp_customize The WP Customize object.
	 * @param string[]             $post_types The available post_types.
	 *
	 * @return WP_Customize_Manager
	 */
	private function add_singles_controls( WP_Customize_Manager $wp_customize, $post_types ) {
		$controls_wrap = ( empty( $post_types ) ) ? 1 : count( $post_types );

		$legacy_display_location = get_theme_mod( self::DISPLAY_LOCATIONS, [ 'post', 'page' ] );

		// Display singles title setting
		$wp_customize->add_setting(
			self::DISPLAY_SINGLES_TITLE,
			[
				'transport'         => 'refresh',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => esc_html__( 'Show on Singles', 'neve' ),
			]
		);

		// Display singles title control
		$heading = new Heading(
			$wp_customize,
			self::DISPLAY_SINGLES_TITLE,
			[
				'type'             => 'neve_customizer_heading',
				'section'          => self::GLOBAL_SETTINGS_SECTION,
				'label'            => esc_html__( 'Show on Singles', 'neve' ),
				'description'      => esc_html__( 'Select post types', 'neve' ),
				'accordion'        => true,
				'expanded'         => true,
				'controls_to_wrap' => $controls_wrap,
				'class'            => self::DISPLAY_SINGLES_TITLE . '-accordion',
			]
		);
		$wp_customize->add_control( $heading );
		$this->customizer_style .= '.' . self::DISPLAY_SINGLES_TITLE . '-accordion.expanded {margin-bottom: 0;}';

		$count = 0;
		foreach ( $post_types as $post_type ) {
			$post_type_object = get_post_type_object( $post_type );

			$post_type_control_id = 'neve_pro_page_header_' . $post_type_object->name . '_enabled';
			$wp_customize->add_setting(
				$post_type_control_id,
				// @phpstan-ignore-next-line - default accepts mixed type.
				[
					'transport'         => 'refresh',
					'sanitize_callback' => 'neve_sanitize_checkbox',
					'default'           => ! empty( $legacy_display_location ) && in_array( $post_type_object->name, $legacy_display_location ),
				]
			);

			$wp_customize->add_control(
				$post_type_control_id,
				[
					'type'    => 'neve_toggle_control',
					'label'   => $post_type_object->label,
					'section' => self::GLOBAL_SETTINGS_SECTION,
				]
			);

			if ( $count < ( $controls_wrap - 1 ) ) {
				$this->customizer_style .= '#customize-control-' . $post_type_control_id . ' {margin-bottom: 0;}';
			}
			$count++;
		}

		return $wp_customize;
	}

	/**
	 * Returns the available post types.
	 *
	 * @return string[]
	 */
	private function get_available_post_types() {
		return array_filter(
			get_post_types( array( 'public' => true ) ),
			function ( $post_type ) {
				$excluded = array( 'attachment', 'neve_custom_layouts' );
				if ( in_array( $post_type, $excluded, true ) ) {
					return false;
				}

				return true;
			}
		);
	}

	/**
	 * Add section.
	 *
	 * @param WP_Customize_Manager $wp_customize wp customize manager instance.
	 * @return WP_Customize_Manager
	 */
	public function customize_register( WP_Customize_Manager $wp_customize ) {
		// Section register
		$wp_customize->add_section(
			self::GLOBAL_SETTINGS_SECTION,
			[
				'priority' => 100,
				'title'    => esc_html__( 'Global Page Header Settings', 'neve' ),
				'panel'    => $this->panel,
			]
		);

		$post_types = $this->get_available_post_types();

		// Add archives toggles
		$wp_customize = $this->add_archives_controls( $wp_customize, $post_types );

		// Add singles toggles
		$wp_customize = $this->add_singles_controls( $wp_customize, $post_types );

		// Add Link
		if ( Loader::has_compatibility( 'link_control' ) ) {
			$wp_customize->add_setting(
				'neve_pro_page_header_link',
				[
					'default'           => '',
					'sanitize_callback' => 'sanitize_text_field',
				]
			);

			$layouts_url  = admin_url( 'post-new.php?post_type=neve_custom_layouts&is=header', 'https' );
			$link_control = new \Neve\Customizer\Controls\React\Link_Control(
				$wp_customize,
				'neve_pro_page_header_link',
				[
					'type'        => 'neve_link',
					'section'     => self::GLOBAL_SETTINGS_SECTION,
					'label'       => esc_html__( 'Create a custom page header', 'neve' ),
					'url'         => $layouts_url,
					'description' => esc_html__( 'Customise further the display of your page headers using custom layouts', 'neve' ),
				]
			);
			$wp_customize->add_control( $link_control );
		}


		if ( Loader::has_compatibility( 'page_header_support' ) ) {
			// Add control for builder components
			$wp_customize->add_setting(
				'neve_pro_page_header_layout_components',
				[
					'capability'        => 'edit_theme_options',
					'default'           => '',
					'sanitize_callback' => 'sanitize_text_field',
				]
			);

			$wp_customize->add_control(
				'neve_pro_page_header_layout_components',
				[
					'type'    => 'text',
					'section' => self::GLOBAL_SETTINGS_SECTION,
					'label'   => esc_html__( 'Available Components', 'neve' ),
				]
			);
		}

		$wp_customize = parent::customize_register( $wp_customize );

		return $wp_customize;
	}

	/**
	 * Sanitize the display option.
	 *
	 * @param array $array post types array.
	 * @return array
	 */
	public function sanitize_display_post_types( $array ) {
		$available = $this->get_post_types();

		if ( ! is_array( $array ) ) {
			return [ 'post', 'page' ];
		}

		foreach ( $array as $post_type_slug ) {
			if ( ! array_key_exists( $post_type_slug, $available ) ) {
				unset( $array[ $post_type_slug ] );
			}
		}
		return $array;
	}

	/**
	 * Get the available post types.
	 *
	 * @return array
	 */
	private function get_post_types() {
		$post_types = [];
		$types      = get_post_types( [ 'public' => true ], 'objects' );

		foreach ( $types as $post_type => $args ) {
			$post_types[ $post_type ] = $args->label;
		}
		return $post_types;
	}
}
