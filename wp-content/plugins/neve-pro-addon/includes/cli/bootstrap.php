<?php
/**
 * Class responsible for bootstrapping wp cli commands.
 * 
 * Author:          Uriahs Victor
 * Created on:      03/10/2021 (d/m/y)
 *
 * @package Neve_Pro\CLI
 */

namespace Neve_Pro\CLI;

/**
 * Class Boostrap.
 * 
 * @package Neve
 */
class Bootstrap {

	/**
	 * Attach to wp cli.
	 */
	public function init() {
		add_action( 'cli_init', array( $this, 'neve_register_cli_commands' ) );
	}

	/**
	 * Register Neve Pro commands under existing wp cli neve namespace.
	 */
	public function neve_register_cli_commands() {
		\WP_CLI::add_command( 'neve module', '\\Neve_Pro\\CLI\\Modules\\Module' );
		\WP_CLI::add_command( 'neve module setting', '\\Neve_Pro\\CLI\\Modules\\Setting' );
	}

}
