<?php
/**
 * Html Component Wrapper class extends Header Footer Grid Component.
 *
 * Name:    Header Footer Grid
 * Author:  Bogdan Preda <bogdan.preda@themeisle.com>
 *
 * @version 1.0.0
 * @package HFG
 */

namespace Neve_Pro\Modules\Header_Footer_Grid\Components;

use HFG\Main;

/**
 * Class Html
 *
 * @package Neve_Pro\Modules\Header_Footer_Grid\Components
 */
class Html_Page extends Html {
	/**
	 * Holds the instance count.
	 * Starts at 1 since the base component is not altered.
	 *
	 * @since   1.0.0
	 * @access  protected
	 * @var int
	 */
	protected static $instance_count = 3;

	/**
	 * The supported magic tags and the callback function.
	 *
	 * @since   1.0.0
	 * @access  protected
	 * @var array
	 */
	protected $magic_tags = array();

	/**
	 * Html constructor.
	 *
	 * @param string $panel Builder panel.
	 */
	public function __construct( $panel ) {
		self::$instance_count ++;
		$this->instance_number = self::$instance_count;
		parent::__construct( $panel );
	}

	/**
	 * Html init.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function init() {
		parent::init();

		$this->magic_tags = array(
			'title'  => array( $this, 'replace_title' ),
			'date'   => array( $this, 'replace_date' ),
			'author' => array( $this, 'replace_author' ),
		);

		add_filter( 'neve_page_header_content', array( $this, 'filter_content_magic_tags' ) );
		add_filter( 'neve_page_header_content', 'do_shortcode' );
	}

	/**
	 * Function to replace the title magic tag.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @return string
	 */
	public function replace_title() {

		// {title} tag is in content => hide title on page
		add_filter(
			'neve_filter_toggle_content_parts',
			function ( $status, $context ) {
				if ( $context === 'title' ) {
					return false;
				}
				return true;
			},
			101,
			2
		);

		// This way we can show and hide the title on live preview
		$css = '<style>.nv-page-title-wrap .nv-page-title{ display: none }</style>';
		if ( is_customize_preview() ) {
			echo $css; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		if ( is_home() && get_option( 'show_on_front' ) === 'posts' ) {
			return '';
		}

		if ( get_option( 'show_on_front' ) === 'page' && is_home() ) {
			$blog_page_id = get_option( 'page_for_posts' );
			return get_the_title( $blog_page_id );
		}

		if ( is_archive() ) {
			return get_the_archive_title();
		}

		return get_the_title();
	}

	/**
	 * Function to replace the date magic tag.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @return false|string
	 */
	public function replace_date() {
		return get_the_date( 'F j, Y' );
	}

	/**
	 * Function to replace the author magic tag.
	 *
	 * @since   1.0.0
	 * @access  public
	 * @return string
	 */
	public function replace_author() {
		return get_the_author_meta( 'display_name', (int) get_post_field( 'post_author', get_the_ID() ) );
	}

	/**
	 * Filter the custom html content.
	 *
	 * @since   1.0.0
	 * @access  public
	 *
	 * @param string $content The content.
	 *
	 * @return mixed
	 */
	public function filter_content_magic_tags( $content ) {
		if ( empty( $this->magic_tags ) ) {
			return $content;
		}

		foreach ( $this->magic_tags as $tag => $function ) {
			$is_tag_in_content = strstr( $content, '{' . $tag . '}' ) !== false;
			if ( ! $is_tag_in_content ) {
				continue;
			}

			$value = call_user_func( $function );

			if ( $tag === 'author' || $tag === 'title' ) {
				$value = html_entity_decode( $value );
			}
			$content = str_replace( '{' . $tag . '}', wp_kses_post( $value ), $content );
		}

		return $content;
	}

	/**
	 * The render method for the component.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function render_component() {
		Main::get_instance()->load( 'components/page-header-html' );
	}
}
