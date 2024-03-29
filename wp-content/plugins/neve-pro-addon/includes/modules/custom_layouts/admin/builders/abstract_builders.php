<?php
/**
 * Abstract class for builders compatibility.
 *
 * @package Neve_Pro\Modules\Custom_Layouts\Admin\Builders
 */

namespace Neve_Pro\Modules\Custom_Layouts\Admin\Builders;

use Neve_Pro\Modules\Custom_Layouts\Admin\Layouts_Metabox;
use Neve_Pro\Traits\Core;
use Neve_Pro\Traits\Conditional_Display;

/**
 * Class Abstract_Builders
 *
 * @package Neve_Pro\Modules\Custom_Layouts\Admin\Builders
 */
abstract class Abstract_Builders {
	use Core;
	use Conditional_Display;

	/**
	 * Id of the current builder
	 *
	 * @var string
	 */
	protected $builder_id;

	/**
	 * Check if class should load or not.
	 *
	 * @return bool
	 */
	abstract function should_load();

	/**
	 * Get builder id.
	 *
	 * @return string
	 */
	abstract function get_builder_id();

	/**
	 * Add actions to hooks.
	 */
	public function register_hooks() {
		add_action( 'wp_enqueue_scripts', array( $this, 'add_styles' ), 9 );
		add_filter( 'neve_custom_layout_magic_tags', array( $this, 'replace_magic_tags' ), 10, 2 );
	}

	/**
	 * Replace magic tags from post content.
	 *
	 * @param string $post_content Current post content.
	 * @param int    $post_id Post id.
	 * @return string
	 */
	public function replace_magic_tags( $post_content, $post_id ) {
		$condition_groups = json_decode( get_post_meta( $post_id, Layouts_Metabox::META_CONDITIONAL, true ), true );
		if ( empty( $condition_groups ) ) {
			return $post_content;
		}

		$archive_taxonomy = array( 'category', 'product_cat', 'post_tag', 'product_tag' );

		foreach ( $archive_taxonomy as $type ) {
			if ( $this->layout_has_condition( 'archive_taxonomy', $type, $condition_groups[0] ) ) {
				$category     = get_queried_object();
				$title        = $category->name;
				$description  = $category->description;
				$post_content = str_replace( '{title}', $title, $post_content );
				$post_content = str_replace( '{description}', $description, $post_content );
			}
		}

		if ( $this->layout_has_condition( 'archive_type', 'author', $condition_groups[0] ) ) {
			$author_id          = get_queried_object_id();
			$author_name        = get_the_author_meta( 'display_name' );
			$author_decription  = get_the_author_meta( 'description' );
			$author_avatar_size = apply_filters( 'nv_custom_layout_avatar_magic_tag_size', 32 );
			/**
			 * Filters the author get_avatar args used when displaying the avatar replaced by the Custom Layout magic tag {author_avatar}.
			 *
			 * @since 2.5.9
			 *
			 * @param array   $args The get_avatar() args.
			 */
			$author_avatar_args = apply_filters( 'nv_custom_layout_avatar_magic_tag_args', array( 'force_display' => true ) );
			$author_avatar      = get_avatar( $author_id, $author_avatar_size, '', '', $author_avatar_args );
			$post_content       = str_replace( '{author}', $author_name, $post_content );
			$post_content       = str_replace( '{author_description}', $author_decription, $post_content );
			$post_content       = str_replace( '{author_avatar}', $author_avatar, $post_content );
		}

		if ( $this->layout_has_condition( 'archive_type', 'date', $condition_groups[0] ) ) {
			$date         = get_the_archive_title();
			$post_content = str_replace( '{date}', $date, $post_content );
		}

		wp_reset_postdata();
		$post_content = \HFG\parse_dynamic_tags( $post_content );

		return $post_content;
	}

	/**
	 * Check if current custom layout has a specific condition.
	 *
	 * @param string $root Page category.
	 * @param string $end  Page type.
	 * @param array  $condition_groups List of conditions.
	 *
	 * @return bool
	 */
	private function layout_has_condition( $root, $end, $condition_groups ) {
		foreach ( $condition_groups as $index => $conditions ) {
			if ( $conditions['root'] === $root && $conditions['end'] === $end && $conditions['condition'] === '===' ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Get the builder that you used to edit a post.
	 *
	 * @param int $post_id Post id.
	 *
	 * @return string
	 */
	public static function get_post_builder( $post_id ) {
		if ( get_post_meta( $post_id, 'neve_editor_mode', true ) === '1' ) {
			return 'custom';
		}

		if ( class_exists( '\Elementor\Plugin', false ) && defined( 'ELEMENTOR_VERSION' ) ) {
			$built_with_elementor = false;
			if ( version_compare( ELEMENTOR_VERSION, '3.2.0', '<=' ) ) {
				$built_with_elementor = \Elementor\Plugin::instance()->db->is_built_with_elementor( $post_id );
			} else {
				/* @phpstan-ignore-next-line */
				$document = \Elementor\Plugin::$instance->documents->get( $post_id );
				if ( ! empty( $document ) ) {
					$built_with_elementor = $document->is_built_with_elementor();
				}
			}
			if ( $built_with_elementor ) {
				return 'elementor';
			}
		}

		if ( class_exists( 'FLBuilderModel', false ) && get_post_meta( $post_id, '_fl_builder_enabled', true ) ) {
			return 'beaver';
		}

		if ( class_exists( 'Brizy_Editor_Post', false ) ) {
			try {
				$post = \Brizy_Editor_Post::get( $post_id );
				if ( $post->uses_editor() ) {
					return 'brizy';
				}
			} catch ( \Exception $exception ) {
				// The post type is not supported by Brizy hence Brizy should not be used render the post.
			}
		}

		return 'default';
	}

	/**
	 * Get the translated layout in Polylang or WPML.
	 *
	 * @param int $post_id Post id.
	 * @return int
	 */
	public static function maybe_get_translated_layout( $post_id ) {
		if ( function_exists( 'pll_current_language' ) && function_exists( 'pll_get_post' ) ) {
			$lang               = pll_current_language();
			$translated_post_id = pll_get_post( $post_id, $lang );
			return is_int( $translated_post_id ) && ! empty( $translated_post_id ) ? $translated_post_id : $post_id;
		}

		// https://wpml.org/documentation/support/wpml-coding-api/wpml-hooks-reference/#hook-605256
		return apply_filters( 'wpml_object_id', $post_id, 'neve_custom_layouts', true );
	}

	/**
	 * Check if a post is expired or not.
	 *
	 * @param int $post_id Post id.
	 *
	 * @return bool
	 */
	public function is_expired( $post_id ) {
		$should_expire = get_post_meta( $post_id, 'custom-layout-options-should-expire', true );
		if ( ! $should_expire ) {
			return false;
		}

		$expiration_date = get_post_meta( $post_id, 'custom-layout-expiration-date', true );
		if ( empty( $expiration_date ) ) {
			return false;
		}

		if ( $expiration_date < date_i18n( 'Y-m-d\TH:i:s', true ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Abstract function that needs to be implemented in Builders classes.
	 * It loads the markup based on current hook.
	 *
	 * @param int $id Layout id.
	 *
	 * @return mixed
	 */
	abstract function render( $id );

	/**
	 * Function that enqueues styles if needed.
	 */
	abstract function add_styles();
}
