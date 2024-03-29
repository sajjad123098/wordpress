<?php
/**
 * Redirect_WP_Login
 *
 * @package Neve_Pro\Modules\Access_Restriction\Router\Restriction_Behavior
 */
namespace  Neve_Pro\Modules\Access_Restriction\Router\Restriction_Behavior;

use Neve_Pro\Modules\Access_Restriction\Router\Restriction_Behavior\Redirection_Decorator;
use Neve_Pro\Modules\Access_Restriction\Utility\Context_Trait;

/**
 * Class Redirect_WP_Login
 */
class Redirect_WP_Login extends Redirection_Decorator {
	use Context_Trait;
	/**
	 * Redirection to the WP Core Login Screen
	 *
	 * @return void
	 */
	public function redirect() {
		wp_safe_redirect( $this->get_wp_core_login_url() );
		exit;
	}
}
