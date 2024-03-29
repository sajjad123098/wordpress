<?php
/**
 * Abstract class to register dynamic CSS.
 *
 * @package Neve_Pro\Core
 */

namespace Neve_Pro\Core;

/**
 * Class Generic_Style
 *
 * @package Neve_Pro\Core
 */
abstract class Generic_Style {

	/**
	 * Register hooks.
	 */
	public function register_hooks() {
		add_filter( 'neve_style_subscribers', [ $this, 'add_subscribers' ], 99, 1 );
	}

	/**
	 *
	 * Register style subscribers.
	 *
	 * @param array $subscribers Subscriber list.
	 *
	 * @return array
	 */
	abstract public function add_subscribers( $subscribers = [] );

}
