<?php

namespace Dentonet\WP;

class Voucher {

	public function __construct(
	private string $voucher_code = '',
	private int $order_id = 0,
	private int $parent_order_id = 0,
	private int $user_id = 0,
	private string $voucher_type = '',
	) {

	}

	public function update( array $data = array() ) {
		if ( empty( $data ) ) {
			return;
		}

		$sql = "UPDATE `{$wpdb->prefix}woo_voucher`
    SET `order_id` = ''
    WHERE `voucher_key` = '{$this->voucher_code}'";
	}

	public function get_voucher_code() {
		return $this->voucher_code;
	}

	public function get_parent_order_id() {
		return $this->parent_order_id;
	}

	public function get_order_id() {
		return $this->order_id;
	}

	public function get_user_id() {
		return $this->user_id;
	}

	public function get_voucher_type() {
		return $this->voucher_type;
	}

	/**
	 * Static function to get voucher for client request validation.
	 * Return whole row to process voucher data further.
	 *
	 * @param  string $voucher Voucher code to retrieve from database.
	 * @return Voucher|false New Voucher instance on success. False if query was empty.
	 */
	public static function get_voucher( string $voucher ) : self | false {
		global $wpdb;

		$query = "SELECT * FROM `{$wpdb->prefix}woo_vouchers` WHERE `voucher_key` = '$voucher' LIMIT 1";

		$data = $wpdb->get_row( $query );

		if ( is_null( $data ) ) {
			return false;
		}

		return new self(
			$data->voucher_key,
			$data->order_id,
			$data->parent_order_id,
			$data->user_id,
			$data->voucher_type,
		);
	}

	public static function generate() : string {
		$charset = array_merge( range( 0, 9 ), range( 'A', 'Z' ) );
		$pool_size = count( $charset ) - 1;
		$voucher_code = '';

		shuffle( $charset );

		for ( $i = 0; $i < 16; $i++ ) {
			$voucher_code .= $charset[ random_int( 0, $pool_size ) ];
		}

		$voucher_code = str_shuffle( $voucher_code );

		return rtrim( chunk_split( $voucher_code, 4, '-' ), '-' );
	}

}
