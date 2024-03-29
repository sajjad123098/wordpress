<?php
/**
 * Author:          Andrei Baicus <andrei@themeisle.com>
 * Created on:      2019-06-04
 *
 * @package starter_sites.php
 */

namespace Neve_Pro\Admin;

use Neve_Pro\Traits\Core;

/**
 * Class Starter_Sites
 *
 * @package Neve_Pro\Admin
 */
class Starter_Sites {
	use Core;

	/**
	 * Initialize the starter sites class.
	 */
	public function init() {
		add_filter( 'neve_filter_onboarding_sites', array( $this, 'add_starter_sites' ) );
	}

	/**
	 * Add the starter sites.
	 *
	 * @param array $starter_sites starter sites array.
	 *
	 * @return mixed
	 */
	public function add_starter_sites( $starter_sites ) {
		if ( $this->get_license_type() < 2 ) {
			return $starter_sites;
		}

		if ( ! isset( $starter_sites['remote'] ) ) {
			$starter_sites['remote'] = [];
		}

		$starter_sites['remote'] = array_merge_recursive( $starter_sites['remote'], $starter_sites['upsell'] );

		unset( $starter_sites['upsell'] );

		return $starter_sites;
	}
}
