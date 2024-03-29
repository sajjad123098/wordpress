<?php
/**
 * Factory for loading the builders compatibility.
 *
 * @package Neve_Pro\Modules\Custom_Layouts\Admin\Builders
 */


namespace Neve_Pro\Modules\Custom_Layouts\Admin\Builders;

use Neve_Pro\Admin\Custom_Layouts_Cpt;
use Neve_Pro\Modules\Custom_Layouts\Admin\Inside_Layout;
use Neve_Pro\Modules\Custom_Layouts\Admin\Layouts_Metabox;
use Neve_Pro\Modules\Custom_Layouts\Admin\Template_Layout;
use Neve_Pro\Traits\Conditional_Display;

/**
 * Class Loader
 *
 * @package Neve_Pro\Modules\Custom_Layouts\Admin\Builders
 */
class Loader {

	use Conditional_Display;

	/**
	 * Possible builders list.
	 *
	 * @var array List of them.
	 */
	public $builders_list = [
		'Default_Editor',
		'Php_Editor',
		'Elementor',
		'Brizy',
		'Beaver',
	];
	/**
	 * List of possible builders.
	 *
	 * @var Abstract_Builders[] $available_builders List.
	 */
	private $available_builders = [];
	/**
	 * Hooks map to check.
	 *
	 * @var array Hooks map.
	 */
	protected $hooks_map = array(
		'neve_do_header'                       => array(
			'hooks_to_deactivate' => array( 'neve_do_header', 'neve_do_top_bar' ),
			'posts_map_key'       => 'header',
		),
		'neve_do_footer'                       => array(
			'hooks_to_deactivate' => array( 'neve_do_footer' ),
			'posts_map_key'       => 'footer',
		),
		'neve_do_global'                       => array(
			'hooks_to_deactivate' => array( 'neve_do_global' ),
			'posts_map_key'       => 'global',
		),
		'neve_do_inside_content'               => array(
			'hooks_to_deactivate' => array( 'neve_do_inside_content' ),
			'posts_map_key'       => 'inside',
		),
		'neve_do_404'                          => array(
			'hooks_to_deactivate' => array( 'neve_do_404' ),
			'posts_map_key'       => 'not_found',
		),
		'neve_do_template_content_single_post' => array(
			'hooks_to_deactivate' => array( 'neve_do_template_content_single_post' ),
			'posts_map_key'       => 'single_post',
		),
		'neve_do_template_content_single_page' => array(
			'hooks_to_deactivate' => array( 'neve_do_template_content_single_page' ),
			'posts_map_key'       => 'single_page',
		),
		'neve_do_template_content_archives'    => array(
			'hooks_to_deactivate' => array( 'neve_do_template_content_archives' ),
			'posts_map_key'       => 'archives',
		),
		'neve_do_template_content_search'      => array(
			'hooks_to_deactivate' => array( 'neve_do_template_content_search' ),
			'posts_map_key'       => 'search',
		),
		'neve_do_offline'                      => array(
			'hooks_to_deactivate' => array( 'neve_do_offline' ),
			'posts_map_key'       => 'offline',
		),
		'neve_do_server_error'                 => array(
			'hooks_to_deactivate' => array( 'neve_do_server_error' ),
			'posts_map_key'       => 'server_error',
		),
		'neve_do_individual'                   => array(
			'posts_map_key' => 'individual',
		),
	);

	/**
	 * Constructor.
	 *
	 * Register actions and editors.
	 *
	 * @param string $namespace Builder Namespace.
	 * @param bool   $add_cl_cpt_hooks Don't register additional hooks as to allow widgets to execute once.
	 */
	public function __construct( $namespace, $add_cl_cpt_hooks = true ) {
		if ( function_exists( 'do_blocks' ) ) {
			add_filter( 'neve_post_content', 'do_blocks' );
		}
		add_filter( 'neve_post_content', 'wptexturize' );
		add_filter( 'neve_post_content', 'convert_smilies' );
		add_filter( 'neve_post_content', 'convert_chars' );
		add_filter( 'neve_post_content', 'wpautop' );
		add_filter( 'neve_post_content', 'shortcode_unautop' );
		add_filter( 'neve_post_content', 'do_shortcode' );
		add_action( 'template_redirect', [ $this, 'render_single' ] );
		foreach ( $this->builders_list as $index => $builder ) {
			$builder = $namespace . $builder;
			$builder = new $builder();
			/**
			 * Builder instance.
			 *
			 * @var Abstract_Builders $builder Builder object.
			 */
			if ( ! $builder->should_load() ) {
				continue;
			}
			$builder->register_hooks();
			$this->available_builders[ $builder->get_builder_id() ] = $builder;
		}

		Inside_Layout::get_instance()->init();
		Template_Layout::get_instance()->init();

		if ( $add_cl_cpt_hooks ) {
			/**
			 * Invoke `neve_do_global` on `wp_footer`
			 */
			add_action(
				'wp_footer',
				function() {
					if ( is_admin() ) {
						return;
					}
					do_action( 'neve_do_global' );
				}
			);
			$post_map = Custom_Layouts_Cpt::get_custom_layouts();
			foreach ( $post_map as $layout => $posts ) {
				switch ( $layout ) {
					case 'header':
						add_action( 'neve_do_header', [ $this, 'render_first_markup' ], 1 );
						break;
					case 'footer':
						add_action( 'neve_do_footer', [ $this, 'render_first_markup' ], 1 );
						break;
					case 'global':
						add_action(
							'neve_do_global',
							function() {
								/**
								 * Render all global custom layouts attached.
								 */
								$this->render_inline_markup( false );
							},
							1
						);
						break;
					case 'inside':
						add_action(
							'neve_do_inside_content',
							function() {
								/**
								 * Render all inside custom layouts attached.
								 */
								$this->render_inline_markup( false, 'inside' );
							},
							1
						);
						break;
					case 'not_found':
						add_action( 'neve_do_404', [ $this, 'render_first_markup' ], 1 );
						break;
					case 'single_post':
					case 'single_page':
					case 'archives':
					case 'search':
						add_action(
							'neve_do_template_content_' . $layout,
							function() use ( $layout ) {
								/**
								 * Render template custom layout.
								 */
								$this->render_inline_markup( true, $layout );
							},
							1
						);
						break;

					case 'offline':
						add_action( 'neve_do_offline', [ $this, 'render_first_markup' ], 1 );
						break;
					case 'server_error':
						add_action( 'neve_do_server_error', [ $this, 'render_first_markup' ], 1 );
						break;
					case 'individual':
						add_action( 'neve_do_individual', [ $this, 'render_specific_markup' ], 1 );
						break;
					case 'sidebar':
						foreach ( $posts as $post_id => $priority ) {
							$sidebar = get_post_meta( $post_id, 'custom-layout-options-sidebar', true );
							if ( empty( $sidebar ) ) {
								continue;
							}

							$action = get_post_meta( $post_id, 'custom-layout-options-sidebar-action', true );
							if ( empty( $action ) ) {
								continue;
							}

							$id     = Abstract_Builders::maybe_get_translated_layout( $post_id );
							$editor = Abstract_Builders::get_post_builder( $id );
							if ( $this->available_builders[ $editor ]->is_expired( $id ) ) {
								continue;
							}

							if ( $sidebar === 'blog' ) {
								add_action(
									'wp',
									function () use ( $action, $priority, $post_id ) {
										$this->render_blog_custom_sidebar( $action, $priority, $post_id );
									},
									$priority
								);
							}

							if ( $sidebar === 'woocommerce' ) {
								// This is because the shop sidebar in Neve is rendered at wp with priority 11 and this action must be after that one
								$priority = $priority + 11;
								add_action(
									'wp',
									function () use ( $action, $priority, $post_id ) {
										$this->render_shop_custom_sidebar( $action, $priority, $post_id );
									},
									$priority
								);
							}

							if ( $sidebar === 'lifter_lms' ) {
								$priority = $priority + 10;
								add_action(
									'wp',
									function () use ( $action, $priority, $post_id ) {
										$this->render_lifter_custom_sidebar( $action, $priority, $post_id );
									},
									$priority
								);
							}
						}
						break;
					default:
						add_action(
							'wp',
							function() use ( $layout ) {
								/**
								 * Render all custom layouts attached.
								 *
								 * @var string $layout specifies the hook name.
								 */
								$this->render_inline_markup( false, $layout );
							}
						);
						break;
				}
			}
		}
	}

	/**
	 * Render lifter custom sidebar.
	 *
	 * @param string $action Sidebar action ( append / prepand / replace ).
	 * @param int    $priority Custom layout priority.
	 * @param int    $post_id Custom layout id.
	 */
	private function render_lifter_custom_sidebar( $action, $priority, $post_id ) {
		if ( ! function_exists( 'is_lifterlms' ) ) {
			return;
		}

		if ( ! is_lifterlms() ) {
			return;
		}

		if ( ! $this->check_conditions( $post_id ) ) {
			return;
		}

		if ( $action === 'replace' ) {
			add_filter(
				'neve_has_custom_sidebar',
				function ( $value, $context ) {
					if ( $context !== 'lifter' ) {
						return $value;
					}
					return true;
				},
				10,
				2
			);
			remove_all_actions( 'neve_after_sidebar_content' );
			remove_all_actions( 'neve_before_sidebar_content' );
		}

		$hook = $action === 'append' ? 'neve_after_sidebar_content' : 'neve_before_sidebar_content';
		add_action(
			$hook,
			function ( $context, $side ) use ( $post_id ) {
				$this->render_sidebar_markup( 'lifter', $side, $post_id );
			},
			$priority,
			2
		);
	}

	/**
	 * Detect WooCommerce pages where sidebar shop is present.
	 *
	 * @return bool
	 */
	private function neve_is_woo_page() {
		$functions_to_check = [ 'is_woocommerce', 'is_cart', 'is_checkout', 'is_account_page' ];
		foreach ( $functions_to_check as $func ) {
			if ( ! function_exists( $func ) ) {
				return false;
			}
		}
		return is_woocommerce() || is_cart() || is_checkout() || is_account_page();
	}

	/**
	 * Render shop custom sidebar.
	 *
	 * @param string $action Sidebar action ( append / prepand / replace ).
	 * @param int    $priority Custom layout priority.
	 * @param int    $post_id Custom layout id.
	 */
	private function render_shop_custom_sidebar( $action, $priority, $post_id ) {
		if ( ! $this->neve_is_woo_page() ) {
			return;
		}

		if ( ! $this->check_conditions( $post_id ) ) {
			return;
		}

		if ( $action === 'replace' ) {
			add_filter(
				'neve_has_custom_sidebar',
				function ( $value, $context ) {
					if ( $context !== 'shop' && $context !== 'woo-page' ) {
						return $value;
					}
					return true;
				},
				10,
				2
			);
			remove_all_actions( 'neve_after_sidebar_content' );
			remove_all_actions( 'neve_before_sidebar_content' );
		}

		$hook = $action === 'append' ? 'neve_after_sidebar_content' : 'neve_before_sidebar_content';
		add_action(
			$hook,
			function ( $context, $side ) use ( $post_id ) {
				$this->render_sidebar_markup( 'shop', $side, $post_id );
			},
			$priority,
			2
		);
	}


	/**
	 * Render blog custom sidebar.
	 *
	 * @param string $action Sidebar action ( append / prepand / replace ).
	 * @param int    $priority Custom layout priority.
	 * @param int    $post_id Custom layout id.
	 */
	private function render_blog_custom_sidebar( $action, $priority, $post_id ) {
		if ( ! \Neve_Pro\Core\Loader::has_compatibility( 'custom_post_types_sidebar' ) ) {
			return;
		}
		if ( ! $this->check_conditions( $post_id ) ) {
			return;
		}

		if ( $action === 'replace' ) {
			add_filter(
				'neve_has_custom_sidebar',
				function ( $value, $context ) {
					if ( $context !== 'blog-archive' && $context !== 'single-post' && $context !== 'single-page' ) {
						return $value;
					}
					return true;
				},
				10,
				2
			);
			remove_all_actions( 'neve_after_sidebar_content' );
			remove_all_actions( 'neve_before_sidebar_content' );
		}

		$hook = $action === 'append' ? 'neve_after_sidebar_content' : 'neve_before_sidebar_content';
		add_action(
			$hook,
			function ( $context, $side ) use ( $post_id ) {
				if ( $context !== 'blog-archive' && $context !== 'single-post' && $context !== 'single-page' ) {
					return;
				}
				$this->render_sidebar_markup( $context, $side, $post_id );
			},
			$priority,
			2
		);
	}

	/**
	 * Render specific markup.
	 *
	 * @since 1.2.8
	 *
	 * @param int $id Custom Layout ID to render.
	 */
	public function render_specific_markup( $id ) {
		$post_id = Abstract_Builders::maybe_get_translated_layout( $id );
		$editor  = Abstract_Builders::get_post_builder( $id );

		if ( $this->available_builders[ $editor ]->is_expired( $post_id ) ) {
			return;
		}

		if ( ! isset( $this->available_builders[ $editor ] ) ) {
			return;
		}
		$this->render_wrap( $editor, $post_id );
	}

	/**
	 * Render sidebar markup.
	 *
	 * @param string $context Sidebar context.
	 * @param string $side Sidebar side ( left / right / off-canvas ).
	 * @param int    $post_id Post id.
	 */
	public function render_sidebar_markup( $context, $side, $post_id ) {
		// Remove rendering on custom layout.
		if ( is_singular( 'neve_custom_layouts' ) ) {
			return;
		}

		$sidebar_setup = apply_filters( 'neve_' . $context . '_sidebar_setup', [] );
		if ( empty( $sidebar_setup ) || ! array_key_exists( 'theme_mod', $sidebar_setup ) ) {
			return;
		}

		$theme_mod = $sidebar_setup['theme_mod'];
		$theme_mod = array_key_exists( 'side', $sidebar_setup ) ? $sidebar_setup['side'] : apply_filters( 'neve_sidebar_position', get_theme_mod( $theme_mod, 'right' ) );
		if ( $theme_mod !== $side ) {
			return;
		}

		if ( $context === 'shop' && ! $this->neve_is_woo_page() ) {
			return;
		}

		$post_type = get_post_type();
		if ( $context === 'blog-archive' && ! is_home() && ! is_post_type_archive( $post_type ) ) {
			return;
		}

		if ( $context === 'lifter' && ( function_exists( 'is_lifterlms' ) && ! is_lifterlms() ) ) {
			return;
		}

		$this->render_specific_markup( $post_id );
	}

	/**
	 * Render first custom layouts attached.
	 */
	public function render_first_markup() {
		$this->render_inline_markup( true );
	}

	/**
	 * Render inline markup.
	 *
	 * @param bool   $single is single post.
	 * @param string $predefined_hook [optional] specifies the hook name. If $predefined_hook has a value, the hook will be fired as a dedicated WP action with its priority.
	 *
	 * @return bool Has rendered?
	 */
	public function render_inline_markup( $single = true, $predefined_hook = null ) {
		// Remove rendering on custom layout.
		if ( is_singular( 'neve_custom_layouts' ) ) {
			return false;
		}

		$current_hook = is_null( $predefined_hook ) ? current_filter() : $predefined_hook;

		$hooks_to_deactivate = isset( $this->hooks_map[ $current_hook ]['hooks_to_deactivate'] ) ? $this->hooks_map[ $current_hook ]['hooks_to_deactivate'] : [];
		$posts_map_key       = isset( $this->hooks_map[ $current_hook ]['posts_map_key'] ) ? $this->hooks_map[ $current_hook ]['posts_map_key'] : $current_hook;

		$all_posts = Custom_Layouts_Cpt::get_custom_layouts();

		if ( empty( $all_posts ) || ! isset( $all_posts[ $posts_map_key ] ) ) {
			return false;
		}

		$posts = $all_posts[ $posts_map_key ];
		if ( empty( $posts ) ) {
			return false;
		}

		if ( in_array( $posts_map_key, [ 'header', 'footer' ], true ) ) {
			$ids_array        = array_keys( $posts );
			$highest_priority = $this->get_greatest_priority_rule( $ids_array );
			if ( $highest_priority === false ) {
				return false;
			}
			$new_posts                      = [];
			$new_posts[ $highest_priority ] = '10';
			$posts                          = $new_posts;

		}

		asort( $posts );
		foreach ( $posts as $post_id => $priority ) {
			$post_id = Abstract_Builders::maybe_get_translated_layout( $post_id );
			$editor  = Abstract_Builders::get_post_builder( $post_id );

			if ( $this->available_builders[ $editor ]->is_expired( $post_id ) ) {
				continue;
			}

			if ( ! isset( $this->available_builders[ $editor ] ) ) {
				continue;
			}

			if ( ! $this->available_builders[ $editor ]->check_conditions( $post_id ) ) {
				continue;
			}

			if ( $single ) {
				foreach ( $hooks_to_deactivate as $hook ) {
					remove_all_actions( $hook );
				}
			}

			Inside_Layout::get_instance()->set_options( $post_id );

			if ( is_null( $predefined_hook ) ) {
				$this->render_wrap( $editor, $post_id );
			} elseif ( $predefined_hook === 'inside' ) {
				add_action(
					'neve_render_inside_content_' . $post_id,
					function () use ( $editor, $post_id ) {
						$this->render_wrap( $editor, $post_id );
					},
					$priority
				);
			} elseif ( in_array( $predefined_hook, [ 'single_post', 'single_page', 'archives', 'search' ], true ) ) {
				$this->render_template( $editor, $post_id );
			} else {
				add_action(
					$current_hook,
					function() use ( $editor, $post_id ) {
						$this->render_wrap( $editor, $post_id );
					},
					$priority
				);
			}

			if ( $single ) {
				return true;
			}
		}

		return true;
	}


	/**
	 * Footer markup on Custom Layouts preview.
	 */
	public function render_footer() {
		echo '<footer class="nv-custom-footer" itemscope="itemscope" itemtype="https://schema.org/WPFooter">';
		$this->render_content();
		echo '</footer>';
	}


	/**
	 * This function handles the display on Custom Layouts preview, the single of Custom Layouts custom post type.
	 */
	public function render_single() {
		if ( ! is_singular( 'neve_custom_layouts' ) ) {
			return;
		}

		Inside_Layout::get_instance()->register_hooks();

		$post_id = get_the_ID();

		$layout = get_post_meta( $post_id, 'custom-layout-options-layout', true );
		switch ( $layout ) {
			case 'header':
				remove_all_actions( 'neve_do_header' );
				remove_all_actions( 'neve_do_top_bar' );
				add_action( 'neve_do_header', array( $this, 'render_header' ) );
				break;
			case 'footer':
				remove_all_actions( 'neve_do_footer' );
				add_action( 'neve_do_footer', array( $this, 'render_footer' ) );
				break;
			case 'global':
				remove_all_actions( 'neve_do_global' );
				add_action( 'neve_custom_layouts_template_content', array( $this, 'render_content' ) );
				break;
			case 'inside':
				remove_all_actions( 'neve_render_inside_content_' . $post_id );
				add_action( 'neve_custom_layouts_template_content', array( $this, 'render_content' ) );
				break;
			case 'offline':
			case 'server_error':
				remove_all_actions( 'neve_do_footer' );
				remove_all_actions( 'neve_do_header' );
				add_action( 'neve_custom_layouts_template_content', array( $this, 'render_content' ) );
				break;
			case 'not_found':
			case 'single_post':
			case 'single_page':
			case 'archives':
			case 'search':
				remove_all_actions( 'neve_do_template_content_single_post' );
				remove_all_actions( 'neve_do_template_content_single_page' );
				remove_all_actions( 'neve_do_template_content_archives' );
				remove_all_actions( 'neve_do_template_content_search' );
				add_action( 'neve_custom_layouts_template_content', array( $this, 'render_content' ) );
				break;
			default:
				remove_all_actions( 'neve_do_global' );
				remove_all_actions( 'neve_do_footer' );
				remove_all_actions( 'neve_do_header' );
				remove_all_actions( 'neve_do_top_bar' );
				remove_all_actions( 'neve_custom_layouts_template_content' );
				add_action( 'neve_custom_layouts_template_content', array( $this, 'render_content' ) );
				break;
		}
	}


	/**
	 * Header markup on Custom Layouts preview.
	 */
	public function render_header() {
		echo '<header class="nv-custom-header" itemscope="itemscope" itemtype="https://schema.org/WPHeader">';
		$this->render_content();
		echo '</header>';
	}

	/**
	 * Get the layout content.
	 */
	public function render_content() {
		while ( have_posts() ) {
			the_post();
			$post_id = get_the_ID();
			$builder = Abstract_Builders::get_post_builder( $post_id );

			if ( $builder !== 'custom' ) {
				the_content();
				continue;
			}
			$file_name = get_post_meta( $post_id, 'neve_editor_content', true );
			if ( empty( $file_name ) ) {
				continue;
			}
			$wp_upload_dir = wp_upload_dir( null, false );
			$upload_dir    = $wp_upload_dir['basedir'] . '/neve-theme/';
			$file_path     = $upload_dir . $file_name . '.php';
			if ( file_exists( $file_path ) && is_readable( $file_path ) ) {
				include_once $file_path;
			}
		}
	}

	/**
	 * Wrap the render call in actions that we can use to wrap content.
	 *
	 * @param string $editor The editor used.
	 * @param int    $post_id The post ID.
	 */
	private function render_wrap( $editor, $post_id ) {
		do_action( 'neve_before_custom_layout', current_action() );
		$this->available_builders[ $editor ]->render( $post_id );
		do_action( 'neve_after_custom_layout', current_action() );
	}

	/**
	 * Render a dynamic custom layout as a template.
	 *
	 * @param string $editor The editor used.
	 * @param int    $post_id The post ID.
	 */
	private function render_template( $editor, $post_id ) {
		// skip rendering for WooCommerce Products if there are no rules selected.
		if ( class_exists( 'WooCommerce', false ) && is_product() ) {
			$meta = get_post_meta( $post_id, Layouts_Metabox::META_CONDITIONAL, true );
			if ( empty( $meta ) ) {
				return;
			}
		}
		get_header();
		$this->render_wrap( $editor, $post_id );
		get_footer();
		exit;
	}

}
