<?php
/**
 * Sparks Compatibility
 *
 * @package Neve_Pro\Modules\Woocommerce_Booster\Compatibility
 */
namespace Neve_Pro\Modules\Woocommerce_Booster\Compatibility;

use Neve_Pro\Modules\Woocommerce_Booster\Compatibility\Sparks_Dependency_Check;
use Neve_Pro\Traits\Core;

/**
 * Class Sparks
 */
class Sparks {
	use Core;
	const MIN_WOOBOOSTER_LICENSE_REQ = 2;

	/**
	 * Initialization
	 *
	 * @return void
	 */
	public function init() {
		if ( ! defined( 'WC_VERSION' ) ) {
			return;
		}

		if ( ! $this->is_woobooster_available_for_license() ) {
			return;
		}

		( new Sparks_Install_Plugin() )->init();

		// TODO: replace woocommerce status with native module method \Neve_Pro\Modules\Woocommerce_Booster\Module::is_active() once automatic module configurations has been implemented.
		$is_woobooster_enabled = get_option( 'nv_pro_woocommerce_booster_status', true );

		if ( ! $is_woobooster_enabled ) {
			return;
		}

		add_action( 'admin_init', array( ( new Sparks_Dependency_Check() ), 'init' ) );

		if ( ! function_exists( 'sparks' ) ) {
			return;
		}
	}

	/**
	 * Checks if module is available for current license.
	 *
	 * @return bool
	 */
	private function is_woobooster_available_for_license() {
		$availability = $this->get_license_type();

		if ( $availability >= self::MIN_WOOBOOSTER_LICENSE_REQ ) {
			return true;
		}

		return false;
	}

}
