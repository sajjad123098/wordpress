<?php
/**
 * Default values for controls from the single post.
 *
 * @package Neve_Pro\Modules\Blog_Pro\Customizer\Defaults
 */

namespace Neve_Pro\Modules\Blog_Pro\Customizer\Defaults;

/**
 * Trait Single_Post
 *
 * @package Neve_Pro\Modules\Blog_Pro\Customizer\Defaults
 */
trait Single_Post {

	/**
	 * Get social icons default value.
	 *
	 * @return array
	 */
	public function social_icons_default() {
		return [
			[
				'social_network'   => 'facebook',
				'title'            => 'Facebook',
				'visibility'       => 'yes',
				'icon_color'       => '',
				'background_color' => '',
				'display_desktop'  => true,
				'display_mobile'   => true,
			],
			[
				'social_network'   => 'twitter',
				'title'            => 'X',
				'visibility'       => 'yes',
				'icon_color'       => '',
				'background_color' => '',
				'display_desktop'  => true,
				'display_mobile'   => true,
			],
			[
				'social_network'   => 'email',
				'title'            => 'Email',
				'visibility'       => 'yes',
				'icon_color'       => '',
				'background_color' => '',
				'display_desktop'  => true,
				'display_mobile'   => true,
			],
		];
	}

	/**
	 * Return the default value for responsive padding control.
	 *
	 * @return array
	 */
	public function responsive_padding_default() {
		return [
			'mobile'       => [
				'top'    => 20,
				'right'  => 20,
				'bottom' => 20,
				'left'   => 20,
			],
			'tablet'       => [
				'top'    => 20,
				'right'  => 20,
				'bottom' => 20,
				'left'   => 20,
			],
			'desktop'      => [
				'top'    => 20,
				'right'  => 20,
				'bottom' => 20,
				'left'   => 20,
			],
			'mobile-unit'  => 'px',
			'tablet-unit'  => 'px',
			'desktop-unit' => 'px',
		];
	}

	/**
	 * Return the default value for related posts columns control.
	 * To avoid any migration we changed the control name from neve_related_posts_columns to neve_related_posts_col_nb. The value of
	 * neve_related_posts_columns control is part of the default value of neve_related_posts_col_nb.
	 *
	 * @param string $control The name of the previous control.
	 *
	 * @return array
	 */
	public function responsive_related_posts_nb( $control ) {
		$old_related_posts_nb = get_theme_mod( $control, 3 );
		return [
			'mobile'  => 1,
			'tablet'  => 1,
			'desktop' => $old_related_posts_nb,
		];
	}

	/**
	 * Get meta options from the ACF plugin.
	 *
	 * @param string $post_type Post type.
	 *
	 * @return array
	 */
	private function get_acf_options( $post_type = 'post' ) {
		$acf_options = [];
		$groups      = acf_get_field_groups( array( 'post_type' => $post_type ) );
		foreach ( $groups as $group ) {
			$fields = acf_get_fields( $group['key'] );
			if ( ! empty( $fields ) ) {
				foreach ( $fields as $field ) {
					$acf_options[ $field['key'] ] = $field['label'];
				}
			}
		}
		return $acf_options;
	}

	/**
	 * Get meta options from the Toolset plugin.
	 *
	 * @return array
	 */
	private function get_toolset_options() {
		$toolset_options = [];
		$groups          = apply_filters( 'wpcf_get_groups_by_post_type', 'post' );
		if ( empty( $groups ) || ! is_array( $groups ) ) {
			return $toolset_options;
		}

		foreach ( $groups as $group_id => $group_data ) {
			$fields_from_group = apply_filters( 'wpcf_fields_by_group', $group_id );
			if ( empty( $fields_from_group ) || ! is_array( $fields_from_group ) ) {
				continue;
			}

			foreach ( $fields_from_group as $group_slug => $group_data ) {
				if ( is_array( $group_data ) && isset( $group_data['name'] ) ) {
					$toolset_options[ 'wpcf-' . $group_slug ] = $group_data['name'];
				}
			}
		}

		return $toolset_options;
	}

	/**
	 * Get meta options from the Metabox plugin.
	 *
	 * @return array
	 */
	private function get_metabox_options() {
		$metabox_options = [];
		if ( ! function_exists( 'rwmb_get_object_fields' ) ) {
			return $metabox_options;
		}

		$fields = rwmb_get_object_fields( 'post' );
		if ( empty( $fields ) || ! is_array( $fields ) ) {
			return $metabox_options;
		}
		foreach ( $fields as $field_id => $field_data ) {
			$metabox_options[ $field_id ] = $field_data['field_name'];
		}

		return $metabox_options;
	}

	/**
	 * Get meta source.
	 *
	 * @return array
	 */
	public function get_meta_type_options() {
		$fields_type = [
			'raw'        => __( 'Post Meta', 'neve' ),
			'custom_tax' => __( 'Custom Taxonomy', 'neve' ),
		];
		if ( class_exists( 'ACF', false ) ) {
			$fields_type['acf'] = 'ACF';
		}

		if ( defined( 'TYPES_ABSPATH' ) ) {
			$fields_type['toolset'] = 'Toolset';
		}
		if ( class_exists( '\MBB\Parsers\MetaBox' ) ) {
			$fields_type['metabox'] = 'Metabox';
		}
		if ( defined( 'PODS_VERSION' ) ) {
			$fields_type['pods'] = 'Pods';
		}
		return $fields_type;
	}

	/**
	 * Get repeater fields for custom meta.
	 *
	 * @param string $post_type Post type.
	 *
	 * @return array[]
	 */
	private function get_new_elements_fields( $post_type = 'post' ) {

		$fields = [
			'hide_on_mobile' => [
				'type'  => 'checkbox',
				'label' => __( 'Hide on mobile', 'neve' ),
			],
			'meta_type'      => [
				'type'      => 'select',
				'label'     => __( 'Type', 'neve' ),
				'choices'   => $this->get_meta_type_options(),
				'dependent' => 'field',
			],
		];

		$field = [
			'type'       => [
				'raw'        => 'text',
				'custom_tax' => 'text',
			],
			'label'      => __( 'Name', 'neve' ),
			'depends_on' => 'meta_type',
		];

		if ( class_exists( 'ACF', false ) ) {
			$field['type']['acf']    = 'select';
			$field['choices']['acf'] = $this->get_acf_options( $post_type );
		}

		if ( defined( 'TYPES_ABSPATH' ) ) {
			$field['type']['toolset']    = 'select';
			$field['choices']['toolset'] = $this->get_toolset_options();
		}

		if ( class_exists( '\MBB\Parsers\MetaBox' ) ) {
			$field['type']['metabox']    = 'select';
			$field['choices']['metabox'] = $this->get_metabox_options();
		}

		if ( defined( 'PODS_VERSION' ) ) {
			$field['type']['pods'] = 'text';
		}

		$fields['field'] = $field;

		$fields['format'] = [
			'type'    => 'text',
			'label'   => __( 'Format', 'neve' ),
			'default' => '{meta}',
		];

		$fields['fallback'] = [
			'type'  => 'text',
			'label' => __( 'Fallback', 'neve' ),
		];

		return $fields;
	}

	/**
	 * Get the fields for meta items that cannot be deleted.
	 *
	 * @return array[]
	 */
	private function get_blocked_elements_fields() {
		return [
			'hide_on_mobile' => [
				'type'  => 'checkbox',
				'label' => __( 'Hide on mobile', 'neve' ),
			],
			'format'         => [
				'type'    => 'text',
				'label'   => __( 'Format', 'neve' ),
				'default' => '{meta}',
			],
		];
	}
}
