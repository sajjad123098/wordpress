<?php
/**
 * Author:          Andrei Baicus <andrei@themeisle.com>
 * Created on:      2019-02-11
 *
 * @package Neve Pro Addon
 */

namespace Neve_Pro\Modules\Woocommerce_Booster\Views;

use Neve\Compatibility\Elementor;
use Neve\Views\Base_View;
use WC_Payment_Gateways;

/**
 * Class Checkout_Page
 *
 * @package Neve_Pro\Modules\Woocommerce_Booster\Views
 */
class Checkout_Page extends Base_View {

	/**
	 * Check if submodule should be loaded.
	 *
	 * @return bool
	 */
	private function should_load() {
		if ( ! class_exists( 'Woocommerce' ) ) {
			return false;
		}

		if ( ! is_checkout() ) {
			return false;
		}

		if ( function_exists( 'WC' ) ) {
			// Prevent doing any modifications on the checkout page if the payment method is Klarna.
			$payment_method = WC()->session->get( 'chosen_payment_method' );
			if ( ! $payment_method && class_exists( 'WC_Payment_Gateways' ) ) {
				// If payment method is null, see if Klarna is the only option.
				$payment_gateways          = new WC_Payment_Gateways();
				$available_payment_methods = $payment_gateways->get_available_payment_gateways();
				if ( is_array( $available_payment_methods ) && count( $available_payment_methods ) === 1 && isset( $available_payment_methods['kco'] ) ) {
					return false;
				}
			}
			if ( $payment_method === 'kco' ) {
				return false;
			}
		}

		/**
		 * Prevent any modifications to the checkout page if the page is built with Elementor Checkout widget.
		 */
		if ( class_exists( '\Neve\Compatibility\Elementor', false ) && method_exists( '\Neve\Compatibility\Elementor', 'is_elementor_checkout' ) && Elementor::is_elementor_checkout() ) {
			return false;
		}

		return true;
	}

	/**
	 * Initialize the module.
	 */
	public function init() {
		add_action( 'wp', array( $this, 'run' ) );
	}

	/**
	 * Register submodule hooks
	 */
	public function register_hooks() {
		$this->init();
	}

	/**
	 * Run the module.
	 */
	public function run() {
		if ( ! $this->should_load() ) {
			return;
		}

		$this->page_layout();
		$this->boxed_style();
		$this->fixed_order_box();
		$this->labels_as_placeholders();
		$this->toggle_order_note();
		$this->toggle_coupon();
	}

	/**
	 * Checkout page layout.
	 *
	 * @return void
	 */
	private function page_layout() {
		$page_layout = get_theme_mod( 'neve_checkout_page_layout', 'standard' );

		add_filter(
			'body_class',
			function ( $classes ) use ( $page_layout ) {
				$classes[] = 'nv-checkout-layout-' . esc_attr( $page_layout );

				return $classes;
			}
		);

		if ( $page_layout !== 'stepped' ) {
			return;
		}
		add_action( 'woocommerce_before_checkout_form', array( $this, 'render_checkout_steps' ) );
		add_action(
			'woocommerce_checkout_after_customer_details',
			array(
				$this,
				'render_billing_next_step_button',
			),
			100
		);
		add_action( 'woocommerce_checkout_order_review', array( $this, 'render_review_next_step_button' ), 30 );
	}

	/**
	 * Render the checkout steps for the stepped layout.
	 */
	public function render_checkout_steps() {
		if ( ! class_exists( 'WooCommerce' ) || ! class_exists( 'WC_Checkout' ) ) {
			return;
		}

		$checkout = WC()->checkout();
		// If checkout registration is disabled and not logged in, the user cannot checkout.
		if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
			return;
		}

		$steps = array(
			'billing' => get_theme_mod( 'neve_checkout_step1_label', __( 'Billing and shipping', 'neve' ) ),
			'review'  => get_theme_mod( 'neve_checkout_step2_label', __( 'Order review', 'neve' ) ),
			'payment' => get_theme_mod( 'neve_checkout_step3_label', __( 'Payment', 'neve' ) ),
		);

		echo '<nav class="nv-checkout-steps-wrapper">';
		$index = 1;
		foreach ( $steps as $step_id => $step_label ) {
			$active = $step_id === 'billing' ? 'active' : '';
			echo '<div data-step="' . esc_attr( $step_id ) . '" class="nv-checkout-step step-' . esc_attr( $step_id ) . ' ' . esc_attr( $active ) . '">';
			echo '<span class="step-number-label">' . esc_html( (string) $index ) . '</span>';
			echo '<span class="step-label">' . wp_kses_post( $step_label ) . '</span>';
			echo '</div>';
			$index ++;
		}
		echo '</nav>';
	}

	/**
	 * Renders the next step button for billing step.
	 */
	public function render_billing_next_step_button() {
		$label = get_theme_mod( 'neve_checkout_step2_label', __( 'Order review', 'neve' ) );
		echo '
		<div class="next-step-button-wrapper billing">
		<a data-step="review" class="button button-primary">' . wp_kses_post( $label ) . '</a>
		</div>';
	}

	/**
	 * Renders the next step button for review step
	 */
	public function render_review_next_step_button() {
		$label = get_theme_mod( 'neve_checkout_step3_label', __( 'Payment', 'neve' ) );
		echo '
		<div class="next-step-button-wrapper review">
		<a data-step="payment" class="button button-primary">' . wp_kses_post( $label ) . '</a>
		</div>';
	}

	/**
	 * Add the boxed style class on body.
	 *
	 * @return bool
	 */
	private function boxed_style() {
		$boxed_style = get_theme_mod( 'neve_checkout_boxed_layout', \Neve_Pro\Modules\Woocommerce_Booster\Customizer\Checkout_Page::get_checkout_boxed_layout_default() );
		if ( ! $boxed_style ) {
			return false;
		}

		add_filter(
			'body_class',
			function ( $classes ) {
				$classes[] = 'nv-checkout-boxed-style';

				return $classes;
			}
		);

		return true;
	}

	/**
	 * Handle the fixed order box
	 *
	 * @return bool
	 */
	private function fixed_order_box() {
		$page_layout = get_theme_mod( 'neve_checkout_page_layout', 'standard' );
		if ( $page_layout !== 'standard' ) {
			return false;
		}

		$fixed_order_box = get_theme_mod( 'neve_enable_checkout_fixed_order', false );
		if ( $fixed_order_box === false ) {
			return false;
		}

		add_filter(
			'body_class',
			function ( $classes ) {
				$classes[] = 'nv-checkout-fixed-total';

				return $classes;
			}
		);
		// Move payment to left column.
		remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );
		add_action( 'woocommerce_checkout_shipping', 'woocommerce_checkout_payment', 100 );

		return true;
	}

	/**
	 * Use labels as placeholders.
	 */
	private function labels_as_placeholders() {
		$placeholder_labels = get_theme_mod( 'neve_checkout_labels_placeholders', false );

		if ( $placeholder_labels === false ) {
			return;
		}

		add_filter(
			'body_class',
			function ( $classes ) {
				$classes[] = 'nv-checkout-labels-placeholders';

				return $classes;
			}
		);
		add_filter(
			'woocommerce_form_field_args',
			function ( $args, $key, $value ) {
				if ( ! isset( $args['label'] ) ) {
					return $args;
				}
				$add_asterisk        = get_option( 'woocommerce_checkout_highlight_required_fields', 'yes' );
				$required            = ( $args['required'] === true && $add_asterisk === 'yes' ) ? ' *' : '';
				$args['placeholder'] = esc_html( $args['label'] . $required );

				return $args;
			},
			0,
			3
		);
	}

	/**
	 * Toggle order note.
	 */
	private function toggle_order_note() {
		$order_note = get_theme_mod( 'neve_enable_checkout_order_note', true );
		if ( $order_note === true ) {
			return;
		}

		add_filter( 'woocommerce_enable_order_notes_field', '__return_false' );
	}

	/**
	 * Toggle checkout coupon.
	 */
	private function toggle_coupon() {
		$coupon = get_theme_mod( 'neve_enable_checkout_coupon', true );
		if ( $coupon === true ) {
			return;
		}

		remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
	}
}
