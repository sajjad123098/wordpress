<?php
/**
 * Plugin Name:       Neve Pro Addon
 * Description:       This plugin is an add-on to Neve WordPress theme which offers exclusive premium features, specially designed for Neve, to enhance your overall WordPress experience.
 * Version:           2.8.4
 * Plugin URI:        https://themeisle.com/themes/neve/
 * Author:            ThemeIsle
 * Author URI:        https://themeisle.com
 * License:           GPLv3
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.en.html
 * Text Domain:       neve
 * Domain Path:       /languages
 * Requires PHP:      7.0
 * WordPress Available:  no
 * Requires License:    yes
 *
 * WC requires at least: 4.3
 * WC tested up to: 8.3
 *
 * Elementor tested up to: 3.18
 *
 * @package Neve Pro Addon
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}


define( 'NEVE_PRO_NAME', 'Neve Pro Addon' );
define( 'NEVE_PRO_REST_NAMESPACE', 'neve_pro/v1' );
define( 'NEVE_PRO_VERSION', '2.8.4' );
define( 'NEVE_PRO_NAMESPACE', 'neve_pro' );

define( 'NEVE_PRO_URL', plugin_dir_url( __FILE__ ) );
define( 'NEVE_PRO_INCLUDES_URL', plugin_dir_url( __FILE__ ) . 'includes/' );

define( 'NEVE_PRO_PATH', plugin_dir_path( __FILE__ ) );
define( 'NEVE_PRO_SPL_ROOT', plugin_dir_path( __FILE__ ) . 'includes/' );
define( 'NEVE_PRO_BASEFILE', __FILE__ );
define( 'NEVE_PRO_BASENAME', basename( NEVE_PRO_PATH ) );
/**
 * Buffer which holds errors during theme inititalization.
 *
 * @var WP_Error $_neve_pro_bootstrap_errors
 */
global $_neve_pro_bootstrap_errors;
$_neve_pro_bootstrap_errors = new WP_Error();

if ( version_compare( PHP_VERSION, '7.0' ) < 0 ) {
	$_neve_pro_bootstrap_errors->add(
		'minimum_php_version',
		sprintf(
		/* translators: %s message to upgrade PHP to the latest version */
			__( "Hey, we've noticed that you're running an outdated version of PHP which is no longer supported. Make sure your site is fast and secure, by %1\$s. Neve's minimal requirement is PHP%2\$s.", 'neve' ),
			sprintf(
			/* translators: %s message to upgrade PHP to the latest version */
				'<a href="https://wordpress.org/support/upgrade-php/">%s</a>',
				__( 'upgrading PHP to the latest version', 'neve' )
			),
			'7.0'
		)
	);
}


/**
 * A list of files to check for existance before bootstraping.
 *
 * @var array Files to check for existance.
 */

$_neve_pro_files_to_check = defined( 'NEVE_PRO_IGNORE_SOURCE_CHECK' ) ? [] : [
	NEVE_PRO_PATH . 'vendor/autoload.php',
	NEVE_PRO_PATH . 'includes/modules/woocommerce_booster/assets/style.css',
	NEVE_PRO_PATH . 'includes/modules/header_footer_grid/assets/style.css',
	NEVE_PRO_PATH . 'includes/customizer/controls/css/customizer-controls.css',
	NEVE_PRO_PATH . 'includes/customizer/controls/js/build/bundle.js',
	NEVE_PRO_PATH . 'includes/modules/woocommerce_booster/assets/js/build/script.js',
];
foreach ( $_neve_pro_files_to_check as $_file_to_check ) {
	if ( ! is_file( $_file_to_check ) ) {
		$_neve_pro_bootstrap_errors->add(
			'build_missing',
			sprintf(
			/* translators: %s: commands to run the theme */
				__( 'You appear to be running the %1$s plugin from source code. Please finish installation by running %2$s.', 'neve' ), // phpcs:ignore WordPress.Security.EscapeOutput
				'Neve Pro',
				'<code>composer install --no-dev &amp;&amp; yarn install --frozen-lockfile &amp;&amp; yarn run build</code>'
			)
		);
		break;
	}
}
/**
 * Adds notice bootstraping errors.
 *
 * @internal
 * @global WP_Error $_neve_pro_bootstrap_errors
 */
function _neve_pro_bootstrap_errors() {
	global $_neve_pro_bootstrap_errors;
	printf( '<div class="notice notice-error"><p>%1$s</p></div>', $_neve_pro_bootstrap_errors->get_error_message() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

if ( $_neve_pro_bootstrap_errors->has_errors() ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
	deactivate_plugins( __FILE__ );
	unset( $_GET['activated'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	add_action( 'admin_notices', '_neve_pro_bootstrap_errors' );

	return;
}

add_filter( 'themeisle_sdk_products', 'neve_pro_load_sdk' );
add_filter( 'themesle_sdk_namespace_' . md5( __FILE__ ), 'neve_pro_load_namespace' );

$theme = get_template();

if ( $theme !== 'neve' ) {
	add_action( 'admin_notices', 'neve_pro_display_wrong_theme_notice' );
	add_action( 'admin_init', 'neve_pro_disable_wrong_theme_notice' );

	return;
}

/**
 * Notice displayed if the theme is not Neve or a child theme of Neve.
 *
 * @since 0.0.1
 */
function neve_pro_display_wrong_theme_notice() {

	global $current_user;
	$user_id        = $current_user->ID;
	$ignored_notice = get_user_meta( $user_id, 'neve_pro_nag_ignore_theme_notice' );
	if ( ! empty( $ignored_notice ) ) {
		return;
	}

	$dismiss_button = sprintf(
		'<a href="%s" class="notice-dismiss" style="text-decoration:none;"></a>',
		'?neve_pro_nag_ignore_theme_notice=ignore'
	);

	$strings = array(
		'errOccured'      => __( 'An error occurred. Please refresh the page and try again.', 'neve' ),
		'activating'      => __( 'Activating...', 'neve' ),
		'activate'        => __( 'Activate Neve', 'neve' ),
		'installActivate' => __( 'Install and Activate Neve', 'neve' ),
	);

	$themes      = wp_get_themes();
	$button_text = $strings['installActivate'];
	$action      = 'install';
	$url         = esc_url( admin_url( 'update.php?action=install-theme&theme=neve&_wpnonce=' . wp_create_nonce( 'install-theme_neve' ) ) );
	if ( isset( $themes['neve'] ) ) {
		$url         = esc_url( admin_url( 'themes.php?action=activate&amp;template=neve&amp;stylesheet=neve&_wpnonce=' . wp_create_nonce( 'switch-theme_neve' ) ) );
		$button_text = $strings['activate'];
		$action      = 'activate';
	}

	/* translators: %1$s - plugin name, %2$s - theme name, %3$s - call to action */
	$message = sprintf( __( '%1$s requires the %2$s theme to be activated to work. %3$s', 'neve' ), sprintf( '<strong>%s</strong>', 'Neve Pro' ), sprintf( '<strong>%s</strong>', 'Neve' ), sprintf( '<br/><a class="install-activate-neve theme-install button button-primary" data-action="%4$s" data-name="Neve" data-slug="neve" href="%1$s" aria-label="%3$s">%2$s</a>', $url, $button_text, $button_text, $action ) );

	printf(
		'<div class="notice notice-error install-neve" style="position:relative;">%1$s<p>%2$s</p></div>',
		$dismiss_button, //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		$message //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	);
	?>
	<style>
		.install-neve .error-message {
			background-color: #F56E28;
			color: #fff;
		}

		.install-neve .install-activate-neve {
			margin-top: 10px;
		}
	</style>
	<script type="application/javascript">
			( function($) {
				let button = $( '.install-activate-neve' );

				$( button ).on( 'click', function(e) {
					if ( $( this ).data( 'action' ) === 'activate' ) {
						$( button ).html( '<?php echo esc_html( $strings['activating'] ); ?>' );
						$( button ).addClass( 'updating-message' );
						return;
					}
					e.preventDefault();
					wp.updates.installTheme( {
						slug: 'neve',
						success: function(response) {
							if ( response.activateUrl ) {
								$( button ).html( '<?php echo esc_html( $strings['activating'] ); ?>' );
								location.href = response.activateUrl;
							}
						},
						error: function(error) {
							var message;
							if ( error.errorMessage ) {
								message = error.errorMessage;
							} else {
								message = '<?php echo esc_html( $strings['errOccured'] ); ?>';
							}
							$( button ).replaceWith( '<code class="error-message">Error: ' + message + '</code>' );
						}
					} );
				} );
			} )( jQuery );
	</script>
	<?php
}

/**
 * Disable the notice that appears if the theme is not Neve or a child theme of Neve.
 *
 * @since 0.0.1
 */
function neve_pro_disable_wrong_theme_notice() {
	global $current_user;
	$user_id = $current_user->ID;
	/* If user clicks to ignore the notice, add that to their user meta */
	if ( isset( $_GET['neve_pro_nag_ignore_theme_notice'] ) && 'ignore' === $_GET['neve_pro_nag_ignore_theme_notice'] ) {
		add_user_meta( $user_id, 'neve_pro_nag_ignore_theme_notice', 'true', true );
	}
}

/**
 * Load the localisation file.
 *
 * @access  public
 * @since   0.0.1
 */
function neve_pro_load_textdomain() {
	load_plugin_textdomain( 'neve', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

add_action( 'init', 'neve_pro_load_textdomain' );

/**
 * Filter products array.
 *
 * @param array $products products array.
 *
 * @return array
 */
function neve_pro_load_sdk( $products ) {
	$products[] = __FILE__;

	return $products;
}
add_filter(
	'themeisle_sdk_compatibilities/' . NEVE_PRO_BASENAME,
	function ( $compatibilities ) {
		$compatibilities['Neve'] = [
			'basefile'  => defined( 'NEVE_MAIN_DIR' ) ? NEVE_MAIN_DIR . 'style.css' : '',
			'required'  => '3.4',
			'tested_up' => '3.8',
		];
		return $compatibilities;
	}
);
/**
 * Define cli namespace for sdk.
 *
 * @return string CLI namespace.
 */
function neve_pro_load_namespace() {
	return 'neve';
}

add_filter(
	'product_neve_license_key_constant',
	function ( $value ) {
		return empty( $value ) ? ( defined( 'NEVE_LICENSE_KEY' ) ? NEVE_LICENSE_KEY : $value ) : $value;
	}
);

/**
 * Add compatibility with WooCommerce Custom Order Tables.
 * To support HPOS feature from WooCommerce.
 */
add_action(
	'before_woocommerce_init',
	function() {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}
);

/**
 * Actions that are running on plugin deactivate.
 */
function run_uninstall_actions() {
	/**
	 * Disable white label and make sure that the module is visible again in dashboard.
	 */
	$white_label_settings                = get_option( 'ti_white_label_inputs' );
	$white_label_settings                = json_decode( $white_label_settings, true );
	$white_label_settings['white_label'] = false;
	update_option( 'ti_white_label_inputs', wp_json_encode( $white_label_settings ) );
}

register_deactivation_hook( __FILE__, 'run_uninstall_actions' );

/**
 * Require package autoload
 */
function neve_pro_run() {
	$vendor_file = trailingslashit( plugin_dir_path( __FILE__ ) ) . 'vendor/autoload.php';
	if ( is_readable( $vendor_file ) ) {
		require_once $vendor_file;
	}

	define(
		'NEVE_PRO_COMPATIBILITY_FEATURES',
		[
			'skinv2'                => true,
			'malformed_div_on_shop' => true,
			'headerv2'              => true,
			'sparks'                => true,
		]
	);
}

neve_pro_run();
