<?php
/**
 * Author:          Andrei Baicus <andrei@themeisle.com>
 * Created on:      2019-02-11
 *
 * @package Neve Pro
 */

namespace Neve_Pro\Modules\Woocommerce_Booster;

use Neve_Pro\Core\Abstract_Module;
use Neve\Core\Settings\Mods;
use Neve_Pro\Traits\Core;

/**
 * Class Module
 *
 * @package Neve_Pro\Modules\Woocommerce_Booster
 */
class Module extends Abstract_Module {
	use Core;

	/**
	 * Holds the base module namespace
	 * Used to load submodules.
	 *
	 * @var string $module_namespace
	 */
	private $module_namespace = 'Neve_Pro\Modules\Woocommerce_Booster';

	/**
	 * Define module properties.
	 *
	 * @access  public
	 * @return void
	 *
	 * @version 1.0.0
	 */
	public function define_module_properties() {
		$this->slug              = 'woocommerce_booster';
		$this->name              = __( 'WooCommerce Booster', 'neve' );
		$this->description       = __( 'Empower your online store with awesome new features, specially designed for a smooth WooCommerce integration.', 'neve' );
		$this->documentation     = array(
			'url'   => 'https://docs.themeisle.com/article/1058-woocommerce-booster-documentation',
			'label' => __( 'Learn more', 'neve' ),
		);
		$this->order             = 2;
		$this->dependent_plugins = array(
			'woocommerce' => array(
				'path' => 'woocommerce/woocommerce.php',
				'name' => 'WooCommerce',
			),
			'sparks'      => array(
				'path'     => 'sparks-for-woocommerce/sparks-for-woocommerce.php',
				'name'     => 'Sparks for WooCommerce',
				'external' => esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=install_sparks' ), 'install_sparks' ) ),
			),
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
		return ( $this->is_active() && class_exists( 'WooCommerce' ) && defined( 'SPARKS_WC_VERSION' ) );
	}

	/**
	 * Run WooCommerce Booster Module
	 */
	public function run_module() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'customize_controls_print_scripts', array( $this, 'change_iframe_preview' ), 30 );

		$submodules = array(
			$this->module_namespace . '\Views\Shop_Page',
			$this->module_namespace . '\Views\Shop_Product',
			$this->module_namespace . '\Views\Single_Product_Video',
			$this->module_namespace . '\Views\Single_Product',
			$this->module_namespace . '\Views\Cart_Page',
			$this->module_namespace . '\Views\Checkout_Page',
			$this->module_namespace . '\Views\Payment_Icons',
		);

		if ( get_theme_mod( 'neve_shop_pagination_type' ) === 'infinite' ) {
			$submodules[] = $this->module_namespace . '\Views\Infinite_Scroll';
		}

		$mods = array();
		foreach ( $submodules as $index => $mod ) {
			if ( class_exists( $mod ) ) {
				$mods[ $index ] = new $mod();
				$mods[ $index ]->register_hooks();
			}
		}

		add_filter( 'neve_pro_filter_customizer_modules', array( $this, 'add_customizer_classes' ) );
		add_filter( 'neve_header_presets_v2', array( $this, 'add_header_presets' ) );
		add_action( 'pre_get_posts', array( $this, 'exclude_hidden_products_from_search' ) );
	}

	/**
	 * Add header presets.
	 *
	 * @param array $presets header presets array.
	 *
	 * @return array
	 */
	public function add_header_presets( $presets ) {
		return array_merge(
			$presets,
			array(
				array(
					'label' => 'Two Row Search Cart',
					'image' => NEVE_PRO_INCLUDES_URL . 'modules/woocommerce_booster/assets/img/TwoRowSearchCart.jpg',
					'setup' => '{"hfg_header_layout_v2":"{\"desktop\":{\"top\":{\"left\":[],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[]},\"main\":{\"left\":[{\"id\":\"logo\"}],\"c-left\":[],\"center\":[{\"id\":\"header_search\"}],\"c-right\":[],\"right\":[{\"id\":\"header_cart_icon\"}]},\"bottom\":{\"left\":[{\"id\":\"primary-menu\"}],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[]}},\"mobile\":{\"top\":{\"left\":[],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[]},\"main\":{\"left\":[{\"id\":\"logo\"}],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[{\"id\":\"header_search_responsive\"},{\"id\":\"header_cart_icon\"}]},\"bottom\":{\"left\":[{\"id\":\"nav-icon\"}],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[]},\"sidebar\":[{\"id\":\"primary-menu\"}]}}","logo_component_align":{"mobile":"center","tablet":"center","desktop":"center"}}',
				),
				array(
					'label' => 'Search Menu Cart',
					'image' => NEVE_PRO_INCLUDES_URL . 'modules/woocommerce_booster/assets/img/SearchMenuCart.jpg',
					'setup' => '{"hfg_header_layout_v2":"{\"desktop\":{\"top\":{\"left\":[],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[]},\"main\":{\"left\":[{\"id\":\"logo\"},{\"id\":\"header_search_responsive\"}],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[{\"id\":\"primary-menu\"},{\"id\":\"header_cart_icon\"}]},\"bottom\":{\"left\":[],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[]}},\"mobile\":{\"top\":{\"left\":[],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[]},\"main\":{\"left\":[{\"id\":\"logo\"}],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[{\"id\":\"header_cart_icon\"},{\"id\":\"nav-icon\"}]},\"bottom\":{\"left\":[],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[]},\"sidebar\":[{\"id\":\"header_search\"},{\"id\":\"primary-menu\"}]}}"}',
				),
				array(
					'label' => 'Two Row Search Cart',
					'image' => NEVE_PRO_INCLUDES_URL . 'modules/woocommerce_booster/assets/img/TwoRowSearchCart2.jpg',
					'setup' => '{"hfg_header_layout_v2":"{\"desktop\":{\"top\":{\"left\":[],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[{\"id\":\"header_cart_icon\"}]},\"main\":{\"left\":[{\"id\":\"logo\"},{\"id\":\"primary-menu\"}],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[{\"id\":\"header_search\"}]},\"bottom\":{\"left\":[],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[]}},\"mobile\":{\"top\":{\"left\":[],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[{\"id\":\"header_cart_icon\"}]},\"main\":{\"left\":[{\"id\":\"logo\"}],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[{\"id\":\"nav-icon\"},{\"id\":\"header_search_responsive\"}]},\"bottom\":{\"left\":[],\"c-left\":[],\"center\":[],\"c-right\":[],\"right\":[]},\"sidebar\":[{\"id\":\"primary-menu\"}]}}"}',
				),
			)
		);
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
				'Modules\Woocommerce_Booster\Customizer\Single_Product',
				'Modules\Woocommerce_Booster\Customizer\Cart_Page',
				'Modules\Woocommerce_Booster\Customizer\Checkout_Page',
				'Modules\Woocommerce_Booster\Customizer\Shop_Page',
				'Modules\Woocommerce_Booster\Customizer\Payment_Icons',
				'Modules\Woocommerce_Booster\Customizer\Cart_Icon',
				'Modules\Woocommerce_Booster\Customizer\Typography',
			),
			$classes
		);
	}

	/**
	 * Enqueue scripts and styles.
	 */
	public function enqueue_scripts() {
		$path = 'style.min.css';

		$this->rtl_enqueue_style( 'neve-pro-addon-woo-booster', NEVE_PRO_INCLUDES_URL . 'modules/woocommerce_booster/assets/' . $path, array(), NEVE_PRO_VERSION );

		wp_register_script(
			'neve-pro-addon-woo-booster',
			NEVE_PRO_INCLUDES_URL . 'modules/woocommerce_booster/assets/js/build/script.js',
			array(
				'jquery',
				'woocommerce',
				'wc-cart-fragments',
			),
			NEVE_PRO_VERSION,
			true
		);

		global $wp_query;


		$woo_booster_options = array(
			'relatedSliderStatus'  => $this->get_theme_mod_status( 'neve_enable_product_related_slider' ),
			'gallerySliderStatus'  => $this->get_theme_mod_status( 'neve_enable_product_gallery_thumbnails_slider' ),
			'recentlyViewedStatus' => $this->get_theme_mod_status( 'neve_enable_related_viewed' ),
			'labelsAsPlaceholders' => $this->get_theme_mod_status( 'neve_checkout_labels_placeholders' ),
			'relatedSliderPerCol'  => get_theme_mod( 'neve_single_product_related_columns' ),
			'galleryLayout'        => $this->get_gallery_layout(),
			'modalContentEndpoint' => rest_url( NEVE_PRO_REST_NAMESPACE . '/products/post/' ),
			'infiniteScrollQuery'  => wp_json_encode( $wp_query->query ),
			'nonce'                => wp_create_nonce( 'wp_rest' ),
			'loggedIn'             => is_user_logged_in(),
			'i18n_selectOptions'   => __( 'Select options', 'neve' ),
		);

		wp_localize_script(
			'neve-pro-addon-woo-booster',
			'neveWooBooster',
			$woo_booster_options
		);

		wp_script_add_data( 'neve-pro-addon-woo-booster', 'async', true );
		wp_enqueue_script( 'neve-pro-addon-woo-booster' );
		wp_enqueue_script( 'wc-add-to-cart-variation' );
	}

	/**
	 * Get status of a theme mod.
	 *
	 * @param string $mod Theme mod name.
	 *
	 * @return string
	 */
	private function get_theme_mod_status( $mod ) {
		$status = get_theme_mod( $mod, false );

		if ( false === $status ) {
			return 'disabled';
		}

		return 'enabled';
	}

	/**
	 * Get gallery layout.
	 *
	 * @return string
	 */
	private function get_gallery_layout() {
		return get_theme_mod( 'neve_single_product_gallery_layout', 'normal' );
	}

	/**
	 * Scripts to change the iframe preview.
	 */
	public function change_iframe_preview() {
		?>
		<script type="text/javascript">
			jQuery(document).ready(function ($) {
				wp.customize.section('neve_cart_page_layout', function (section) {
					section.expanded.bind(function (isExpanded) {
						if (isExpanded) {
							wp.customize.previewer.previewUrl.set('<?php echo esc_js( wc_get_page_permalink( 'cart' ) ); ?>')
						}
					});
				});
			});
		</script>
		<?php
	}

	/**
	 * Exclude hidden products from search.
	 *
	 * @param \WP_Query $query WP Query.
	 */
	public function exclude_hidden_products_from_search( $query ) {
		if ( ! $query->is_main_query() || ! $query->is_search() || is_admin() ) {
			return;
		}
		
		$tax_query = $query->get( 'tax_query' );
		
		if ( ! $tax_query ) {
			$tax_query = array();
		}
		
		$tax_query[] = array(
			'taxonomy' => 'product_visibility',
			'field'    => 'name',
			'terms'    => 'exclude-from-search',
			'operator' => 'NOT IN',
		);
		
		$query->set( 'tax_query', $tax_query );
	}
}
