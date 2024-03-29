<?php
/**
 * Advanced Search Core Component class
 *
 * @version 2.4.0
 * @package HFG
 */

namespace Neve_Pro\Modules\Header_Footer_Grid\Components;

use HFG\Core\Components\Abstract_Component;
use HFG\Core\Settings\Manager as SettingsManager;
use Neve_Pro\Traits\Sanitize_Functions;
use Neve_Pro\Modules\Post_Type_Enhancements\Module;
use Neve_Pro\Traits\Core;
use Neve\Core\Settings\Mods;
use Neve_Pro\Core\Loader;

/**
 * Class Advanced_Search
 *
 * @package Neve_Pro\Modules\Header_Footer_Grid\Components
 */
abstract class Advanced_Search_Core extends Abstract_Component {
	use Sanitize_Functions;
	use Core;

	const COMPONENT_ID         = 'advanced_search';
	const POST_TYPES           = 'post_types';
	const EXCLUDE_STICKY       = 'exclude_sticky';
	const SEARCH_POST_TITLE    = 'search_post_title';
	const SEARCH_POST_CONTENT  = 'search_post_content';
	const SEARCH_POST_EXCERPT  = 'search_post_excerpt';
	const SEARCH_CAT_TAG_TITLE = 'search_cat_tag_title';
	const SEARCH_CAT_TAG_DESC  = 'search_cat_tag_desc';

	// Common constants which used by Advanced_Search_Form and Advanced_Search_Icon
	const PLACEHOLDER_ID      = 'placeholder';
	const FIELD_HEIGHT        = 'field_height';
	const FIELD_FONT_SIZE     = 'field_text_size';
	const FIELD_BG            = 'field_background';
	const FIELD_TEXT_COLOR    = 'field_text_color';
	const FIELD_BORDER_WIDTH  = 'field_border_width';
	const FIELD_BORDER_RADIUS = 'field_border_radius';

	/**
	 * Holds the current instance count.
	 *
	 * @since   2.4.0
	 * @access  protected
	 * @var int
	 */
	protected $instance_number;

	/**
	 * Holds the component label.
	 *
	 * @since   2.4.0
	 * @access  protected
	 * @var string
	 */
	protected $label;

	/**
	 * Holds the component icon.
	 *
	 * @since   2.4.0
	 * @access  protected
	 * @var string
	 */
	protected $icon;

	/**
	 * The maximum allowed instances of this class.
	 * This refers to the global scope, across all builders.
	 *
	 * @since   2.4.0
	 * @access  protected
	 * @var int
	 */
	protected $max_instance = 2;

	/**
	 * Has support for the text based button?
	 *
	 * @var bool
	 */
	protected $has_textbutton_support = false;

	/**
	 * Instance of SearchIconButton which responsible adding&rendering icon&buttons to the search component.
	 *
	 * @var \HFG\Core\Components\Utility\SearchIconButton|null
	 */
	protected $search_icon_button_instance = null;

	/**
	 * Has support for customizable icon&button.
	 *
	 * @var bool
	 */
	protected static $customizable_iconbutton = false;

	/**
	 * Social_Icons constructor.
	 *
	 * @param string $panel Builder panel.
	 *
	 * @since   2.4.0
	 * @access  public
	 */
	public function __construct( $panel ) {
		parent::__construct( $panel );
		$this->set_property( 'section', $this->get_class_const( 'COMPONENT_ID' ) );

		if ( Loader::has_compatibility( 'hfg_d_search_iconbutton' ) ) {
			$this->search_icon_button_instance = new \HFG\Core\Components\Utility\SearchIconButton( $this->section, $this->get_id(), $this->has_textbutton_support );
			self::$customizable_iconbutton     = true;
		}
	}

	/**
	 * Get component label.
	 *
	 * @since   2.4.0
	 * @access  private
	 * @return string
	 */
	private function get_label() {
		if ( $this->instance_number > 1 ) {
			return $this->label . ' ' . ( $this->instance_number - 1 );
		}
		return $this->label;
	}

	/**
	 * Initialize.
	 *
	 * @since   2.4.0
	 * @access  public
	 */
	public function init() {
		$this->set_property( 'label', $this->get_label() );
		$this->set_property( 'id', $this->get_class_const( 'COMPONENT_ID' ) );
		$this->set_property( 'width', 2 );
		$this->set_property( 'icon', $this->icon );
		$this->set_property( 'component_slug', 'hfg-advanced-search' );
		$this->set_property( 'default_selector', '.builder-item--' . $this->get_id() );

		add_action(
			'init',
			function () {
				if ( ! isset( $_GET['form-instance'] ) ) {
					return;
				}
				add_filter( 'posts_distinct_request', [ $this, 'posts_distinct_request' ], PHP_INT_MAX, 2 );
				add_filter( 'posts_join', [ $this, 'posts_join' ], PHP_INT_MAX, 2 );
				add_filter( 'posts_search', [ $this, 'posts_search' ], PHP_INT_MAX, 2 );
				add_action( 'pre_get_posts', [ $this, 'add_search_options' ], PHP_INT_MAX, 1 );
			},
			PHP_INT_MAX
		);
	}

	/**
	 * Get advanced search post types.
	 *
	 * @param string $component_id Component instance id.
	 *
	 * @since   2.4.0
	 * @access  private
	 * @return mixed
	 */
	private function get_search_post_types( $component_id ) {
		$post_types = Mods::get( $component_id . '_' . self::POST_TYPES, wp_json_encode( [] ) );
		if ( is_string( $post_types ) ) {
			$post_types = json_decode( wp_unslash( $post_types ), true );
		}

		return $post_types;
	}

	/**
	 * Requests distinct results
	 *
	 * @param string    $distinct Distinct parameter.
	 * @param \WP_Query $query Query.
	 *
	 * @since   2.4.0
	 * @access  public
	 * @return string
	 */
	public function posts_distinct_request( $distinct, $query ) {
		if ( ( ! is_admin() || defined( 'DOING_AJAX' ) && DOING_AJAX === true ) && ! empty( $query->query_vars['s'] ) ) {
			return 'DISTINCT';
		}
		return $distinct;
	}

	/**
	 * Filters the JOIN clause of the query.
	 *
	 * @param string    $join JOIN clause.
	 * @param \WP_Query $query Query.
	 *
	 * @since   2.4.0
	 * @access  public
	 * @return mixed|void|null
	 */
	public function posts_join( $join, $query ) {
		global  $wpdb;

		if ( empty( $wpdb ) || ! isset( $query->query_vars ) ) { // @phpstan-ignore-line The query vars are set by WordPress core.
			return $join;
		}

		$q = $query->query_vars;
		if ( empty( $q['s'] ) || is_admin() && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX === true ) ) {
			return $join;
		}

		if ( ! isset( $_GET['form-instance'] ) ) {
			return $join;
		}

		$component_id = sanitize_text_field( $_GET['form-instance'] );
		if ( $component_id !== $this->id ) {
			return $join;
		}

		$search_cat_tag_title = Mods::get( $component_id . '_' . self::SEARCH_CAT_TAG_TITLE, false );
		$search_cat_tag_desc  = Mods::get( $component_id . '_' . self::SEARCH_CAT_TAG_DESC, false );

		$tt_table = $search_cat_tag_title || $search_cat_tag_desc;
		if ( $tt_table ) {
			$join .= " LEFT JOIN {$wpdb->term_relationships} AS tr ON ({$wpdb->posts}.ID = tr.object_id) ";
			$join .= " LEFT JOIN {$wpdb->term_taxonomy} AS tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id) ";
			$join .= " LEFT JOIN {$wpdb->terms} AS t ON (tt.term_id = t.term_id) ";
		}

		return apply_filters( 'is_posts_join', $join );
	}

	/**
	 * Verifies if the DB uses ICU REGEXP implementation.
	 *
	 * MySQL implements regular expression support using
	 * International Components for Unicode (ICU), which
	 * provides full Unicode support and is multibyte safe.
	 *
	 * (Prior to MySQL 8.0.4, MySQL used Henry Spencer's
	 * implementation of regular expressions, which operates
	 * in byte-wise fashion and is not multibyte safe.
	 *
	 * @since   2.4.0
	 * @access  public
	 * @return bool
	 */
	private function is_icu_regexp() {
		// return true;
		$is_icu_regexp = false;
		global  $wpdb;
		$db_version = $wpdb->db_version();

		if ( version_compare( $db_version, '8.0.4', '>=' ) ) {

			if ( empty( $wpdb->use_mysqli ) ) {
				// deprecated in php 7.0
				if ( ! function_exists( 'mysql_get_server_info' ) ) {
					return $is_icu_regexp;
				}
				/* @phpstan-ignore-next-line Call necessary to know how to alterate the query */
				$vesion_details = mysql_get_server_info(); // phpcs:ignore WordPress.DB.RestrictedFunctions.mysql_mysql_get_server_info, PHPCompatibility.Extensions.RemovedExtensions.mysql_DeprecatedRemoved
			} else {
				if ( ! function_exists( 'mysqli_get_server_info' ) ) {
					return $is_icu_regexp;
				}
				/* @phpstan-ignore-next-line Call necessary to know how to alterate the query */
				$vesion_details = mysqli_get_server_info( $wpdb->dbh ); // phpcs:ignore WordPress.DB.RestrictedFunctions.mysql_mysqli_get_server_info
			}

			// mariadb
			if ( stripos( $vesion_details, 'maria' ) !== false && version_compare( $db_version, '10.0.5', '<' ) ) {
				return $is_icu_regexp;
			}
			$is_icu_regexp = true;
		}

		return $is_icu_regexp;
	}

	/**
	 * Filters the search SQL that is used in the WHERE clause of WP_Query.
	 *
	 * @param string    $search WHERE clause.
	 * @param \WP_Query $wp_query Query.
	 *
	 * @since   2.4.0
	 * @access  public
	 * @return mixed|void|null
	 */
	public function posts_search( $search, $wp_query ) {

		if ( empty( $search ) ) {
			return $search;
		}

		if ( ! isset( $_GET['form-instance'] ) ) {
			return $search;
		}

		$component_id = sanitize_text_field( $_GET['form-instance'] );
		if ( $component_id !== $this->id ) {
			return $search;
		}

		$q = $wp_query->query_vars;
		if ( is_array( $q['search_terms'] ) && 1 == count( $q['search_terms'] ) ) {
			if ( 0 !== strpos( $q['s'], '"' ) ) {
				$q['search_terms'] = explode( ' ', $q['search_terms'][0] );
			}
		}

		$terms_relation_type = ( isset( $q['_is_settings']['term_rel'] ) && 'OR' === $q['_is_settings']['term_rel'] ? 'OR' : 'AND' );

		$search_title         = Mods::get( $component_id . '_' . self::SEARCH_POST_TITLE, true );
		$search_content       = Mods::get( $component_id . '_' . self::SEARCH_POST_CONTENT, true );
		$search_excerpt       = Mods::get( $component_id . '_' . self::SEARCH_POST_EXCERPT, true );
		$search_cat_tag_title = Mods::get( $component_id . '_' . self::SEARCH_CAT_TAG_TITLE, false );
		$search_cat_tag_desc  = Mods::get( $component_id . '_' . self::SEARCH_CAT_TAG_DESC, false );

		global $wpdb;
		$f = '([[:<:]])';
		$l = '([[:>:]])';
		if ( $this->is_icu_regexp() ) {
			$f = '\\b';
			$l = '\\b';
		}

		$searchand = '';
		$search    = ' AND ( ';
		$or        = '';

		foreach ( (array) $q['search_terms'] as $term2 ) {

			$term2 = str_replace( array( ']', ')' ), array( '', '' ), $term2 );
			$term2 = str_replace( array( '[', '(', ')' ), array( '[[]', '[(]', '[)]' ), $term2 );
			$term2 = str_replace( array( '{', '}' ), array( '', '' ), $term2 );
			$term  = $f . $wpdb->esc_like( $term2 ) . '|' . $wpdb->esc_like( $term2 ) . $l;

			$or      = '';
			$search .= "{$searchand} (";

			if ( $search_title ) {
				$search .= $wpdb->prepare( "({$wpdb->posts}.post_title REGEXP %s)", $term );
				$or      = ' OR ';
			}

			if ( $search_content ) {
				$search .= $or;
				$search .= $wpdb->prepare( "({$wpdb->posts}.post_content REGEXP %s)", $term );
				$or      = ' OR ';
			}

			if ( $search_excerpt ) {
				$search .= $or;
				$search .= $wpdb->prepare( "({$wpdb->posts}.post_excerpt REGEXP %s)", $term );
				$or      = ' OR ';
			}

			if ( $search_cat_tag_title || $search_cat_tag_desc ) {
				$tax_or  = '';
				$search .= $or;
				$search .= '( ';

				if ( $search_cat_tag_title ) {
					$search .= $wpdb->prepare( '(t.name REGEXP %s)', $term );
					$tax_or  = ' OR ';
				}


				if ( $search_cat_tag_desc ) {
					$search .= $tax_or;
					$search .= $wpdb->prepare( '(tt.description REGEXP %s)', $term );
				}

				$search .= ' )';
				$or      = ' OR ';
			}

			$search   .= ')';
			$searchand = " {$terms_relation_type} ";
		}

		if ( '' === $or ) {
			$search = ' AND ( 0 ';
		}

		$search  = apply_filters( 'is_posts_search_terms', $search, $q['search_terms'] );
		$search .= ')';
		return apply_filters( 'is_posts_search', $search );
	}

	/**
	 * Add the restrictions for search form.
	 *
	 * @param \WP_Query $query WP_Query object.
	 *
	 * @since   2.4.0
	 * @access  public
	 */
	public function add_search_options( $query ) {

		// Early exit if conditions are not met.
		if ( ! $query->is_main_query() ) {
			return;
		}
		if ( is_admin() ) {
			return;
		}
		if ( ! isset( $_GET['s'] ) ) {
			return;
		}
		if ( ! isset( $_GET['form-instance'] ) ) {
			return;
		}

		$component_id = sanitize_text_field( $_GET['form-instance'] );
		if ( $component_id !== $this->id ) {
			return;
		}

		// Filter by post type
		$post_types = $this->get_search_post_types( $component_id );
		if ( ! empty( $post_types ) ) {
			$query->set( 'post_type', $post_types );
		}

		// Exclude posts and taxonomies from search
		$exclude_posts = [];

		// Exclude sticky posts
		$exclude_sticky = Mods::get( $this->id . '_' . self::EXCLUDE_STICKY, false );
		if ( $exclude_sticky === true ) {
			$exclude_posts = array_merge( $exclude_posts, get_option( 'sticky_posts' ) );
		}

		if ( ! empty( $exclude_posts ) ) {
			$query->set( 'post__not_in', $exclude_posts );
		}

		$query->set(
			'post_status',
			array(
				'publish' => 'publish',
				'inherit' => 'inherit',
			) 
		);

		do_action( 'is_pre_get_posts', $query );
	}

	/**
	 * Called to register component controls.
	 *
	 * @since   2.4.0
	 * @access  public
	 */
	public function add_settings() {

		if ( ! is_customize_preview() ) {
			return;
		}

		SettingsManager::get_instance()->add(
			[
				'id'                 => self::EXCLUDE_STICKY,
				'group'              => $this->get_id(),
				'tab'                => SettingsManager::TAB_GENERAL,
				'transport'          => 'refresh',
				'sanitize_callback'  => 'absint',
				'default'            => false,
				'label'              => __( 'Exclude Sticky Posts', 'neve' ),
				'type'               => 'neve_toggle_control',
				'section'            => $this->section,
				'conditional_header' => true,
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                 => self::SEARCH_POST_TITLE,
				'group'              => $this->get_id(),
				'tab'                => SettingsManager::TAB_GENERAL,
				'transport'          => 'refresh',
				'sanitize_callback'  => 'absint',
				'default'            => true,
				'label'              => __( 'Search post title', 'neve' ),
				'type'               => 'neve_toggle_control',
				'section'            => $this->section,
				'conditional_header' => true,
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                 => self::SEARCH_POST_EXCERPT,
				'group'              => $this->get_id(),
				'tab'                => SettingsManager::TAB_GENERAL,
				'transport'          => 'refresh',
				'sanitize_callback'  => 'absint',
				'default'            => true,
				'label'              => __( 'Search post excerpt', 'neve' ),
				'type'               => 'neve_toggle_control',
				'section'            => $this->section,
				'conditional_header' => true,
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                 => self::SEARCH_POST_CONTENT,
				'group'              => $this->get_id(),
				'tab'                => SettingsManager::TAB_GENERAL,
				'transport'          => 'refresh',
				'sanitize_callback'  => 'absint',
				'default'            => true,
				'label'              => __( 'Search post content', 'neve' ),
				'type'               => 'neve_toggle_control',
				'section'            => $this->section,
				'conditional_header' => true,
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                 => self::SEARCH_CAT_TAG_TITLE,
				'group'              => $this->get_id(),
				'tab'                => SettingsManager::TAB_GENERAL,
				'transport'          => 'refresh',
				'sanitize_callback'  => 'absint',
				'default'            => false,
				'label'              => __( 'Search category/tag title', 'neve' ),
				'type'               => 'neve_toggle_control',
				'section'            => $this->section,
				'conditional_header' => true,
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                 => self::SEARCH_CAT_TAG_DESC,
				'group'              => $this->get_id(),
				'tab'                => SettingsManager::TAB_GENERAL,
				'transport'          => 'refresh',
				'sanitize_callback'  => 'absint',
				'default'            => false,
				'label'              => __( 'Search category/tag description', 'neve' ),
				'type'               => 'neve_toggle_control',
				'section'            => $this->section,
				'conditional_header' => true,
			]
		);

		$cpt_list = $this->get_available_post_types();
		SettingsManager::get_instance()->add(
			array(
				'id'                 => self::POST_TYPES,
				'group'              => $this->get_id(),
				'tab'                => SettingsManager::TAB_GENERAL,
				'transport'          => 'refresh',
				'sanitize_callback'  => [ $this, 'sanitize_posttype_array' ],
				'label'              => __( 'Search in', 'neve' ),
				'type'               => '\Neve\Customizer\Controls\React\Form_Token_Field',
				'default'            => wp_json_encode( [] ),
				'options'            => [
					'choices'     => $cpt_list,
					'description' => __( 'Leave empty to search in all post types', 'neve' ),
				],
				'section'            => $this->section,
				'conditional_header' => $this->get_builder_id() === 'header',
			)
		);
	}

	/**
	 * Function that returns the available post types for quick search.
	 *
	 * @return array
	 */
	private function get_available_post_types() {

		$cpt_list = [];

		$post_types = get_post_types( [ 'public' => true ], 'objects', 'and' );
		if ( empty( $post_types ) ) {
			return $cpt_list;
		}

		foreach ( $post_types as $post_type ) {
			$cpt_list[] = [
				'id'    => $post_type->name,
				'label' => $post_type->label,
			];
		}

		/**
		 * There are some plugins (eg. ACF Extended ) that are registering their custom post types later than the execution of this method.
		 * In order to get those too, we need to check the cached post type names that we collect for post types enhancements module.
		 */
		$cache = get_transient( Module::POST_TYPES_CACHE_KEY );
		if ( empty( $cache ) ) {
			return $cpt_list;
		}

		foreach ( $cache as $post_type ) {
			if ( in_array( $post_type, array_column( $cpt_list, 'id' ) ) ) {
				continue;
			}

			$cpt_list[] = [
				'id'    => $post_type,
				'label' => $this->get_post_type_label( $post_type ),
			];
		}

		return $cpt_list;
	}

	/**
	 * Get the post type label.
	 *
	 * This function is used to get a pretty name for cached post types. Since the post type is not yet register at this
	 * stage and we only store post type names, we need this function to generate a label based on the post type name.
	 *
	 * @param string $post_type Post type.
	 */
	private function get_post_type_label( $post_type ) {
		$label = str_replace( '_', ' ', $post_type );
		$label = str_replace( '-', ' ', $label );
		return ucfirst( $label );
	}

	/**
	 * Add input inside the HTML of search form to differentiate the instances.
	 *
	 * @param string $form Form HTML.
	 *
	 * @since   2.4.0
	 * @access  public
	 * @return string
	 */
	public function add_instance_id( $form ) {
		if ( isset( $_GET['form-instance'] ) ) {
			$form_ids = [ 'advanced_search_form_1', 'advanced_search_form_2', 'advanced_search_icon_1', 'advanced_search_icon_2' ];
			foreach ( $form_ids as $form_id ) {
				$form = str_replace( $form_id, esc_attr( $this->get_id() ), $form );
			}
			return $form;
		}
		$form = str_replace( '</label>', '</label><input type="hidden" name="form-instance" value="' . esc_attr( $this->get_id() ) . '">', $form );
		return str_replace( 'search-submit', 'search-submit nv-submit', $form );
	}

	/**
	 * The render method for the component.
	 *
	 * @since   2.4.0
	 * @access  public
	 */
	public function render_component() {
		add_filter( 'get_search_form', [ $this, 'add_instance_id' ] );
		$this->load_template();
		remove_filter( 'get_search_form', [ $this, 'add_instance_id' ] );
	}

	/**
	 * Load required template.
	 *
	 * @since   2.4.0
	 * @access  public
	 */
	abstract protected function load_template();

	/**
	 * Allow for constant changes in pro.
	 *
	 * @param string $const constant name.
	 *
	 * @since   2.4.0
	 * @access  protected
	 * @return mixed|string
	 */
	protected function get_class_const( $const ) {
		return constant( 'static::' . $const ) . '_' . $this->instance_number;
	}

	/**
	 * Method to filter component loading if needed.
	 *
	 * @since   2.4.0
	 * @access  public
	 * @return bool
	 */
	public function is_active() {
		if ( $this->max_instance < $this->instance_number ) {
			return false;
		}

		return parent::is_active();
	}

	/**
	 * Sanitize post types array.
	 *
	 * @param string|array $input Control input.
	 *
	 * @since   2.4.0
	 * @access  public
	 * @return string
	 */
	public function sanitize_posttype_array( $input ) {
		if ( empty( $input ) ) {
			return $input;
		}
		if ( ! is_array( $input ) ) {
			$input = json_decode( wp_unslash( $input ), true );
		}

		$valid_post_types = [];
		foreach ( $input as $post_type ) {
			if ( ! post_type_exists( $post_type ) ) {
				continue;
			}
			$valid_post_types[] = $post_type;
		}

		return wp_json_encode( $valid_post_types );
	}

	/**
	 * Method to add Component css styles.
	 *
	 * @param array $css_array An array containing css rules.
	 *
	 * @return array
	 * @since   1.0.0
	 * @access  public
	 */
	public function add_style( array $css_array = array() ) {
		if ( ! self::supports_customizable_iconbutton() ) {
			return parent::add_style( $css_array );
		}

		return parent::add_style( $this->search_icon_button_instance->get_style( $css_array ) );
	}

	/**
	 * Override parent::define_settings to add additional controls
	 *
	 * @return void
	 */
	public function define_settings() {
		parent::define_settings();

		if ( ! self::supports_customizable_iconbutton() ) {
			return;
		}

		$this->search_icon_button_instance->add_controls();
	}

	/**
	 * Supports customizable icon&button
	 *
	 * @return bool
	 */
	public static function supports_customizable_iconbutton() {
		return self::$customizable_iconbutton;
	}
}
