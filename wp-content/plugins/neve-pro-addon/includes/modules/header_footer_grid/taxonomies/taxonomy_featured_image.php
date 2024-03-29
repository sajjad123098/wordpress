<?php
/**
 * Module Class for adding featured image support to registered taxonomies.
 *
 * Name:    Header Footer Grid Addon
 * Author:  Bogdan Preda <bogdan.preda@themeisle.com>
 *
 * @version 3.0.0
 * @package Neve Pro Addon
 */
namespace Neve_Pro\Modules\Header_Footer_Grid\Taxonomies;

use Neve_Pro\Core\Loader;
use Neve_Pro\Modules\Header_Footer_Grid\Module;
use WP_Error;
use WP_Term;

/**
 * Class Taxonomy_Featured_Image
 *
 * @package Neve_Pro\Modules\Header_Footer_Grid\Taxonomies
 */
class Taxonomy_Featured_Image {

	/**
	 * Neve_Pro\Loader The single instance of Starter_Plugin.
	 *
	 * @var Taxonomy_Featured_Image|null
	 * @access   private
	 * @since    3.0.0
	 */
	private static $_instance = null;

	/**
	 * Array of slug taxonomies to target
	 *
	 * @var string[]
	 */
	private $taxonomies = array( 'category' );

	/**
	 * Holds the translations
	 *
	 * @var array
	 */
	private $labels = array();

	/**
	 * The meta key for storing the image.
	 *
	 * @var string
	 */
	private $term_meta_key = 'term_image';

	/**
	 * Allow html input tags for wp_kses function.
	 *
	 * @var array
	 */
	private $allowed_html = array(
		'input' => array(
			'type'  => array(),
			'id'    => array(),
			'class' => array(),
			'name'  => array(),
			'value' => array(),
		),
		'img'   => array(
			'id'    => array(),
			'class' => array(),
			'src'   => array(),
		),
		'div'   => array(
			'id'    => array(),
			'class' => array(),
			'style' => array(),
		),
		'p'     => array(
			'id'    => array(),
			'class' => array(),
			'style' => array(),
		),
	);

	/**
	 * Simple singleton to enforce one instance
	 *
	 * @return Taxonomy_Featured_Image
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Constructor for Taxonomy_Featured_Image
	 */
	private function __construct() {
		$this->labels = array(
			'fieldTitle'       => __( 'Featured Image', 'neve' ),
			/* translators: s - taxonomy singular (eg. Category) */
			'fieldDescription' => __( 'Select an image to use as a featured image for %s.', 'neve' ),
			'imageButton'      => __( 'Upload/Add Image', 'neve' ),
			'removeButton'     => __( 'Remove Image', 'neve' ),
			/* translators: s - taxonomy singular (eg. Category) */
			'modalTitle'       => __( 'Select or upload an image for %s', 'neve' ),
			'modalButton'      => __( 'Use image', 'neve' ),
			'adminColumnTitle' => __( 'Image', 'neve' ),
		);


		$this->taxonomies = get_option( 'nv_pro_' . Module::MODS_FEATURED_IMAGE_TAXONOMIES, $this->taxonomies );

		// register our meta
		register_meta(
			'term',
			$this->term_meta_key,
			array(
				'type'   => 'integer',
				'single' => true,
			)
		);

		foreach ( $this->taxonomies as $taxonomy ) {
			register_term_meta( $taxonomy, $this->term_meta_key, [ 'show_in_rest' => true ] );
		}

		// add our data when term is retrieved
		add_filter( 'get_term', array( $this, 'get_term' ) );
		add_filter( 'get_terms', array( $this, 'get_terms' ) );
		add_filter( 'get_object_terms', array( $this, 'get_terms' ) );

		// we only need to add most hooks on the admin side
		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'taxonomy_media_library_enqueue_scripts' ) );

			foreach ( $this->taxonomies as $taxonomy ) {
				// add our image field to the taxonomy term forms
				add_action( $taxonomy . '_add_form_fields', array( $this, 'taxonomy_add_form' ) );
				add_action( $taxonomy . '_edit_form_fields', array( $this, 'taxonomy_edit_form' ) );

				// hook into term administration actions
				add_action( 'create_' . $taxonomy, array( $this, 'taxonomy_term_form_save' ) );
				add_action( 'edit_' . $taxonomy, array( $this, 'taxonomy_term_form_save' ) );

				// custom admin taxonomy term list columns
				add_filter( 'manage_edit-' . $taxonomy . '_columns', array( $this, 'taxonomy_term_column_image' ) );
				add_filter( 'manage_' . $taxonomy . '_custom_column', array( $this, 'taxonomy_term_column_image_content' ), 10, 3 );
			}
		}
	}

	/**
	 * Add additional column for the taxonomy image.
	 *
	 * @param array $columns Existing taxonomy columns.
	 *
	 * @return array
	 */
	final public function taxonomy_term_column_image( $columns ) {
		$columns['term_image'] = __( 'Image', 'neve' );
		return $columns;
	}

	/**
	 * Retrieve the content for the image column.
	 *
	 * @param string  $content The content of the column.
	 * @param string  $column_name The column name.
	 * @param integer $term_id The term ID.
	 *
	 * @return string
	 */
	final public function taxonomy_term_column_image_content( $content, $column_name, $term_id ) {
		if ( 'term_image' == $column_name ) {
			$term = get_term( $term_id, '', 'ARRAY_A' );

			if ( is_wp_error( $term ) ) {
				return $content;
			}
			if ( $term[ $this->term_meta_key ] ) {
				$content = wp_get_attachment_image( $term[ $this->term_meta_key ], 'thumbnail', false, array( 'style' => 'width:100%; max-width:48px; height:auto;' ) );
			}
		}
		return $content;
	}

	/**
	 * Append image meta to taxonomy information.
	 *
	 * @param mixed $_term A WordPress term object.
	 *
	 * @return mixed
	 */
	final public function get_term( $_term ) {
		// only modify term when dealing with our taxonomies
		if ( is_object( $_term ) && in_array( $_term->taxonomy, $this->taxonomies ) ) {

			// default to null if not found
			$image_id          = get_term_meta( $_term->term_id, $this->term_meta_key, true );
			$_term->term_image = ! empty( $image_id ) ? $image_id : null;
		}
		return $_term;
	}

	/**
	 * Return all terms with additional image meta.
	 *
	 * @param array $terms A list of term objects.
	 *
	 * @return array
	 */
	final public function get_terms( $terms ) {
		foreach ( $terms as $i => $term ) {
			if ( is_object( $term ) && ! empty( $term->taxonomy ) ) {
				$terms[ $i ] = $this->get_term( $term );
			}
		}
		return $terms;
	}

	/**
	 * Enqueue media library for taxonomy script
	 */
	final public function taxonomy_media_library_enqueue_scripts() {
		$screen = get_current_screen();

		foreach ( $this->taxonomies as $taxonomy ) {
			if ( isset( $screen->id ) && $screen->id == 'edit-' . $taxonomy ) {
				$term = get_taxonomy( $taxonomy );
				// WP core stuff we need
				wp_enqueue_media();
				wp_enqueue_style( 'thickbox' );

				wp_register_script( 'nv-taxonomy-term-image-js', NEVE_PRO_INCLUDES_URL . 'modules/header_footer_grid/assets/js/taxonomy_image_library.js', array( 'jquery', 'thickbox', 'media-upload' ), NEVE_PRO_VERSION, true );
				$labels                     = $this->labels;
				$labels['fieldDescription'] = sprintf( $labels['fieldDescription'], $term->labels->singular_name );
				$labels['modalTitle']       = sprintf( $labels['modalTitle'], $term->labels->singular_name );
				wp_localize_script( 'nv-taxonomy-term-image-js', 'TaxonomyTermTranslations', $labels );
				wp_enqueue_script( 'nv-taxonomy-term-image-js' );
				break;
			}
		}
	}

	/**
	 * Return the image field for the provided image ID.
	 *
	 * @param null|integer $image_id The image ID.
	 * @param boolean      $echo Flag to specify if it should echo the $output.
	 *
	 * @return string|void
	 */
	final public function taxonomy_term_image_field( $image_id = null, $echo = true ) {
		$image_src = ( $image_id ) ? wp_get_attachment_image_src( $image_id, 'thumbnail' ) : array();

		wp_nonce_field( 'taxonomy-term-image-form-save', 'taxonomy-term-image-save-form-nonce' );

		$image = '';
		if ( isset( $image_src[0] ) ) {
			$image = '<img class="taxonomy-term-image-attach" src="' . esc_url( $image_src[0] ) . '" />';
		}

		$taxonomy                   = isset( $_GET['taxonomy'] ) ? sanitize_text_field( $_GET['taxonomy'] ) : '';
		$term                       = get_taxonomy( $taxonomy );
		$labels                     = $this->labels;
		$labels['fieldDescription'] = sprintf( $labels['fieldDescription'], $term->labels->singular_name );

		$output = '
		<input type="button" class="taxonomy-term-image-attach button" value="' . esc_attr( $labels['imageButton'] ) . '" />
		<input type="button" class="taxonomy-term-image-remove button" value="' . esc_attr( $labels['removeButton'] ) . '" />
		<input type="hidden" id="taxonomy-term-image-id" name="taxonomy_term_image" value="' . $image_id . '" />
		<div style="margin-top: 10px;" id="taxonomy-term-image-container">
			' . $image . '
		</div>
		<p class="description">' . $labels['fieldDescription'] . '</p>
		';

		if ( $echo ) {
			echo wp_kses( $output, $this->allowed_html );
			return;
		}
		return $output;
	}

	/**
	 * The taxonomy featured image add form template.
	 *
	 * @return void
	 */
	final public function taxonomy_add_form() {
		echo '
		<div class="form-field term-image-wrap">
			<label>' . esc_attr( $this->labels['fieldTitle'] ) . '</label>
			 ' . wp_kses( $this->taxonomy_term_image_field( null, false ), $this->allowed_html ) . '
		</div>
		';
	}

	/**
	 * The taxonomy featured image edit form template.
	 *
	 * @param mixed $term The WordPress term object.
	 *
	 * @return void
	 */
	final public function taxonomy_edit_form( $term ) {
		if ( ! isset( $term->term_image ) ) {
			$term = $this->get_term( $term );
		}

		echo '
		    <tr class="form-field">
		        <th scope="row" valign="top"><label>' . esc_attr( $this->labels['fieldTitle'] ) . '</label></th>
                <td class="taxonomy-term-image-row">
                    ' . wp_kses( $this->taxonomy_term_image_field( $term->term_image, false ), $this->allowed_html ) . '
                </td>
		    </tr>
		';
	}

	/**
	 * Save the  featured image meta for the provided term ID.
	 *
	 * @param integer $term_id The term ID.
	 *
	 * @return void
	 */
	final public function taxonomy_term_form_save( $term_id ) {
		// our requirements for saving:
		if (
			// nonce was submitted and is verified
			isset( $_POST['taxonomy-term-image-save-form-nonce'] ) &&
			wp_verify_nonce( sanitize_key( $_POST['taxonomy-term-image-save-form-nonce'] ), 'taxonomy-term-image-form-save' ) &&

			// taxonomy data and taxonomy_term_image data was submitted
			isset( $_POST['taxonomy'] ) &&
			isset( $_POST['taxonomy_term_image'] ) &&

			// the taxonomy submitted is one of the taxonomies we are dealing with
			in_array( $_POST['taxonomy'], $this->taxonomies )
		) {
			// get the term_meta and assign it the old_image
			$old_image = get_term_meta( $term_id, $this->term_meta_key, true );

			// sanitize the data and save it as the new_image
			$new_image = absint( $_POST['taxonomy_term_image'] );

			// if an image was removed, delete the meta data
			if ( $old_image && empty( $new_image ) ) {
				delete_term_meta( $term_id, $this->term_meta_key );
			} elseif ( $old_image !== $new_image ) { // if the new image is not the same as the old update the term_meta
				update_term_meta( $term_id, $this->term_meta_key, $new_image );
				if ( Loader::has_compatibility( 'track' ) ) {
					\Neve\Core\Tracker::track(
						array(
							array(
								'feature'          => 'taxonomy-featured-image',
								'featureComponent' => 'image-upload',
								'featureValue'     => true,
							),
						)
					);
				}
			}
		}
	}


	/**
	 * Cloning is forbidden.
	 *
	 * @access protected
	 * @since  3.0.0
	 */
	protected function __clone(){}

	/**
	 * Un-serializing instances of this class is forbidden.
	 *
	 * @access public
	 * @since  3.0.0
	 */
	public function __wakeup(){}
}
