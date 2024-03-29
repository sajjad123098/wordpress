<?php
/**
 * Inside Layout class to insert custom layouts inside content.
 *
 * @package Neve_Pro\Modules\Custom_Layouts\Admin
 */

namespace Neve_Pro\Modules\Custom_Layouts\Admin;

use Neve_Pro\Core\Abstract_Module;
use Neve_Pro\Modules\Custom_Layouts\Admin\Builders\Abstract_Builders;
use Neve_Pro\Traits\Conditional_Display;
use Neve_Pro\Traits\Core;

/**
 * Class Inside_Layout
 *
 * @package Neve_Pro\Modules\Custom_Layouts\Admin
 */
class Inside_Layout {
	use Core;
	use Conditional_Display;

	const AFTER_HEADINGS = 'after_x_headings';
	const AFTER_BLOCKS   = 'after_x_blocks';

	/**
	 * Keep track of core headings count.
	 *
	 * @var integer
	 */
	private static $headings_count = 1;

	/**
	 * Keep track of main level blocks count.
	 *
	 * @var int
	 */
	private static $blocks_count = 0;

	/**
	 * Holds an instance of this class.
	 *
	 * @var null|Inside_Layout
	 */
	private static $_instance = null;

	/**
	 * Inside display rules for each inside event.
	 * Holds the event type, event number and posts ids that are affected.
	 *
	 * @var array[]
	 */
	private static $inside_rules = [
		self::AFTER_HEADINGS => [],
		self::AFTER_BLOCKS   => [],
	];

	/**
	 * Holds the state of the layout blocks.
	 * If the display is in use, filters should skip as to not process layout blocks while rendering.
	 *
	 * @var bool
	 */
	private static $layout_blocks_in_progress = false;

	/**
	 * Return an instance of the class.
	 *
	 * @return Inside_Layout;
	 */
	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Add to the inside rules array.
	 *
	 * @param string $display A type of display ( after_x_headings | after_x_blocks ).
	 * @param int    $events_no Event occurrence before trigger.
	 * @param int    $post_id The post id of the custom template to display at that location.
	 */
	private function add_to_rules( $display, $events_no, $post_id ) {
		if ( ! isset( self::$inside_rules[ $display ][ $events_no ] ) ) {
			self::$inside_rules[ $display ][ $events_no ] = [];
		}
		array_push( self::$inside_rules[ $display ][ $events_no ], $post_id );
	}

	/**
	 * Set properties before render for the active custom layout.
	 *
	 * @param int $post_id The custom layout post ID.
	 */
	public function set_options( $post_id ) {
		$display   = get_post_meta( $post_id, 'custom-layout-options-inside-display', true );
		$events_no = get_post_meta( $post_id, 'custom-layout-options-events-no', true );
		$this->add_to_rules( $display, $events_no, $post_id );
	}

	/**
	 * Init main hook.
	 */
	public function init() {
		add_action( 'template_redirect', [ $this, 'register_hooks' ] );
	}

	/**
	 * Trigger `neve_do_inside_content` hook, and store the content.
	 */
	public function register_hooks() {

		$post_id = null;
		global $post;
		if ( isset( $post->ID ) ) {
			$post_id = (string) $post->ID;
		}

		$editor = Abstract_Builders::get_post_builder( (int) $post_id );


		if ( is_singular( 'neve_custom_layouts' ) && is_preview() ) {
			return;
		}
		do_action( 'neve_do_inside_content' );

		if ( $editor === 'default' ) {
			/**
			 * This method invoked here is shared from the Performance Module, it adds a new filter `render_block_top_level_only`
			 * that we can hook into for each block parsed. It is used to evaluate different blocks.
			 */
			add_filter( 'render_block_data', array( $this, 'process_content_blocks' ), -99, 3 );
			add_filter( 'render_block', array( $this, 'filter_block' ), 10, 2 );
			return;
		}

		// For other editors than Gutenberg parse the HTML content
		add_filter( 'the_content', array( $this, 'filter_content' ), 10 );
	}

	/**
	 * Return the results of the render as string.
	 *
	 * @return string
	 */
	private function get_inside_content( $post_ids ) {
		sort( $post_ids );
		ob_start();
		foreach ( $post_ids as $post_id ) {
			/**
			 * This hook triggers the render of the inside content custom layout template.
			 * Here we use it to capture the rendered content and return it as a string so that we can add it based on the
			 * selected display options inside the qualified posts.
			 *
			 * @since 3.0.5
			 */
			do_action( 'neve_render_inside_content_' . $post_id );
		}
		return ob_get_clean();
	}

	/**
	 * Return the builder blocks in HTML context.
	 *
	 * @param string $content The content.
	 *
	 * @return array Tuple containing the blocks and the content.
	 */
	private function get_builder_blocks( $content ) {
		/**
		 * Holds the classes that other builders might use to define a main block.
		 *
		 * @var array $top_level_classes
		 */
		$top_level_classes = array(
			'elementor-top-section',
			'et_pb_section',
			'fl-row',
		);

		preg_match_all( '/<([A-Z][A-Z0-9]*)\b.*class=".*(' . implode( '|', $top_level_classes ) . ')/iU', $content, $blocks, PREG_OFFSET_CAPTURE, 0 );

		return [ $blocks, $content ];
	}

	/**
	 * Method to count headings via HTML regex parse and insert content.
	 *
	 * @param string $content The post content.
	 *
	 * @return string
	 */
	private function count_headings_html( $content ) {
		if ( empty( self::$inside_rules[ self::AFTER_HEADINGS ] ) ) {
			return $content;
		}

		list( $blocks, $new_content ) = $this->get_builder_blocks( $content );

		if ( empty( $blocks[0] ) ) {
			return $content;
		}

		preg_match_all( '/<h\d(?:.*)>.*<\/h\d>/iU', $content, $headings, PREG_OFFSET_CAPTURE, 0 );
		foreach ( self::$inside_rules[ self::AFTER_HEADINGS ] as $event => $post_ids ) {
			$match_position = $event - 2;
			$layout_content = $this->get_inside_content( $post_ids );
			if ( $match_position < 0 ) {
				if ( isset( $blocks[0][0] ) ) {
					$block_start_content  = $blocks[0][0][0];
					$block_start_position = $blocks[0][0][1];
					$inside_content       = $layout_content . $block_start_content;
					$new_content          = substr_replace( $new_content, $inside_content, $block_start_position, strlen( $block_start_content ) );
				}
			}
			if ( ! empty( $headings[0] ) && isset( $headings[0][ $match_position ] ) ) {
				$heading_content  = $headings[0][ $match_position ][0];
				$heading_position = $headings[0][ $match_position ][1];
				$inside_content   = $heading_content . $layout_content;
				$new_content      = substr_replace( $new_content, $inside_content, $heading_position, strlen( $heading_content ) );
			}
		}

		return $new_content;
	}

	/**
	 * Method to count blocks via HTML regex parse and insert content.
	 *
	 * @param string $content The post content.
	 *
	 * @return string
	 */
	private function count_blocks_html( $content ) {
		if ( empty( self::$inside_rules[ self::AFTER_BLOCKS ] ) ) {
			return $content;
		}

		list( $blocks, $new_content ) = $this->get_builder_blocks( $content );

		if ( empty( $blocks[0] ) ) {
			return $content;
		}

		if ( in_array( count( $blocks[0] ), array_keys( self::$inside_rules[ self::AFTER_BLOCKS ] ) ) ) {
			$new_content .= $this->get_inside_content( self::$inside_rules[ self::AFTER_BLOCKS ][ count( $blocks[0] ) ] );
		}

		$offset = 0;
		foreach ( self::$inside_rules[ self::AFTER_BLOCKS ] as $event => $post_ids ) {
			$match_position = $event;

			if ( isset( $blocks[0][ $match_position ] ) ) {
				$block_start_content  = $blocks[0][ $match_position ][0];
				$block_start_position = $blocks[0][ $match_position ][1] + $offset;
				$layout_content       = $this->get_inside_content( $post_ids );
				$inside_content       = $layout_content . $block_start_content;
				$offset              += strlen( $layout_content );
				$new_content          = substr_replace( $new_content, $inside_content, $block_start_position, strlen( $block_start_content ) );
			}
		}
		return $new_content;
	}

	/**
	 * Add inside custom content. HTML parse.
	 *
	 * @param string $content The post content.
	 *
	 * @return string
	 */
	final public function filter_content( $content ) {
		if ( is_admin() || wp_is_json_request() ) {
			return $content;
		}

		if ( ! empty( self::$inside_rules[ self::AFTER_HEADINGS ] ) ) {
			$content = $this->count_headings_html( $content );
		}

		if ( ! empty( self::$inside_rules[ self::AFTER_BLOCKS ] ) ) {
			$content = $this->count_blocks_html( $content );
		}

		return $content;
	}

	/**
	 * Method to count headings and insert content.
	 *
	 * @param string $block_content The block content.
	 * @param array  $block The block data.
	 *
	 * @return string
	 */
	private function count_headings( $block_content, $block ) {
		if ( in_array( 1, array_keys( self::$inside_rules[ self::AFTER_HEADINGS ] ) ) && self::$headings_count === 1 ) {
			self::$headings_count++;
			self::$layout_blocks_in_progress = true;
			$new_block_content               = $this->get_inside_content( self::$inside_rules[ self::AFTER_HEADINGS ][1] ) . $block_content;
			self::$layout_blocks_in_progress = false;
			return $new_block_content;
		}

		if ( strpos( $block['blockName'], 'heading' ) !== false && self::$headings_count > 0 && in_array( self::$headings_count + 1, array_keys( self::$inside_rules[ self::AFTER_HEADINGS ] ) ) ) {
			$heading_position = strpos( $block_content, $block['innerHTML'] );
			if ( $heading_position !== false ) {
				self::$layout_blocks_in_progress = true;
				$new_content                     = $block['innerHTML'] . $this->get_inside_content( self::$inside_rules[ self::AFTER_HEADINGS ][ self::$headings_count + 1 ] );
				$block_content                   = substr_replace( $block_content, $new_content, $heading_position, strlen( $block['innerHTML'] ) );
				self::$headings_count++;
				self::$layout_blocks_in_progress = false;
			}

			return $block_content;
		}

		if ( strpos( $block['blockName'], 'heading' ) !== false ) {
			self::$headings_count++;
		}

		if ( ! empty( $block['innerBlocks'] ) ) {
			foreach ( $block['innerBlocks'] as $index => $inner_block ) {
				$block_content = $this->count_headings( $block_content, $inner_block );
			}
		}

		return $block_content;
	}

	/**
	 * Method to count main blocks and insert content.
	 *
	 * @param string $block_content The block content.
	 * @param array  $block The block data.
	 *
	 * @return string
	 */
	private function count_blocks( $block_content, $block ) {
		if ( self::$layout_blocks_in_progress === true ) {
			return $block_content;
		}
		if ( $block['blockName'] && isset( $block['isTopLevelBlock'] ) && $block['isTopLevelBlock'] ) {
			self::$blocks_count++;
		}

		if ( $block['blockName'] && in_array( self::$blocks_count, array_keys( self::$inside_rules[ self::AFTER_BLOCKS ] ) ) ) {
			self::$layout_blocks_in_progress = true;
			$new_block_content               = $block_content . $this->get_inside_content( self::$inside_rules[ self::AFTER_BLOCKS ][ self::$blocks_count ] );
			self::$layout_blocks_in_progress = false;
			return $new_block_content;
		}

		return $block_content;
	}

	/**
	 * Add inside custom content.
	 *
	 * @param string $block_content The block content.
	 * @param array  $block The block data.
	 *
	 * @return string
	 */
	final public function filter_block( $block_content, $block ) {
		if ( is_admin() || wp_is_json_request() ) {
			return $block_content;
		}
		if ( ! isset( $block[ Abstract_Module::TOP_LEVEL_BLOCK_FLAG ] ) ) {
			return $block_content;
		}
		if ( ! empty( self::$inside_rules[ self::AFTER_HEADINGS ] ) ) {
			$block_content = $this->count_headings( $block_content, $block );
		}
		if ( ! empty( self::$inside_rules[ self::AFTER_BLOCKS ] ) ) {
			$block_content = $this->count_blocks( $block_content, $block );
		}

		return $block_content;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @access public
	 * @since  3.0.5
	 */
	public function __clone() {}

	/**
	 * Un-serializing instances of this class is forbidden.
	 *
	 * @access public
	 * @since  3.0.5
	 */
	public function __wakeup() {}
}
