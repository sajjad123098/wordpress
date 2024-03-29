<?php
/**
 * Class responsible for handling Neve Pro Modules via wp cli.
 * 
 * Author:          Uriahs Victor
 * Created on:      03/10/2021 (d/m/y)
 *
 * @package Neve_Pro\CLI
 */

namespace Neve_Pro\CLI\Modules;

use Neve_Pro\Core\Loader;
use Neve_Pro\CLI\Messages;

/**
 * Allows for activating, deactivating and listing of Neve main modules.
 * 
 * @package Neve_Pro
 */
class Module {

	/**
	 * Get all modules and their suboptions available in Neve Pro.
	 *
	 * @return array 
	 */
	private function get_all_modules() {
		return Loader::$cli_modules_data;
	}

	/**
	 * Get the activated/deactivated status of a module.
	 *
	 * @param array $data 
	 * @return bool
	 */
	private function get_option( $data ) {

		if ( empty( $data['option_key'] ) ) {
			return false;
		}

		$default = $data['default'] ?? '';

		$option = get_option( $data['option_key'] );
	
		$option = ( $option !== false ) ? $option : $default; // If the option does not yet exist in the DB return the default.
		
		return $option;
	}

	/**
	 * Retrieve details about Neve Pro modules.
	 * 
	 * @param string $name The module name to retrieve the details for.
	 * @return void|array 
	 */
	private function get_module_data( $name ) {

		$modules = $this->get_all_modules();

		if ( ! is_array( $modules ) || empty( $modules ) ) {
			$message = esc_html__( 'Initializing modules failed.', 'neve' );
			\WP_CLI::error( $message, true );
		}
		
		if ( ! in_array( $name, array_keys( $modules ) ) ) {
			/* translators: Module name passed via wp cli. */
			$message = sprintf( esc_html__( 'Module %s not found.', 'neve' ), $name );
			\WP_CLI::error( $message, true );
		}

		return $modules[ $name ];

	}

	/**
	 * Orchestrate the listing of one, many, or all Neve Pro module statuses.
	 * 
	 * ## OPTIONS
	 * [<module_name>...]
	 * : The module(s) which you'd like to list the activation status for.
	 * 
	 * [--status=<value>]
	 * : The module status to list.
	 * ---
	 * options:
	 *   - active
	 *   - inactive
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 * wp neve module list                                  List the activation status of all main modules.    
	 * wp neve module list woocommerce_booster              List the activation status of Woocommerce Booster. 
	 * wp neve module list woocommerce_booster blog_pro     List the activation status of Woocommerce Booster and Blog Pro. 
	 * wp neve module list --status=active                  List all main modules that are active. 
	 * wp neve module list --status=inactive                List all main modules that are inactive. 
	 *
	 * @param array $modules 
	 * @param array $assoc_args 
	 * @return void 
	 */
	public function list( $modules, $assoc_args ) {

		$status_type = $assoc_args['status'] ?? ''; 

		if ( ! empty( $status_type ) || empty( $modules ) ) {
			$this->list_all_modules_status( $status_type );
		} else {
			$this->list_module_status( $modules );
		}

	}

	/**
	 * Output the activated/deactivated status of one or more modules to the terminal.
	 * 
	 * Example: wp neve module list woocommerce_booster
	 * Example: wp neve module list woocommerce_booster blog_pro
	 * 
	 * @param array $names The module(s) name.
	 * @return void 
	 */
	private function list_module_status( $names ) {

		// Allow for listing one or n number of module statuses
		$items = array();

		$modules = $this->get_all_modules();

		foreach ( $names as $module_name ) {

			$module = $modules[ $module_name ] ?? '';
		
			if ( empty( $module ) ) {
				/* translators: %s is the module name dynamically passed into the string. */
				$message = sprintf( esc_html__( 'Module %s not found', 'neve' ), $module_name );
				\WP_CLI::error( $message, false );
				continue;
			}

			$status = $this->get_option( $module );

			$status_text = ( $status ) ? Messages::get_active_text() : Messages::get_inactive_text();
		
			$items[] = array(
				'module' => $module['name'],
				'slug'   => $module['slug'],
				'status' => $status_text . "\t",
			); 

		}

		\WP_CLI\Utils\format_items( 'table', $items, array( 'module', 'slug', 'status' ) );

	}

	/**
	 * Output the activated/deactivated status of all modules to the terminal.
	 * 
	 * Example: wp neve module list --status=active
	 * Example: wp neve module list --status=inactive
	 * 
	 * @param string $status_type Whether to list active or inactive modules.
	 * @return void 
	 */
	private function list_all_modules_status( $status_type ) {
		
		$modules = $this->get_all_modules();

		$items = array();

		foreach ( $modules as $module => $data ) {

			$status = $this->get_option( $data );

			$status_text = ( $status ) ? Messages::get_active_text() : Messages::get_inactive_text();

			if ( ( empty( $status_type ) ) || ( $status && $status_type === 'active' ) || ( ! $status && $status_type === 'inactive' ) ) {
				$items[] = array(
					'module' => $data['name'],
					'key'    => $data['slug'],
					'status' => $status_text . "\t",
				); 
			}       
		}
		
		if ( ! empty( $items ) ) {
			\WP_CLI\Utils\format_items( 'table', $items, array( 'module', 'key', 'status' ) );
		} else {
			/* translators: %s is the active/inactive text dynamically passed into the string. */
			\WP_CLI::line( sprintf( esc_html__( 'No %s modules.', 'neve' ), $status_type ) );
		}

	}
	
	/**
	 * Orchestrate the activating of one, many, or all Neve Pro modules.
	 *
	 * ## OPTIONS
	 *
	 * [<module_name>...] 
	 * : The module(s) name that you want to activate.
	 *
	 * [--all]
	 * : Activate all main modules.
	 *
	 * ## EXAMPLES
	 *
	 * wp neve module activate woocommerce_booster                   Activates WooCommerce Booster module.      
	 * wp neve module activate woocommerce_booster blog_pro          Activates WooCommerce Booster and Blog Booster module.
	 * wp neve module activate  --all                                Activates all modules.
	 *
	 * @param array $modules 
	 * @param array $assoc_args 
	 * @return void 
	 */
	public function activate( $modules, $assoc_args ) {

		$activate_all = $assoc_args['all'] ?? false;

		if ( $activate_all ) {
			$this->activate_all_modules();
		} else {
			$this->activate_module( $modules );
		}

	}

	/**
	 * Activate a module or n number of modules (Appearance->Neve Options->Neve Pro). 
	 * 
	 * Example: wp neve module activate blog_pro
	 * Example: wp neve module activate blog_pro white_label
	 * 
	 * @param array $args The module(s) to activate.
	 * @return void 
	 */
	private function activate_module( $args ) {

		if ( empty( $args ) || ! is_array( $args ) ) {
			$message = esc_html__( 'Module name not given.', 'neve' );
			\WP_CLI::error( $message, true );
		}

		foreach ( $args as $module ) {

			$data = $this->get_module_data( $module );

			$status = update_option( $data['option_key'], true );

			Messages::output_activated_status_message( $data, $status );
		}

	}

	/**
	 * Activate all Neve Pro modules
	 * 
	 * Example: wp neve module activate --all
	 * 
	 * @return void 
	 */
	private function activate_all_modules() {
		
		$all_modules = $this->get_all_modules();

		foreach ( $all_modules as $module ) {

			$status = update_option( $module['option_key'], true );

			Messages::output_activated_status_message( $module, $status );
	 
		}

	}

	/**
	 * Orchestrate the deactivating of one, many, or all Neve Pro modules.
	 *
	 * ## OPTIONS
	 *
	 * [<module_name>...]
	 * : The module(s) name that you want to deactivate.
	 *
	 * [--all]
	 * : deactivate all main modules.
	 * 
	 * 
	 * ## EXAMPLES
	 *
	 * wp neve module deactivate woocommerce_booster                   Deactivates WooCommerce Booster module.      
	 * wp neve module deactivate woocommerce_booster blog_pro          Deactivates WooCommerce Booster and Blog Booster module.
	 * wp neve module deactivate --all                                 Deactivates all modules.
	 *
	 * @param array $modules 
	 * @param array $assoc_args 
	 * @return void 
	 */
	public function deactivate( $modules, $assoc_args ) {

		$activate_all = $assoc_args['all'] ?? false;

		if ( $activate_all ) {
			$this->deactivate_all_modules();
		} else {
			$this->deactivate_module( $modules );
		}

	}

	/**
	 * Deactivate a module or n number of modules (Appearance->Neve Options->Neve Pro). 
	 * 
	 * Example: wp neve module deactivate blog_pro
	 * Example: wp neve module deactivate blog_pro white_label
	 * 
	 * @param array $args The module(s) to deactivate.
	 * @return void 
	 */
	private function deactivate_module( $args ) {
	
		if ( empty( $args ) || ! is_array( $args ) ) {
			$message = esc_html__( 'Module name not given.', 'neve' );
			\WP_CLI::error( $message, true );
		}

		foreach ( $args as $module ) {

			$data = $this->get_module_data( $module );

			$status = update_option( $data['option_key'], false );

			Messages::output_deactivated_status_message( $data, $status );
		}

	}

	/**
	 * Deactivate all Neve Pro modules
	 * 
	 * Example: wp neve module activate --all
	 * 
	 * @return void 
	 */
	private function deactivate_all_modules() {

		$all_modules = $this->get_all_modules();
		foreach ( $all_modules as $module ) {
			$status = update_option( $module['option_key'], false );
			Messages::output_deactivated_status_message( $module, $status );
		}

	}

}
