<?php
/**
 * White Label Main Actions
 *
 * @package Neve_Pro\Modules\White_Label\Includes
 */

namespace Neve_Pro\Modules\White_Label\Includes;

use ReflectionObject;

/**
 * Class Markup
 */
class Markup {

	/**
	 * Branding options
	 *
	 * @var array $branding Branding settings.
	 */
	private $branding;

	/**
	 * Details of the product where the module is called.
	 *
	 * @var array $product_settings Product details.
	 */
	private $product_details;

	/**
	 * Markup constructor.
	 *
	 * @param array $settings Product details.
	 */
	public function __construct( $settings ) {
		$this->product_details = $settings;
		$this->branding        = Admin::get_options();
		$this->disable_sdk_features();

		if ( is_admin() ) {
			add_filter( 'wp_prepare_themes_for_js', array( $this, 'themes_page' ) );
			add_filter( 'all_themes', array( $this, 'network_themes_page' ) );

			add_filter( 'all_plugins', array( $this, 'plugins_page' ) );
			add_filter( 'update_right_now_text', array( $this, 'admin_dashboard_page' ) );
			add_filter( 'neve_disable_starter_sites_admin_notice', array( $this, 'remove_starter_sites_notice' ), 0 );
		}

		add_filter( 'ti_wl_theme_name', array( $this, 'change_theme_name' ) );
		add_filter( 'ti_wl_agency_url', array( $this, 'change_theme_url' ) );
		add_filter( 'ti_wl_plugin_name', array( $this, 'change_plugin_name' ) );
		add_filter( 'neve_dashboard_page_data', array( $this, 'neve_dashboard_localization' ), 100 );
		add_filter( 'ti_wl_copyright', array( $this, 'copyright_default' ) );
		add_action( 'customize_register', array( $this, 'change_customizer_controls' ) );

		add_filter( 'themeisle_sdk_hide_dashboard_widget', '__return_true' );
		add_filter( 'themeisle_sdk_hide_notifications', '__return_true' );

		add_filter( 'neve_pro_filter_dashboard_modules', array( $this, 'remove_module_documentation' ) );

		/**
		 * Disable HFG Descriptions
		 */
		if ( $this->is_theme_whitelabeled() ) {
			add_filter( 'hfg_header_panel_description', '__return_false' );
			add_filter( 'hfg_footer_panel_description', '__return_false' );
			add_filter( 'ti_wl_theme_is_localized', '__return_true' );
			add_filter( 'neve_meta_sidebar_localize_filter', array( $this, 'meta_sidebar_localized_data' ) );
			add_filter( 'neve_pro_react_controls_localization', array( $this, 'add_customizer_vars' ) );
			add_filter( 'neve_is_theme_whitelabeled', '__return_true' );
		}

		if ( $this->is_plugin_whitelabeld() ) {
			add_filter( 'neve_is_plugin_whitelabeled', '__return_true' );
		}
	}

	/**
	 * Add variables to react localization.
	 *
	 * @param array $arr localization array.
	 * @return array
	 */
	public function add_customizer_vars( $arr ) {
		$arr['whiteLabel'] = true;

		return $arr;
	}

	/**
	 * Remove starter sites notice.
	 *
	 * @param bool $value filter value.
	 * @return bool
	 */
	public function remove_starter_sites_notice( $value ) {
		$data = $this->branding;
		if ( isset( $data['starter_sites'] ) && (bool) $data['starter_sites'] === true ) {
			return true;
		}
		return $value;
	}

	/**
	 * Disable sdk features.
	 */
	private function disable_sdk_features() {
		$data = $this->branding;

		// Hide license fields.
		if ( $data['license'] === true ) {
			add_filter( 'neve_pro_addon_hide_license_field', '__return_true' );
			add_filter( 'neve_pro_addon_hide_license_notices', '__return_true' );
		}

		// Disable uninstall feedback for theme.
		if ( ! empty( $data['theme_name'] ) || ! empty( $data['theme_description'] ) || ! empty( $data['screenshot_url'] ) ) {
			add_filter( 'neve_hide_uninstall_feedback', '__return_true' );
		}

		// Disable uninstall feedback for plugin if needed.
		if ( ( ! empty( $data['plugin_name'] ) || ! empty( $data['plugin_description'] ) ) ) {
			add_filter( 'neve_pro_addon_hide_uninstall_feedback', '__return_true' );
		}
	}

	/**
	 * Replace theme name, description, author, screenshot and and parent theme of child themes.
	 *
	 * @param array $themes The themes array.
	 *
	 * @return mixed
	 */
	public function themes_page( $themes ) {
		$theme        = wp_get_theme();
		$theme_parent = $theme->parent();
		if ( ! empty( $theme_parent ) ) {
			$theme = $theme->parent();
		}
		$theme_slug = $theme->get( 'TextDomain' );
		$theme_name = $theme->get( 'Name' );

		if ( ! isset( $themes[ $theme_slug ] ) ) {
			return $themes;
		}

		$data = $this->branding;

		if ( ! empty( $data['theme_name'] ) ) {
			/**
			 * Change theme name.
			 */
			$themes[ $theme_slug ]['name'] = $data['theme_name'];

			/**
			 * Change child-themes parent.
			 */
			foreach ( $themes as $key => $theme ) {
				if ( ! empty( $theme['parent'] ) && $theme_name === $theme['parent'] ) {
					$themes[ $key ]['parent'] = $data['theme_name'];
				}
			}
		}

		if ( ! empty( $data['author_name'] ) ) {
			$author_url                            = empty( $data['author_url'] ) ? '#' : $data['author_url'];
			$themes[ $theme_slug ]['author']       = $data['author_name'];
			$themes[ $theme_slug ]['authorAndUri'] = '<a href="' . esc_url( $author_url ) . '">' . $data['author_name'] . '</a>';
		}

		if ( ! empty( $data['theme_description'] ) ) {
			$themes[ $theme_slug ]['description'] = $data['theme_description'];
		}

		if ( ! empty( $data['screenshot_url'] ) ) {
			$themes[ $theme_slug ]['screenshot'] = array( $data['screenshot_url'] );
		}

		return $themes;
	}

	/**
	 * White labels the theme on the network admin themes page.
	 *
	 * @param array $themes Themes Array.
	 *
	 * @return array
	 */
	public function network_themes_page( $themes ) {
		if ( ! is_network_admin() ) {
			return $themes;
		}

		$theme      = wp_get_theme();
		$theme_slug = $theme->get( 'TextDomain' );
		$theme_name = $theme->get( 'Name' );
		if ( ! isset( $themes[ $theme_slug ] ) ) {
			return $themes;
		}

		$data               = $this->branding;
		$network_theme_data = array();

		if ( ! empty( $data['theme_name'] ) ) {
			$network_theme_data['Name'] = $data['theme_name'];
			foreach ( $themes as $theme_key => $theme ) {
				if ( isset( $theme['parent'] ) && $theme_name === $theme['parent'] ) {
					$themes[ $theme_key ]['parent'] = $data['theme_name'];
				}
			}
		}

		if ( ! empty( $data['theme_description'] ) ) {
			$network_theme_data['Description'] = $data['theme_description'];
		}

		if ( ! empty( $data['author_name'] ) ) {
			$author_url                      = empty( $data['author_url'] ) ? '#' : $data['author_url'];
			$network_theme_data['Author']    = $data['author_name'];
			$network_theme_data['AuthorURI'] = $author_url;
			$network_theme_data['ThemeURI']  = $author_url;
		}

		if ( count( $network_theme_data ) > 0 ) {
			$reflection_object = new ReflectionObject( $themes[ $theme_slug ] );
			$headers           = $reflection_object->getProperty( 'headers' );
			$headers->setAccessible( true );
			$default_properties = $headers->getValue( $themes[ $theme_slug ] );
			$network_theme_data = wp_parse_args( $network_theme_data, $default_properties );
			$headers->setValue( $themes[ $theme_slug ], $network_theme_data );

			$headers_sanitized = $reflection_object->getProperty( 'headers_sanitized' );
			$headers_sanitized->setAccessible( true );
			$default_properties = $headers_sanitized->getValue( $themes[ $theme_slug ] );
			$network_theme_data = wp_parse_args( $network_theme_data, $default_properties );
			$headers_sanitized->setValue( $themes[ $theme_slug ], $network_theme_data );

			// Reset back to private.
			$headers->setAccessible( false );
			$headers_sanitized->setAccessible( false );
		}

		return $themes;
	}

	/**
	 * Replace plugin description and author name and url.
	 *
	 * @param array $plugins Plugins array.
	 *
	 * @return mixed
	 */
	public function plugins_page( $plugins ) {
		if ( empty( $this->product_details['plugin_base_name'] ) ) {
			return $plugins;
		}

		$data = $this->branding;
		$key  = $this->product_details['plugin_base_name'];

		if ( isset( $plugins[ $key ] ) && '' !== $data['plugin_name'] ) {
			$plugins[ $key ]['Name']        = $data['plugin_name'];
			$plugins[ $key ]['Description'] = $data['plugin_description'];
		}

		$author     = $data['author_name'];
		$author_uri = $data['author_url'];

		if ( ! empty( $author ) ) {
			$plugins[ $key ]['Author']     = $author;
			$plugins[ $key ]['AuthorName'] = $author;
		}

		if ( ! empty( $author_uri ) ) {
			$plugins[ $key ]['AuthorURI'] = $author_uri;
			$plugins[ $key ]['PluginURI'] = $author_uri;
		}

		return $plugins;
	}

	/**
	 * White labels the theme on the dashboard 'At a Glance' metabox
	 *
	 * @param mixed $content Content.
	 *
	 * @return string
	 */
	public function admin_dashboard_page( $content ) {
		$data = $this->branding;

		if ( is_admin() && ! empty( $data['theme_name'] ) ) {
			return sprintf( $content, get_bloginfo( 'version', 'display' ), '<a href="themes.php">' . $data['theme_name'] . '</a>' );
		}

		return $content;
	}

	/**
	 * Filter for changing theme name in about page.
	 *
	 * @param string $theme_name Current theme name.
	 *
	 * @return mixed
	 */
	public function change_theme_name( $theme_name ) {
		$data = $this->branding;
		if ( ! empty( $data['theme_name'] ) ) {
			return $data['theme_name'];
		}

		return $theme_name;
	}

	/**
	 * Filter for changing an url with Agency Url.
	 *
	 * @param string $url Current url.
	 *
	 * @return string
	 */
	public function change_theme_url( $url ) {
		$data = $this->branding;
		if ( ! empty( $data['author_url'] ) ) {
			return $data['author_url'];
		}

		return $url;
	}

	/**
	 * Filter for changing theme name in about page.
	 *
	 * @param string $plugin_name The plugin name.
	 *
	 * @return mixed
	 */
	public function change_plugin_name( $plugin_name ) {
		$data = $this->branding;
		if ( ! empty( $data['plugin_name'] ) ) {
			return $data['plugin_name'];
		}

		return $plugin_name;
	}

	/**
	 * Change Neve new dashboard page configuration.
	 *
	 * @param array $config the dashboard localization array.
	 *
	 * @return array
	 */
	public function neve_dashboard_localization( $config ) {
		$data = $this->branding;

		if ( $data['white_label'] ) {
			unset( $config['modules']['white_label'] );
		}

		$should_whitelabel = array_filter(
			$data,
			function ( $item ) {
				return ( ! empty( $item ) );
			}
		);

		if ( empty( $should_whitelabel ) ) {
			return $config;
		}

		$config['whiteLabel'] = array(
			'agencyURL'        => $data['author_url'] ? $data['author_url'] : null,
			'hideStarterSites' => (bool) $data['starter_sites'] === true,
			'hideMyLibrary'    => (bool) $data['my_library'] === true,
			'hideLicense'      => (bool) $data['license'] === true,
		);

		return $config;
	}

	/**
	 * Check if any fields from theme are filled.
	 *
	 * @return bool
	 */
	private function is_theme_whitelabeled() {
		$data = $this->branding;
		if ( array_key_exists( 'theme_name', $data ) && ! empty( $data['theme_name'] ) ) {
			return true;
		}
		if ( array_key_exists( 'theme_description', $data ) && ! empty( $data['theme_description'] ) ) {
			return true;
		}
		if ( array_key_exists( 'screenshot_url', $data ) && ! empty( $data['screenshot_url'] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if any fields from plugin are filled.
	 *
	 * @return bool
	 */
	private function is_plugin_whitelabeld() {
		$data = $this->branding;
		if ( array_key_exists( 'plugin_name', $data ) && ! empty( $data['plugin_name'] ) ) {
			return true;
		}
		if ( array_key_exists( 'plugin_description', $data ) && ! empty( $data['plugin_description'] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Change the default value of a copyright control.
	 *
	 * @param string $value String value.
	 *
	 * @return string
	 */
	public function copyright_default( $value ) {
		$data = $this->branding;
		if ( ! empty( $data['theme_name'] ) ) {
			$author_url = empty( $data['author_url'] ) ? '#' : $data['author_url'];

			return sprintf(
				/* translators: %1$s is Theme Name, %2$s is WordPress */
				esc_html__( '%1$s | Powered by %2$s', 'neve' ),
				wp_kses_post( '<a href="' . esc_url( $author_url ) . '" rel="nofollow">' . $data['theme_name'] . '</a>' ),
				wp_kses_post( '<a href="http://wordpress.org" rel="nofollow">WordPress</a>' )
			);
		}

		return $value;
	}

	/**
	 * Change teheme name in customizer
	 */
	public function change_customizer_controls() {
		global $wp_customize;
		$panel_title                                = $wp_customize->get_panel( 'themes' )->title;
		$wp_customize->get_panel( 'themes' )->title = apply_filters( 'ti_wl_theme_name', $panel_title );

		do_action( 'ti_change_customizer_controls' );
	}

	/**
	 * Remove module documentation links if white label for plugins are not empty.
	 *
	 * @param array $modules Module settings array.
	 *
	 * @return array
	 */
	public function remove_module_documentation( $modules ) {
		$data = $this->branding;
		if ( ! empty( $data['plugin_name'] ) || ! empty( $data['plugin_description'] ) ) {
			foreach ( $modules as $module => $module_settings ) {
				$modules[ $module ]['documentation'] = array();
			}
		}

		return $modules;
	}

	/**
	 * Filter localized data for meta sidebar to add whiteLabeled parameter.
	 *
	 * @param array $data Localized data.
	 *
	 * @return array
	 */
	public function meta_sidebar_localized_data( $data ) {
		$data['whiteLabeled'] = true;
		if ( array_key_exists( 'theme_name', $this->branding ) && ! empty( $this->branding['theme_name'] ) ) {
			$data['whiteLabelThemeName'] = $this->branding['theme_name'];
		}

		return $data;
	}

}
