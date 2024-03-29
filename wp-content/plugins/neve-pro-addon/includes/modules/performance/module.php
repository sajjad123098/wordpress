<?php
/**
 * Remove emoji script from WordPress
 * 
 * Author:          Bogdan Preda <bogdan.preda@themeisle.com>
 * Created on:      2021-04-26
 *
 * @package Neve Pro Addon
 */

namespace Neve_Pro\Modules\Performance;

use Neve_Pro\Core\Abstract_Module;

/**
 * Performance Module Class
 */
class Module extends Abstract_Module {

	const EMOJI_ID        = 'enable_emoji_removal';
	const EMBEDDED_ID     = 'enable_embedded_removal';
	const LOCAL_FONTS_ID  = 'enable_local_fonts';
	const LAZY_CONTENT_ID = 'enable_lazy_content';

	/**
	 * Post counter.
	 *
	 * @var integer
	 */
	private static $post_count = 0;
	/**
	 * Product counter.
	 *
	 * @var integer
	 */
	private static $product_count = 0;
	/**
	 * Keep track of core block count.
	 *
	 * @var integer
	 */
	private static $block_count = 0;
	/**
	 * Define module properties.
	 *
	 * @access  public
	 * @return void
	 *
	 * @version 1.0.0
	 */
	final public function define_module_properties() {
		$this->slug          = 'performance_features';
		$this->name          = __( 'Performance', 'neve' );
		$this->description   = __( 'Simple and effective optimizations options to enhance the performance of your site.', 'neve' );
		$this->documentation = array(
			'url'   => 'https://docs.themeisle.com/article/1366-performance-module-documentation',
			'label' => __( 'Learn more', 'neve' ),
		);
		$this->order         = 5;

		$available_options = [];

		$emoji_script_front = has_action( 'wp_head', 'print_emoji_detection_script' );
		$emoji_removal      = $this->get_module_option( self::EMOJI_ID, false );
		// only add/show this option if emoji is not disabled by a 3rd party plugin
		if ( $emoji_script_front || $emoji_removal ) {
			$available_options[ self::EMOJI_ID ] = array(
				'label'             => __( 'Enable removal of emoji scripts', 'neve' ),
				'type'              => 'toggle',
				'default'           => false,
				'show_in_rest'      => true,
				'sanitize_callback' => function ( $value ) {
					return is_bool( $value ) ? $value : false;
				},
			);
		}

		$embed_script_front = has_action( 'wp_head', 'wp_oembed_add_host_js' );
		$embeds_removal     = $this->get_module_option( self::EMBEDDED_ID, false );
		// only add/show this option if emoji is not disabled by a 3rd party plugin
		if ( $embed_script_front || $embeds_removal ) {
			$available_options[ self::EMBEDDED_ID ] = array(
				'label'             => esc_html__( 'Enable embed scripts removal', 'neve' ),
				'type'              => 'toggle',
				'default'           => false,
				'sanitize_callback' => function ( $value ) {
					return is_bool( $value ) ? $value : false;
				},
			);
		}

		$available_options[ self::LOCAL_FONTS_ID ] = array(
			'label'             => esc_html__( 'Enable local hosting of external fonts', 'neve' ),
			'type'              => 'toggle',
			'default'           => false,
			'sanitize_callback' => function ( $value ) {
				return is_bool( $value ) ? $value : false;
			},
		);

		$available_options[ self::LAZY_CONTENT_ID ] = array(
			'label'             => esc_html__( 'Enable automatically lazy rendering of off-screen elements', 'neve' ),
			'type'              => 'toggle',
			'default'           => false,
			'sanitize_callback' => function ( $value ) {
				return is_bool( $value ) ? $value : false;
			},
		);

		$this->options = array(
			array(
				'label'   => __( 'Options', 'neve' ),
				'options' => $available_options,
			),
		);
	}

	/**
	 * Set the default state for this module as off by default.
	 *
	 * @return false
	 */
	final public function get_default_module_status() {
		return false;
	}

	/**
	 * Method to return module options
	 *
	 * @param string $key The option key.
	 * @param mixed  $default The default value.
	 *
	 * @return mixed
	 */
	private function get_module_option( $key, $default = false ) {
		return get_option( 'nv_pro_' . $key, $default );
	}

	/**
	 * Check if module should load.
	 *
	 * @return bool
	 */
	final public function should_load() {
		return true;
	}

	/**
	 * Run Module
	 *
	 * @retun void
	 */
	final public function run_module() {
		add_filter( 'neve_dynamic_style_output', array( $this, 'module_css' ), 99, 2 );
		add_action( 'init', array( $this, 'run_on_init' ) );
	}

	/**
	 * Add module css.
	 *
	 * @param string $css Current CSS style.
	 * @param string $context Current context.
	 *
	 * @return string Altered CSS.
	 */
	final public function module_css( $css, $context = 'frontend' ) {
		if ( ! $this->is_active() ) {
			return $css;
		}
		if ( $context !== 'frontend' ) {
			return $css;
		}

		$lazyload_content = $this->get_module_option( self::LAZY_CONTENT_ID, false );
		if ( ! $lazyload_content ) {
			return $css;
		}

		$masonry_layout_enabled = get_theme_mod( 'neve_enable_masonry', false );

		// content-visibility css property breaks masonry layout.
		if ( $masonry_layout_enabled ) {
			return $css;
		}

		$scroll_to_top_css = '
		@media (min-width: 960px) {
			.nv-cv-d {
				content-visibility: auto;
			}
		}
		@media(max-width: 576px) {
			.nv-cv-m {
				content-visibility: auto;
			}
		}';

		return $css . $scroll_to_top_css;
	}

	/**
	 * Method called on `init` action
	 *
	 * @return void
	 */
	final public function run_on_init() {
		if ( isset( $_GET['perf'] ) && $_GET['perf'] == 'off' ) {
			return;
		}

		if ( ! $this->is_active() ) {
			return;
		}

		$emoji_removal = $this->get_module_option( self::EMOJI_ID, false );

		if ( $emoji_removal ) {
			remove_action( 'admin_print_styles', 'print_emoji_styles' ); 
			remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
			remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
			remove_action( 'wp_print_styles', 'print_emoji_styles' );  
			remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' ); 
			remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
			remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );  
			
			add_filter( 'tiny_mce_plugins', array( $this, 'remove_emoji_from_tinymce' ) );
			add_filter( 'wp_resource_hints', array( $this, 'remove_emoji_dns_prefetch' ), 10, 2 );
		}

		$embeds_removal = $this->get_module_option( self::EMBEDDED_ID, false );

		if ( $embeds_removal ) {
			remove_action( 'rest_api_init', 'wp_oembed_register_route' );
			remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result' );
			remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
			remove_action( 'wp_head', 'wp_oembed_add_host_js' );

			add_filter( 'embed_oembed_discover', '__return_false' );
			add_filter( 'tiny_mce_plugins', array( $this, 'remove_embeds_from_tinymce' ) );
			add_filter( 'rewrite_rules_array', array( $this, 'disable_embeds_rewrites' ) );

			remove_filter( 'pre_oembed_result', 'wp_filter_pre_oembed_result' );
		}

		add_filter(
			'neve_load_remote_fonts_locally',
			function() { 
				return $this->get_module_option( self::LOCAL_FONTS_ID, false );
			} 
		);

		$lazyload_content = $this->get_module_option( self::LAZY_CONTENT_ID, false );
		if ( $lazyload_content ) {
			add_filter( 'neve_footer_wrap_classes', array( $this, 'add_content_visible_classes' ), PHP_INT_MAX );
			add_action( 'neve_loop_entry_after', array( $this, 'after_post_entry' ) );
			add_action( 'woocommerce_after_shop_loop_item', array( $this, 'after_product_entry' ) );
			add_filter( 'post_class', array( $this, 'performance_post_classes' ), 10, 3 );
			add_filter( 'render_block_data', array( $this, 'process_content_blocks' ), -99, 3 );
			add_filter( 'render_block', array( $this, 'filter_block' ), 10, 2 );

			add_filter( 'neve_layout_single_post_elements_order', array( $this, 'add_filters_for_elements_order' ), PHP_INT_MAX );
			add_filter( 'neve_exclusive_products_class', array( $this, 'add_content_visible_classes' ), PHP_INT_MAX );

			add_action( 'woocommerce_before_shop_loop', array( $this, 'add_product_filter_cv_class' ), 10 );
			add_action( 'woocommerce_after_shop_loop', array( $this, 'remove_product_filter_cv_class' ), 10 );

			// for related products we need to use the same method as for custom layouts and wrap the generated content
			add_action( 'woocommerce_after_single_product_summary', array( $this, 'wrap_woo_content_start' ), -99 );
			add_action( 'woocommerce_after_single_product_summary', array( $this, 'wrap_woo_content_end' ), PHP_INT_MAX );

			// for custom layouts and other action hooks bellow the fold, wrap in a layout-neutral tag
			add_action( 'neve_before_custom_layout', array( $this, 'wrap_action_content_start' ), -99 );
			add_action( 'neve_after_custom_layout', array( $this, 'wrap_action_content_end' ), PHP_INT_MAX );
		}
	}

	/**
	 * Method to add the product filter class when needed.
	 *
	 * @param string $woocommerce_pagination The woocommerce pagination.
	 */
	final public function add_product_filter_cv_class( $woocommerce_pagination ) {
		add_filter( 'post_class', array( $this, 'performance_product_classes' ), 10, 3 );
	}

	/**
	 * Method to remove the product filter class when needed.
	 *
	 * @param string $woocommerce_pagination The woocommerce pagination.
	 */
	final public function remove_product_filter_cv_class( $woocommerce_pagination ) {
		remove_filter( 'post_class', array( $this, 'performance_product_classes' ) );
	}

	/**
	 * Only allow specific hooks to wrap content.
	 *
	 * @param string $action The hook name.
	 *
	 * @return bool
	 */
	private function is_custom_layout_hook_allowed( $action ) {
		return in_array( $action, [ 'neve_after_content', 'neve_before_footer_hook', 'neve_after_footer_hook', 'neve_body_end_before' ] );
	}

	/**
	 * Wrap WooCommerce section tag start
	 */
	final public function wrap_woo_content_start( $args ) {
		if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
			echo '<div class="nv-cv-m nv-cv-d" style="display: contents;">';
		}
	}

	/**
	 * Wrap WooCommerce section tag end
	 */
	final public function wrap_woo_content_end( $args ) {
		if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
			echo '</div>';
		}
	}

	/**
	 * Wrap tag start
	 */
	final public function wrap_action_content_start( $action ) {
		if ( ! $this->is_custom_layout_hook_allowed( $action ) ) {
			return;
		}
		echo '<div class="nv-cv-m nv-cv-d" style="display: contents;">';
	}

	/**
	 * Wrap tag end
	 */
	final public function wrap_action_content_end( $action ) {
		if ( ! $this->is_custom_layout_hook_allowed( $action ) ) {
			return;
		}
		echo '</div>';
	}

	/**
	 * This adds additional filters for specific orders based on content position.
	 *
	 * @param array $content_order The content order array.
	 * @return array
	 */
	final public function add_filters_for_elements_order( $content_order ) {
		if ( empty( $content_order ) ) {
			return $content_order;
		}

		$content_pos    = array_search( 'content', $content_order );
		$comments_pos   = array_search( 'comments', $content_order );
		$related_pos    = array_search( 'related-posts', $content_order );
		$navigation_pos = array_search( 'post-navigation', $content_order );
		$author_pos     = array_search( 'author-biography', $content_order );
		$sharing_pos    = array_search( 'sharing-icons', $content_order );

		if ( $content_pos < $comments_pos ) {
			add_filter( 'neve_comments_area_class', array( $this, 'add_content_visible_classes' ) );
		}
		if ( $content_pos < $related_pos ) {
			add_filter( 'neve_related_posts_class', array( $this, 'add_content_visible_classes' ) );
		}
		if ( $content_pos < $navigation_pos ) {
			add_filter( 'neve_post_navigation_class', array( $this, 'add_content_visible_classes' ) );
		}
		if ( $content_pos < $author_pos ) {
			add_filter( 'neve_author_biography_class', array( $this, 'add_content_visible_classes' ) );
		}
		if ( $content_pos < $sharing_pos ) {
			add_filter( 'neve_post_share_class', array( $this, 'add_content_visible_classes' ) );
		}

		return $content_order;
	}

	/**
	 * Used to append additional classes for contents visible.
	 *
	 * @param string $class The class for the section.
	 * @retun string
	 */
	final public function add_content_visible_classes( $class ) {
		return $class . ' nv-cv-m nv-cv-d';
	}

	/**
	 * Replace only first occurrence.
	 *
	 * @param string $search The search string.
	 * @param string $replace The replace string.
	 * @param string $subject The target for the function.
	 *
	 * @return string
	 */
	private function str_replace_first_only( $search, $replace, $subject ) {
		$pos = strpos( $subject, $search );
		if ( $pos !== false ) {
			return substr_replace( $subject, $replace, $pos, strlen( $search ) );
		}
		return $subject;
	}

	/**
	 * Add content-visible class to core blocks.
	 *
	 * @param string $block_content The block content.
	 * @param array  $block The block array.
	 * @return string
	 */
	final public function filter_block( $block_content, $block ) {
		if ( is_admin() || wp_is_json_request() ) {
			return $block_content;
		}
		if ( ! isset( $block[ Abstract_Module::TOP_LEVEL_BLOCK_FLAG ] ) ) {
			return $block_content;
		}
		if ( $block['blockName'] ) {
			if ( ! empty( $block['innerBlocks'] ) ) {
				self::$block_count++;
			}
			if ( isset( $block['attrs'] ) && isset( $block['attrs']['height'] ) && $block['attrs']['height'] >= 250 ) {
				self::$block_count++;
			}
		}

		if ( ! empty( $block['innerBlocks'] ) ) {
			if ( self::$block_count >= 3 ) {
				$block_content = $this->str_replace_first_only( 'class="', 'class="nv-cv-m ', $block_content );
			}
	
			if ( self::$block_count >= 5 ) {
				$block_content = $this->str_replace_first_only( 'class="', 'class="nv-cv-d ', $block_content );
			}
		}

		return $block_content;
	}

	/**
	 * Add content-visible for product list
	 *
	 * @param array  $classes The product classes.
	 * @param string $class The product class to add.
	 * @param int    $post_id The post id.
	 * @return array
	 */
	final public function performance_product_classes( $classes, $class, $post_id ) {

		// We use `is_woocommerce` here as `is_shop` does not work in this context
		// We do this to not interfere with the post listing logic.
		if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
			if ( self::$product_count >= 3 ) {
				$classes[] = 'nv-cv-m';
			}
			if ( self::$product_count >= 6 ) {
				$classes[] = 'nv-cv-d';
			}
			return $classes;
		}

		return $classes;
	}

	/**
	 * Add content-visible for post list
	 *
	 * @param array  $classes The post classes.
	 * @param string $class The post class to add.
	 * @param int    $post_id The post id.
	 * @return array
	 */
	final public function performance_post_classes( $classes, $class, $post_id ) {
		if ( self::$post_count >= 3 ) {
			$classes[] = 'nv-cv-m';
		}
		if ( self::$post_count >= 6 ) {
			$classes[] = 'nv-cv-d';
		}
		return $classes;
	}

	/**
	 * Increment post count from loop.
	 *
	 * @return void
	 */
	final public function after_post_entry() {
		self::$post_count++;
	}

	/**
	 * Increment post count from loop.
	 *
	 * @return void
	 */
	final public function after_product_entry() {
		self::$product_count++;
	}

	/**
	 * Filter footer row classes and add lazyload content classes.
	 *
	 * @param array  $classes Classes added to row.
	 * @param string $row_index The row index.
	 * @return array
	 */
	final public function lazyload_footer_row_classes( $classes, $row_index ) {
		$classes[] = 'nv-cv-d';

		return $classes;
	}

	/**
	 * Remove wpemoji option from tinymce.
	 *
	 * @param array $plugins The plugins list.
	 * @return array
	 */
	final public function remove_emoji_from_tinymce( $plugins ) {
		return array_diff( $plugins, array( 'wpemoji' ) );
	}

	/**
	 * Remove emoji DNS prefetch.
	 *
	 * @param array  $urls List of prefetch URLs.
	 * @param string $relation_type Relation type.
	 * @return array
	 */
	final public function remove_emoji_dns_prefetch( $urls, $relation_type ) {
		$emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/' );

		return $this->filter_remove_dns_prefetch( $urls, $relation_type, $emoji_svg_url );
	}

	/**
	 * Remove specific url from DNS prefetch list.
	 *
	 * @param array  $urls List of prefetch URLs.
	 * @param string $relation_type Relation type.
	 * @param string $url_to_remove The URL to remove.
	 * @return array
	 */
	final public function filter_remove_dns_prefetch( $urls, $relation_type, $url_to_remove ) {
		$url_to_remove = (string) $url_to_remove;

		if ( ! empty( $url_to_remove ) && 'dns-prefetch' === $relation_type ) {
			$count = 0;
			foreach ( $urls as $url ) {
				if ( false !== strpos( $url, $url_to_remove ) ) {
					unset( $urls[ $count ] );
				}
				$count++;
			}
		}

		return $urls;
	}


	/**
	 * Remove wpembed option from tinymce.
	 *
	 * @param array $plugins The plugins list.
	 * @return array
	 */
	final public function remove_embeds_from_tinymce( $plugins ) {
		return array_diff( $plugins, array( 'wpembed' ) );
	}

	/**
	 * Disable embeds rewrite rule.
	 *
	 * @param array $rules Rewrite rules list.
	 * @return array
	 */
	final public function disable_embeds_rewrites( $rules ) {
		foreach ( $rules as $rule => $rewrite ) {
			if ( false !== strpos( $rewrite, 'embed=true' ) ) {
				unset( $rules[ $rule ] );
			}
		}
		return $rules;
	}

}
