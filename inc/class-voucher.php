<?php
/**
 * Dentonet\WP\Voucher Class
 *
 * @package dentonet
 */

namespace Dentonet\WP;

/**
 * Base class for voucher creation and simple manipulation.
 */
class Voucher {

	/**
	 * Voucher class constructor.
	 *
	 * @param string $voucher_code
	 * @param int    $order_id
	 * @param int    $parent_order_id
	 * @param int    $user_id
	 * @param string $voucher_type
	 */
	public function __construct(
	private string $voucher_code = '',
	private int $order_id = 0,
	private int $parent_order_id = 0,
	private int $user_id = 0,
	private string $voucher_type = '',
	) {}

	/**
	 * Associate user with voucher to mark voucher as taken.
	 *
	 * @param int $user_id ID of user who got voucher.
	 * @param int $order_id ID of order with voucher.
	 */
	public function add_user_to_voucher( int $user_id, int $order_id ) {
		global $wpdb;

		try {
			$wpdb->query(
				$wpdb->prepare(
					"
					UPDATE `{$wpdb->prefix}woo_vouchers`
					SET `user_id` = %d, `order_id` = %d
					WHERE `voucher_key` = %s
					",
					array(
						$user_id,
						$order_id,
						$this->voucher_code,
					)
				)
			);
		} catch ( Exception $e ) {
			error_log( $e->getMessage() );
		}

	}

	/**
	 * Get generated voucher code.
	 *
	 * @return string Voucher code.
	 */
	public function get_voucher_code() : string {
		return $this->voucher_code;
	}

	/**
	 * Get ID of superior order.
	 *
	 * @return int Superior order ID.
	 */
	public function get_parent_order_id() : int {
		return $this->parent_order_id;
	}

	/**
	 * Get associated order ID.
	 *
	 * @return int Associated order ID.
	 */
	public function get_order_id() :int {
		return $this->order_id;
	}

	/**
	 * Get ID of user who used voucher.
	 *
	 * @return int ID of user.
	 */
	public function get_user_id() : int {
		return $this->user_id;
	}

	public function get_voucher_type() : string {
		return $this->voucher_type;
	}

	/**
	 * Check if current voucher has been used already.
	 * @method is_used
	 * @return bool True if used, false if not.
	 */
	public function is_used() : bool {
		return 0 !== $this->user_id;
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

		$data = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM `{$wpdb->prefix}woo_vouchers` WHERE `voucher_key` = %s LIMIT 1",
				$voucher,
			)
		);

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

	public static function generate_voucher_code() : string {
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
