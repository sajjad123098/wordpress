<?php
/**
 * Sparks_Dependency_Check
 *
 * @package Neve_Pro\Modules\Woocommerce_Booster\Compatibility
 */
namespace Neve_Pro\Modules\Woocommerce_Booster\Compatibility;

use Neve_Pro\Core\Loader;
use Neve_Pro\Modules\Woocommerce_Booster\Compatibility\Sparks_Install_Plugin;

/**
 * Class Sparks_Dependency_Check
 * Throws notifications on WP admin, Neve Dashboard and gives directive to user for installing the Sparks with clicking.
 */
class Sparks_Dependency_Check {
	const SPARKS_PLUGIN_PATH = 'sparks-for-woocommerce/sparks-for-woocommerce.php';

	/**
	 * Is Sparks activated?
	 *
	 * @var bool|null
	 */
	protected $is_sparks_active = null;

	/**
	 * Is Sparks installed?
	 *
	 * @var bool|null
	 */
	protected $is_sparks_installed = null;

	/**
	 * Initialization - Should be executed on only admin side.
	 *
	 * @return void
	 */
	public function init() {
		// If Neve license is not activated, do not run.
		if ( 'valid' !== apply_filters( 'product_neve_license_status', false ) ) {
			return;
		}

		$this->set_props();

		add_filter( 'neve_dashboard_notifications', array( $this, 'add_dashboard_notifications' ), 1 );
		$this->wp_admin_notices();
	}

	/**
	 * Set properties
	 *
	 * @return void
	 */
	public function set_props() {
		$this->is_sparks_active = defined( 'SPARKS_WC_VERSION' );

		$installed_plugins         = get_plugins();
		$this->is_sparks_installed = array_key_exists( self::SPARKS_PLUGIN_PATH, $installed_plugins );
	}

	/**
	 * Throw WP admin notices related installation/activation
	 *
	 * @return void
	 */
	private function wp_admin_notices() {
		if ( current_user_can( 'install_plugins' ) && ! $this->is_sparks_installed ) {
			add_action( 'admin_notices', array( $this, 'wp_admin_notice_sparks_missing' ) );
		}

		if ( current_user_can( 'activate_plugins' ) && $this->is_sparks_installed && ! $this->is_sparks_active ) {
			add_action( 'admin_notices', array( $this, 'wp_admin_notice_sparks_not_activated' ) );
		}
	}

	/**
	 * Render WP admin notice related with Sparks is missing.
	 *
	 * @return void
	 */
	public function wp_admin_notice_sparks_missing() {
		$plugin_name = __( 'Sparks for WooCommerce', 'neve' );
		$message     = __( 'You need to install Sparks for WooCommerce to continue using WooCommerce Booster.', 'neve' );
		$page        = Loader::has_compatibility( 'theme_dedicated_menu' ) ? 'admin.php' : 'themes.php';

		printf(
			'<div class="error"><p><b>%1$s</b> %2$s <a href="%3$s">%4$s</a></p></div>',
			esc_html( $plugin_name ),
			esc_html( $message ),
			esc_url( admin_url( $page . '?page=neve-welcome' ) ),
			esc_html__( 'Install', 'neve' )
		);
	}

	/**
	 * Render WP admin notice related with Sparks is not activated.
	 *
	 * @return void
	 */
	public function wp_admin_notice_sparks_not_activated() {
		$plugin_name = __( 'Sparks for WooCommerce', 'neve' );
		$message     = __( 'You need to activate Sparks for WooCommerce to continue using WooCommerce Booster.', 'neve' );
		$page        = Loader::has_compatibility( 'theme_dedicated_menu' ) ? 'admin.php' : 'themes.php';

		printf(
			'<div class="error"><p><b>%1$s</b> %2$s <a href="%3$s">%4$s</a></p></div>',
			esc_html( $plugin_name ),
			esc_html( $message ),
			esc_url( admin_url( $page . '?page=neve-welcome' ) ),
			esc_html__( 'Activate', 'neve' )
		);
	}

	/**
	 * Add Dashboard Notifications.
	 *
	 * @return array
	 */
	public function add_dashboard_notifications( $notifications ) {
		if ( ! $this->is_sparks_installed && ! $this->is_sparks_active ) {
			$notifications['sparks-missing'] = [
				'text'   => __( 'You need to install Sparks to continue using WooCommerce Booster', 'neve' ),
				'update' => [
					'type' => 'plugin',
					'slug' => 'sparks-for-woocommerce',
					'path' => self::SPARKS_PLUGIN_PATH,
				],
				'cta'    => __( 'Install', 'neve' ),
				'type'   => 'warning',
			];
		}

		if ( $this->is_sparks_installed && ! $this->is_sparks_active ) {
			$notifications['sparks-not-active'] = [
				'text'   => __( 'You need to activate Sparks to continue using WooCommerce Booster', 'neve' ),
				'update' => [
					'type' => 'plugin',
					'slug' => 'sparks-for-woocommerce',
					'path' => self::SPARKS_PLUGIN_PATH,
				],
				'cta'    => __( 'Activate', 'neve' ),
				'type'   => 'warning',
			];
		}

		return $notifications;
	}
}
