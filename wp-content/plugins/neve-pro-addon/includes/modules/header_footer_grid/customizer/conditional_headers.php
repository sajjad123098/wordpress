<?php
/**
 * Created on:      2019-12-19
 *
 * @package Neve Pro
 */

namespace Neve_Pro\Modules\Header_Footer_Grid\Customizer;

use Neve\Customizer\Base_Customizer;
use Neve\Customizer\Types\Control;
use Neve\Customizer\Types\Section;
use Neve_Pro\Admin\Custom_Layouts_Cpt;
use Neve_Pro\Core\Loader;
use Neve_Pro\Modules\Custom_Layouts\Admin\Layouts_Metabox;

/**
 * Class Conditional_Headers
 *
 * @package Neve_Pro\Modules\Header_Footer_Grid\Customizer
 */
class Conditional_Headers extends Base_Customizer {
	/**
	 * Theme mods that should be JSON.
	 *
	 * @var array
	 */
	public static $theme_mods_keys = [
		'hfg_header_layout',
		'hfg_header_layout_v2',
		'hfg_header_layout_main_height',
		'hfg_header_layout_top_height',
		'hfg_header_layout_bottom_height',
	];

	/**
	 * Wrapper for get page by path.
	 *
	 * @param string $layout Layout name.
	 * @param string $output Output.
	 * @param string $post_type Post type.
	 *
	 * @return array|\WP_Post|null
	 */
	public static function get_page_by_path( $layout, $output, $post_type ) {
		return function_exists( 'wpcom_vip_get_page_by_path' )
			? wpcom_vip_get_page_by_path( $layout, $output, $post_type )
			: get_page_by_path( $layout, $output, $post_type ); //phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.get_page_by_path_get_page_by_path
	}

	/**
	 * Initialize the class.
	 */
	public function init() {
		parent::init();

		add_filter( 'customize_changeset_save_data', array( $this, 'conditional_headers_filtering' ), 10, 2 );
		add_filter( 'sanitize_post_meta_theme-mods', array( $this, 'allow_escaped_json_meta' ), 10, 3 );
	}

	/**
	 * Utility function to html encode decode custom html value.
	 *
	 * @param array   $theme_mods A list of theme mods.
	 * @param boolean $encode A flag to specify if encode or decode is required.
	 *
	 * @return array
	 */
	private function encode_decode_custom_html( $theme_mods, $encode = false ) {
		foreach ( $theme_mods as $slug => $content ) {
			if ( strpos( $slug, 'custom_html_' ) !== false && strpos( $slug, '_content' ) !== false ) {
				$theme_mods[ $slug ] = ( $encode ) ? htmlentities( $content, ENT_QUOTES ) : html_entity_decode( $content );
			}
		}
		return $theme_mods;
	}

	/**
	 * Allow JSON escaping.
	 *
	 * @param array  $val meta value.
	 * @param string $key meta key.
	 * @param string $type meta type.
	 *
	 * @return string
	 */
	public function allow_escaped_json_meta( $val, $key, $type ) {
		$val = $this->encode_decode_custom_html( $val, true );
		return wp_json_encode( wp_slash( $val ), JSON_UNESCAPED_UNICODE );
	}

	/**
	 * Catch theme mods for conditional headers and use them as meta.
	 *
	 * @param array $data data coming from the customizer.
	 * @param array $filter_context data about the current change-set.
	 *
	 * @return array
	 */
	public function conditional_headers_filtering( $data, $filter_context ) {
		$prefix = get_option( 'stylesheet' ) . '::';
		$mod    = $prefix . 'neve_header_conditional_selector';
		if ( $filter_context['status'] !== 'publish' || ! isset( $data[ $mod ] ) ) {
			return $data;
		}

		if ( isset( $data[ $mod ]['value']['delete'] ) ) {
			$to_delete = $data[ $mod ]['value']['delete'];
			if ( is_array( $to_delete ) ) {
				foreach ( $to_delete as $layout ) {
					$layout_to_delete = self::get_page_by_path( $layout, OBJECT, 'neve_custom_layouts' );
					if ( $layout_to_delete !== null ) {
						$post_id = $layout_to_delete->ID;
						wp_delete_post( $post_id, true );
					}
				}
			}
		}

		if ( isset( $data[ $mod ]['value']['add'] ) ) {
			$to_add = $data[ $mod ]['value']['add'];
			if ( is_array( $to_add ) ) {
				foreach ( $to_add as $layout => $name ) {
					$layout_to_add = self::get_page_by_path( $layout, OBJECT, 'neve_custom_layouts' );
					if ( $layout_to_add === null ) {
						$post_id = wp_insert_post(
							array(
								'post_title'  => $name,
								'post_name'   => $layout,
								'post_status' => 'publish',
								'post_type'   => 'neve_custom_layouts',
								'post_author' => get_current_user_id(),
							)
						);
						update_post_meta( $post_id, 'header-layout', true );
					}
				}
			}
		}

		if ( isset( $data[ $mod ]['value']['headers'] ) ) {
			foreach ( $data[ $mod ]['value']['headers'] as $header_slug => $args ) {
				if ( $header_slug === 'default' ) {
					if ( ! isset( $args['mods'] ) ) {
						continue;
					}

					// Set default theme mods.
					foreach ( $args['mods'] as $mod_key => $value ) {
						$value = in_array( $mod_key, self::$theme_mods_keys, true ) ? wp_json_encode( $value ) : $value;
						set_theme_mod( $mod_key, $value );
						unset( $data[ $prefix . $mod_key ] );
					}

					continue;
				}

				$custom_layout = self::get_page_by_path( $header_slug, OBJECT, 'neve_custom_layouts' );
				if ( $custom_layout === null ) {
					continue;
				}

				if ( isset( $args['rules'] ) ) {
					$rules = $args['rules'];
					foreach ( $rules as $rule_group_index => $rule_group ) {

						if ( empty( $rule_group ) ) {
							unset( $rules[ $rule_group_index ] );
							continue;
						}

						foreach ( $rule_group as $index => $rule ) {
							if ( empty( $rule ) ) {
								unset( $rules[ $rule_group_index ][ $index ] );
							}

							if ( empty( $rule['root'] ) || empty( $rule['end'] ) ) {
								unset( $rules[ $rule_group_index ][ $index ] );
							}
						}

						if ( empty( $rule_group ) ) { // @phpstan-ignore-line This is a safeguard.
							unset( $rules[ $rule_group_index ] );
						}
					}
					update_post_meta( $custom_layout->ID, Layouts_Metabox::META_CONDITIONAL, wp_json_encode( $rules ) );
				}

				if ( isset( $args['mods'] ) ) {
					$page = self::get_page_by_path( $header_slug, OBJECT, 'neve_custom_layouts' );
					if ( $page === null ) {
						continue;
					}

					$new_meta = [];
					$old_meta = json_decode( get_post_meta( $page->ID, 'theme-mods', true ), true );


					$to_update = $args['mods'];

					foreach ( $to_update as $mod_key => $value ) {
						$new_meta[ $mod_key ] = $value;
						if ( isset( $data[ $prefix . $mod_key ] ) ) {
							unset( $data[ $prefix . $mod_key ] );
						}
					}

					if ( is_array( $old_meta ) ) {
						$new_meta = array_merge( $old_meta, $new_meta );
					}
					// Check the `allow_escaped_json_meta` method in this class.
					// We have to filter this late because WP forces wp_unslash.
					update_post_meta( $page->ID, 'theme-mods', $new_meta );
				}
			}

			unset( $data[ $mod ] );

			delete_transient( 'custom_layouts_post_map_v3' );
		}

		return $data;
	}

	/**
	 * Add customizer controls.
	 */
	public function add_controls() {
		$this->add_section(
			new Section(
				'neve_pro_global_header_settings',
				[
					'priority' => 100,
					'title'    => esc_html__( 'Global Header Settings', 'neve' ),
					'panel'    => 'hfg_header',
				]
			)
		);

		$this->add_control(
			new Control(
				'neve_global_header',
				array(
					'transport'         => 'refresh',
					'sanitize_callback' => 'neve_sanitize_checkbox',
					'default'           => true,
				),
				array(
					'label'    => esc_html__( 'Show This Header Site-wide', 'neve' ),
					'section'  => 'neve_pro_global_header_settings',
					'type'     => 'neve_toggle_control',
					'priority' => 20,
				)
			)
		);
		$this->add_control(
			new Control(
				'neve_header_conditional_selector',
				[
					'transport'         => 'postMessage',
					'sanitize_callback' => function ( $val ) {
						return $val;
					},
					'default'           => [
						'headers'       => $this->get_header_layouts(),
						'currentHeader' => 'default',
					],
				],
				[
					'section'         => 'neve_pro_global_header_settings',
					'type'            => 'neve_context_conditional_selector',
					'active_callback' => [ $this, 'is_not_global_header' ],
					'priority'        => 20,
				],
				Loader::has_compatibility( 'php81_react_ctrls_fix' ) ? '\Neve\Customizer\Controls\React\Conditional_Selector' : null
			)
		);
	}

	/**
	 * Is not set to global header.
	 *
	 * @return bool
	 */
	public function is_not_global_header() {
		return ! get_theme_mod( 'neve_global_header' );
	}

	/**
	 * Get header layouts already available.
	 *
	 * @return array
	 */
	private function get_header_layouts() {
		$posts              = [];
		$default_theme_mods = new \StdClass();
		$headers            = Custom_Layouts_Cpt::get_conditional_headers();

		$posts['default'] = [
			'label' => __( 'Default', 'neve' ),
			'mods'  => $default_theme_mods,
		];

		foreach ( $headers as $id => $theme_mods ) {
			$theme_mods = json_decode( $theme_mods, true );
			if ( ! empty( $theme_mods ) ) {
				$theme_mods = $this->encode_decode_custom_html( $theme_mods );
			}

			if ( $theme_mods === null ) {
				$theme_mods = $default_theme_mods;
			}

			$post = get_post( $id );
			if ( empty( $post ) ) {
				continue;
			}
			$posts[ $post->post_name ] = [
				'label' => $post->post_title,
				'rules' => json_decode( get_post_meta( $post->ID, Layouts_Metabox::META_CONDITIONAL, true ), true ),
				'mods'  => $theme_mods,
			];
		}

		return $posts;
	}
}
