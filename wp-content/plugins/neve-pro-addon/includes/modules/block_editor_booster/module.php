<?php
/**
 * Block Editor Booster Module main file.
 *
 * @package Neve_Pro\Modules\Block_Editor_Booster
 */

namespace Neve_Pro\Modules\Block_Editor_Booster;

use Neve_Pro\Core\Abstract_Module;

use Neve_Pro\Core\Loader;
use ThemeisleSDK\Product;
use Neve_Pro\Traits\Core;

/**
 * Class Module
 *
 * @package Neve_Pro\Modules\Block_Editor_Booster
 */
class Module extends Abstract_Module {
	use Core;

	/**
	 * Is Otter New
	 *
	 * @var boolean
	 */
	private $is_otter_new;

	/**
	 * Is Otter New
	 *
	 * @var boolean
	 */
	private $has_otter_pro;

	/**
	 * Holds the base module namespace
	 * Used to load submodules.
	 *
	 * @var string $module_namespace
	 */
	private $module_namespace = 'Neve_Pro\Modules\Block_Editor_Booster'; // @phpstan-ignore-line - not read in this context. Can be removed if not used in the future.

	/**
	 * Conditional_Display constructor.
	 */
	public function __construct() {
		define( 'NEVE_PRO_HIDE_UPDATE_NOTICE', true );
		$this->is_otter_new  = defined( 'OTTER_BLOCKS_VERSION' ) && defined( 'OTTER_BLOCKS_PRO_SUPPORT' );
		$this->has_otter_pro = defined( 'OTTER_PRO_VERSION' );
		add_filter( 'plugins_api', array( $this, 'plugins_api_otter_pro' ), 10, 3 );

		parent::__construct();
	}

	/**
	 * Adds support to plugins_api for "otter-pro" slug
	 *
	 * @param  false|object|array $object Current object.
	 * @param  string             $action Request action type. E.g:plugin_information .
	 * @param  object             $args plugins_api params.
	 * @return false|object|array
	 */
	public function plugins_api_otter_pro( $object, $action, $args ) {
		if ( $action !== 'plugin_information' ) {
			return $object;
		}

		if ( $args->slug !== 'otter-pro' ) {
			return $object;
		}

		$pro_data = $this->get_pro_plugin_data();

		if ( $pro_data === false ) {
			return $object;
		}

		$r                = new \stdClass();
		$r->name          = $pro_data->name;
		$r->slug          = $pro_data->slug;
		$r->version       = $pro_data->new_version;
		$r->download_link = $pro_data->download_link;

		return $r;
	}

	/**
	 * Get Otter Pro details (zip, version etc.)
	 *
	 * @return object|false
	 */
	private function get_pro_plugin_data() {
		$response = $this->remote_get(
			sprintf(
				'%slicense/version/%s/%s/%s/%s',
				Product::API_URL,
				rawurlencode( 'Otter Pro' ),
				apply_filters( 'product_neve_license_key', 'free' ),
				defined( 'OTTER_BLOCKS_VERSION' ) ? OTTER_BLOCKS_VERSION : 'latest',
				rawurlencode( home_url() )
			),
			array(
				'timeout'   => 15, //phpcs:ignore WordPressVIPMinimum.Performance.RemoteRequestTimeout.timeout_timeout, Inherited by wp_remote_get only, for vip environment we use defaults.
				'sslverify' => false,
			)
		);

		if ( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) ) {
			return false;
		}

		return json_decode( wp_remote_retrieve_body( $response ) );
	}

	/**
	 * Define module properties.
	 *
	 * @access  public
	 * @return void
	 *
	 * @version 1.0.0
	 */
	public function define_module_properties() {
		$this->slug                      = 'block_editor_booster';
		$this->name                      = __( 'Block Editor Booster', 'neve' );
		$this->description               = __( 'Do more with the Block Editor with Otter\'s additional blocks made specifically for Neve Pro.', 'neve' );
		$this->order                     = 5;
		$this->min_req_license           = 1;
		$this->manageable_plugins        = true;
		$this->manageable_plugins_labels = [
			'firstLabel'      => __( 'Install & Activate Otter Blocks PRO', 'neve' ),
			/* translators: %s plugin name */
			'installing'      => __( '%s is installing', 'neve' ),
			/* translators: %s plugin name */
			'activating'      => __( '%s is activating', 'neve' ),
			/* translators: %s plugin name */
			'installActivate' => __( 'Install & Activate %s', 'neve' ),
			/* translators: %s plugin name */
			'activate'        => __( 'Activate %s', 'neve' ),
		];

		if ( ! $this->license_includes_otter() && ! $this->has_otter_pro ) {
			$this->min_req_license = 3;
		}

		$this->dependent_plugins = array(
			'otter-blocks' => array(
				'path' => 'otter-blocks/otter-blocks.php',
				'name' => 'Gutenberg Blocks and Template Library by Otter',
			),
			'otter-pro'    => array(
				'path' => 'otter-pro/otter-pro.php',
				'name' => 'Otter Pro',
			),
		);

		$this->links         = array(
			array(
				'url'   => admin_url( 'options-general.php?page=otter' ),
				'label' => __( 'Settings', 'neve' ),
			),
		);
		$this->documentation = array(
			'url'   => 'https://bit.ly/nv-gb-bl',
			'label' => __( 'Learn more', 'neve' ),
		);
	}

	/**
	 * Check if module should be loaded.
	 *
	 * @return bool
	 */
	function should_load() {
		return ( $this->is_active() && defined( 'OTTER_BLOCKS_VERSION' ) );
	}

	/**
	 * Run Block Editor Booster Module
	 */
	function run_module() {
		add_filter( 'neve_has_block_editor_module', '__return_true' );
	}

	/**
	 * Add Dashboard Notifications.
	 */
	public function add_dashboard_notifications( $notifications ) {
		$is_booster_active = true === boolval( get_option( 'nv_pro_block_editor_booster_status', true ) ) && 'valid' === apply_filters( 'product_neve_license_status', false ) && defined( 'OTTER_BLOCKS_VERSION' );
		$is_otter_new      = defined( 'OTTER_BLOCKS_VERSION' ) && defined( 'OTTER_BLOCKS_PRO_SUPPORT' );
		$plugin_folder     = defined( 'OTTER_BLOCKS_PATH' ) ? basename( OTTER_BLOCKS_PATH ) : null;
		$plugin_path       = $plugin_folder ? $plugin_folder . '/otter-blocks.php' : null;

		if ( ! $this->license_includes_otter() && ! $this->has_otter_pro ) {
			return $notifications;
		}

		if ( $is_booster_active && ! $is_otter_new ) {
			$notifications['otter-old'] = [
				'text'   => __( 'You need to update Otter and install Otter Pro to continue using Block Editor Booster', 'neve' ),
				'update' => [
					'type' => 'otter',
					'slug' => 'otter-old',
					'path' => $plugin_path,
				],
				'cta'    => __( 'Update & Install', 'neve' ),
				'type'   => 'warning',
			];
		}

		return $notifications;
	}

	/**
	 * Initialize the module.
	 */
	public function init() {
		if ( $this->should_load() && ! $this->is_otter_new ) {
			add_action( 'admin_notices', array( $this, 'old_otter_notice' ) );
		}
	}

	/**
	 * Notice displayed if using old version of Otter.
	 */
	function old_otter_notice() {
		$plugin_name = __( 'Block Editor Booster', 'neve' );
		$message     = __( 'You need to update Otter and install Otter Pro to continue using Block Editor Booster.', 'neve' );
		$page        = Loader::has_compatibility( 'theme_dedicated_menu' ) ? 'admin.php' : 'themes.php';

		printf(
			'<div class="error"><p><b>%1$s</b> %2$s <a href="%3$s">%4$s</a></p></div>',
			esc_html( $plugin_name ),
			esc_html( $message ),
			esc_url( admin_url( $page . '?page=neve-welcome' ) ),
			esc_html__( 'Update & Install', 'neve' )
		);
	}
}
