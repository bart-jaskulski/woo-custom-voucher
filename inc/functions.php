<?php
defined( 'ABSPATH' ) || exit;

/**
 * Get order by associated voucher key.
 *
 * @method get_order_by_meta_key
 * @param  string $meta_value Voucher key to find.
 * @return array|WP_Error Returns array of orders on success. WP_Error on failure.
 */
function get_order_by_meta_key( string $meta_value ) {

	$query_vars = array(
		'meta_key' => 'voucher_key',
		'meta_value' => $meta_value,
		'post_type' => 'shop_order',
		'post_status' => array( 'wc-on-hold', 'wc-pending' ),
	);

	$data_store = WC_Data_Store::load( 'order' );
	$voucher_orders = $data_store->query( $query_vars );

	if ( count( $voucher_orders ) === 0 ) {
		return new WP_Error( 'voucher-not-found', __( 'There\'s no voucher associated with that code in database.', 'woo-custom-voucher' ) );
	}

	return $voucher_orders;
}
