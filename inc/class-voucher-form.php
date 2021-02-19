<?php

namespace Dentonet\WP;

use Dentonet\WP\Component_Interface;
use Dentonet\WP\Voucher;

defined( 'ABSPATH' ) || exit;

class Voucher_Form implements Component_Interface {

	/**
	 * Add all hooks and filters to WordPress.
	 */
	public function initialize() {
		add_filter( 'template_include', array( $this, 'filter_add_voucher_template' ) );
		add_action( 'wp_ajax_woo_voucher', array( $this, 'action_ajax_validate_voucher' ) );
		add_action( 'wp_ajax_nopriv_woo_voucher', array( $this, 'action_ajax_validate_voucher' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'action_ajax_add_global_variables' ) );
	}

	/**
	 * Ajax call handler. Read and validate voucher, then maybe create new user and order.
	 */
	public function action_ajax_validate_voucher() {
		if (
		! isset( $_POST['_voucher_nonce'] )
		|| ! wp_verify_nonce( $_POST['_voucher_nonce'], 'claim-voucher' ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		) {
			wp_nonce_ays( 'wrong-nonce' );
		}

		$voucher_key = isset( $_POST['voucher-key'] ) ? $_POST['voucher-key'] : '';

		$voucher = Voucher::get_voucher( strtoupper( $voucher_key ) );

		if ( ! $voucher ) {
			wp_send_json_error( 'no voucher in database' );
		}
		if ( $voucher->is_used() ) {
			wp_send_json_error( 'voucher was used' );
		}

		$email = ! empty( $_POST['user']['email'] ) ? sanitize_email( wp_unslash( $_POST['user']['email'] ) ) : '';
		$username = ! empty( $_POST['user']['name'] && $_POST['user']['surname'] ) ? sanitize_user( wp_unslash( $_POST['user']['name'] . ' ' . $_POST['user']['surname'] ) ) : '';
		$password = ! empty( $_POST['user']['password'] ) ? $_POST['user']['password'] : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput

		$customer_id = wc_create_new_customer( $email, $username, $password );
		if ( is_wp_error( $customer_id ) ) {
			wp_send_json_error( $customer_id->get_error_message() );
		}

		// Setup new order.
		$new_order_data = array(
			'status'        => 'completed',
			'customer_id'   => $customer_id,
			'parent'        => $voucher->get_parent_order_id(),
			'created_via'   => 'voucher',
		);

		$order = wc_create_order( $new_order_data );
		$order->add_meta_data( 'voucher_key', $voucher->get_voucher_code(), true );

		$product = wc_get_product( wc_get_product_id_by_sku( $voucher->get_voucher_type() ) );
		$order->add_product( $product, 1 );

		// Create 100% discount.
		$subtotal = $order->get_subtotal();
		$discount = new \WC_Order_Item_Fee();
		$discount->set_name( 'Voucher' );
		$discount->set_amount( -$subtotal );
		$discount->set_total( -$subtotal );
		$discount->save();
		$order->add_item( $discount );
		$order->calculate_totals();
		$order->save();

		// Finally pair user and order with voucher.
		$voucher->add_user_to_voucher( $customer_id, $order->get_id() );

		wp_send_json_success( array( 'return_url' => home_url() ) );
	}

	/**
	 * Set global js variable with ajax url.
	 */
	public function action_ajax_add_global_variables() {
		wp_register_script( 'woo-voucher', plugin_dir_url( __DIR__ ) . 'assets/voucher.js', array(), '', true );

		$vars = array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		);
		if ( is_page( 'voucher' ) ) {
			wp_enqueue_script( 'woo-voucher' );
			wp_localize_script( 'woo-voucher', 'wooVoucher', $vars );
		}
	}

	public function filter_add_voucher_template( $template ) {
		if ( is_page( 'voucher' ) ) {
			return $this->get_voucher_form();
		}

		return $template;
	}

	/**
	 * Retrieve html template.
	 */
	public function get_voucher_form() {
		include __DIR__ . '/templates/voucher-template.php';
	}
}
