<?php
/**
 * Module Class for Typekit fonts.
 *
 * Name:    Typekit fonts module.
 * Author:  Cristian Ungureanu <cristian@themeisle.com>
 *
 * @version 1.0.0
 * @package Neve Pro Addon
 */

namespace Neve_Pro\Modules\Typekit_Fonts;

use Neve_Pro\Core\Abstract_Module;

/**
 * Class Module
 *
 * @package Neve_Pro\Modules\Typekit_Fonts
 */
class Module extends Abstract_Module {

	/**
	 * Define module properties.
	 *
	 * @access  public
	 * @return void
	 *
	 * @version 1.0.0
	 */
	public function define_module_properties() {
		$this->slug              = 'typekit_fonts';
		$this->name              = __( 'Typekit Fonts', 'neve' );
		$this->description       = __( 'Easily embed Adobe Fonts in your WordPress website.', 'neve' );
		$this->documentation     = array(
			'url'   => 'https://docs.themeisle.com/article/1085-typekit-fonts-documentation',
			'label' => __( 'Learn more', 'neve' ),
		);
		$this->theme_min_version = '2.3.13';
		$this->order             = 9;

		$old_setting   = get_option( 'neve_pro_typekit_id' );
		$this->options = [
			[
				'label'   => __( 'Add Typekit Project ID', 'neve' ),
				'options' => [
					'typekit_id'             => [
						'label'             => __( 'Project ID', 'neve' ),
						'type'              => 'text',
						'default'           => ! empty( $old_setting ) ? $old_setting : '',
						'show_in_rest'      => true,
						'sanitize_callback' => [ $this, 'sanitize_typekit_fonts' ],
					],
					'typekit_loading_method' => [
						'label'             => __( 'Loading method', 'neve' ),
						'type'              => 'select',
						'choices'           => [
							'css' => esc_html__( 'CSS Link', 'neve' ),
							'js'  => esc_html__( 'Javascript', 'neve' ),
						],
						'default'           => 'css',
						'show_in_rest'      => true,
						'sanitize_callback' => [ $this, 'sanitize_loading_method' ],
					],
				],
			],
		];
		register_setting(
			'neve_pro_settings',
			'neve_pro_typekit_data',
			[
				'type'         => 'string',
				'show_in_rest' => true,
				'default'      => null,
			]
		);

		add_action( $this->slug . '_disable_actions', array( $this, 'reset_font' ) );
	}

	/**
	 * Make sure that typekit data is correct when it comes through rest update from the dashboard.
	 *
	 * @param string $val The typekit kit ID.
	 * @return string
	 */
	public function sanitize_typekit_fonts( $val ) {
		$typekit_uri = 'https://typekit.com/api/v1/json/kits/' . $val . '/published';
		$response    = $this->remote_get( $typekit_uri );
		if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
			$this->reset_font();
			update_option( 'neve_pro_typekit_data', null );
			return '';
		}

		$typekit_info = [];
		$data         = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( ! isset( $data['kit']['families'] ) ) {
			$this->reset_font();
			update_option( 'neve_pro_typekit_data', null );
			return '';
		}

		$families = array_filter(
			$data['kit']['families'],
			function ( $family ) {
				$fields_to_check = [ 'name', 'css_stack', 'variations', 'slug', 'css_names' ];
				foreach ( $fields_to_check as $field ) {
					if ( ! array_key_exists( $field, $family ) ) {
						return false;
					}
				}
				return true;
			} 
		);

		foreach ( $families as $family ) {
			$family_name = str_replace( ' ', '-', $family['name'] );

			$typekit_info[ $family_name ] = [
				'family'   => $family_name,
				'fallback' => str_replace( '"', '', $family['css_stack'] ),
				'weights'  => [],
			];

			foreach ( $family['variations'] as $variation ) {

				$variations = str_split( $variation );
				$weight     = $variations[1] . '00';

				if ( ! in_array( $weight, $typekit_info[ $family_name ]['weights'], true ) ) {
					$typekit_info[ $family_name ]['weights'][] = $weight;
				}
			}

			$typekit_info[ $family_name ]['slug']      = $family['slug'];
			$typekit_info[ $family_name ]['css_names'] = $family['css_names'];
		}
		update_option( 'neve_pro_typekit_data', wp_json_encode( $typekit_info ) );

		return $val;
	}

	/**
	 * Sanitize the loading method input.
	 *
	 * @param string $val The loading method.
	 * @return string
	 */
	public function sanitize_loading_method( $val ) {
		$available_methods = [ 'css', 'js' ];
		if ( ! in_array( $val, $available_methods, true ) ) {
			return 'css';
		}

		return $val;
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
	 * Run Header Footer Grid Module
	 */
	public function run_module() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_typekit_fonts' ) );
		add_filter( 'style_loader_tag', array( $this, 'add_rel_preload' ), 10, 2 );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_typekit_fonts' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_typekit_fonts' ) );
		add_filter( 'neve_react_controls_localization', array( $this, 'add_typekit_fonts' ) );
	}

	/**
	 * Enqueues a Typekit Font.
	 *
	 * @since 2.3.12
	 */
	public function enqueue_typekit_fonts() {
		$old_option = get_option( 'neve_pro_typekit_id' );
		$new_option = get_option( 'nv_pro_typekit_id' );

		$typekit_id = $old_option;
		if ( ! empty( $new_option ) ) {
			$typekit_id = $new_option;
		}

		if ( empty( $typekit_id ) ) {
			return;
		}

		$loading_type = get_option( 'nv_pro_typekit_loading_method', 'css' );
		if ( neve_is_amp() ) {
			$loading_type = 'css';
		}

		if ( $loading_type === 'css' ) {
			$url = '//use.typekit.net/' . $typekit_id . '.css';
			wp_enqueue_style( 'neve-typekit-font', $url, array(), NEVE_PRO_VERSION );
		}

		if ( $loading_type === 'js' ) {
			wp_register_script( 'neve-typekit-font', NEVE_PRO_INCLUDES_URL . 'modules/typekit_fonts/assets/js/typekit.js', array(), NEVE_PRO_VERSION, true );
			wp_enqueue_script( 'neve-typekit-font' );
			wp_script_add_data( 'neve-typekit-font', 'async', true );
			wp_localize_script( 'neve-typekit-font', 'neveTypekit', array( 'id' => $typekit_id ) );
		}

	}

	/**
	 * Add onload, rel and as for typekit script.
	 * https://joshuatz.com/posts/2019/wordpress-script-and-style-tags-adding-defer-async-and-lazy-load/
	 *
	 * @param string $html   Current html code.
	 * @param string $handle Current script handle.
	 *
	 * @return string
	 */
	public function add_rel_preload( $html, $handle ) {
		if ( is_admin() ) {
			return $html;
		}
		if ( $handle === 'neve-typekit-font' ) {
			// Lazy load with JS, but also but noscript in case no JS
			$no_script = '<noscript>' . $html . '</noscript>';
			// Add onload, rel="preload", as="style", and put together with noscript
			$html = str_replace( 'rel=\'stylesheet\'', 'rel="preload" as="style" onload="this.rel=\'stylesheet\';"', $html ) . $no_script;
		}

		return $html;
	}

	/**
	 * List of Typekit fonts.
	 *
	 * @param  array $localization_data The customizer fonts localization data.
	 * @return array
	 */
	public function add_typekit_fonts( $localization_data ) {
		$fonts         = array();
		$typekit_slugs = array();
		$typekit_fonts = get_option( 'neve_pro_typekit_data' );
		if ( empty( $typekit_fonts ) ) {
			return $localization_data;
		}
		$typekit_fonts = json_decode( $typekit_fonts, true );
		foreach ( $typekit_fonts as $font_name => $font_options ) {
			$fonts[]                                  = $font_options['family'];
			$typekit_slugs[ $font_options['family'] ] = $font_options['css_names'][0];
		}
		$returnable = array_merge(
			[
				'System'  => [],
				'Typekit' => $fonts,
				'Google'  => [],
			],
			$localization_data['fonts']
		);

		$localization_data['fonts']        = $returnable;
		$localization_data['typekitSlugs'] = $typekit_slugs;
		return $localization_data;
	}

	/**
	 * Reset fonts to default if module is disabled.
	 */
	public function reset_font() {
		$typekit_fonts = apply_filters( 'neve_typekit_fonts', array() );

		$headings_font = get_theme_mod( 'neve_headings_font_family', apply_filters( 'neve_headings_default', false ) );
		if ( ! empty( $headings_font ) && in_array( $headings_font, $typekit_fonts, true ) ) {
			set_theme_mod( 'neve_headings_font_family', 'default' );
		}

		$body_font = get_theme_mod( 'neve_body_font_family', apply_filters( 'neve_body_font_default', false ) );
		if ( ! empty( $body_font ) && in_array( $body_font, $typekit_fonts, true ) ) {
			set_theme_mod( 'neve_body_font_family', 'default' );
		}
	}
}
