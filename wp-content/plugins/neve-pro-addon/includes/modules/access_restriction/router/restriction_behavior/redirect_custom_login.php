<?php
/**
 * Redirect_Custom_Login
 *
 * @package Neve_Pro\Modules\Access_Restriction\Router\Restriction_Behavior
 */
namespace  Neve_Pro\Modules\Access_Restriction\Router\Restriction_Behavior;

use Neve_Pro\Modules\Access_Restriction\Router\Restriction_Behavior\Redirection_Decorator;
use Neve_Pro\Modules\Access_Restriction\General_Settings\Module_Settings;

/**
 * Class Redirect_Custom_Login
 */
class Redirect_Custom_Login extends Redirection_Decorator {
	const CUSTOM_QUERY_PARAM = 'nv_redirect_to';

	/**
	 * Redirection
	 *
	 * Redirect to the matched custom login page
	 *
	 * @return void
	 */
	public function redirect() {
		$page_id = ( new Module_Settings() )->get_restriction_custom_login_page_id();

		if ( false === $page_id ) {
			$page_url = $this->get_fallback_login_page_url();
		} else {
			$page_url = add_query_arg( self::CUSTOM_QUERY_PARAM, urlencode( $this->get_current_url() ), get_permalink( $page_id ) );
		}

		wp_safe_redirect( $page_url );
		exit;
	}

	/**
	 * Get fallback login page url if custom login page is not set
	 *
	 * @return string
	 */
	protected function get_fallback_login_page_url() {
		return $this->get_wp_core_login_url();
	}

	/**
	 * Registers a hook to return to the current page after login.
	 *
	 * @return void
	 */
	public static function register_hook_for_custom_login_redirect() {
		add_filter( 'login_redirect', array( __CLASS__, 'override_login_redirect_url' ), 10, 1 );
	}

	/**
	 * Override login redirect url
	 *
	 * Filter all login redirect urls and override the URL if it contains
	 * self::CUSTOM_QUERY_PARAM query param.
	 *
	 * This method returns the user to the previously
	 * visited restricted page upon successful login.
	 *
	 * @param  string $redirect_to current redirect url.
	 * @return string
	 */
	public static function override_login_redirect_url( $redirect_to ) {
		$components = wp_parse_url( $redirect_to );

		if ( ! is_array( $components ) || ! array_key_exists( 'query', $components ) ) {
			return $redirect_to;
		}

		parse_str( $components['query'], $params );

		if ( ! is_array( $params ) || ! array_key_exists( self::CUSTOM_QUERY_PARAM, $params ) ) {
			return $redirect_to;
		}

		return $params[ self::CUSTOM_QUERY_PARAM ];
	}
}
