<?php
/**
 * Replace header, footer or hooks with the default editor.
 *
 * @package Neve_Pro\Modules\Custom_Layouts\Admin\Builders
 */

namespace Neve_Pro\Modules\Custom_Layouts\Admin\Builders;

use Neve_Pro\Modules\Custom_Layouts\Module;
use Neve_Pro\Traits\Core;

/**
 * Class Default_Editor
 *
 * @package Neve_Pro\Modules\Custom_Layouts\Admin\Builders
 */
class Default_Editor extends Abstract_Builders {
	use Core;

	/**
	 * Original post.
	 *
	 * @var \WP_Post|array|null
	 */
	private $original_post;

	/**
	 * Custom Layout ID.
	 *
	 * @var int
	 */
	private $layout_id;


	/**
	 * Default_Editor constructor.
	 */
	public function __construct() {

	}

	/**
	 * Check if class should load or not.
	 *
	 * @return bool
	 */
	public function should_load() {
		return true;
	}

	/**
	 * Function that enqueues styles if needed.
	 */
	public function add_styles() {
		return false;
	}

	/**
	 * Builder id.
	 *
	 * @return string
	 */
	function get_builder_id() {
		return 'default';
	}

	/**
	 * Load markup for current hook.
	 *
	 * @param int $post_id Layout id.
	 *
	 * @return true
	 */
	function render( $post_id ) {
		global $post;
		$this->original_post = $post;
		$this->layout_id     = Abstract_Builders::maybe_get_translated_layout( $post_id );
		setup_postdata( $this->layout_id );

		$post    = get_post( $this->layout_id );//phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$content = get_the_content( null, false, $post );
		
		$is_acf_active = class_exists( 'acf', false );
		
		if ( $is_acf_active ) {
			add_filter( 'acf/pre_load_value', array( $this, 'acf_load_value_custom_layout' ), 10, 3 );
		}
		
		$content = apply_filters( 'the_content', $content );
		echo apply_filters( 'neve_custom_layout_magic_tags', $content, $this->layout_id ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		if ( $is_acf_active ) {
			remove_filter( 'acf/pre_load_value', array( $this, 'acf_load_value_custom_layout' ), 10 );
		}
		
		$post = $this->original_post;//phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		wp_reset_postdata();
		setup_postdata( $this->original_post );
		return true;
	}

	/**
	 * Load the ACF fields for the current layout using the original post.
	 *
	 * @param mixed $value Value of the field.
	 * @param int   $post_id Post id.
	 * @param array $field Field data.
	 *
	 * @return mixed
	 */
	public function acf_load_value_custom_layout( $value, $post_id, $field ) {
		if ( $post_id === $this->layout_id && isset( $field['name'] ) && isset( $this->original_post->ID ) ) {
			return get_field( $field['name'], $this->original_post->ID );
		}
		return $value;
	}
}
