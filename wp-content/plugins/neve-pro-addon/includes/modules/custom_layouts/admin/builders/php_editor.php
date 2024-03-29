<?php
/**
 * Php Editor to add custom code;
 *
 * @package Neve_Pro\Modules\Custom_Layouts\Admin\Builders
 */

namespace Neve_Pro\Modules\Custom_Layouts\Admin\Builders;

use Neve_Pro\Modules\Custom_Layouts\Module;
use Neve_Pro\Traits\Core;

/**
 * Class Php_Editor
 *
 * @package Neve_Pro\Modules\Custom_Layouts\Admin\Builders
 */
class Php_Editor extends Abstract_Builders {
	use Core;

	/**
	 * Check if class should load or not.
	 *
	 * @return bool
	 */
	function should_load() {
		return true;
	}

	/**
	 * Function that enqueues styles if needed.
	 */
	function add_styles() {
		return false;
	}

	/**
	 * Builder id.
	 *
	 * @return string
	 */
	function get_builder_id() {
		return 'custom';
	}

	/**
	 * Load markup for current hook.
	 *
	 * @param int $post_id Layout id.
	 */
	function render( $post_id ) {
		$action        = current_action();
		$post_id       = Abstract_Builders::maybe_get_translated_layout( $post_id );
		$file_name     = get_post_meta( $post_id, 'neve_editor_content', true );
		$wp_upload_dir = wp_upload_dir( null, false );
		$upload_dir    = $wp_upload_dir['basedir'] . '/neve-theme/';
		$file_path     = $upload_dir . $file_name . '.php';

		if ( ! file_exists( $file_path ) ) {
			return;
		}

		if ( ! empty( $action ) && $action === 'neve_do_individual' ) {
			include $file_path;
			return;
		}

		include_once $file_path;

	}

}
