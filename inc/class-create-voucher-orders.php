<?php
defined( 'ABSPATH' ) || exit;

class Create_Voucher_Orders implements Component_Interface {

	/**
	 * @inheritDoc
	 */
	public function initialize() {
		add_action( 'woocommerce_voucher_payment_complete', array( $this, 'action_create_order_from_voucher' ) );
	}

	public function action_create_order_from_voucher( int $order_id ) {
		// Get variables from order.
		$order = wc_get_order( $order_id );
		$voucher_type = '';

		$items = $order->get_items();
		$count = 0;
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

			// Prepare the amount of tickets to generate
			$count += $item->get_quantity();
		}

		// Create sufficient amount of voucher orders based on tickets quantity.
		for ( $i = 0; $i < $count; $i++ ) {
			$this->create_order( $voucher_type );
		}

	}

	/**
	 * Create order programmatically for voucher.
	 * Add correct product to the order and set 100% discount.
	 * Order gets on hold until end users inserts voucher code.
	 *
	 * @param  string $voucher_type Item SKU
	 * @return WC_Order|bool
	 */
	private function create_order( string $voucher_type ) {
		if ( empty( $voucher_type ) ) {
			return;
		}

		// Add product to the order and get price before discount.
		$order = wc_create_order();
		$order->set_created_via( 'Voucher' );
		$order->update_status( 'on-hold' );

		$product = wc_get_product( wc_get_product_id_by_sku( $voucher_type ) );
		$order->add_product( $product, 1 );

		// Add hash key for user to claim order.
		// TODO: move it to its own class with validation etc.
		$voucher_key = substr( md5( microtime() ), 0, 12 );
		$order->add_meta_data( 'voucher_key', $voucher_key, true );

		// Create 100% discount.
		// $discount = new WC_Order_Item_Coupon();
		// $discount->set_name( 'Voucher' );
		// $discount->set_code( 'Voucher' );
		// $discount->set_discount( '100' );
		// $discount->set_taxes( false );
		// $discount->save();

		// Apply discount and recalculate total.
		// $orded->set_total( '0' );
		$order->calculate_totals();
		$order->save();

		return $order;
	}

	public function get_order_by_meta_key( $meta_key ) {

		$query_vars = array(
			'meta_key' => 'voucher_key',
			'meta_value' => $meta_key,
			'post_type' => 'shop_order',
			'post_status' => 'wc-on-hold',
		);
		$voucher_orders = WC_Data_Store::load( 'order' )->query( $query_vars );
		// $voucher_orders = new WP_Query( $query_vars );

		return $voucher_orders;
	}
}
