<?php
/**
 * Elementor Booster Module main file.
 *
 * @package Neve_Pro\Modules\Elementor_Booster
 */

namespace Neve_Pro\Modules\Elementor_Booster;

use Elementor\Core\Files\CSS\Post;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Neve_Pro\Core\Abstract_Module;

/**
 * Class Module
 *
 * @package Neve_Pro\Modules\Elementor_Booster
 */
class Module extends Abstract_Module {

	/**
	 * Holds the base module namespace
	 * Used to load submodules.
	 *
	 * @var string $module_namespace
	 */
	private $module_namespace = 'Neve_Pro\Modules\Elementor_Booster';

	/**
	 * Elementor widgets class array.
	 *
	 * @var array
	 */
	private $widgets;

	/**
	 * Elementor extensions.
	 *
	 * @var array
	 */
	private $extensions;

	/**
	 * Define module properties.
	 *
	 * @access  public
	 * @return void
	 *
	 * @version 1.0.0
	 */
	public function define_module_properties() {
		$this->slug              = 'elementor_booster';
		$this->name              = __( 'Elementor Booster', 'neve' );
		$this->description       = __( 'Leverage the true flexibility of Elementor with powerful addons and templates that you can import with just one click.', 'neve' );
		$this->order             = 7;
		$this->dependent_plugins = array(
			'elementor' => array(
				'path' => 'elementor/elementor.php',
				'name' => 'Elementor',
			),
		);
		$this->documentation     = array(
			'url'   => 'https://docs.themeisle.com/article/1063-elementor-booster-module-documentation',
			'label' => __( 'Learn more', 'neve' ),
		);

		$this->widgets = array(
			$this->module_namespace . '\Widgets\Flip_Card',
			$this->module_namespace . '\Widgets\Review_Box',
			$this->module_namespace . '\Widgets\Share_Buttons',
			$this->module_namespace . '\Widgets\Typed_Headline',
			$this->module_namespace . '\Widgets\Team_Member',
			$this->module_namespace . '\Widgets\Progress_Circle',
			$this->module_namespace . '\Widgets\Banner',
			$this->module_namespace . '\Widgets\Content_Switcher',
			$this->module_namespace . '\Widgets\Custom_Field',
			$this->module_namespace . '\Widgets\Instagram_Feed',
		);

		$this->extensions = array(
			$this->module_namespace . '\Extensions\Particle_Section',
			$this->module_namespace . '\Extensions\Content_Protection',
			$this->module_namespace . '\Extensions\Advanced_Animation',
		);
	}

	/**
	 * Check if module should be loaded.
	 *
	 * @return bool
	 */
	function should_load() {
		return ( $this->is_active() && defined( 'ELEMENTOR_VERSION' ) );
	}

	/**
	 * Run Elementor Booster Module
	 */
	function run_module() {
		add_action( 'after_setup_theme', array( $this, 'register_extensions' ) );
		add_action( self::get_register_widget_hook(), array( $this, 'register_widgets' ) );
		add_action( 'elementor/elements/categories_registered', array( $this, 'add_elementor_widget_categories' ) );
		add_action( 'elementor/frontend/after_enqueue_styles', array( $this, 'register_styles' ) );

		add_action( 'elementor/frontend/after_register_scripts', array( $this, 'register_scripts' ) );

		// Register and load Font Awesome 5 in Elementor editor pane.
		add_action(
			'elementor/editor/before_enqueue_scripts',
			function() {
				wp_enqueue_style( 'font-awesome-5-all', ELEMENTOR_ASSETS_URL . '/lib/font-awesome/css/all.min.css', array(), NEVE_PRO_VERSION );
			}
		);

		add_action(
			'elementor/editor/after_enqueue_scripts',
			function() {
				do_action( 'themeisle_sdk_load_telemetry' );
			}
		);

		/**
		 * Instagram Feed Widget
		 */
		add_action( 'elementor/editor/after_save', array( $this, 'do_after_save' ), 10, 2 );
		add_action( 'neve_elementor_booster_refresh_instagram_access_token', array( $this, 'instagram_feed__refresh_access_tokens' ) );
		add_action( 'delete_post', array( $this, 'instagram_feed__prune_old_widgets' ) );
		$this->instagram_feed__setup_cron_tasks();
	}

	/**
	 * Register extensions
	 */
	public function register_extensions() {
		foreach ( $this->extensions as $extension ) {
			new $extension();
		}
	}

	/**
	 * Get WP hook action name to register Elementor Widgets.
	 *
	 * @return string
	 */
	public static function get_register_widget_hook() {
		if ( defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '3.5.0', '<' ) ) {
			return 'elementor/widgets/widgets_registered';
		}

		return 'elementor/widgets/register';
	}

	/**
	 * Wrapper for core Elementor Register Widget method.
	 *
	 * @param  \Elementor\Widget_Base $widget Widget Instance.
	 * @return void
	 */
	public static function wrapper_register_widget( $widget ) {
		if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
			return;
		}

		if ( version_compare( ELEMENTOR_VERSION, '3.5.0', '<' ) ) {
			Plugin::instance()->widgets_manager->register_widget_type( new $widget() );
		}

		Plugin::instance()->widgets_manager->register( new $widget() ); // @phpstan-ignore-line
	}

	/**
	 * Register Elementor Widgets.
	 */
	public function register_widgets() {
		foreach ( $this->widgets as $widget ) {
			self::wrapper_register_widget( new $widget() );
		}
	}

	/**
	 * Add a new category of widgets.
	 *
	 * @param \Elementor\Elements_Manager $elements_manager Elementor elements manager.
	 */
	public function add_elementor_widget_categories( $elements_manager ) {
		$elements_manager->add_category(
			'neve-elementor-widgets',
			array(
				'title' => esc_html__( 'Neve Pro Addon Widgets', 'neve' ),
				'icon'  => 'fa fa-plug',
			)
		);
	}

	/**
	 * Register styles and maybe load them on the editor side when needed.
	 */
	function register_styles() {
		$this->rtl_enqueue_style( 'neve-elementor-widgets-styles', NEVE_PRO_INCLUDES_URL . 'modules/elementor_booster/assets/css/style.min.css', array(), NEVE_PRO_VERSION );
		wp_register_style(
			'font-awesome-5-all',
			ELEMENTOR_ASSETS_URL . 'lib/font-awesome/css/all.min.css',
			array(),
			NEVE_PRO_VERSION
		);
	}

	/**
	 * Register scripts.
	 */
	public function register_scripts() {

		// Typed text widget scripts
		wp_register_script( 'neb-typed-animation', NEVE_PRO_INCLUDES_URL . 'modules/elementor_booster/assets/js/typed-text/typed.min.js', array( 'jquery' ), NEVE_PRO_VERSION, true );
		wp_register_script( 'neb-typed-script', NEVE_PRO_INCLUDES_URL . 'modules/elementor_booster/assets/js/typed-text/typed-main.js', array( 'neb-typed-animation' ), NEVE_PRO_VERSION, true );

		// Flip card widget scripts
		wp_register_script( 'neb-flip-card-script', NEVE_PRO_INCLUDES_URL . 'modules/elementor_booster/assets/js/flip-card.js', array( 'jquery' ), NEVE_PRO_VERSION, true );

		// Particles script
		wp_register_script( 'neb-particles', NEVE_PRO_INCLUDES_URL . 'modules/elementor_booster/assets/js/particles/particles.min.js', array( 'jquery' ), NEVE_PRO_VERSION, true );
		wp_register_script( 'neb-particles-script', NEVE_PRO_INCLUDES_URL . 'modules/elementor_booster/assets/js/particles/particles-main.js', array( 'neb-particles' ), NEVE_PRO_VERSION, true );
		wp_localize_script( 'neb-particles-script', 'nebData', $this->localize_data() );
		if ( Plugin::$instance->preview->is_preview_mode() ) {
			wp_enqueue_script( 'neb-particles-script' );
		}

		// Progress Circle
		wp_register_script( 'neb-as-pie-progress', NEVE_PRO_INCLUDES_URL . 'modules/elementor_booster/assets/js/progress-circle/jquery-asPieProgress.min.js', array( 'jquery' ), NEVE_PRO_VERSION, true );
		wp_register_script( 'neb-appear', NEVE_PRO_INCLUDES_URL . 'modules/elementor_booster/assets/js/progress-circle/jquery.appear.min.js', array( 'jquery' ), NEVE_PRO_VERSION, true );
		wp_register_script( 'neb-progress-circle', NEVE_PRO_INCLUDES_URL . 'modules/elementor_booster/assets/js/progress-circle/progress-circle.js', array( 'neb-as-pie-progress', 'neb-appear' ), NEVE_PRO_VERSION, true );

		// Content Switcher
		wp_register_script( 'neb-content-switcher', NEVE_PRO_INCLUDES_URL . 'modules/elementor_booster/assets/js/content-switcher.js', array( 'jquery' ), NEVE_PRO_VERSION, true );

		// Advanced Animations
		wp_register_script( 'neb-anime', NEVE_PRO_INCLUDES_URL . 'modules/elementor_booster/assets/js/advanced-animations/anime.min.js', array(), NEVE_PRO_VERSION, true );
		wp_register_script( 'neb-animations', NEVE_PRO_INCLUDES_URL . 'modules/elementor_booster/assets/js/advanced-animations/advanced-animations.js', array( 'neb-anime', 'jquery' ), NEVE_PRO_VERSION, true );

		// Instagram Feed
		wp_register_script( 'neb-instagram-feed', NEVE_PRO_INCLUDES_URL . 'modules/elementor_booster/assets/js/instagram-feed.js', array( 'jquery', 'swiper' ), NEVE_PRO_VERSION, true );

		// Usage observer
		wp_enqueue_script( 'neb-track', NEVE_PRO_INCLUDES_URL . 'modules/elementor_booster/assets/js/track.js', array( 'jquery' ), NEVE_PRO_VERSION, true );

		if ( Plugin::$instance->preview->is_preview_mode() ) {
			wp_enqueue_script( 'neb-animations' );
		}

	}

	/**
	 * Localize data for js script
	 *
	 * @return array
	 */
	private function localize_data() {

		$data                       = [];
		$data['ParticleThemesData'] = [
			'default'  => '{"particles":{"number":{"value":160,"density":{"enable":true,"value_area":800}},"color":{"value":"#ffffff"},"shape":{"type":"circle","stroke":{"width":0,"color":"#000000"},"polygon":{"nb_sides":5},"image":{"src":"img/github.svg","width":100,"height":100}},"opacity":{"value":0.5,"random":false,"anim":{"enable":false,"speed":1,"opacity_min":0.1,"sync":false}},"size":{"value":3,"random":true,"anim":{"enable":false,"speed":40,"size_min":0.1,"sync":false}},"line_linked":{"enable":true,"distance":150,"color":"#ffffff","opacity":0.4,"width":1},"move":{"enable":true,"speed":6,"direction":"none","random":false,"straight":false,"out_mode":"out","bounce":false,"attract":{"enable":false,"rotateX":600,"rotateY":1200}}},"interactivity":{"detect_on":"canvas","events":{"onhover":{"enable":true,"mode":"repulse"},"onclick":{"enable":true,"mode":"push"},"resize":true},"modes":{"grab":{"distance":400,"line_linked":{"opacity":1}},"bubble":{"distance":400,"size":40,"duration":2,"opacity":8,"speed":3},"repulse":{"distance":200,"duration":0.4},"push":{"particles_nb":4},"remove":{"particles_nb":2}}},"retina_detect":true}',
			'nasa'     => '{"particles":{"number":{"value":250,"density":{"enable":true,"value_area":800}},"color":{"value":"#ffffff"},"shape":{"type":"circle","stroke":{"width":0,"color":"#000000"},"polygon":{"nb_sides":5},"image":{"src":"img/github.svg","width":100,"height":100}},"opacity":{"value":1,"random":true,"anim":{"enable":true,"speed":1,"opacity_min":0,"sync":false}},"size":{"value":3,"random":true,"anim":{"enable":false,"speed":4,"size_min":0.3,"sync":false}},"line_linked":{"enable":false,"distance":150,"color":"#ffffff","opacity":0.4,"width":1},"move":{"enable":true,"speed":1,"direction":"none","random":true,"straight":false,"out_mode":"out","bounce":false,"attract":{"enable":false,"rotateX":600,"rotateY":600}}},"interactivity":{"detect_on":"canvas","events":{"onhover":{"enable":true,"mode":"bubble"},"onclick":{"enable":true,"mode":"repulse"},"resize":true},"modes":{"grab":{"distance":400,"line_linked":{"opacity":1}},"bubble":{"distance":250,"size":0,"duration":2,"opacity":0,"speed":3},"repulse":{"distance":400,"duration":0.4},"push":{"particles_nb":4},"remove":{"particles_nb":2}}},"retina_detect":true}',
			'bubble'   => '{"particles":{"number":{"value":15,"density":{"enable":true,"value_area":800}},"color":{"value":"#1b1e34"},"shape":{"type":"polygon","stroke":{"width":0,"color":"#000"},"polygon":{"nb_sides":6},"image":{"src":"img/github.svg","width":100,"height":100}},"opacity":{"value":0.3,"random":true,"anim":{"enable":false,"speed":1,"opacity_min":0.1,"sync":false}},"size":{"value":50,"random":false,"anim":{"enable":true,"speed":10,"size_min":40,"sync":false}},"line_linked":{"enable":false,"distance":200,"color":"#ffffff","opacity":1,"width":2},"move":{"enable":true,"speed":8,"direction":"none","random":false,"straight":false,"out_mode":"out","bounce":false,"attract":{"enable":false,"rotateX":600,"rotateY":1200}}},"interactivity":{"detect_on":"canvas","events":{"onhover":{"enable":false,"mode":"grab"},"onclick":{"enable":false,"mode":"push"},"resize":true},"modes":{"grab":{"distance":400,"line_linked":{"opacity":1}},"bubble":{"distance":400,"size":40,"duration":2,"opacity":8,"speed":3},"repulse":{"distance":200,"duration":0.4},"push":{"particles_nb":4},"remove":{"particles_nb":2}}},"retina_detect":true}',
			'snow'     => '{"particles":{"number":{"value":450,"density":{"enable":true,"value_area":800}},"color":{"value":"#fff"},"shape":{"type":"circle","stroke":{"width":0,"color":"#000000"},"polygon":{"nb_sides":5},"image":{"src":"img/github.svg","width":100,"height":100}},"opacity":{"value":0.5,"random":true,"anim":{"enable":false,"speed":1,"opacity_min":0.1,"sync":false}},"size":{"value":5,"random":true,"anim":{"enable":false,"speed":40,"size_min":0.1,"sync":false}},"line_linked":{"enable":false,"distance":500,"color":"#ffffff","opacity":0.4,"width":2},"move":{"enable":true,"speed":6,"direction":"bottom","random":false,"straight":false,"out_mode":"out","bounce":false,"attract":{"enable":false,"rotateX":600,"rotateY":1200}}},"interactivity":{"detect_on":"canvas","events":{"onhover":{"enable":true,"mode":"bubble"},"onclick":{"enable":true,"mode":"repulse"},"resize":true},"modes":{"grab":{"distance":400,"line_linked":{"opacity":0.5}},"bubble":{"distance":400,"size":4,"duration":0.3,"opacity":1,"speed":3},"repulse":{"distance":200,"duration":0.4},"push":{"particles_nb":4},"remove":{"particles_nb":2}}},"retina_detect":true}',
			'nyan_cat' => '{"particles":{"number":{"value":150,"density":{"enable":false,"value_area":800}},"color":{"value":"#ffffff"},"shape":{"type":"star","stroke":{"width":0,"color":"#000000"},"polygon":{"nb_sides":5},"image":{"src":"http://wiki.lexisnexis.com/academic/images/f/fb/Itunes_podcast_icon_300.jpg","width":100,"height":100}},"opacity":{"value":0.5,"random":false,"anim":{"enable":false,"speed":1,"opacity_min":0.1,"sync":false}},"size":{"value":4,"random":true,"anim":{"enable":false,"speed":40,"size_min":0.1,"sync":false}},"line_linked":{"enable":false,"distance":150,"color":"#ffffff","opacity":0.4,"width":1},"move":{"enable":true,"speed":14,"direction":"left","random":false,"straight":true,"out_mode":"out","bounce":false,"attract":{"enable":false,"rotateX":600,"rotateY":1200}}},"interactivity":{"detect_on":"canvas","events":{"onhover":{"enable":false,"mode":"grab"},"onclick":{"enable":true,"mode":"repulse"},"resize":true},"modes":{"grab":{"distance":200,"line_linked":{"opacity":1}},"bubble":{"distance":400,"size":40,"duration":2,"opacity":8,"speed":3},"repulse":{"distance":200,"duration":0.4},"push":{"particles_nb":4},"remove":{"particles_nb":2}}},"retina_detect":true}',
		];

		return $data;
	}

	/**
	 * Enqueue font awesome 5.
	 *
	 * @param int $post_id Post id.
	 *
	 * @return bool
	 */
	public function enqueue_fa5_fonts( $post_id ) {
		$post_css = new Post( $post_id );
		$meta     = $post_css->get_meta();
		if ( empty( $meta['icons'] ) ) {
			return false;
		}
		$icons_types = Icons_Manager::get_icon_manager_tabs();
		foreach ( $meta['icons'] as $icon_font ) {
			if ( ! isset( $icons_types[ $icon_font ] ) ) {
				continue;
			}
			Plugin::instance()->frontend->enqueue_font( $icon_font );
		}

		return true;
	}

	/**
	 * Clear an Instagram import scheduled task from WordPress.
	 *
	 * @param int    $post_id The WP Post ID where the Instagram widget lives.
	 * @param string $widget_id The Elementor widget ID.
	 *
	 * @return void
	 */
	private function instagram_feed__clear_scheduled_task( $post_id, $widget_id ) {
		wp_clear_scheduled_hook( 'neve_elementor_booster_cache_instagram_feed_data_' . $post_id . '_' . $widget_id, array( $post_id, $widget_id ) );
	}

	/**
	 * Remove transient widget settings from the DB.
	 *
	 * @param mixed $post_id The current Post ID.
	 * @param mixed $widget_id The widget ID to use for our operations.
	 * @return void
	 */
	private function instagram_feed__remove_transient_widget_settings( $post_id, $widget_id ) {

		$widgets_settings = get_option( 'neb_instagram_feed_transient_widgets' );

		foreach ( $widgets_settings as $key => $widget_setting ) {

			if ( $widget_setting['post_id'] === $post_id && $widget_setting['widget_id'] === $widget_id ) {

				unset( $widgets_settings[ $key ] );

				// Remove the widget transient
				delete_transient( 'neb_instagram_api_media_data_' . $post_id . '_' . $widget_id );

			}
		}

		$widgets_settings = array_values( $widgets_settings );
		update_option( 'neb_instagram_feed_transient_widgets', $widgets_settings );

	}

	/**
	 * Prune or remove a widget's details based on the post ID and widget ID.
	 *
	 * @param int    $post_id The WP post ID.
	 * @param string $widget_id The widget ID to use for our operations.
	 *
	 * @return mixed
	 */
	private function instagram_feed__remove_cron_widget_settings( $post_id, $widget_id ) {

		$widgets_settings = get_option( 'neb_instagram_feed_cron_widgets' );
		$cached_image_ids = get_option( 'neb_instagram_feed_cached_image_ids' );

		foreach ( $widgets_settings as $key => $widget_setting ) {

			if ( $widget_setting['post_id'] === $post_id && $widget_setting['widget_id'] === $widget_id ) {

				unset( $widgets_settings[ $key ] );

				/* Remove the widget scheduled task. */
				$this->instagram_feed__clear_scheduled_task( $widget_setting['post_id'], $widget_id );

				/* Remove the widget transient */
				delete_transient( 'neb_instagram_api_media_data_' . $post_id . '_' . $widget_id );

			}
		}

		foreach ( $cached_image_ids as $key => $data ) {

			if ( $data['post_id'] === $post_id && $data['widget_id'] === $widget_id ) {

				/* Remove the widget key from our cached image ids array */
				unset( $cached_image_ids[ $key ] );

				/* Delete the images that have been unset from the media library. */
				$this->instagram_feed__delete_images_from_library( $data['widget_images'] );

			}
		}

		/* Reset indexes */
		$widgets_settings = array_values( $widgets_settings );
		$cached_image_ids = array_values( $cached_image_ids );

		update_option( 'neb_instagram_feed_cron_widgets', $widgets_settings );
		update_option( 'neb_instagram_feed_cached_image_ids', $cached_image_ids );

		return $widgets_settings;

	}

	/**
	 * Save cron instagram feed widgets.
	 *
	 * @param mixed $post_id The current Post ID.
	 * @param mixed $page_cron_widgets Cron widgets that currently exist on the page.
	 * @return mixed
	 */
	private function instagram_feed__save_cron_widgets( $post_id, $page_cron_widgets ) {

		$cron_widgets_settings = get_option( 'neb_instagram_feed_cron_widgets', array() );

		/*
		* If the $cron_widgets_settings variable is empty, it means we should save all the current cron widgets in the $page_cron_widgets array
		* since there's no need to do further operations at this time we're bailing by returning the $page_cron_widgets which exist on the page.
		*/
		if ( empty( $cron_widgets_settings ) ) {
			update_option( 'neb_instagram_feed_cron_widgets', $page_cron_widgets );
			return $page_cron_widgets;
		}

		/* If no cron widgets exist on the page again, lets clear them from the saved option */
		if ( empty( $page_cron_widgets ) ) {
			foreach ( $cron_widgets_settings as $key => $cron_widgets_setting ) {
				if ( $post_id === $cron_widgets_setting['post_id'] ) {
					$this->instagram_feed__remove_cron_widget_settings( $post_id, $cron_widgets_setting['widget_id'] );
				}
			}
			return;
		}

		$saved_cron_widget_ids = array_column( $cron_widgets_settings, 'widget_id' );
		$page_cron_widgets_ids = array_column( $page_cron_widgets, 'widget_id' );

		foreach ( $page_cron_widgets as $held_setting_key => $held_setting ) {
			foreach ( $cron_widgets_settings as $widget_setting_key => $setting ) {

				/* If a cron widget already exists in our array of saved cron widgets, update it with new values from elementor. */
				if ( $setting['post_id'] === $held_setting['post_id'] && $setting['widget_id'] === $held_setting['widget_id'] ) {
					$cron_widgets_settings[ $widget_setting_key ] = $held_setting;
				}

				/* If a cron widget doesn't exist in our array of saved cron widgets yet, add the new values from elementor to our array. */
				if ( ! in_array( $held_setting['widget_id'], $saved_cron_widget_ids ) ) {
					$cron_widgets_settings[] = $held_setting;
				}

				/* If a cron widget no longer exists on the page, then remove its settings and operations */
				if ( ! in_array( $setting['widget_id'], $page_cron_widgets_ids ) && $setting['post_id'] === $post_id ) {
					$cron_widgets_settings = $this->instagram_feed__remove_cron_widget_settings( $setting['post_id'], $setting['widget_id'] );
				}
			}
		}

		/* Reset our keys */
		$cron_widgets_settings = array_values( $cron_widgets_settings );
		$cron_widgets_settings = array_unique( $cron_widgets_settings, SORT_REGULAR );

		update_option( 'neb_instagram_feed_cron_widgets', $cron_widgets_settings );

		return $cron_widgets_settings;

	}

	/**
	 * Save transient instagram feed widgets.
	 *
	 * @param mixed $post_id The current Post ID.
	 * @param mixed $page_transient_widgets Transient widgets that currently exist on the page.
	 * @return void
	 */
	private function instagram_feed__save_transient_widgets( $post_id, $page_transient_widgets ) {

		/* Save metadata about widgets that are not caching to media library */
		$saved_transient_widgets = get_option( 'neb_instagram_feed_transient_widgets', array() );

		if ( empty( $saved_transient_widgets ) ) {
			update_option( 'neb_instagram_feed_transient_widgets', $page_transient_widgets );
			return;
		}

		/* If no transient widgets exist on the page again, lets clear them from the saved option */
		if ( empty( $page_transient_widgets ) ) {
			foreach ( $saved_transient_widgets as $key => $saved_transient_widget ) {

				if ( $post_id === $saved_transient_widget['post_id'] ) {
					$this->instagram_feed__remove_transient_widget_settings( $post_id, $saved_transient_widget['widget_id'] );
				}
			}
			return;
		}

		$saved_transient_widgets_ids = array_column( $saved_transient_widgets, 'widget_id' );
		$page_transient_widgets_ids  = array_column( $page_transient_widgets, 'widget_id' );

		foreach ( $page_transient_widgets as $page_transient_widget_key => $page_transient_widget_settings ) {
			foreach ( $saved_transient_widgets as $saved_transient_widget_key => $saved_transient_widget_settings ) {

				/* If a transient widget already exists in our array of saved transient widgets, update it with new values from elementor. */
				if ( $saved_transient_widget_settings['widget_id'] === $page_transient_widget_settings['widget_id'] &&
					$saved_transient_widget_settings['post_id'] === $page_transient_widget_settings['post_id']
				) {
					$saved_transient_widgets[ $saved_transient_widget_key ] = $page_transient_widget_settings;
				}

				/* If a transient widget doesn't exist in our array of saved transient widgets yet, add the new values from elementor to our array. */
				if ( ! in_array( $page_transient_widget_settings['widget_id'], $saved_transient_widgets_ids ) ) {
					$saved_transient_widgets[] = $page_transient_widget_settings;
				}

				/* If a transient widget no longer exists on the page, then remove its settings */
				if ( ! in_array( $saved_transient_widget_settings['widget_id'], $page_transient_widgets_ids ) &&
					$saved_transient_widget_settings['post_id'] === $post_id
				) {
					unset( $saved_transient_widgets[ $saved_transient_widget_key ] );
				}
			}
		}

		/* Reset our keys */
		$saved_transient_widgets = array_values( $saved_transient_widgets );
		$saved_transient_widgets = array_unique( $saved_transient_widgets, SORT_REGULAR );

		$this->instagram_feed__delete_api_data_transients( $saved_transient_widgets );

		update_option( 'neb_instagram_feed_transient_widgets', $saved_transient_widgets );

	}

	/**
	 * Save a normalized version of our needed Instagram Feed Widget settings for later retrieval.
	 *
	 * @param int   $post_id The post id.
	 * @param array $editor_data The page data from elementor.
	 *
	 * @return mixed
	 */
	public function instagram_feed__save_widget_settings( $post_id, $editor_data ) {

		/* If this is a revision bail */
		if ( ! empty( wp_is_post_revision( $post_id ) ) ) {
			return;
		}

		$access_token             = '';
		$number_of_images_to_show = '';
		$cron_recurrence          = '';

		$cron_widgets_settings      = array();
		$transient_widgets_settings = array();

		/**
		 * The settings we want are buried inside the Elementor Editor Data.
		 */
		foreach ( $editor_data as $key => $value ) {
			foreach ( $value['elements'] as $key => $value ) {
				foreach ( $value['elements'] as $key => $value ) {

					if ( ! empty( $value['widgetType'] ) && $value['widgetType'] === 'neve_instagram_feed' ) {

						if ( empty( $value['settings']['access_token'] ) ) {
							continue;
						}

						$access_token             = $value['settings']['access_token'];
						$number_of_images_to_show = ( ! empty( $value['settings']['number_of_images_to_show'] ) ) ? $value['settings']['number_of_images_to_show'] : 12;

						/**
						 * If the user has switched the widget to not save images to the libary, we need to make sure that widget ID doesn't
						 * Get added to our $page_widget_ids array. That way, when we're pruning, we can delete those images from the library since they are now being stored as a transient.
						 */
						if ( empty( $value['settings']['save_instagram_media_to_library'] ) ) {
							$transient_widgets_settings[] = array(
								'post_id'                  => $post_id,
								'widget_id'                => sanitize_text_field( $value['id'] ),
								'access_token'             => sanitize_text_field( $access_token ),
								'number_of_images_to_show' => sanitize_text_field( $number_of_images_to_show ),
								'widget_type'              => 'save_to_transient',
							);
							continue;
						}

						$cron_recurrence = ( ! empty( $value['settings']['cron_schedules'] ) ) ? $value['settings']['cron_schedules'] : 'daily';

						$cron_widgets_settings[] = array(
							'post_id'                  => $post_id,
							'widget_id'                => sanitize_text_field( $value['id'] ),
							'access_token'             => sanitize_text_field( $access_token ),
							'cron_recurrence'          => sanitize_text_field( $cron_recurrence ),
							'number_of_images_to_show' => sanitize_text_field( $number_of_images_to_show ),
							'widget_type'              => 'save_to_library',
						);

					}
				}
			}
		}

		$cron_widgets_settings = $this->instagram_feed__save_cron_widgets( $post_id, $cron_widgets_settings );
		$this->instagram_feed__save_transient_widgets( $post_id, $transient_widgets_settings );

		return $cron_widgets_settings;

	}

	/**
	 * Refresh the Instagram Long Lived access token duration.
	 *
	 * Failing to refresh it would require the user to regenerate it from their developers.facebook.com account.
	 * https://developers.facebook.com/docs/instagram-basic-display-api/overview#long-lived-access-tokens
	 * https://developers.facebook.com/docs/instagram-basic-display-api/reference/refresh_access_token
	 *
	 * @return void
	 */
	public function instagram_feed__refresh_access_tokens() {

		/**
		 * User might have multiple different access tokens from different instagram accounts on the same page.
		 * Here we're making sure that all are refreshed.
		 */
		$cron_widgets_settings           = get_option( 'neb_instagram_feed_cron_widgets', array() );
		$cron_widgets_access_tokens      = array_column( $cron_widgets_settings, 'access_token' );
		$transient_widgets_settings      = get_option( 'neb_instagram_feed_transient_widgets', array() );
		$transient_widgets_access_tokens = array_column( $transient_widgets_settings, 'access_token' );

		$access_tokens = array_merge( $cron_widgets_access_tokens, $transient_widgets_access_tokens );
		$access_tokens = array_unique( $access_tokens );

		foreach ( $access_tokens as $access_token ) {

			$ig_media_end_point = 'https://graph.instagram.com/refresh_access_token';
			$ig_api_params      = array(
				'grant_type'   => 'ig_refresh_token',
				'access_token' => $access_token,
			);

			$url = add_query_arg( $ig_api_params, $ig_media_end_point );

			$response = wp_safe_remote_get( $url, array() );

			if ( is_wp_error( $response ) ) {
				continue;
			}
		}

	}

	/**
	 * Create Instagram scheduled tasks needed by the widget.
	 *
	 * @param array $widget_settings Settings for the Instagram widgets on the page.
	 * @see self::do_after_save()
	 *
	 * @return void
	 */
	public function instagram_feed__setup_cron_schedules( $widget_settings ) {

		if ( empty( $widget_settings ) ) {
			return;
		}

		foreach ( $widget_settings as $key => $widget_setting ) {

			$post_id    = $widget_setting['post_id'];
			$widget_id  = $widget_setting['widget_id'];
			$recurrence = $widget_setting['cron_recurrence'];

			$scheduled_cron_event = wp_get_scheduled_event( 'neve_elementor_booster_cache_instagram_feed_data_' . $post_id . '_' . $widget_id, array( $post_id, $widget_id ) );

			/**
			 * Add cron hook if it doesn't exist yet.
			 */
			if ( empty( $scheduled_cron_event ) ) {
				wp_schedule_event( time() + HOUR_IN_SECONDS, $recurrence, 'neve_elementor_booster_cache_instagram_feed_data_' . $post_id . '_' . $widget_id, array( $post_id, $widget_id ) );
			}

			/**
			 * Update our cron hook if user changed the refresh data duration in the Elementor widget settings.
			 */
			if ( is_object( $scheduled_cron_event ) && $scheduled_cron_event->schedule !== $recurrence ) {
				$this->instagram_feed__clear_scheduled_task( $post_id, $widget_id );
				wp_schedule_event( time() + HOUR_IN_SECONDS, $recurrence, 'neve_elementor_booster_cache_instagram_feed_data_' . $post_id . '_' . $widget_id, array( $post_id, $widget_id ) );
			}
		}

		/**
		 * Schedule our access token refreshing cron task.
		 */
		if ( wp_next_scheduled( 'neve_elementor_booster_refresh_instagram_access_token' ) === false ) {
			wp_schedule_event( time() + HOUR_IN_SECONDS, 'weekly', 'neve_elementor_booster_refresh_instagram_access_token' );
		}

	}

	/**
	 * Attach the Instagram media library caching method to WP Schedule events on a per widget basis.
	 *
	 * This allows users to have different widgets refreshing data at different times.
	 *
	 * @return void
	 */
	public function instagram_feed__setup_cron_tasks() {
		$widgets_settings = get_option( 'neb_instagram_feed_cron_widgets' );
		if ( empty( $widgets_settings ) || ! is_array( $widgets_settings ) ) {
			return;
		}

		foreach ( $widgets_settings as $key => $widget_setting ) {
			add_action( 'neve_elementor_booster_cache_instagram_feed_data_' . $widget_setting['post_id'] . '_' . $widget_setting['widget_id'], array( $this, 'instagram_feed__refresh_cached_data_cron' ), 10, 2 );
		}
	}

	/**
	 * Delete the stored transient consisting of our media data from the Instagram API response.
	 *
	 * @param array $widgets_settings Settings for the Instagram widgets on the page.
	 *
	 * @return void
	 */
	public function instagram_feed__delete_api_data_transients( $widgets_settings ) {
		foreach ( $widgets_settings as $key => $widget_setting ) {
			delete_transient( 'neb_instagram_api_media_data_' . $widget_setting['post_id'] . '_' . $widget_setting['widget_id'] );
		}
	}

	/**
	 * Get Instagram media from API.
	 *
	 * @param array $widget_setting Array of settings for the widget on the page.
	 * @param bool  $doing_cron Whether this function is being called from WP cron task scheduler.
	 *
	 * @return mixed $response_body An array of Instagram images or an error if failed.
	 */
	public function instagram_feed__get_media_from_api( $widget_setting, $doing_cron = false ) {

		if ( $doing_cron ) {
			$access_token = isset( $widget_setting['access_token'] ) ? trim( $widget_setting['access_token'] ) : '';
		} else {
			$access_token = isset( $widget_setting['api']['access_token'] ) ? trim( $widget_setting['api']['access_token'] ) : '';
		}

		if ( empty( $access_token ) ) {

			$error_message = __( 'To display your Instagram feed, please enter the API Key.', 'neve' );

			return $error_message;

		}

		if ( $doing_cron ) { // This will happen when cron task is being run. See $this::instagram_feed__cache_instagram_data()
			$number_of_images_to_pull = $widget_setting['number_of_images_to_show'];
		}

		if ( ! $doing_cron ) { // This will happen when widget is called by Elementor. See Instagram_Feed::render()
			$number_of_images_to_pull = $widget_setting['api']['number_of_images_to_show'];
		}

		$ig_media_end_point = 'https://graph.instagram.com/me/media';
		$ig_api_params      = array(
			'fields'       => 'id,caption,media_type,media_url,thumbnail_url,permalink',
			'access_token' => $access_token,
			'limit'        => (int) $number_of_images_to_pull,
		);

		$url = add_query_arg( $ig_api_params, $ig_media_end_point );

		$response = wp_safe_remote_get( $url, array() );

		if ( is_wp_error( $response ) ) {
			return (string) $response->get_error_message();
		}

		$response_code    = wp_remote_retrieve_response_code( $response );
		$response_message = $response['response']['message'] ?? ''; // @phpstan-ignore-line - $response['response']['message'] is always set.

		if ( $response_code !== 200 ) {
			$error_message = $response_message . ': ' . ( $response['headers']['www-authenticate'] ?? '' );
			return $error_message;
		}

		$response_body = wp_remote_retrieve_body( $response );

		$response_body = json_decode( $response_body, true )['data'];

		if ( empty( $response_body ) ) {
			return __( 'No Instagram Images Available', 'neve' );
		}

		return $response_body;

	}

	/**
	 * Check if an image exists based on it's filename which is also the post title.
	 *
	 * The get_page_by_title() function is open to false positives which we do not want here.
	 * WordPress might save a post with '-1' at the end even if there's no post which exists with that name.
	 *
	 * @see https://vertis.d.pr/IwJdtc
	 *
	 * @param string $post_title The attachment post title.
	 *
	 * @return mixed false if image doesn't exist, image array if it does.
	 */
	private function instagram_feed__check_if_image_exists( $post_title ) {
		// WordPress.DB.DirectDatabaseQuery.DirectQuery get_page_by_title() is not trust worthy enough here
		// WordPress.DB.DirectDatabaseQuery.NoCaching We do not want to cache these results as an image can be deleted externally
		// phpcs:disable
		global $wpdb;
		$wpdb->flush();
		$image = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT `ID`, `guid` FROM {$wpdb->posts} WHERE `post_title` LIKE %s;",
				$wpdb->esc_like( $post_title ) . '%'
			),
			ARRAY_N
		);
		// phpcs:enable
		return ( empty( $image ) ) ? false : $image;
	}

	/**
	 * Removes excess images from WP media library.
	 *
	 * @param array $removed_image_ids The Instagram image IDs that should be removed.
	 *
	 * @return void
	 */
	private function instagram_feed__delete_images_from_library( $removed_image_ids ) {

		$cached_image_ids = get_option( 'neb_instagram_feed_cached_image_ids' );

		if ( empty( $cached_image_ids ) ) {
			return;
		}

		// Normalize our array so we can work with it.
		$all_cached_image_ids = array_column( $cached_image_ids, 'widget_images' );
		$all_cached_image_ids = array_values( $all_cached_image_ids );
		$all_cached_image_ids = array_merge( ...$all_cached_image_ids );

		$count = array_count_values( $all_cached_image_ids );

		foreach ( $removed_image_ids as $image_id ) {

			/**
			 * Check if the removed image ID is used by another widget.
			 * If it is then continue checking the other ids.
			 */
			$occurences = $count[ $image_id ] ?? 0;
			if ( (int) $occurences >= 2 ) {
				continue;
			}

			$image = $this->instagram_feed__check_if_image_exists( $image_id );

			if ( ! empty( $image ) ) {
				wp_delete_post( $image[0], true );
			}
		}

	}

	/**
	 * Removes excess images from WP media library after an import of new images.
	 *
	 * @see instagram_feed__cache_instagram_data()
	 *
	 * @param array $widget_setting The Instagram widget setting.
	 * @param array $new_instagram_image_ids Array of Image IDs from Instagram.
	 */
	private function instagram_feed__prune_cached_images( $widget_setting, $new_instagram_image_ids ) {

		$cached_image_ids = get_option( 'neb_instagram_feed_cached_image_ids' );
		$widget_id        = $widget_setting['widget_id'];
		$post_id          = $widget_setting['post_id'];
		$widget_images    = '';

		if ( empty( $cached_image_ids ) ) {
			$cached_image_ids = array();
		}

		$new_addition = array(
			'post_id'       => $post_id,
			'widget_id'     => $widget_id,
			'widget_images' => $new_instagram_image_ids,
		);

		foreach ( $cached_image_ids as $key => $data ) {

			if ( $post_id === $data['post_id'] && $widget_id === $data['widget_id'] ) {
				$widget_images = $cached_image_ids[ $key ];
				break;
			}
		}

		/**
		 * If we do not have image IDs saved for this widget yet; add them to the saved option and bail.
		 */
		if ( empty( $widget_images ) ) {
			$cached_image_ids[] = $new_addition;
			update_option( 'neb_instagram_feed_cached_image_ids', $cached_image_ids );
			return;
		}

		$widget_image_ids = $widget_images['widget_images'];

		/**
		 * If nothing has changed no need to continue.
		 */
		if ( $new_instagram_image_ids === $widget_image_ids ) {
			return;
		}

		/**
		 * Compare our stored instagram Image IDs with the new set of imported IDs to find out which ones should be deleted from the library.
		 */
		$difference = array_diff( $widget_image_ids, $new_instagram_image_ids );
		if ( is_array( $difference ) && ! empty( $difference ) ) {
			$this->instagram_feed__delete_images_from_library( $difference );
		}

		/**
		 * Replace cached image IDs for this widget.
		 */
		foreach ( $cached_image_ids as $key => $data ) {

			if ( $post_id === $data['post_id'] && $widget_id === $data['widget_id'] ) {
				$cached_image_ids[ $key ] = $new_addition;
			}
		}

		$cached_image_ids = array_unique( $cached_image_ids, SORT_REGULAR );

		update_option( 'neb_instagram_feed_cached_image_ids', $cached_image_ids );

	}

	/**
	 * Remove old Instagram widget settings for a page that has been deleted from WP.
	 *
	 * This method is useful in cases where a page containing an Instagram widget is deleted but Neve would still have the import scheduled tasks active.
	 *
	 * @param int $post_id The WP post ID.
	 *
	 * @return void
	 */
	public function instagram_feed__prune_old_widgets( $post_id ) {

		if ( empty( $post_id ) ) {
			return;
		}

		if ( get_post_type( $post_id ) === 'attachment' ) {
			return;
		}

		$cron_widgets_settings = get_option( 'neb_instagram_feed_cron_widgets', array() );
		if ( ! is_array( $cron_widgets_settings ) ) {
			return;
		}
		foreach ( $cron_widgets_settings as $key => $widget_setting ) {
			if ( $post_id === $widget_setting['post_id'] ) {
				$this->instagram_feed__remove_cron_widget_settings( $post_id, $widget_setting['widget_id'] );
			}
		}

		$transient_widgets_settings = get_option( 'neb_instagram_feed_transient_widgets', array() );
		if ( ! is_array( $transient_widgets_settings ) ) {
			return;
		}
		foreach ( $transient_widgets_settings as $key => $widget_setting ) {
			if ( $post_id === $widget_setting['post_id'] ) {
				$this->instagram_feed__remove_transient_widget_settings( $post_id, $widget_setting['widget_id'] );
			}
		}

	}

	/**
	 * Cron task that refreshes the instagram data.
	 *
	 * @param string $post_id The WP post ID.
	 * @param string $widget_id The ID of the Instagram widget we're caching the data for.
	 *
	 * @return void
	 */
	public function instagram_feed__refresh_cached_data_cron( $post_id = '', $widget_id = '' ) {

		$widgets_settings = get_option( 'neb_instagram_feed_cron_widgets' );

		if ( empty( $widgets_settings ) ) {
			return;
		}

		if ( ! is_array( $widgets_settings ) ) {
			return;
		}

		foreach ( $widgets_settings as $key => $widget_setting ) {

			if ( $widget_setting['post_id'] === $post_id && $widget_setting['widget_id'] === $widget_id ) {
				$this->instagram_feed__cache_instagram_data( $widget_setting );
			}
		}

	}

	/**
	 * Refresh Cached media Library after Elementor save event.
	 *
	 * @param array $widgets_settings Settings for the Instagram widgets on the page.
	 *
	 * @return void
	 */
	public function instagram_feed__refresh_cached_data_elementor( $widgets_settings ) {

		foreach ( $widgets_settings as $key => $widget_setting ) {
			$this->instagram_feed__cache_instagram_data( $widget_setting );
		}

	}

	/**
	 * Saves the Instagram images and metadata such as captions to the server.
	 *
	 * @param array $widget_setting The Instagram widget setting.
	 *
	 * @return void
	 */
	public function instagram_feed__cache_instagram_data( $widget_setting ) {

		/**
		 * The media_handle_sideload() and download_url() functions require these files for it to work in the background.
		 */
		if ( ! function_exists( 'media_handle_sideload' ) ) {
			require_once ABSPATH . 'wp-admin/includes/media.php';
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/image.php';
		}

		/**
		 * Get a fresh set of media items from the Instagram API to cache.
		 */
		// TODO-UV We can reduce API calls by only calling this function when needed
		// If the access token is the same as a previous call, and the number of images is also the same, then don't call the function again
		// Just use the old values
		$images_from_api = $this->instagram_feed__get_media_from_api( $widget_setting, true );

		if ( ! is_array( $images_from_api ) ) {
			return;
		}

		$error_prefix = __METHOD__ . ' Line ';

		$new_instagram_image_ids = array();

		foreach ( $images_from_api as $image => $image_properties ) {

			$image_id                  = $image_properties['id'];
			$new_instagram_image_ids[] = $image_id;

			/**
			 * Check if the image exists, in the event the user deleted it from the WP media library.
			 */
			$image_exists = $this->instagram_feed__check_if_image_exists( $image_id );
			if ( ! empty( $image_exists ) ) {
				continue;
			}
			$image_name = $image_id . '.jpg';

			/**
			 * If this is a video post from the api, let's cache it's thumbnail URL instead of it's media URL.
			 *
			 * It's media URL is a direct link to the Instagram video, which we are not uploading to the site. What we want is the link to an image.
			 */
			$url = ( empty( $image_properties['thumbnail_url'] ) && $image_properties['media_type'] !== 'VIDEO' ) ? $image_properties['media_url'] : $image_properties['thumbnail_url'];

			$timeout_seconds = 10;

			$temp_file = download_url( $url, $timeout_seconds );

			if ( is_wp_error( $temp_file ) ) {
				// phpcs:ignore
				error_log( $error_prefix . __LINE__ . ': ' . print_r( $temp_file->get_error_message(), true ) );
				continue;
			}

			$image_info = array(
				'name'     => $image_name,
				'type'     => 'image/jpeg',
				'tmp_name' => $temp_file,
				'size'     => filesize( $temp_file ),
			);

			$id = media_handle_sideload( $image_info ); // @phpstan-ignore-line - $image_info is an array of $_FILES keys, which is what media_handle_sideload() expects.

			if ( is_wp_error( $id ) ) {
				// phpcs:ignore
				error_log( $error_prefix . __LINE__ . ': ' . print_r( $id->get_error_message(), true ) );
				// phpcs:ignore
				@unlink( $image_info['tmp_name'] );
				continue;
			}

			$image_meta = array(
				'caption'    => sanitize_text_field( $image_properties['caption'] ?? '' ),
				'media_url'  => esc_url_raw( $image_properties['media_url'] ),
				'permalink'  => esc_url_raw( $image_properties['permalink'] ),
				'media_type' => sanitize_text_field( $image_properties['media_type'] ),
			);

			if ( $image_properties['media_type'] === 'VIDEO' ) {
				$image_meta['thumbnail_url'] = esc_url_raw( $image_properties['thumbnail_url'] );
			}

			update_post_meta( $id, 'neb_instagram_feed_media_meta', $image_meta );

		}

		$this->instagram_feed__prune_cached_images( $widget_setting, $new_instagram_image_ids );

	}

	/**
	 * Call methods that should run after Elementor Save event.
	 *
	 * @param int   $post_id The post id of the elementor page.
	 * @param array $editor_data The page data from elementor.
	 *
	 * @return void
	 */
	public function do_after_save( $post_id, $editor_data ) {

		$widget_settings = $this->instagram_feed__save_widget_settings( $post_id, $editor_data );

		if ( empty( $widget_settings ) ) {
			return;
		}

		$this->instagram_feed__setup_cron_schedules( $widget_settings );
		$this->instagram_feed__delete_api_data_transients( $widget_settings );
		$this->instagram_feed__refresh_cached_data_elementor( $widget_settings );

	}

}
