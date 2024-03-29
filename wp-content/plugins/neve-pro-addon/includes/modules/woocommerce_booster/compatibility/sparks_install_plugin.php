<?php
/**
 * Sparks_Install_Plugin
 *
 * @package Neve_Pro\Modules\Woocommerce_Booster\Compatibility
 */
namespace Neve_Pro\Modules\Woocommerce_Booster\Compatibility;

use ThemeisleSDK\Product;
use Neve_Pro\Traits\Core;

/**
 * Class Sparks_Install_Plugin
 * Responsible from Sparks for WooCommerce plugin installation.
 */
class Sparks_Install_Plugin {
	use Core;

	/**
	 * Initialization
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'admin_post_install_sparks', array( $this, 'install_sparks_plugin' ) );
	}

	/**
	 * Get download URL from themeisle SDK.
	 *
	 * @return string|false
	 */
	private function get_download_url_from_themeisle() {
		$response = $this->remote_get(
			sprintf(
				'%slicense/version/%s/%s/%s/%s',
				Product::API_URL,
				rawurlencode( 'Sparks for WooCommerce' ),
				apply_filters( 'product_neve_license_key', 'free' ),
				'latest',
				rawurlencode( home_url() )
			),
			array(
				'timeout'   => 15, //phpcs:ignore WordPressVIPMinimum.Performance.RemoteRequestTimeout.timeout_timeout, Inherited by wp_remote_get only, for vip environment we use defaults.
				'sslverify' => false,
			)
		);

		if ( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) ) {
			return false;
		}

		$update_data = json_decode( wp_remote_retrieve_body( $response ) );

		$cache_token = sprintf( '%s-neve-pro-v-%s', time(), NEVE_PRO_VERSION );

		return $update_data->download_link . $cache_token;
	}

	/**
	 * Handler for immediately installation of Sparks from external URL.
	 *
	 * @return void
	 */
	public function install_sparks_plugin() {
		if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'install_sparks' ) ) { //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			wp_nonce_ays( '' );
		}

		/* translators: %s: plugin update url*/
		$title     = __( 'Installing Sparks for WooCommerce', 'neve' );
		$nonce     = 'neve-install-sparks-plugin';
		$overwrite = 'update-plugin';

		$download_url = $this->get_download_url_from_themeisle();

		if ( ! $download_url ) {
			wp_die();
		}

		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		$upgrader = new \Plugin_Upgrader( new \Plugin_Installer_Skin( compact( 'title', 'nonce', 'overwrite' ) ) );

		?>
		<title><?php echo esc_html( $title ); ?></title>
		<?php

		echo '<style>#error-page { word-break: break-all; }</style>';
		$upgrader->install( $download_url );
		wp_die();
	}
}
