<?php
defined( 'ABSPATH' ) || exit;

function get_order_by_meta_key( $meta_key ) {

  $query_vars = array(
    'meta_key' => 'voucher_key',
    'meta_value' => $meta_key,
    'post_type' => 'shop_order',
    'post_status' => array( 'wc-on-hold', 'wc-pending' ),
  );

  $data_store = WC_Data_Store::load( 'order' );
  $voucher_orders = $data_store->query( $query_vars );
  // $voucher_orders = new WP_Query( $query_vars );

  return $voucher_orders;
}
