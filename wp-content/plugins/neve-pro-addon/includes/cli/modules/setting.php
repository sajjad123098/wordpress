<?php
/**
 * Class responsible for handling Neve Pro Module settings via wp cli.
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
 * Allows for activating, deactivating and listing of Neve main modules' subsettings.
 * 
 * @package Neve_Pro
 */
class Setting {

	/**
	 * List subsettings and their activation status.
	 * 
	 * ## OPTIONS
	 *
	 * <main_module>
	 * : The main module that the setting belongs to.
	 * 
	 * ## EXAMPLES
	 * 
	 * wp neve module setting list woocommerce_booster    List the status of the WooCommerce Booster module subsettings.
	 * wp neve module setting list white_label            List the status and values of the White Label module subsettings.
	 * 
	 * @param array $args 
	 * @return void 
	 */
	public function list( $args ) {

		$main_module = $args[0] ?? false;

		/* White label module's options are stored differently from our other modules */
		if ( $main_module === 'white_label' ) {
			$this->list_white_label_subsettings();
			return;
		}

		$subsettings = $this->get_all_subsettings()[ $main_module ] ?? '';

		if ( empty( $subsettings ) ) {
			/* translators: 1: Module name passed via wp cli. 2: Please try command again text string. */
			$message = sprintf( esc_html__( 'Module %1$s does not contain subsettings.%2$s', 'neve' ), $main_module, Messages::get_wrong_command_text() );
			\WP_CLI::error( $message, true );
		}

		$aliases = $this->get_aliases();
		$items   = array();

		foreach ( $subsettings as $subsetting => $details ) {
			
			$option = $this->get_option( $details );

			if ( $option == true && $details['type'] === 'toggle' ) {
				$option = 'on';
			}
			if ( $option == false && $details['type'] === 'toggle' ) {
				$option = 'off';
			}

			$items[] = array(
				'subsetting' => $details['name'],
				'key'        => $subsetting,
				'key_alias'  => $aliases[ $subsetting ] ?? '',
				'value'      => $option,
			); 
		}

		\WP_CLI\Utils\format_items( 'table', $items, array( 'subsetting', 'key', 'key_alias', 'value' ) );

	}

	/**
	 * Set a status of module settings. You can find a list of available setting keys by running: wp neve module setting list <main_module>
	 * 
	 * ## OPTIONS
	 *
	 * <main_module>
	 * : The main module that the setting belongs to.
	 *
	 * [<setting_key>]
	 * : The subsetting.
	 * 
	 * <setting_value>
	 * : The value to set for the setting.
	 * 
	 * [--all]
	 * : Toggles all the settings that contain switches according to the <setting_value> passed.
	 * 
	 * ## EXAMPLES
	 * 
	 * wp neve module setting set woocommerce_booster cart_notices on                        Turns on the "Multi-Announcement Bars" feature for WooCommerce Booster.
	 * wp neve module setting set woocommerce_booster cart_notices off                       Turns off the "Multi-Announcement Bars" feature for WooCommerce Booster.
	 * wp neve module setting set hfg_module featured_image_taxonomies 'post_tag,category'   Sets the taxonomies for "Allow featured image for taxonomies" feature of Header Booster.
	 * wp neve module setting set type_kit project_id f2n9u4                                 Sets the Typekit Project ID.
	 * wp neve module setting set type_kit loading_method js                                 Sets the Typekit loading method.
	 * 
	 * @param array $args 
	 * @return void 
	 */
	public function set( $args, $assoc_args ) {

		/* The main module this command should act on */
		$main_module = $args[0] ?? '';

		/* If user decides to toggle all setting switches for the module. */
		if ( in_array( 'all', $assoc_args ) ) {
			$this->toggle_all_subsettings( $main_module, $args[1] );
			return;
		}

		/* The subsetting this command should act on */
		$subsetting = $args[1] ?? '';

		/* The change the user would like to make to the module */
		$command_setting_value = $args[2] ?? '';

		/* White Label options of Neve Pro are stored differently from our other modules. */
		if ( $main_module === 'white_label' ) {
			$this->set_subsetting_white_label( $args );
			return;
		}

		$main_module_subsettings = $this->get_all_subsettings();

		if ( empty( $main_module_subsettings ) ) {
			\WP_CLI::error( esc_html__( 'No subsettings available.', 'neve' ) . Messages::get_wrong_command_text(), true );
		}

		$subsetting = $this->get_key_from_aliases( $subsetting );
		
		$subsetting = $main_module_subsettings[ $main_module ][ $subsetting ] ?? '';

		if ( empty( $subsetting ) ) {
			\WP_CLI::error( esc_html__( 'subsetting does not exist.', 'neve' ) . Messages::get_wrong_command_text(), true );
		}

		if ( empty( $command_setting_value ) ) {
			\WP_CLI::error( esc_html__( 'No setting value was provided.', 'neve' ) . Messages::get_wrong_command_text(), true );
		}

		$this->set_subsettings( $subsetting, $command_setting_value );
	}

	/**
	 * Get the activated/deactivated status of a module or subsetting
	 *
	 * @param array $data 
	 * @return bool|string
	 */
	private function get_option( $data ) {

		if ( empty( $data['option_key'] ) || ! isset( $data['default'] ) ) {
			return false;
		}

		$option = get_option( $data['option_key'] );
		$option = ( $option !== false ) ? $option : $data['default']; // If the option does not yet exist in the DB return the default.
		
		return $option;
	}

	/**
	 * Create our subsetting keys and their respective aliases.
	 * 
	 * @return array
	 */
	private function get_aliases() {
		
		$aliases = array(
			'enable_page_header'             => 'page_header',
			'enable_featured_image_taxonomy' => 'featured_image_taxonomy',
			'typekit_id'                     => 'project_id',
			'typekit_loading_method'         => 'loading_method',
			'enable_emoji_removal'           => 'emoji_removal',
			'enable_embedded_removal'        => 'embedded_removal',
			'enable_local_fonts'             => 'local_fonts',
			'enable_lazy_content'            => 'lazy_content',
			'author_name'                    => 'agency_author',
			'author_url'                     => 'agency_url',
			'starter_sites'                  => 'hide_sites_library',
			'my_library'                     => 'hide_my_templates',
			'white_label'                    => 'hide_options',
			'license'                        => 'hide_license',
		);

		return $aliases;
	}

	/**
	 * Get a subsetting option key name.
	 * 
	 * Returns the original key whether or not an alias was used in the command.
	 * 
	 * @param string $key 
	 * @return string 
	 */
	private function get_key_from_aliases( $key ) {

		$aliases = $this->get_aliases();
		$keys    = array_keys( $aliases );
		$values  = array_values( $aliases );

		if ( in_array( $key, $keys ) ) {
			$subsetting_key = $key;
		} elseif ( in_array( $key, $values ) ) {
			$subsetting_key = array_flip( $aliases )[ $key ];
		} else {
			$subsetting_key = $key;
		}

		return $subsetting_key;
	}

	/**
	 * Get all subsettings from main modules.
	 * 
	 * @return array 
	 */
	private function get_all_subsettings() {
		$main_modules = Loader::$cli_modules_data;
		$subsettings  = array_column( $main_modules, 'subsettings', 'slug' );
		return $subsettings;
	}

	/**
	 * List the Neve Pro white label modules.
	 * 
	 * Neve Pro's white label subsettings are stored differently from our other modules, so we need to account for them differently.
	 * 
	 * @return void 
	 */
	private function list_white_label_subsettings() {

		$possible_options = array(
			'author_name'        => '',
			'author_url'         => '',
			'starter_sites'      => '',
			'my_library'         => '',
			'plugin_name'        => '',
			'plugin_description' => '',
			'theme_name'         => '',
			'theme_description'  => '',
			'screenshot_url'     => '',
			'white_label'        => '',
			'license'            => '',
		);

		$currently_set_options = get_option( 'ti_white_label_inputs', array() );
		$options               = '';
		$items                 = array();
		
		if ( ! empty( $currently_set_options ) ) {
			$currently_set_options = json_decode( $currently_set_options, true );
			$options               = array_merge( $possible_options, $currently_set_options );
		} else {
			$options = $possible_options;
		}
	
		$nice_names = array(
			'author_name'        => esc_html__( 'Agency Author', 'neve' ),
			'author_url'         => esc_html__( 'Agency Author URL', 'neve' ),
			'starter_sites'      => esc_html__( 'Hide Sites Library', 'neve' ),
			'my_library'         => esc_html__( 'Hide My Library', 'neve' ),
			'plugin_name'        => esc_html__( 'Plugin Name', 'neve' ),
			'plugin_description' => esc_html__( 'Plugin Description', 'neve' ),
			'theme_name'         => esc_html__( 'Theme Name', 'neve' ),
			'theme_description'  => esc_html__( 'Theme Description', 'neve' ),
			'screenshot_url'     => esc_html__( 'Screenshot URL', 'neve' ),
			'white_label'        => esc_html__( 'Hide Options from Dashboard', 'neve' ),
			'license'            => esc_html__( 'Enable License Hiding', 'neve' ),
		);

		$booleans = array(
			'starter_sites',
			'my_library',
			'white_label',
			'license',
		);

		$aliases = $this->get_aliases();

		foreach ( $options as $option_key => $option_value ) {

			if ( in_array( $option_key, $booleans ) && ! empty( $option_value ) ) {
				$option_value = 'on';
			}

			if ( in_array( $option_key, $booleans ) && empty( $option_value ) ) {
				$option_value = 'off';
			}

			$items[] = array(
				'subsetting' => $nice_names[ $option_key ],
				'key'        => $option_key,
				'key_alias'  => $aliases[ $option_key ] ?? '',
				'value'      => $option_value,
			); 
		}
	
		\WP_CLI\Utils\format_items( 'table', $items, array( 'subsetting', 'key', 'key_alias', 'value' ) );

	}

	/**
	 * Set the status for subsettings.
	 * 
	 * @param array  $subsetting The Module's subsetting (aka Option) name.
	 * @param string $setting The value the user wants to set for the subsetting.
	 * @return void 
	 */
	private function set_subsettings( $subsetting, $setting ) {

		$option_key   = $subsetting['option_key'];
		$setting_type = $subsetting['type'];
		$setting      = sanitize_text_field( $setting );
		
		if ( $setting_type === 'text' ) {
			$status = update_option( $option_key, $setting );
			Messages::output_updated_status_message( $subsetting, $status );

		} elseif ( $setting_type === 'select' ) {

			if ( ! array_key_exists( $setting, $subsetting['choices'] ) ) {
				$valid_choices = implode( ', ', array_keys( $subsetting['choices'] ) );
				/* translators: %s: List of valid choices */
				\WP_CLI::error( sprintf( esc_html__( 'That setting value is not valid. Valid choices are: %s', 'neve' ), $valid_choices ), true );
			}

			$status = update_option( $option_key, $setting );
			Messages::output_updated_status_message( $subsetting, $status );

		} elseif ( $setting_type === 'toggle' && $setting === 'on' ) {
			$status = update_option( $option_key, true );
			Messages::output_updated_status_message( $subsetting, $status );

		} elseif ( $setting_type === 'toggle' && $setting === 'off' ) {
			$status = update_option( $option_key, false );
			Messages::output_updated_status_message( $subsetting, $status );

		} elseif ( $setting_type === 'multi_select' ) {
			/* translators: %s: Specifies the option key */
			\WP_CLI::error( sprintf( esc_html__( 'That setting key(%s) is not supported.', 'neve' ), $option_key ), true );
		} else {
			\WP_CLI::error( esc_html__( 'That setting value is not supported.', 'neve' ) . Messages::get_wrong_command_text(), true );
		}

	}

	/**
	 * 
	 * Toggle all the subsetting switches for a module.
	 * 
	 * Used when a user wants to toggle all subsettings that have switches.
	 * 
	 * @param string $main_module The main module name.
	 * @param string $setting The position of the toggle switch, whether 'on' or 'off'.
	 * @return void 
	 */
	private function toggle_all_subsettings( $main_module, $setting ) {

		$all_subsettings = $this->get_all_subsettings()[ $main_module ] ?? '';

		/* White label settings are stored differently from our other modules... */ 
		if ( $main_module === 'white_label' ) {
			$this->toggle_all_white_label_subsettings( $setting );
			return;
		}

		if ( empty( $all_subsettings ) ) {
			/* translators: 1: Module name passed via wp cli. 2: Please try command again text string. */
			$message = sprintf( esc_html__( 'Module %1$s does not contain subsettings.%2$s', 'neve' ), $main_module, Messages::get_wrong_command_text() );
			\WP_CLI::error( $message, true );
		}

		$status = false;

		foreach ( $all_subsettings as $subsetting => $details ) {
			$type       = $details['type'];
			$option_key = $details['option_key'];

			if ( $type === 'toggle' && $setting === 'on' ) {
				$status = update_option( $option_key, true );
			}

			if ( $type === 'toggle' && $setting === 'off' ) {
				$status = update_option( $option_key, false );
			}

			Messages::output_updated_status_message( $details, $status );
		}

	}

	/**
	 * Toggle all the subsetting switches for the white label modules.
	 * 
	 * White label module settings are stored differently from our other modules so we need to handle it differently.
	 * 
	 * @param string $setting The position of the toggle switch, whether 'on' or 'off'.
	 * @return void 
	 */
	private function toggle_all_white_label_subsettings( $setting ) {

		$setting = filter_var( $setting, FILTER_VALIDATE_BOOLEAN );
		
		$toggles = array(
			'starter_sites' => $setting,
			'my_library'    => $setting,
			'white_label'   => $setting,
			'license'       => $setting,
		);

		$nice_names = array(
			'starter_sites' => esc_html__( 'Hide Sites Library', 'neve' ),
			'my_library'    => esc_html__( 'Hide My Library', 'neve' ),
			'white_label'   => esc_html__( 'Hide Options from Dashboard', 'neve' ),
			'license'       => esc_html__( 'Enable License Hiding', 'neve' ),
		);

		$currently_set_options = get_option( 'ti_white_label_inputs', $toggles );

		if ( ! is_array( $currently_set_options ) ) {
			$currently_set_options = json_decode( $currently_set_options, true );
		}

		$updated_options = array_merge( $currently_set_options, $toggles );
		$updated_options = wp_json_encode( $updated_options );
		$status          = update_option( 'ti_white_label_inputs', $updated_options );

		foreach ( $toggles as $subsetting => $value ) {
			Messages::output_updated_status_message( array( 'name' => $nice_names[ $subsetting ] ), $status );
		}
		
	}

	/**
	 * Handle the subsetting settings for Neve White Label feature.
	 * 
	 * @param array $args 
	 * @return void 
	 */
	private function set_subsetting_white_label( $args ) {
		
		$settings = get_option( 'ti_white_label_inputs', array() );
		
		$subsetting = $args[1] ?? '';
		$subsetting = $this->get_key_from_aliases( $subsetting );

		if ( empty( $subsetting ) ) {
			\WP_CLI::error( esc_html__( 'subsetting does not exist.', 'neve' ) . Messages::get_wrong_command_text(), true );
		}

		$command_setting_value = $args[2] ?? '';
		if ( $command_setting_value === '' ) { // Allow users to still pass '0' as a value
			\WP_CLI::error( esc_html__( 'No setting value was provided.', 'neve' ) . Messages::get_wrong_command_text(), true );
		}

		$booleans = array(
			'starter_sites',
			'my_library',
			'white_label',
			'license',
		);

		/* Ensure these values are boolean */
		if ( in_array( $subsetting, $booleans ) ) {
			$command_setting_value = filter_var( $command_setting_value, FILTER_VALIDATE_BOOLEAN );
		}
		
		$settings                = json_decode( $settings, true );
		$settings[ $subsetting ] = $command_setting_value;
		$settings                = wp_json_encode( $settings );

		$status = update_option( 'ti_white_label_inputs', $settings );
		
		Messages::output_updated_status_message( array( 'name' => esc_html__( 'White Label', 'neve' ) ), $status );

	}

}
