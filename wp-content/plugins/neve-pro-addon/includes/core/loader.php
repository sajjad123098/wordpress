<?php
/**
 * Main core file.
 *
 * Handles module loading and main hooks.
 *
 * Author:          Andrei Baicus <andrei@themeisle.com>
 * Created on:      2018-12-03
 *
 * @package Neve Pro Addon
 */

namespace Neve_Pro\Core;

use Neve\Core\Factory;
use Neve_Pro\Traits\Core;

/**
 * Class Loader
 *
 * @package Neve_Pro\Core
 */
class Loader {
	use Core;

	/**
	 * Neve_Pro\Loader The single instance of Starter_Plugin.
	 *
	 * @var Loader|null
	 * @access   private
	 * @since    0.0.1
	 */
	private static $_instance = null;

	/**
	 * Modules to load.
	 *
	 * @var array
	 * @access private
	 * @since  0.0.1
	 */
	private $modules = array();

	/**
	 * Holds a list of pluggable modules.
	 *
	 * @since   1.0.0
	 * @access  private
	 * @var array
	 */
	private $pluggable_modules = array();

	/**
	 * Holds pluggable modules and their sub-settings/features for usage by WP CLI
	 *
	 * @see Neve_Pro\CLI::get_module_data()
	 * @var array
	 */
	public static $cli_modules_data = array();

	/**
	 * License data
	 *
	 * @var null|false|\stdClass
	 */
	private static $license_data = null;

	/**
	 * Loader constructor.
	 *
	 * @access public
	 * @since  0.0.1
	 */
	public function __construct() {
		( new \Neve_Pro\Modules\Woocommerce_Booster\Compatibility\Sparks() )->init();
		$this->declare_modules();
		$this->load_modules();
		$this->sdk_compatibility();
		$this->define_widgets_hook();
		$this->add_action_links();
		$this->register_settings();
	}

	/**
	 * Add Neve Pro's action links to plugin list.
	 *
	 * @return void
	 */
	public function add_action_links() {
		add_filter(
			'plugin_action_links_neve-pro-addon/neve-pro-addon.php',
			array( $this, 'create_action_links' )
		);
	}

	/**
	 * Create Neve Pro action links.
	 *
	 * @param string[] $links Current links.
	 * @return string[] Links with activate, renew, upgrade
	 */
	public function create_action_links( $links ) {
		$license_status = $this->get_license_status();

		if ( in_array( $license_status, array( 'not_active', 'invalid' ), true ) ) {
			$links[] = '<a href="' . esc_url( self::get_action_links( 'activate' ) ) . '">' . esc_html__( 'Activate', 'neve' ) . '</a>';
		} elseif ( $license_status === 'active_expired' ) {
			$links[] = '<a href="' . esc_url( self::get_action_links( 'renew' ) ) . '" target="_blank" rel="external noopener noreferrer">' . esc_html__( 'Renew', 'neve' ) . '</a>';
		} elseif ( $this->get_license_type() < 3 ) {
			$links[] = '<a href="' . esc_url( self::get_action_links( 'upgrade' ) ) . '" target="_blank" rel="external noopener noreferrer">' . esc_html__( 'Upgrade', 'neve' ) . '</a>';
		}

		return $links;
	}

	/**
	 * Adds compatiblity with SDK.
	 */
	public function sdk_compatibility() {
		if ( ! defined( 'NEVE_VERSION' ) ) {
			return;
		}
		$key_pro  = str_replace( '-', '_', basename( dirname( NEVE_PRO_BASEFILE ) ) );
		$key_lite = str_replace( '-', '_', basename( get_template_directory() ) );
		/**
		 * Don't use the logger for the pro plugin, use the one from free theme.
		 */
		add_filter(
			'default_option_' . $key_pro . '_logger_flag',
			function ( $value ) {
				return 'no';
			}
		);
		/**
		 * As the free product sends the logging data, we need to pass the status there.
		 */
		add_filter(
			$key_lite . '_license_status',
			function ( $value ) {
				return apply_filters( 'product_neve_license_status', '' );
			}
		);
		add_filter(
			$key_pro . '_lc_no_valid_string',
			function ( $message ) {
				$page = self::has_compatibility( 'theme_dedicated_menu' ) ? 'admin.php' : 'themes.php';
				return str_replace( '<a href="%s">', '<a href="' . admin_url( $page . '?page=neve-welcome#pro' ) . '">', $message );
			}
		);
		add_filter( $key_pro . '_hide_license_field', '__return_true' );
	}

	/**
	 * Declare the modules that will be loaded.
	 *
	 * @access private
	 * @since  0.0.1
	 */
	private function declare_modules() {
		$this->add_pluggable_modules();
		$core_modules = array(
			'Admin\Dashboard',
			'Admin\Starter_Sites',
			'Customizer\Loader',
			'Views\Inline\Injector',
			'Translations\Translations_Manager',
			'Admin\Metabox\Injector',
			'Admin\Custom_Layouts_Cpt',
		);

		// Neve Pro CLI
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			array_push( $core_modules, 'CLI\Bootstrap' );
		}

		$core_modules = apply_filters( 'neve_pro_main_modules', $core_modules );

		$this->modules = array_merge( $this->modules, $core_modules );
	}

	/**
	 * Add pluggable modules.
	 */
	private function add_pluggable_modules() {

		$modules_to_load = array(
			'Modules\Blog_Pro\Module',
			'Modules\Header_Footer_Grid\Module',
			'Modules\Scroll_To_Top\Module',
			'Modules\Performance\Module',
			'Modules\Woocommerce_Booster\Module',
			'Modules\Elementor_Booster\Module',
			'Modules\White_Label\Module',
			'Modules\Custom_Layouts\Module',
			'Modules\LifterLMS_Booster\Module',
			'Modules\Typekit_Fonts\Module',
			'Modules\Easy_Digital_Downloads\Module',
			'Modules\Block_Editor_Booster\Module',
			'Modules\Custom_Sidebars\Module',
		);

		if ( self::has_compatibility( 'restrict_content' ) ) {
			$modules_to_load[] = 'Modules\Access_Restriction\Module';
		}

		if ( self::has_compatibility( 'custom_post_types_enh' ) ) {
			$modules_to_load[] = 'Modules\Post_Type_Enhancements\Module';
		}


		if ( NEVE_DEBUG ) {
			$modules_to_load[] = 'Modules\Debug\Module';
		}
		$modules_to_load = apply_filters( 'neve_pro_add_pluggable_modules', $modules_to_load );
		foreach ( $modules_to_load as $module_name ) {
			$class_name = '\Neve_Pro\\' . $module_name;
			if ( ! class_exists( $class_name ) || ! in_array( 'Neve_Pro\Core\Module_Interface', class_implements( $class_name ), true ) ) {
				continue;
			}

			array_push( $this->modules, $module_name );
		}
	}

	/**
	 * Check Features and register them.
	 *
	 * @access  private
	 * @since   0.0.1
	 */
	private function load_modules() {
		$factory = new Factory( $this->modules, '\\Neve_Pro\\' );
		foreach ( $this->modules as $module_name ) {
			$module = $factory->build( $module_name );
			if ( $module !== null ) {
				$module->init();
				if ( in_array( 'Neve_Pro\Core\Module_Interface', class_implements( $module ), true ) ) {
					$this->pluggable_modules[] = $module;
				}
			}
		}

		// Neve Pro CLI
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			add_action( 'init', array( $this, 'prepare_modules_for_cli' ) );
		}
	}

	/**
	 * Setup needed modules info for usage by WP CLI
	 *
	 * @return void
	 */
	public function prepare_modules_for_cli() {

		foreach ( $this->pluggable_modules as $module_class ) {

			$module = array(
				$module_class->slug => array(
					'name'       => $module_class->name,
					'slug'       => $module_class->slug,
					'default'    => $module_class->get_default_module_status(),
					'option_key' => $module_class->status_key,
				),
			);

			$module_options = $module_class->get_module_options();
			$module_options = reset( $module_options );

			/* If the module has subsettings/features, example WooCommerce Booster */
			if ( isset( $module_options['options'] ) && is_array( $module_options['options'] ) ) {

				$options = $module_options['options'];

				foreach ( $options as $option_key => $details ) {

					$name = isset( $details['label'] ) ? $details['label'] : $option_key;
					$type = $details['type'];

					$module[ $module_class->slug ]['subsettings'][ $option_key ] = array(
						'name'       => ucwords( $name ),
						'type'       => $type,
						'default'    => $details['default'],
						'option_key' => $module_class::get_module_option_key( $option_key ),
					);

					if ( $type === 'select' ) {
						$module[ $module_class->slug ]['subsettings'][ $option_key ]['choices'] = $details['choices'];
					}
				}
			}

			self::$cli_modules_data = array_merge( self::$cli_modules_data, $module );
		}

	}

	/**
	 * Retrieve a list of pluggable modules.
	 *
	 * @return array
	 * @since   1.0.0
	 * @access  public
	 */
	public function get_modules() {
		return $this->pluggable_modules;
	}

	/**
	 * Do we have the compatibility enabled?
	 *
	 * @return bool Compatibility status.
	 */
	public static function has_compatibility( $key ) {
		return defined( 'NEVE_COMPATIBILITY_FEATURES' ) && isset( NEVE_COMPATIBILITY_FEATURES[ $key ] );
	}

	/**
	 * Get the license data.
	 *
	 * @return false|\stdClass
	 */
	public static function get_license_data() {
		if ( is_null( self::$license_data ) ) {
			$option_name = basename( dirname( NEVE_PRO_BASEFILE ) );
			$option_name = str_replace( '-', '_', strtolower( trim( $option_name ) ) );

			self::$license_data = get_option( $option_name . '_license_data' );
		}

		return self::$license_data;
	}

	/**
	 * Flushes $license_data prop content which keeps cache.
	 *
	 * @return void
	 */
	public static function flush_license_cache() {
		self::$license_data = null;
	}

	/**
	 * Main Loader Instance
	 *
	 * @access public
	 * @return Loader Plugin instance.
	 * @since  0.0.1
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Adds the function that adds the widget areas to the widgets_init hook
	 */
	public function define_widgets_hook() {
		add_action( 'widgets_init', [ $this, 'register_sidebars' ] );
	}

	/**
	 * Registers the widget areas
	 */
	public function register_sidebars() {
		for ( $i = 1; $i <= 9; $i++ ) {
			$sidebar_settings = array(
				'name'          => esc_html__( 'Widget Area', 'neve' ) . ' ' . $i,
				'id'            => 'widget-area-' . $i,
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<p class="widget-title">',
				'after_title'   => '</p>',
			);
			register_sidebar( $sidebar_settings );
		}
	}

	/**
	 * Needed register_setting calls. (the settings that are used by globally can be registered here.)
	 *
	 * @return void
	 */
	public function register_settings() {
		register_setting(
			'neve_pro_settings',
			\Neve_Pro\Customizer\Loader::CUSTOMIZER_WC_SPARKS_NOTICE_HIDE_OPT_KEY,
			[
				'type'         => 'boolean',
				'show_in_rest' => true,
				'default'      => false,
			]
		);

		register_setting(
			'neve_pro_settings',
			\Neve_Pro\Customizer\Loader::CUSTOMIZER_WC_SPARKS_INSTALLATION_NOTICE_HIDE_OPT_KEY,
			[
				'type'         => 'string',
				'show_in_rest' => true,
				'default'      => 'show',
			]
		);
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @access public
	 * @since  0.0.1
	 */
	public function __clone() {

	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @access public
	 * @since  0.0.1
	 */
	public function __wakeup() {

	}

}
