<?php
/**
 * Payment Icons component class, Header Footer Grid Component.
 *
 * @package HFG
 */

namespace Neve_Pro\Modules\Header_Footer_Grid\Components;

use HFG\Core\Components\Abstract_Component;
use HFG\Core\Settings\Manager as SettingsManager;
use Neve\Core\Settings\Config;
use Neve\Core\Styles\Dynamic_Selector;
use Neve_Pro\Core\Loader;
use Neve_Pro\Core\Settings;

/**
 * Class Payment_Icons
 *
 * @package Neve_Pro\Modules\Header_Footer_Grid\Components
 */
class Payment_Icons extends Abstract_Component {
	const COMPONENT_ID     = 'payment_icons';
	const ITEM_ORDERING    = 'ordering_shortcut';
	const COLOR            = 'color';
	const BACKGROUND_COLOR = 'background_color';

	/**
	 * Should the Sparks plugin is needed notice or not.
	 *
	 * @var bool
	 */
	private $sparks_show_notice = false;

	/**
	 * Sparks notice type (Indicates the addressee of the alert.)
	 * Two different notices are shown (for admin and for other attendent users(shop_manager|author|editor))
	 *
	 * @var string (attendant_user|admin_user)
	 */
	private $sparks_notice_type = '';

	/**
	 * Check if component should be active.
	 *
	 * @return bool
	 */
	public function is_active() {
		if ( ! apply_filters( 'nv_pro_woocommerce_booster_status', false ) || ! class_exists( 'WooCommerce' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Payment icons component Constructor
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function init() {
		$this->set_property( 'label', __( 'Payment Icons', 'neve' ) );
		$this->set_property( 'id', $this->get_class_const( 'COMPONENT_ID' ) );
		$this->set_property( 'width', 3 );
		$this->set_property( 'section', 'hfg_payment_icons_component' );
		$this->set_property( 'icon', 'images-alt' );
		$this->set_property( 'default_selector', '.builder-item--' . $this->get_id() );

		$this->set_sparks_notice_props();
		$this->sparks_notice_style();
	}


	/**
	 * The customizer settings for this component are added in WooCommerce Booster module.
	 */
	public function add_settings() {
		$description = sprintf(
		/* translators: %s is link to section */
			esc_html__( 'Click %s to edit payment icons', 'neve' ),
			sprintf(
			/* translators: %s is link label */
				'<span class="quick-links"><a href="#" data-control-focus="neve_payment_icons_new">%s</a></span>',
				esc_html__( 'here', 'neve' )
			)
		);

		SettingsManager::get_instance()->add(
			[
				'id'                => self::ITEM_ORDERING,
				'group'             => $this->get_class_const( 'COMPONENT_ID' ),
				'tab'               => SettingsManager::TAB_LAYOUT,
				'transport'         => 'post' . $this->get_class_const( 'COMPONENT_ID' ),
				'sanitize_callback' => 'sanitize_text_field',
				'label'             => esc_html__( 'Edit Payment Icons', 'neve' ),
				'description'       => $description,
				'type'              => 'hidden',
				'options'           => [
					'priority' => 70,
				],
				'section'           => $this->section,
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                    => self::COLOR,
				'group'                 => $this->get_class_const( 'COMPONENT_ID' ),
				'tab'                   => SettingsManager::TAB_STYLE,
				'transport'             => 'postMessage',
				'sanitize_callback'     => 'neve_sanitize_colors',
				'label'                 => esc_html__( 'Color', 'neve' ),
				'type'                  => 'neve_color_control',
				'default'               => '#9b9b9b',
				'section'               => $this->section,
				'live_refresh_selector' => true,
				'live_refresh_css_prop' => [
					[
						'selector' => $this->default_selector . ' .nv-payment-icons-wrapper',
						'prop'     => 'fill',
						'fallback' => '#9b9b9b',
					],
				],
			]
		);
		SettingsManager::get_instance()->add(
			[
				'id'                    => self::BACKGROUND_COLOR,
				'group'                 => $this->get_class_const( 'COMPONENT_ID' ),
				'tab'                   => SettingsManager::TAB_STYLE,
				'transport'             => 'post' . $this->get_class_const( 'COMPONENT_ID' ),
				'sanitize_callback'     => 'neve_sanitize_colors',
				'label'                 => esc_html__( 'Background Color', 'neve' ),
				'type'                  => '\Neve\Customizer\Controls\React\Color',
				'section'               => $this->section,
				'default'               => '#e5e5e5',
				'options'               => [
					'input_attrs' => [
						'allow_gradient' => Loader::has_compatibility( 'gradient_picker' ),
					],
				],
				'live_refresh_selector' => true,
				'live_refresh_css_prop' => [
					[
						'selector' => $this->default_selector . ' .nv-payment-icon',
						'prop'     => 'background',
						'fallback' => '#e5e5e5',
					],
				],
			]
		);
	}

	/**
	 * Method to add Component css styles.
	 *
	 * @param array $css_array An array containing css rules.
	 *
	 * @return array
	 */
	public function add_style( array $css_array = array() ) {
		$rules = [
			'--color'   => [
				'key'     => $this->id . '_' . self::COLOR,
				'default' => '#9b9b9b',
			],
			'--bgcolor' => [
				'key'     => $this->id . '_' . self::BACKGROUND_COLOR,
				'default' => '#e5e5e5',
			],
		];

		$css_array[] = [
			'selectors' => '.builder-item--' . $this->get_id(),
			'rules'     => $rules,
		];

		return parent::add_style( $css_array );
	}

	/**
	 * Can be rendered? If the requirements met or not.
	 *
	 * @return bool
	 */
	private function allow_render(): bool {
		return ( $this->sparks_show_notice === false ) && function_exists( 'sparks' );
	}

	/**
	 * Render Payment Icons component.
	 *
	 * @return mixed|void
	 */
	public function render_component() {
		if ( $this->sparks_show_notice ) {
			$this->render_notice_sparks();
			return;
		}

		if ( ! $this->allow_render() ) {
			return;
		}

		echo \Neve_Pro\Modules\Woocommerce_Booster\Views\Payment_Icons::render_payment_icons(); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Set class props about Sparks notice is needed or not and notice type.
	 *
	 * @return void
	 */
	private function set_sparks_notice_props() {
		if ( function_exists( 'sparks' ) ) {
			return;
		}

		$roles = wp_get_current_user()->roles;

		// Early return if no roles applies
		$has_roles = count( array_intersect( [ 'administrator', 'shop_manager', 'author', 'editor' ], $roles ) ) > 0;
		if ( ! $has_roles ) {
			return; }

		$this->sparks_show_notice = true;

		if ( in_array( 'administrator', $roles, true ) ) {
			$this->sparks_notice_type = 'admin_user';
		}

		if ( in_array( 'shop_manager', $roles, true ) || in_array( 'author', $roles, true ) || in_array( 'editor', $roles, true ) ) {
			$this->sparks_notice_type = 'attendant_user';
		}
	}

	/**
	 * Render Sparks is needed notice
	 *
	 * @return void
	 */
	private function render_notice_sparks() {
		$page = Loader::has_compatibility( 'theme_dedicated_menu' ) ? 'admin.php' : 'themes.php';
		if ( $this->sparks_notice_type === 'admin_user' ) {
			printf(
				'%1$s <a target="_blank" href="%2$s">%3$s</a>',
				wp_kses( __( 'In order to use <strong>Payment Icons Component</strong>, you need to install <strong>Sparks for WooCommerce</strong> plugin.', 'neve' ), [ 'strong' => [] ] ),
				esc_html( admin_url( $page . '?page=neve-welcome' ) ),
				esc_html__( 'Install & Activate', 'neve' )
			);
		}

		if ( $this->sparks_notice_type === 'attendant_user' ) {
			echo wp_kses( __( 'In order to use <strong>Payment Icons Component</strong>, you need to install <strong>Sparks for WooCommerce</strong>. Please contact your website administrator.', 'neve' ), [ 'strong' => [] ] );
		}
	}

	/**
	 * If Sparks is missing; add inline style for add notice that is shown to admin users.
	 *
	 * @return void
	 */
	private function sparks_notice_style() {
		if ( $this->sparks_notice_type !== 'admin_user' ) {
			return;
		}

		add_action( 'wp_head', array( $this, 'inline_css_sparks_needed_notice' ) );
	}

	/**
	 * Add inline style to styling the admin notice about the Sparks is needed.
	 *
	 * @return void
	 */
	public function inline_css_sparks_needed_notice() {
		?>
		<style>
			.builder-item--payment_icons a {
				color: var(--nv-primary-accent);
				text-decoration: underline;
			}

			.builder-item--payment_icons a:hover {
				color: var(--nv-secondary-accent);
			}
		</style>
		<?php
	}
}
