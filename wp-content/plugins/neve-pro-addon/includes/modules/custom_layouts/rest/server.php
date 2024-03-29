<?php
/**
 * Rest Endpoints Handler.
 *
 * @package Neve_Pro\Modules\Custom_Layouts\Rest
 */

namespace Neve_Pro\Modules\Custom_Layouts\Rest;

use Neve_Pro\Admin\Conditional_Display;

/**
 * Class Server
 *
 * @package Neve_Pro\Modules\Custom_Layouts\Rest
 */
class Server {

	/**
	 * Initialize the rest functionality.
	 */
	public function init() {
		add_action( 'rest_api_init', array( $this, 'register_endpoints' ) );
	}

	/**
	 * Register endpoints.
	 */
	public function register_endpoints() {
		register_rest_field(
			'neve_custom_layouts',
			'neve_editor_mode',
			array(
				'get_callback'    => array( $this, 'get_meta_field' ),
				'update_callback' => array( $this, 'update_meta_field' ),
			)
		);

		register_rest_field(
			'neve_custom_layouts',
			'neve_editor_content',
			array(
				'get_callback'    => array( $this, 'get_meta_field' ),
				'update_callback' => array( $this, 'update_editor_content_field' ),
			)
		);

		$route_args = [
			'query' => [
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => function ( $key ) {
					return is_string( $key );
				},
			],
			'type'  => [
				'type'              => 'string',
				'sanitize_callback' => function ( $value ) {
					return in_array( $value, [ 'post', 'page' ], true ) ? $value : '';
				},
				'validate_callback' => function ( $param ) {
					return is_string( $param );
				},
			],
		];
		register_rest_route(
			NEVE_PRO_REST_NAMESPACE,
			'/custom-layouts/options',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'get_options' ],
				'args'                => $route_args,
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			)
		);

		$route_args = [
			'id' => [
				'type'              => 'number',
				'sanitize_callback' => 'absint',
				'validate_callback' => function ( $param, $request, $key ) {
					return is_numeric( $param );
				},
			],
		];
		register_rest_route(
			NEVE_PRO_REST_NAMESPACE,
			'/custom-layouts/options/(?P<id>\d+)',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_option_value' ],
				'args'                => $route_args,
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			)
		);
	}

	/**
	 * Return a single selected option value.
	 *
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @return \WP_REST_Response
	 */
	public function get_option_value( \WP_REST_Request $request ) {
		$id   = $request['id'];
		$post = get_post( $id );
		if ( empty( $post ) ) {
			return new \WP_REST_Response(
				[
					'success' => true,
					'data'    => [],
				]
			);
		}

		return new \WP_REST_Response(
			[
				'success' => true,
				'data'    => [
					'value' => $post->ID,
					'label' => $post->post_title,
				],
			]
		);
	}

	/**
	 * Get options by search query.
	 *
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @return \WP_REST_Response
	 */
	public function get_options( \WP_REST_Request $request ) {
		$query = $request['query'];
		$type  = $request['type'];

		if ( empty( $type ) ) {
			return new \WP_REST_Response(
				[
					'success' => true,
					'data'    => [],
				]
			);
		}

		$results = Conditional_Display::get_options_list( $type, $query );

		return new \WP_REST_Response(
			[
				'success' => true,
				'data'    => $results,
			]
		);
	}

	/**
	 * Editor mode endpoint get.
	 *
	 * @param array  $object Post.
	 * @param string $field_name Field name.
	 * @param Object $request Request object.
	 *
	 * @return mixed
	 */
	public function get_meta_field( $object, $field_name, $request ) {
		return get_post_meta( $object['id'], $field_name, true );
	}

	/**
	 * Editor mode endpoint update callback.
	 *
	 * @param array  $value Request data.
	 * @param Object $object Request object.
	 * @param string $field_name Field name.
	 *
	 * @return bool
	 */
	public function update_meta_field( $value, $object, $field_name ) {
		if ( $field_name !== 'neve_editor_mode' ) {
			return false;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		$meta_value    = get_post_meta( $object->ID, $field_name, true );
		$updated_value = '0';
		if ( empty( $meta_value ) || (string) $meta_value === '0' ) {
			$updated_value = '1';
		}
		update_post_meta( $object->ID, $field_name, $updated_value );

		return true;
	}

	/**
	 * Editor content endpoint update callback.
	 *
	 * @param array  $value Request data.
	 * @param Object $object Request object.
	 * @param string $field_name Field name.
	 *
	 * @return mixed
	 */
	public function update_editor_content_field( $value, $object, $field_name ) {
		if ( $field_name !== 'neve_editor_content' ) {
			return false;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		$post_id       = $object->ID;
		$file_name     = 'neve-custom-script-' . $post_id;
		$wp_upload_dir = wp_upload_dir( null, false );
		$upload_dir    = $wp_upload_dir['basedir'] . '/neve-theme/';
		$file_path     = $upload_dir . $file_name . '.php';

		require_once ABSPATH . '/wp-admin/includes/file.php';
		global $wp_filesystem;
		WP_Filesystem();
		// Make sure the upload directory exists
		wp_mkdir_p( $upload_dir );
		$value = apply_filters( 'neve_custom_layout_magic_tags', $value, $post_id );
		$wp_filesystem->put_contents( $file_path, $value );
		update_post_meta( $object->ID, $field_name, $file_name );

		return true;
	}

}
