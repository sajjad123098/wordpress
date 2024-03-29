<?php
/**
 * Class responsible for housing messages that get outputted to stdout after a command has been ran.
 * 
 * Author:          Uriahs Victor
 * Created on:      03/10/2021 (d/m/y)
 *
 * @package Neve_Pro\CLI
 */

namespace Neve_Pro\CLI;

/**
 * Houses possible messages that are outputted to stdout when various commands are ran.
 * 
 * @package Neve_Pro\CLI
 */
class Messages {

	/**
	 * "Please check the command and try again." text.
	 * 
	 * @return string 
	 */
	public static function get_wrong_command_text() {
		return \WP_CLI::colorize( '%Y' . esc_html__( ' Please check the command and try again.', 'neve' ) . '%n' );
	}

	/**
	 * "Active" text.
	 * 
	 * @return string 
	 */
	public static function get_active_text() {
		return \WP_CLI::colorize( '%G' . esc_html__( 'Active', 'neve' ) . '%n' );
	}
	
	/**
	 * "Inactive" text.
	 * 
	 * @return string 
	 */
	public static function get_inactive_text() {
		return \WP_CLI::colorize( '%R' . esc_html__( 'Inactive', 'neve' ) . '%n' );
	}
	
	/**
	 * "Activated" text.
	 * 
	 * @return string 
	 */
	public static function get_activated_text() {
		return \WP_CLI::colorize( '%G' . esc_html__( 'Activated', 'neve' ) . '%n' );
	}

	/**
	 * "Deactivated" text.
	 * 
	 * @return string 
	 */
	public static function get_deactivated_text() {
		return \WP_CLI::colorize( '%R' . esc_html__( 'Deactivated', 'neve' ) . '%n' );

	}

	/**
	 * Output the outcome of an activate or on command.
	 *
	 * @param array $module 
	 * @param bool  $status 
	 * @return void 
	 */
	public static function output_activated_status_message( $module, $status ) {
		
		if ( $status ) {
			\WP_CLI::success( $module['name'] . ' ' . self::get_activated_text() );
		} else {
			\WP_CLI::line( esc_html__( 'Status unchanged:', 'neve' ) . ' ' . $module['name'] );
		}           

	}

	/**
	 * "Option Updated" text.
	 * 
	 * @return string 
	 */
	public static function get_option_updated_text() {
		return \WP_CLI::colorize( '%G' . esc_html__( 'Option Updated', 'neve' ) . '%n' );
	}

	/**
	 * Output the outcome of a deactivate or off command.
	 *
	 * @param array $module 
	 * @param bool  $status 
	 * @return void 
	 */
	public static function output_deactivated_status_message( $module, $status ) {

		if ( $status ) {
			\WP_CLI::success( $module['name'] . ' ' . self::get_deactivated_text() );
		} else {
			\WP_CLI::line( esc_html__( 'Status unchanged:', 'neve' ) . ' ' . $module['name'] );
		}       

	}

	/**
	 * Output the outcome of updating a submodule value in the database.
	 *
	 * @param array $module 
	 * @param bool  $status 
	 * @return void 
	 */
	public static function output_updated_status_message( $module, $status ) {

		if ( $status ) {
			\WP_CLI::success( $module['name'] . ' ' . self::get_option_updated_text() );
		} else {
			\WP_CLI::line( esc_html__( 'Status unchanged:', 'neve' ) . ' ' . $module['name'] );
		}       

	}

}
