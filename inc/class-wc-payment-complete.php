<?php

namespace Dentonet\WP;

use Dentonet\WP\Component_Interface;

defined( 'ABSPATH' ) || exit;

class WC_Payment_Complete implements Component_Interface {

	public function initialize() {
		add_action( 'woocommerce_payment_complete', array( $this, 'action_on_payment_complete' ) );
		add_action( 'woocommerce_voucher_payment_complete', array( $this, 'action_create_vouchers_form_order' ) );
	}

	public function action_create_vouchers_form_order( int $order_id ) {
		// Get variables from order.
		$order = wc_get_order( $order_id );
		$voucher_type = '';
		$count = 0;

		$items = $order->get_items();

		foreach ( $items as $item ) {

			$item_sku = $item->get_product()->get_sku();

			// Set correct voucher ticket SKU for new order.
			switch ( $item_sku ) {
				case 'bilet-grupowy-premium':
					$voucher_type = 'bilet-premium';
					break;
				case 'bilet-grupowy-standard':
					$voucher_type = 'bilet-standard';
					break;
			}
			// Prepare the amount of tickets to generate.
			$count = $item->get_quantity();
		}

		$voucher_factory = new Voucher_Manager();
		$voucher_factory->set_voucher_type( $voucher_type );
		$voucher_factory->set_parent_order_id( $order_id );

		for ( $i = 0; $i < $count; $i++ ) {
			$voucher_factory->add_voucher();
		}

		$voucher_factory->save();
	}

	/**
	 * When payment for voucher is marked complete, run this hook.
	 * It calls creation of separate transactions for new users
	 * and generates codes to claim singular ticket.
	 *
	 * @param  int $order_id ID of order marked completed (or processing).
	 */
	public function action_on_payment_complete( int $order_id ) {
		// Firsty focus on checking wheter bought item is instance of voucher.
		if ( $this->is_voucher_active( $order_id ) ) {
			do_action( 'woocommerce_voucher_payment_complete', $order_id );
		}
	}

	/**
	 * Check whether current product marked complete is voucher.
	 *
	 * @param  int $order_id ID of current order.
	 * @return bool true if product has set _voucher meta.
	 */
	private function is_voucher_active( int $order_id ) : bool {
		$order = wc_get_order( $order_id );
		$order_items = $order->get_items();

		// Usually there will be only one item in order.
		// For now skip checking the array.
		foreach ( $order_items as $item ) {

			$product = $item->get_product();

			// Return if not product's variant.
			if ( ! wc_get_product( $product->get_parent_id() ) ) {
				return false;
			}

			$product_parent = wc_get_product( $product->get_parent_id() );

			if ( $product_parent->get_meta( '_voucher' ) ) {
				return true;
			}
		}
		return false;
	}
}
