<?php
/**
 * Edit_Term
 *
 * @package Neve_Pro\Modules\Access_Restriction
 */
namespace Neve_Pro\Modules\Access_Restriction\Admin\Content_Restriction;

use Neve_Pro\Modules\Access_Restriction\Admin\Content_Restriction\Base;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Type\Term;
use Neve_Pro\Modules\Access_Restriction\General_Settings\Module_Settings;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Resource_Factory;
use Neve_Pro\Modules\Access_Restriction\Content_Resource\Settings\Resource_Settings;
use WP_Term;

/**
 * Class Edit_Term
 */
class Edit_Term extends Base {
	/**
	 * Get the term which is being edited.
	 *
	 * @return WP_Term | false
	 */
	protected function get_term() {
		$term_id = isset( $_REQUEST['tag_ID'] ) ? absint( $_REQUEST['tag_ID'] ) : 0;

		if ( ! $term_id ) {
			return false;
		}

		return get_term( $term_id );
	}

	/**
	 * Set the resource which is being edited.
	 *
	 * @return void
	 */
	protected function set_resource() {
		$term = $this->get_term();
		if ( ! $term ) {
			$this->resource = new Term();
			$taxonomy       = isset( $_REQUEST['taxonomy'] ) ? sanitize_text_field( $_REQUEST['taxonomy'] ) : '';
			$this->resource->set_taxonomy( $taxonomy );
			return;
		}

		$this->resource = ( new Resource_Factory() )->get_resource( $this->get_term() );
	}

	/**
	 * Define when the resource settings should be registered.
	 * For terms, 'init' is enough since the term id is getting from the 'tag_ID' query param.
	 *
	 * @return string
	 */
	protected function get_resource_settings_reg_hook() {
		return 'init';
	}

	/**
	 * Register the hooks for the render.
	 * That method is called only when the user is visiting the term edit screen.
	 *
	 * @return void
	 */
	protected function register_hooks() {
		parent::register_hooks();
		$this->register_render();
	}

	/**
	 * Checks if the current screen is the term edit screen.
	 *
	 * @return bool
	 */
	protected function is_the_current_screen() {
		global $pagenow;

		// edit-tags.php is the page for new terms
		return 'term.php' === $pagenow || 'edit-tags.php' === $pagenow;
	}

	/**
	 * Register the hooks for the background process.
	 * That are called even though user is not visiting the post edit screen.
	 *
	 * @return void
	 */
	protected function register_background_hooks() {
		add_filter( 'wp_update_term_data', [ $this, 'handle_update_term_data' ], 10, 4 );

		$enabled_taxonomies = ( new Module_Settings() )->get_enabled_taxonomies();
		foreach ( $enabled_taxonomies as $taxonomy ) {
			add_action( 'create_' . $taxonomy, array( $this, 'taxonomy_term_form_save' ) );
		}
	}

	/**
	 * Will handle the setting update.
	 *
	 * @param int   $term_id Term ID.
	 * @param array $args Arguments passed when updating the term.
	 *
	 * @return void
	 */
	private function update_settings_using_term_meta( $term_id, $args ) {
		$resource = ( new Resource_Factory() )->get_resource( get_term( $term_id ) );
		$settings = new Resource_Settings( $resource );

		$settings->update_restriction_types( json_decode( wp_unslash( $args['nv_ac_restriction_types'] ), true ) );

		if ( array_key_exists( 'nv_ac_users', $args ) ) {
			$settings->update_allowed_user_ids( explode( ',', $args['nv_ac_users'] ) );
		}

		if ( array_key_exists( 'nv_ac_user_roles', $args ) ) {
			$settings->update_allowed_user_roles( explode( ',', ( $args['nv_ac_user_roles'] ) ) );
		}

		if ( array_key_exists( 'nv_ac_password', $args ) ) {
			$settings->update_restriction_password( $args['nv_ac_password'] );
		}
	}

	/**
	 * Filters term data before it is updated in the database.
	 *
	 * @param array  $data Term data to be updated.
	 * @param int    $term_id Term ID.
	 * @param string $taxonomy Taxonomy slug.
	 * @param array  $args Arguments passed to wp_update_term().
	 *
	 * @return array
	 */
	public function handle_update_term_data( $data, $term_id, $taxonomy, $args ) {
		$supported_taxonomies = $this->module_settings->get_enabled_taxonomies();

		if ( ! in_array( $taxonomy, $supported_taxonomies, true ) ) {
			return $data;
		}

		if ( empty( $args ) ) {
			return $data;
		}

		$this->update_settings_using_term_meta( $term_id, $args );
		return $data;
	}

	/**
	 * Handler for the term meta saving.
	 * This method is called when the term is being saved.
	 *
	 * @param int    $term_id Term ID.
	 * @param  string $taxonomy Taxonomy slug.
	 * @param  array  $args Array of arguments.
	 * @return void
	 */
	public function handle_meta_saving( $term_id, $taxonomy, $args = [] ) {
		$supported_taxonomies = $this->module_settings->get_enabled_taxonomies();

		if ( ! in_array( $taxonomy, $supported_taxonomies, true ) ) {
			return;
		}

		if ( empty( $args ) ) {
			return;
		}

		$this->update_settings_using_term_meta( $term_id, $args );
	}

	/**
	 * Will forward the request to the save method if required fields are set.
	 *
	 * @param int $term_id The term ID.
	 *
	 * @return void
	 */
	public function taxonomy_term_form_save( $term_id ) {
        // phpcs:disable WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( isset( $_POST['taxonomy'] ) && isset( $_POST['nv_ac_restriction_types'] ) ) {
			$args = array(
				'nv_ac_restriction_types' => $_POST['nv_ac_restriction_types'],
			);
			if ( isset( $_POST['nv_ac_users'] ) ) {
				$args['nv_ac_users'] = $_POST['nv_ac_users'];
			}
			if ( isset( $_POST['nv_ac_user_roles'] ) ) {
				$args['nv_ac_user_roles'] = $_POST['nv_ac_user_roles'];
			}
			if ( isset( $_POST['nv_ac_password'] ) ) {
				$args['nv_ac_password'] = $_POST['nv_ac_password'];
			}
			$this->handle_meta_saving( $term_id, $_POST['taxonomy'], $args );
		}
        // phpcs:enable WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	}

	/**
	 * Init
	 */
	public function register_render() {
		$enabled_taxonomies = ( new Module_Settings() )->get_enabled_taxonomies();

		foreach ( $enabled_taxonomies as $taxonomy ) {
			add_action( $taxonomy . '_edit_form', [ $this, 'render' ], 10, 0 );
			add_action( $taxonomy . '_add_form_fields', [ $this, 'render' ], 11, 0 );
		}
	}

	/**
	 * Render the term restriction UI.
	 *
	 * @return void
	 */
	public function render() {
		?>
		<div id="neve-access-restriction-te"></div>
		<?php
	}

	/**
	 * Enqueue the scripts and styles for the term edit screen.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		$dependencies = include $this->assets_path . 'edit-term/index.asset.php';

		wp_register_script( 'neve-access-restriction-te', $this->assets_url . 'edit-term/index.js', $dependencies['dependencies'], $dependencies['version'], true );

		add_filter( 'nv_ar_edit_content_type_localize', [ $this, 'modify_localize_data' ] );
		wp_localize_script( 'neve-access-restriction-te', 'neveAccessRestriction', $this->get_localize_data() );
		wp_enqueue_script( 'neve-access-restriction-te' );

		wp_register_style( 'neve-access-restriction-te', $this->assets_url . 'edit-term/style-index.css', [ 'wp-components' ], $dependencies['version'] );
		wp_enqueue_style( 'neve-access-restriction-te' );
	}

	/**
	 * Get the data needed by the JS.
	 *
	 * @return array
	 */
	public function modify_localize_data( $data ) {
		$data['currentValues']['allowedPassword']  = $this->resource_settings->get_restriction_password();
		$data['currentValues']['restrictionTypes'] = $this->resource_settings->get_restriction_types();

		return $data;
	}
}
