<?php
/**
 * Wish List Component class for Header Footer Grid.
 *
 * @since 1.2.8
 * @package HFG
 */

namespace Neve_Pro\Modules\Header_Footer_Grid\Components;

use HFG\Core\Settings\Manager as SettingsManager;
use HFG\Core\Components\Abstract_Component;
use HFG\Main;
use Neve_Pro\Admin\Custom_Layouts_Cpt;

/**
 * Class Wish_List
 */
class Custom_Layout extends Abstract_Component {
	/**
	 * Holds the instance count.
	 * Starts at 1 since the base component is not altered.
	 *
	 * @since   1.2.8
	 * @access  protected
	 * @var int
	 */
	protected static $instance_count = 1;
	/**
	 * Holds the current instance count.
	 *
	 * @since   1.2.8
	 * @access  protected
	 * @var int
	 */
	protected $instance_number;

	/**
	 * The maximum allowed instances of this class.
	 * This refers to the global scope, across all builders.
	 *
	 * @since   1.2.8
	 * @access  protected
	 * @var int
	 */
	protected $max_instance = 10;

	const COMPONENT_ID = 'custom_layout';
	const POST_ID      = 'post_id';

	/**
	 * Custom_Layout constructor.
	 *
	 * @param string $panel Builder panel.
	 */
	public function __construct( $panel ) {
		self::$instance_count ++;
		$this->instance_number = self::$instance_count;
		parent::__construct( $panel );
		$this->set_property( 'section', 'custom_layout_' . $this->instance_number );
	}

	/**
	 * Wish List constructor.
	 *
	 * @since   1.2.8
	 * @access  public
	 */
	public function init() {
		$label = __( 'Custom Layout', 'neve' );
		if ( $this->instance_number > 1 ) {
			$label .= ' ' . ( $this->instance_number - 1 );
		}
		$this->set_property( 'label', $label );
		$this->set_property( 'id', $this->get_class_const( 'COMPONENT_ID' ) );
		$this->set_property( 'width', 3 );
		$this->set_property( 'icon', 'embed-generic' );
		$this->set_property( 'component_slug', 'hfg-custom-layout' );
		$this->set_property( 'default_selector', '.builder-item--' . $this->get_id() );
	}

	/**
	 * Called to register component controls.
	 *
	 * @since   1.2.8
	 * @access  public
	 */
	public function add_settings() {
		SettingsManager::get_instance()->add(
			[
				'id'                 => self::POST_ID,
				'group'              => $this->get_id(),
				'tab'                => SettingsManager::TAB_GENERAL,
				'transport'          => 'refresh',
				'sanitize_callback'  => 'sanitize_text_field',
				'label'              => __( 'Layout to display', 'neve' ),
				'type'               => 'select',
				'section'            => $this->section,
				'conditional_header' => $this->get_builder_id() === 'header',
				'default'            => 'none',
				'options'            => [
					'choices' => $this->get_custom_layouts_choices(),
				],
			]
		);
	}

	/**
	 * Get custom layouts.
	 */
	private function get_custom_layouts_choices() {
		$layouts = Custom_Layouts_Cpt::get_custom_layouts();
		$parsed  = [ 'none' => __( 'None', 'neve' ) ];

		if ( ! isset( $layouts['individual'] ) ) {
			return $parsed;
		}

		foreach ( $layouts['individual'] as $id => $rules_priority ) {
			$title = get_the_title( $id );
			if ( empty( $title ) ) {
				/* translators: %d - Post ID (number) */
				$title = sprintf( esc_html__( 'Custom layout %d (No title)', 'neve' ), $id );
			}
			$parsed[ $id ] = $title;
		}

		return $parsed;
	}


	/**
	 * The render method for the component.
	 *
	 * @since   1.2.8
	 * @access  public
	 */
	public function render_component() {
		Main::get_instance()->load( 'component-custom-layout' );
	}

	/**
	 * Method to filter component loading if needed.
	 *
	 * @return bool
	 * @since   1.0.1
	 * @access public
	 */
	public function is_active() {
		if ( $this->max_instance < $this->instance_number ) {
			return false;
		}
		return parent::is_active();
	}

	/**
	 * Allow for constant changes in pro.
	 *
	 * @param string $const Name of the constant.
	 *
	 * @return mixed
	 * @since   1.2.8
	 * @access  protected
	 */
	protected function get_class_const( $const ) {
		return constant( 'static::' . $const ) . '_' . $this->instance_number;
	}
}
