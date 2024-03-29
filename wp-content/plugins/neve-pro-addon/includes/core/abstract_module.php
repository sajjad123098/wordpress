<?php
/**
 * Abstract Module Class for Neve Pro Addon Modules.
 *
 * Name:    Neve Pro Addon
 * Author:  Bogdan Preda <friends@themeisle.com>
 *
 * @version 1.0.0
 * @package Neve Pro Addon
 */

namespace Neve_Pro\Core;

use Neve_Pro\Traits\Core;
use Neve\Admin\Dashboard\Plugin_Helper;

/**
 * Class Abstract_Module
 *
 * @package Neve_Pro\Core
 */
abstract class Abstract_Module implements Module_Interface {
	use Core;
	const TOP_LEVEL_BLOCK_FLAG = 'isTopLevelBlock';
	/**
	 * The module slug.
	 * Must be unique.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @var string $slug
	 */
	public $slug = 'module-slug';
	/**
	 * The module name.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @var string
	 */
	public $name = 'Unnamed Module';
	/**
	 * The module description.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @var string
	 */
	public $description = 'An unnamed module.';
	/**
	 * Optional links for frontend tiles.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @var array
	 */
	public $links = array();
	/**
	 * Optional documentation links.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @var array
	 */
	public $documentation = array();
	/**
	 * Optional order for the module when displayed in frontend.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @var int
	 */
	public $order = 0;
	/**
	 * Dependent plugins for the module.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @var array
	 */
	public $dependent_plugins = array();

	/**
	 * Allow manage plugins in Neve Dashboard -> module panel.
	 * That allows activate/install plugins which listed in $this->dependent_plugins.
	 *
	 * @var bool
	 */
	public $manageable_plugins = false;

	/**
	 * Button Labels of Manageable Plugins
	 *
	 * @var array
	 */
	public $manageable_plugins_labels = [];

	/**
	 * Type of license.
	 *
	 * @var int
	 */
	public $min_req_license = 1;
	/**
	 * Minimum version of theme that the module requires.
	 *
	 * @var string
	 */
	public $theme_min_version = '2.3.10';
	/**
	 * Default state for the module.
	 *
	 * @since   1.0.0
	 * @access  protected
	 * @var bool
	 */
	protected $active = false;
	/**
	 * Class namespace.
	 *
	 * @var null|string Namespace.
	 */
	protected $namespace = null;

	/**
	 * Does it use Dynamic style?
	 *
	 * @var bool Dynamic style flag.
	 */
	public $has_dynamic_style = false;

	/**
	 * Module status key.
	 *
	 * @var string
	 */
	public $status_key;
	/**
	 * Default status of module.
	 *
	 * @var bool
	 */
	private $default;

	/**
	 * Module Options.
	 *
	 * @var array
	 */
	protected $options = [];

	/**
	 * Abstract_Module constructor.
	 */
	public function __construct() {
		$this->define_module_properties();
		$this->status_key = 'nv_pro_' . $this->slug . '_status';
		$this->default    = $this->get_default_module_status();
		$this->add_toggle_option();
		$this->set_active_status();

		if ( $this->is_active() ) {
			add_filter( $this->status_key, '__return_true' );
		}
		add_action( 'init', [ $this, 'add_module_settings' ] );
	}

	/**
	 * Get module subfeatures/settings also reffered to as options.
	 *
	 * @return array
	 */
	public function get_module_options() {
		return $this->options;
	}

	/**
	 * Create the option key stored in the DB for a module's subfeature/setting also referred to as option.
	 *
	 * @param mixed $slug The slug of the option.
	 * @return string
	 */
	public static function get_module_option_key( $slug ) {
		return 'nv_pro_' . $slug;
	}

	/**
	 * Add the module settings.
	 */
	public function add_module_settings() {
		if ( empty( $this->options ) ) {
			return;
		}

		$arg_map = [
			'toggle'       => [
				'type'    => 'boolean',
				'default' => false,
			],
			'text'         => [
				'type'    => 'string',
				'default' => '',
			],
			'select'       => [
				'type'    => 'string',
				'default' => '',
			],
			'multi_select' => [
				'type'    => 'array',
				'default' => '',
			],
			'react'        => [
				'type'    => 'string',
				'default' => '',
			],
		];

		foreach ( $this->options as $option_group ) {

			if ( ! isset( $option_group['options'] ) || empty( $option_group['options'] ) ) {
				continue;
			}

			foreach ( $option_group['options'] as $slug => $args ) {
				if ( ! isset( $args['type'] ) || ! in_array( $args['type'], array_keys( $arg_map ), true ) ) {
					continue;
				}
				$type         = $args['type'];
				$setting_args = [
					'type'         => $arg_map[ $type ]['type'],
					'show_in_rest' => isset( $args['show_in_rest'] ) ? $args['show_in_rest'] : true,
					'default'      => isset( $args['default'] ) ? $args['default'] : $arg_map[ $type ]['default'],
				];

				if ( isset( $args['sanitize_callback'] ) ) {
					$setting_args['sanitize_callback'] = $args['sanitize_callback'];
				}

				register_setting(
					'neve_pro_settings',
					self::get_module_option_key( $slug ),
					$setting_args
				);
			}
		}

	}

	/**
	 * Get the default module status.
	 *
	 * Handles migration from old dashboard options.
	 *
	 * @return bool
	 */
	public function get_default_module_status() {
		$old_settings = get_option( NEVE_PRO_NAMESPACE . '_settings' );

		$this->default = false;
		if ( $old_settings !== false ) {
			$old_settings = json_decode( $old_settings, true );
			if ( isset( $old_settings['modules_status'] ) && isset( $old_settings['modules_status'][ $this->slug ] ) ) {
				return $old_settings['modules_status'][ $this->slug ] === 'enabled';
			}
		}

		if ( $this->is_available_for_license() ) {
			return true;
		}

		return false;
	}

	/**
	 * Register module toggle option.
	 */
	public function add_toggle_option() {
		register_setting(
			'neve_pro_settings',
			$this->status_key,
			[
				'type'         => 'boolean',
				'show_in_rest' => true,
				'default'      => $this->default,
			]
		);
	}

	/**
	 * Check if module is active.
	 *
	 * @return bool
	 */
	protected function is_active() {
		return (bool) get_option( $this->status_key, $this->default );
	}

	/**
	 * Define module properties.
	 *
	 * @access  public
	 * @return void
	 *
	 * @version 1.0.0
	 */
	abstract public function define_module_properties();

	/**
	 * Set active status depending on the license.
	 *
	 * @return void
	 */
	private function set_active_status() {
		$this->active = apply_filters( 'nv_pro_module_active_' . $this->slug, $this->is_available_for_license() );
		if ( $this->active ) {
			return;
		}
		$this->active = (bool) get_option( $this->status_key, $this->default );
	}

	/**
	 * Checks if module is available for current license.
	 *
	 * @return bool
	 */
	private function is_available_for_license() {
		$availability = $this->get_license_type();

		if ( $availability >= $this->min_req_license ) {
			return true;
		}

		return false;
	}

	/**
	 * Check theme version before module load.
	 *
	 * @return bool
	 */
	protected function is_min_req_theme_version() {
		if ( version_compare( NEVE_VERSION, $this->theme_min_version ) < 0 ) {
			return false;
		}

		return true;
	}

	/**
	 * Retrieve info about the module.
	 *
	 * @return array
	 * @since   1.0.0
	 * @access  public
	 */
	public function get_module_info() {
		$info = array(
			$this->slug => array(
				'nicename'          => $this->name,
				'description'       => $this->description,
				'order'             => $this->order,
				'availabilityLevel' => $this->min_req_license,
				'options'           => $this->options,
			),
		);

		$info[ $this->slug ]['required_actions'] = $this->check_theme_version();
		if ( $info[ $this->slug ]['required_actions'] === false && is_array( $this->dependent_plugins ) && ! empty( $this->dependent_plugins ) ) {
			$info[ $this->slug ]['required_actions'] = $this->check_dependent_plugins();
		}

		if ( is_array( $this->links ) && ! empty( $this->links ) ) {
			$info[ $this->slug ]['links'] = $this->links;
		}

		if ( is_array( $this->documentation ) && ! empty( $this->documentation ) ) {
			$info[ $this->slug ]['documentation'] = $this->documentation;
		}

		if ( $this->manageable_plugins ) {
			$info[ $this->slug ]['manageableDependentPlugins'] = true;
			$info[ $this->slug ]['manageablePluginsLabels']    = $this->markup_manageable_plugin_labels( $this->manageable_plugins_labels, $this->dependent_plugins );
			$info[ $this->slug ]['dependentPlugins']           = [];

			foreach ( $this->dependent_plugins as $plugin_slug => $plugin_info ) {
				$info[ $this->slug ]['dependentPlugins'][] = [
					'name'           => $plugin_info['name'],
					'slug'           => $plugin_slug,
					'pluginBasename' => $plugin_info['path'],
					'pluginState'    => ( new Plugin_Helper() )->get_plugin_state( $plugin_slug ),
					'activateURL'    => ( new Plugin_Helper() )->get_plugin_action_link( $plugin_slug ),
					'description'    => '',
				];
			}
		} else {
			$info[ $this->slug ]['manageableDependentPlugins'] = false;
		}

		return $info;
	}

	/**
	 * Inject Plugin names if there are.
	 *
	 * @return array
	 */
	private function markup_manageable_plugin_labels( $labels, $dependent_plugins ) {
		$plugin_labels = [];

		foreach ( $dependent_plugins as $slug => $plugin ) {
			$plugin_labels[ $slug ] = [];
			foreach ( $labels as $label_key => $label_value ) {
				$plugin_labels[ $slug ][ $label_key ] = sprintf( $label_value, esc_html( $plugin['name'] ) );
			}
		}

		return $plugin_labels;
	}

	/**
	 * Check if the theme should update.
	 *
	 * @return string|false
	 */
	private function check_theme_version() {
		if ( ! $this->is_min_req_theme_version() ) {
			$link = admin_url( 'themes.php' );

			return sprintf(
				'<a href="%1$s" target="_blank"><span class="dashicons dashicons-warning"></span> <span>%2$s</span> </a>',
				esc_url( $link ),
				esc_html__( 'You need to update the theme in order to use this module!', 'neve' )
			);
		}

		return false;
	}

	/**
	 * Check dependent plugins.
	 *
	 * @return string|false
	 */
	protected function check_dependent_plugins() {
		if ( empty( $this->dependent_plugins ) ) {
			return false;
		}
		$link = '';

		if ( array_key_exists( 'relation', $this->dependent_plugins ) && $this->dependent_plugins['relation'] === 'OR' ) {
			foreach ( $this->dependent_plugins as $slug => $plugin ) {
				if ( $slug === 'relation' ) {
					continue;
				}
				if ( is_plugin_active( $plugin['path'] ) ) {
					return false;
				}
			}
		}

		foreach ( $this->dependent_plugins as $slug => $plugin ) {
			if ( $slug === 'relation' ) {
				continue;
			}
			if ( ! is_plugin_active( $plugin['path'] ) ) {
				$state = $this->check_plugin_state( $plugin['path'] );
				if ( $state === 'install' ) {
					if ( isset( $plugin['external'] ) ) {
						$link = esc_url( $plugin['external'] );
					} else {
						$link = wp_nonce_url(
							add_query_arg(
								array(
									'action' => 'install-plugin',
									'plugin' => $slug,
								),
								admin_url( 'update.php' )
							),
							'install-plugin_' . $slug
						);
					}
				}
				if ( $state === 'activate' ) {
					$link = wp_nonce_url(
						add_query_arg(
							array(
								'action' => 'activate',
								'plugin' => $plugin['path'],
							),
							admin_url( 'plugins.php' )
						),
						'activate-plugin_' . $plugin['path']
					);
				}

				$stat_strings = array(
					'install'  => __( 'installing', 'neve' ),
					'activate' => __( 'activating', 'neve' ),
				);

				$need_admin_action = ( $state === 'install' && ! current_user_can( 'install_plugins' ) ) || ( $state === 'activate' && ! current_user_can( 'activate_plugins' ) );
				if ( $need_admin_action ) {
					return sprintf(
					/* translators: %s - Required action text */
						'<p><span class="dashicons dashicons-warning"></span><span>%s</span></p>',
						sprintf(
						/* translators: %s - plugin to activate */
							__( 'The module requires %s plugin. Ask your admin to activate it.', 'neve' ),
							$stat_strings[ $state ] . ' ' . $plugin['name']
						)
					);
				}
				return sprintf(
					/* translators: %1$s - plugin install url, %2$s - Required action text */
					'<a href="%1$s" target="_blank"><span class="dashicons dashicons-warning"></span><span>%2$s</span></a>',
					esc_url( $link ),
					sprintf(
						/* translators: %s - plugin to activate */
						__( 'The module requires %s plugin.', 'neve' ),
						$stat_strings[ $state ] . ' ' . $plugin['name']
					)
				);
			}
		}

		return false;
	}

	/**
	 * Check plugin state.
	 *
	 * @param string $plugin_path Plugin path.
	 *
	 * @return string
	 */
	public function check_plugin_state( $plugin_path ) {
		if ( file_exists( ABSPATH . 'wp-content/plugins/' . $plugin_path ) ) {
			return 'activate';
		}

		return 'install';
	}
	/**
	 * Init module function
	 *
	 * @return void
	 */
	public function init() {
		if ( ! $this->is_available_for_license() ) {
			return;
		}

		if ( ! $this->is_min_req_theme_version() ) {
			return;
		}

		if ( ! $this->should_load() ) {
			return;
		}
		if ( defined( 'NEVE_NEW_DYNAMIC_STYLE' ) && $this->has_dynamic_style && $this->is_active() ) {
			try {
				$dynamic_style_class = ( new \ReflectionClass( $this ) )->getNamespaceName() . '\\Dynamic_Style';
				( new $dynamic_style_class() )->register_hooks();
			} catch ( \ReflectionException $exception ) {
				// Not handled.
			}
		}
		$this->run_module();
	}

	/**
	 * Check if module is active.
	 *
	 * @param string $status_key module status key - see status key of Abstract_module class.
	 *
	 * @return bool
	 */
	private function get_module_status( $status_key ) {
		return get_option( $status_key, $this->default );
	}

	/**
	 * Check if module should load
	 *
	 * @return bool
	 */
	public function should_load() {
		return $this->get_module_status( $this->status_key );
	}

	/**
	 * Run module's functions.
	 *
	 * @return void
	 */
	abstract function run_module();
}
