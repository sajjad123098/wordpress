<?php
/**
 * The customizer addons loader class.
 *
 * Author:          Andrei Baicus <andrei@themeisle.com>
 * Created on:      2018-12-03
 *
 * @package Neve Pro Addon
 */

namespace Neve_Pro\Customizer;

use Neve\Core\Factory;
use Neve_Pro\Admin\Conditional_Display;
use Neve_Pro\Traits\Core;
use Neve_Pro\Customizer\Sections\Neve_Pro_Main;

/**
 * Class Loader
 *
 * @since   0.0.1
 * @package Neve Pro Addon
 */
class Loader {
	use Core;

	const CUSTOMIZER_WC_SPARKS_NOTICE_HIDE_OPT_KEY              = 'nv_pro_hide_sparks_customizer_notice';
	const CUSTOMIZER_WC_SPARKS_INSTALLATION_NOTICE_HIDE_OPT_KEY = 'nv_pro_sparks_installation_notice';

	/**
	 * Customizer modules.
	 *
	 * @access private
	 * @since  0.0.1
	 * @var array
	 */
	private $modules = array();

	/**
	 * Loader constructor.
	 *
	 * @access public
	 * @since  0.0.1
	 */
	public function __construct() {
		add_action( 'customize_preview_init', array( $this, 'enqueue_customizer_preview' ) );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_customizer_controls' ) );
	}

	/**
	 * Initialize the customizer functionality
	 *
	 * @access public
	 * @since  0.0.1
	 */
	public function init() {
		global $wp_customize;

		if ( ! isset( $wp_customize ) ) {
			return;
		}

		$this->define_modules();
		$this->load_modules();
		add_action( 'customize_register', array( $this, 'init_custom_main_section' ) );
	}

	/**
	 * Define the modules that will be loaded.
	 *
	 * @access private
	 * @since  0.0.1
	 */
	private function define_modules() {
		$this->modules = apply_filters(
			'neve_pro_filter_customizer_modules',
			array(
				'Customizer\Options\Main',
			)
		);
	}

	/**
	 * Enqueue customizer controls script.
	 *
	 * @access public
	 * @since  0.0.1
	 */
	public function enqueue_customizer_controls() {
		// Legacy controls.
		wp_enqueue_script( 'neve-pro-controls', NEVE_PRO_INCLUDES_URL . 'customizer/controls/js/build/bundle.js', array( 'customize-controls' ), NEVE_PRO_VERSION, true );
		$this->rtl_enqueue_style( 'neve-pro-controls', NEVE_PRO_INCLUDES_URL . 'customizer/controls/css/customizer-controls.min.css', array(), NEVE_PRO_VERSION );
		$editor_dependencies = include_once plugin_dir_path( __FILE__ ) . 'controls/react/build/controls.asset.php';

		wp_register_script( 'neve-pro-react-controls', NEVE_PRO_INCLUDES_URL . 'customizer/controls/react/build/controls.js', $editor_dependencies['dependencies'], $editor_dependencies['version'], true );

		$localization = apply_filters(
			'neve_pro_react_controls_localization',
			[
				'conditionalRules'             => $this->get_conditional_rules_array(),
				'headerControls'               => [ 'hfg_header_layout' ],
				'newBuilder'                   => function_exists( 'neve_is_new_builder' ) && neve_is_new_builder(),
				'multiSelectRules'             => Conditional_Display::MULTISELECT_RULES,
				'conditionTextMap'             => Conditional_Display::create_custom_layouts_condition_text_map(),
				'sparksNotice'                 => $this->show_sparks_notice_in_customizer() ? sprintf(
					/* translators: %s is link of Sparks dashboard */
					__( 'Please note that some of the options previously available in the WooCommerce Booster module are now part of the Sparks for WooCommerce settings page in %s', 'neve' ),
					/* translators: %s is link text */
					sprintf(
						'<strong><a href="' . get_dashboard_url( 0, 'options-general.php?page=sparks' ) . '" target="_blank">%s</a></strong>',
						__( 'Dashboard -> Settings', 'neve' )
					)
				) : '',
				'sparksInstallationNotice'     => $this->show_sparks_notice_installation_in_customizer() ? sprintf(
					/* translators: %s is link of Sparks dashboard */
					__( 'Please note that you must have Sparks plugin installed and activated to empower WooCommerce Booster. %s', 'neve' ),
					/* translators: %s is link text */
					sprintf(
						'<strong><a href="' . esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=install_sparks' ), 'install_sparks' ) ) . '">%s</a></strong>',
						__( 'Install now', 'neve' )
					)
				) : '',
				'wooCartCheckoutCompatibility' => $this->show_woo_blocks_compatibility_notice() ? sprintf(
					/* translators: %s is link to documentation */
					__( 'Please note that these settings will not be available while using WooCommerce blocks for the Cart and Checkout pages. You can read more in our %s.', 'neve' ),
					/* translators: %s is link text */
					sprintf(
						'<strong><a href="' . tsdk_utmify( 'https://docs.themeisle.com/article/1058-woocommerce-booster-documentation', 'wooboster', 'customizer' ) . '" target="_blank">%s</a></strong>',
						__( 'Documentation', 'neve' )
					)
				) : '',
				'usesWooCartBlock'             => (int) $this->woo_page_uses_block( 'cart', 'wp:woocommerce/cart' ),
				'usesWooCheckoutBlock'         => (int) $this->woo_page_uses_block( 'checkout', 'wp:woocommerce/checkout' ),
				'missingLicenseNotice'         => $this->get_missing_license_notice(),
			]
		);

		wp_localize_script( 'neve-pro-react-controls', 'NeveProReactCustomize', $localization );
		wp_enqueue_script( 'neve-pro-react-controls' );

		$this->rtl_enqueue_style( 'neve-pro-react-controls', NEVE_PRO_INCLUDES_URL . 'customizer/controls/react/build/style-controls.css', [ 'wp-components' ], NEVE_PRO_VERSION );
	}

	/**
	 * Should be shown a customizer notice regarding with Sparks plugin in Customizer->WooCommerce panel?
	 *
	 * @return bool
	 */
	private function show_sparks_notice_in_customizer(): bool {
		return ( ! get_option( self::CUSTOMIZER_WC_SPARKS_NOTICE_HIDE_OPT_KEY, false ) ) && defined( 'SPARKS_WC_VERSION' );
	}

	/**
	 * Should be shown a customizer notice (for Business and Agency) for installing Sparks plugin to access Woo Booster module?
	 *
	 * @return bool
	 */
	private function show_sparks_notice_installation_in_customizer(): bool {
		if ( 'hide' === get_option( self::CUSTOMIZER_WC_SPARKS_INSTALLATION_NOTICE_HIDE_OPT_KEY, 'show' ) ) {
			return false;
		}

		$installed_plugins   = get_plugins();
		$is_sparks_installed = array_key_exists( 'sparks-for-woocommerce/sparks-for-woocommerce.php', $installed_plugins );
		$plan_id             = apply_filters( 'product_neve_license_plan', -1 );
		$plan                = $plan_id > -1 ? $this->tier_map[ $plan_id ] : -1;
		return ! $is_sparks_installed && 3 <= $plan;
	}

	/**
	 * Check whether the WooCommerce  page uses specific woocommerce block.
	 *
	 * @param string $page_slug The page slug.
	 * @param string $block_name The block name. E.g.` wp:woocommerce/cart`.
	 *
	 * @return bool
	 */
	private function woo_page_uses_block( $page_slug, $block_name ) {
		if ( ! defined( 'WC_VERSION' ) ) {
			return false;
		}
		$woo_page_id      = wc_get_page_id( $page_slug );
		$woo_page_content = get_post_field( 'post_content', $woo_page_id );
		return strpos( $woo_page_content, $block_name ) !== false;
	}

	/**
	 * Determine if a notice for the Cart and Checkout sections inside the Customizer should be shown.
	 *
	 * @return bool
	 */
	private function show_woo_blocks_compatibility_notice(): bool {
		if ( ! defined( 'WC_VERSION' ) ) {
			return false;
		}

		if ( version_compare( WC_VERSION, '8.3.0', '<' ) ) {
			return false;
		}

		if ( $this->woo_page_uses_block( 'cart', 'wp:woocommerce/cart' ) ) {
			return true;
		}

		if ( $this->woo_page_uses_block( 'checkout', 'wp:woocommerce/checkout' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get missing license notice
	 *
	 * @return string|false
	 */
	private function get_missing_license_notice() {
		$status = $this->get_license_status();

		if ( in_array( $status, array( 'not_active', 'invalid' ), true ) ) {
			return sprintf( '%s %s', esc_html__( 'It appears that Neve PRO is active, but your license is missing or invalid. Please check your license to be able to use all Neve PRO features.', 'neve' ), sprintf( '<div class="activate-license"><a target="_blank" href="%s">%s</a></div>', esc_url( $this->get_action_links( 'activate' ) ), esc_html__( 'Go to license.', 'neve' ) ) );
		}

		if ( $status === 'active_expired' ) {
			return sprintf( '%s %s', esc_html__( 'It appears that Neve PRO is active, but your license is expired. Please check your license to be able to use all Neve PRO features.', 'neve' ), sprintf( '<div class="activate-license"><a target="_blank" href="%s">%s</a></div>', esc_url( $this->get_action_links( 'renew' ) ), esc_html__( 'Go to license.', 'neve' ) ) );
		}

		return false;
	}

	/**
	 * Should be shown a customizer notice regarding with missing license notice?
	 *
	 * @return bool
	 */
	private function show_missing_license_notice(): bool {
		return $this->get_missing_license_notice() !== false;
	}

	/**
	 * Enqueue customizer preview script.
	 *
	 * @access public
	 * @since  0.0.1
	 */
	public function enqueue_customizer_preview() {
		$handle              = 'neve-pro-customize-preview';
		$editor_dependencies = include_once plugin_dir_path( __FILE__ ) . 'controls/react/build/customize-preview.asset.php';

		wp_register_script( $handle, NEVE_PRO_INCLUDES_URL . 'customizer/controls/react/build/customize-preview.js', $editor_dependencies['dependencies'], $editor_dependencies['version'], true );
		wp_enqueue_script( $handle );
	}

	/**
	 * Load the customizer modules.
	 *
	 * @access private
	 * @return void
	 * @since  0.0.1
	 */
	private function load_modules() {
		$factory = new Factory( $this->modules, '\\Neve_Pro\\' );
		$factory->load_modules();
	}

	/**
	 * Get the conditional rules array.
	 *
	 * @return array
	 */
	private function get_conditional_rules_array() {
		$conditional_display = new Conditional_Display();

		return [
			'root' => $conditional_display->get_root_ruleset(),
			'end'  => $conditional_display->get_end_ruleset(),
			'map'  => $conditional_display->get_ruleset_map(),
		];
	}

	/**
	 * Init Neve Main Section (which is used for missing license notification)
	 *
	 * @return void
	 */
	public function init_custom_main_section() {
		if ( ! $this->show_missing_license_notice() ) {
			return;
		}

		global $wp_customize;

		$wp_customize->register_section_type( Neve_Pro_Main::class );
		$wp_customize->add_section(
			new Neve_Pro_Main(
				$wp_customize,
				'neve_pro_main',
				[
					'priority' => 0,
				]
			)
		);
	}
}
