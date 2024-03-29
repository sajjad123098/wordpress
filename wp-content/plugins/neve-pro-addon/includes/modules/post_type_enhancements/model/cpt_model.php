<?php
/**
 * A custom post type model.
 *
 * Used to better access and manage a custom post type properties.
 *
 * Author:      Bogdan Preda <bogdan.preda@themeisle.com>
 * Created on:  15-12-{2021}
 *
 * @package Neve Pro Addon
 */

namespace Neve_Pro\Modules\Post_Type_Enhancements\Model;

use Neve\Core\Settings\Mods;
use Neve\Customizer\Options\Layout_Single_Post;
use Neve_Pro\Modules\Post_Type_Enhancements\Module as Post_Type_Enhancements_Module;
use WP_Post_Type;

/**
 * Class Layout_Custom_Single_Post
 *
 * @since   3.1.0
 * @package Neve Pro Addon
 */
class CPT_Model {

	/**
	 * Holds the post type slug.
	 *
	 * @var string|null $type
	 */
	private $type = null;

	/**
	 * Holds the priority for this post type.
	 *
	 * @var int $priority
	 */
	private $priority = 0;

	/**
	 * Holds the post type object.
	 *
	 * @var WP_Post_Type|null $ctp_object
	 */
	private $cpt_object = null;

	/**
	 * Constructor for the class.
	 *
	 * @since   3.1.0
	 * @param string $type The post type slug. Default `post`.
	 * @param int    $priority The priority for the post type. Default `0`.
	 */
	public function __construct( $type = 'post', $priority = 0 ) {
		$cpt_object = get_post_type_object( $type );
		if ( is_null( $cpt_object ) ) {
			$cache_post_type_objects = get_transient( Post_Type_Enhancements_Module::POST_TYPES_OBJ_CACHE_KEY );
			if ( ! empty( $cache_post_type_objects ) && isset( $cache_post_type_objects[ $type ] ) ) {
				$cpt_object = $cache_post_type_objects[ $type ];
			}
		}
		if ( $cpt_object instanceof WP_Post_Type ) {
			$this->type       = $type;
			$this->cpt_object = $cpt_object;
			$this->priority   = $priority;
		}
	}

	/**
	 * Check if the model can be used.
	 *
	 * @since 3.1.0
	 * @return bool
	 */
	final public function can_use_model() {
		if ( is_null( $this->cpt_object ) ) {
			return false;
		}
		return $this->cpt_object instanceof WP_Post_Type;
	}

	/**
	 * Returns the model type.
	 *
	 * @since 3.1.0
	 * @return string|null
	 */
	final public function get_type() {
		if ( empty( $this->type ) ) {
			return 'post';
		}
		return $this->type;
	}

	/**
	 * Returns the model type as archive.
	 *
	 * @since 3.1.0
	 * @return string
	 */
	final public function get_archive_type() {
		return $this->get_type() . '_archive';
	}

	/**
	 * Returns the model priority.
	 *
	 * @since 3.1.0
	 * @return int
	 */
	final public function get_priority() {
		return $this->priority;
	}

	/**
	 * Singular label for the model
	 *
	 * @since 3.1.0
	 * @return mixed
	 */
	final public function get_singular() {
		return $this->cpt_object->labels->singular_name;
	}

	/**
	 * Plural label for the model
	 *
	 * @since 3.1.0
	 * @return mixed
	 */
	final public function get_plural() {
		return $this->cpt_object->labels->name;
	}

	/**
	 * Return the support of the model for archive.
	 *
	 * @since 3.1.0
	 * @return bool
	 */
	final public function has_archive() {
		if ( ! $this->can_use_model() ) {
			return false;
		}
		return $this->cpt_object->has_archive !== false;
	}

	/**
	 * Returns true if custom layout is enabled for this model.
	 *
	 * @since 3.1.0
	 * @return bool
	 */
	final public function is_custom_layout_enabled() {
		if ( empty( $this->type ) ) {
			return false;
		}
		return Mods::get( 'neve_' . $this->type . '_use_custom', false );
	}

	/**
	 * Returns true if custom layout is enabled for this model.
	 *
	 * @since 3.1.0
	 * @return bool
	 */
	final public function is_custom_layout_archive_enabled() {
		if ( empty( $this->type ) ) {
			return false;
		}
		if ( ! $this->has_archive() ) {
			return false;
		}
		return Mods::get( 'neve_' . $this->get_archive_type() . '_use_custom', false );
	}

	/**
	 * Returns true if cover layout is enabled for this model.
	 *
	 * @since 3.1.0
	 * @return bool
	 */
	final public function is_cover_layout() {
		if ( ! $this->is_custom_layout_enabled() ) {
			return Layout_Single_Post::is_cover_layout();
		}
		return Mods::get( 'neve_' . $this->type . '_header_layout' ) === 'cover';
	}

	/**
	 * Function used for active_callback control property for boxed title.
	 *
	 * @since 3.1.0
	 * @return bool
	 */
	final public function is_boxed_title() {
		if ( ! $this->is_cover_layout() ) {
			return false;
		}

		return Mods::get( 'neve_' . $this->type . '_cover_title_boxed_layout', false );
	}
}
