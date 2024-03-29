<?php
/**
 * Author:          Bogdan Preda <bogdan.preda@themeisle.com>
 * Created on:      2021-10-22
 *
 * @package Neve Pro
 */

namespace Neve_Pro\Modules\Post_Type_Enhancements;

use Neve_Pro\Core\Abstract_Module;
use Neve_Pro\Modules\Post_Type_Enhancements\Customizer\Blog_Pro_CPT;
use Neve_Pro\Modules\Post_Type_Enhancements\Customizer\Layout_CPT_Archive;
use Neve_Pro\Modules\Post_Type_Enhancements\Customizer\Layout_CPT_Sidebar_Container;
use Neve_Pro\Modules\Post_Type_Enhancements\Customizer\Layout_CPT_Single;
use Neve_Pro\Modules\Post_Type_Enhancements\Model\CPT_Model;
use Neve_Pro\Modules\Post_Type_Enhancements\Views\Blog_Pro_CPT_View;
use Neve_Pro\Modules\Post_Type_Enhancements\Views\Layout_CPT_Archive_View;
use Neve_Pro\Modules\Post_Type_Enhancements\Views\Layout_CPT_View;
use WP_Post_Type;

/**
 * Class Module  - main class for the module
 *
 * @package Neve_Pro\Modules\Post_Type_Enhancements
 */
class Module extends Abstract_Module {
	const POST_TYPES_CACHE_KEY     = 'neve_supported_cpt_cache';
	const POST_TYPES_OBJ_CACHE_KEY = 'neve_supported_cpt_objects_cache';

	/**
	 * Define module properties.
	 *
	 * @access  public
	 * @return void
	 *
	 * @version 3.0.7
	 */
	final public function define_module_properties() {
		$this->slug          = 'post_type_enhancements';
		$this->name          = __( 'Post types enhancements', 'neve' );
		$this->description   = __( 'Enable Neve post enhancements for custom post types.', 'neve' );
		$this->documentation = array(
			'url'   => 'https://bit.ly/nv-pte',
			'label' => __( 'Learn more', 'neve' ),
		);
		$this->order         = 4;
	}

	/**
	 * Check if module should load.
	 *
	 * @return bool
	 */
	final public function should_load() {
		return $this->is_active();
	}

	/**
	 * Returns a list of supported custom post types.
	 * The results are served from cache if available.
	 *
	 * @return array
	 */
	private function get_supported_post_types_cached() {
		$cache = get_transient( self::POST_TYPES_CACHE_KEY );
		if ( ! empty( $cache ) ) {
			return $cache;
		}

		return apply_filters( 'neve_post_type_supported_list', [], 'block_editor' );
	}

	/**
	 * Updates the cache value of supported post types.
	 * Adds custom post types that were registered after the `init` hook.
	 *
	 * @return void
	 */
	final public function update_supported_cpt() {
		$cache = get_transient( self::POST_TYPES_CACHE_KEY );
		if ( empty( $cache ) ) {
			$supported_post_types = apply_filters( 'neve_post_type_supported_list', [], 'block_editor' );
			$post_type_objects    = [];
			foreach ( $supported_post_types as $post_type ) {
				$cpt_object = get_post_type_object( $post_type );
				if ( $cpt_object instanceof WP_Post_Type && ! empty( $post_type ) ) {
					$post_type_objects[ $post_type ] = $cpt_object;
				}
			}
			set_transient( self::POST_TYPES_OBJ_CACHE_KEY, $post_type_objects, 12 * HOUR_IN_SECONDS );
			set_transient( self::POST_TYPES_CACHE_KEY, $supported_post_types, 12 * HOUR_IN_SECONDS );
		}
	}

	/**
	 * Clears the cache if a new post type is detected.
	 *
	 * @param string       $post_type The post type slug.
	 * @param WP_Post_Type $post_type_object The post type object.
	 *
	 * @return void
	 */
	final public function supported_cpt_cache_clear( $post_type, $post_type_object ) {
		if (
			$post_type_object->_builtin === true ||
			$post_type_object->public === false ||
			$post_type_object->capability_type !== 'post' ||
			post_type_supports( $post_type, 'editor' ) === false ||
			post_type_supports( $post_type, 'custom-fields' ) === false
		) {
			return;
		}

		$cache = get_transient( self::POST_TYPES_CACHE_KEY );

		// no cache, bail
		if ( empty( $cache ) ) {
			return;
		}

		// already present, bail
		if ( in_array( $post_type, $cache ) ) {
			return;
		}
		delete_transient( self::POST_TYPES_OBJ_CACHE_KEY );
		delete_transient( self::POST_TYPES_CACHE_KEY );
	}

	/**
	 * Run Post Type Enhancements Module
	 */
	final public function run_module() {
		add_action( 'shutdown', [ $this, 'update_supported_cpt' ] );
		add_action( 'registered_post_type', [ $this, 'supported_cpt_cache_clear' ], 10, 2 );

		add_action(
			'init',
			function () {
				$supported_custom_post_types = $this->get_supported_post_types_cached();
				foreach ( $supported_custom_post_types as $order => $custom_post_type ) {
					$model = new CPT_Model( $custom_post_type );
					if ( $model->can_use_model() ) {
						new Layout_CPT_Sidebar_Container( $model );
						new Layout_CPT_Single( $model );
						new Layout_CPT_View( $model );
						new Layout_CPT_Archive( $model );
						new Layout_CPT_Archive_View( $model );

						if ( get_option( 'nv_pro_blog_pro_status' ) ) {
							new Blog_Pro_CPT( $model );
							new Blog_Pro_CPT_View( $model );
						}
					}
				}
			},
			PHP_INT_MAX
		);
	}
}
