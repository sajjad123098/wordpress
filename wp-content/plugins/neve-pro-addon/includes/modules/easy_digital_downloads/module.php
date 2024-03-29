<?php
/**
 * Author:          Uriahs Victor
 * Created on:      27/09/2021 (d/m/y)
 *
 * @package Neve
 */

namespace Neve_Pro\Modules\Easy_Digital_Downloads;

use Neve_Pro\Core\Abstract_Module;

/**
 * Class Module
 *
 * @package Neve_Pro\Modules\Woocommerce_Booster
 */
class Module extends Abstract_Module {

	/**
	 * Holds the base module namespace
	 * Used to load submodules.
	 *
	 * @var string $module_namespace
	 */
	private $module_namespace = 'Neve_Pro\Modules\Easy_Digital_Downloads';

	/**
	 * Define module properties.
	 *
	 * @access  public
	 * @return void
	 *
	 * @version 3.1.0
	 */
	public function define_module_properties() {
		$this->slug              = 'easy_digital_downloads';
		$this->name              = __( 'Easy Digital Downloads Booster', 'neve' );
		$this->description       = __( 'Enhance your Easy Digital Downloads store with additional customization settings.', 'neve' );
		$this->documentation     = array(
			'url'   => 'https://bit.ly/nv-edd-booster',
			'label' => __( 'Learn more', 'neve' ),
		);
		$this->order             = 2;
		$this->dependent_plugins = array(
			'easy-digital-downloads'     => array(
				'path' => 'easy-digital-downloads/easy-digital-downloads.php',
				'name' => 'Easy Digital Downloads',
			),
			'easy-digital-downloads-pro' => array(
				'path' => 'easy-digital-downloads-pro/easy-digital-downloads.php',
				'name' => 'Easy Digital Downloads Pro',
			),
			'relation'                   => 'OR',
		);

		$this->has_dynamic_style = true;
		$this->min_req_license   = 2;

	}

	/**
	 * Check if module should be loaded.
	 *
	 * @return bool
	 */
	public function should_load() {
		return ( $this->is_active() && class_exists( 'Easy_Digital_Downloads' ) );
	}

	/**
	 * Enqueue scripts and styles.
	 */
	public function enqueue_scripts() {

		wp_register_script(
			'neve-pro-addon-easy-digital-downloads',
			NEVE_PRO_INCLUDES_URL . 'modules/easy_digital_downloads/assets/js/build/script.js',
			array(
				'jquery',
			),
			NEVE_PRO_VERSION,
			true
		);

		wp_enqueue_script( 'neve-pro-addon-easy-digital-downloads' );

		$style_path = NEVE_PRO_INCLUDES_URL . 'modules/easy_digital_downloads/assets/css/style';
		wp_register_style( 'neve-pro-easy-digital-downloads', $style_path . ( ( NEVE_DEBUG ) ? '' : '.min' ) . '.css', array(), apply_filters( 'neve_version_filter', NEVE_VERSION ) );
		wp_style_add_data( 'neve-pro-easy-digital-downloads', 'rtl', 'replace' );
		wp_style_add_data( 'neve-pro-easy-digital-downloads', 'suffix', '.min' );
		wp_enqueue_style( 'neve-pro-easy-digital-downloads' );

	}

	/**
	 * Add customizer classes.
	 *
	 * @param array $classes loaded classes.
	 *
	 * @return array
	 */
	public function add_customizer_classes( $classes ) {

		return array_merge(
			array(
				'Modules\Easy_Digital_Downloads\Customizer\Layout',
				'Modules\Easy_Digital_Downloads\Customizer\Typography',
				'Modules\Easy_Digital_Downloads\Customizer\Cart_Icon',
			),
			$classes
		);

	}

	/**
	 * Show EDD Hooks when "Show Hooks" button is clicked in admin bar for custom layout module.
	 *
	 * @param mixed $hooks Hooks array.
	 * @return mixed
	 */
	public function show_additional_hooks( $hooks ) {

		$hooks['download-archive'] = array(
			'neve_before_download_archive',
			'neve_after_download_archive',
			'edd_download_before',
			'edd_download_after_thumbnail',
			'edd_download_before_title',
			'edd_download_after_title',
			'edd_download_after_content',
			'edd_download_after_price',
			'edd_download_after',
		);

		$hooks['single-download'] = array(
			'neve_before_single_download_title',
			'neve_after_single_download_title',
			'neve_before_download_meta',
			'neve_after_download_meta',
			'neve_before_download_thumbnail',
			'neve_after_download_thumbnail',
			'neve_before_download_content',
			'neve_after_download_content',
		);

		return $hooks;
	}

	/**
	 * Run Easy Digital Downloads Module
	 */
	public function run_module() {

		if ( ! $this->should_load() ) {
			return;
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		$submodules = array(
			$this->module_namespace . '\Views\EDD_Templates',
		);

		$mods = array();
		foreach ( $submodules as $index => $mod ) {
			if ( class_exists( $mod ) ) {
				$mods[ $index ] = new $mod();
				$mods[ $index ]->register_hooks();
			}
		}

		add_filter( 'neve_pro_filter_customizer_modules', array( $this, 'add_customizer_classes' ) );
		add_filter( 'neve_hooks_list', array( $this, 'show_additional_hooks' ) );
	}

}
