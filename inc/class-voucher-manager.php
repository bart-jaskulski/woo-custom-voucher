<?php

namespace Dentonet\WP;

use Dentonet\WP\Voucher;

class Voucher_Manager {

	/**
	 * Generated voucher objects to save.
	 *
	 * @var Voucher[]
	 */
	private array $vouchers = array();

	/**
	 * Type of voucher to generate.
	 *
	 * @var string
	 */
	private string $voucher_type;
	/**
	 * ID of superior order
	 *
	 * @var int
	 */
	private int $parent_order_id;

	/**
	 * Base factory function.
	 * Adds new instance of Voucher and stores it in object cache.
	 */
	public function add_voucher() {
		$voucher = new Voucher(
			Voucher::generate_voucher_code(),
			0,
			isset( $this->parent_order_id ) ? $this->parent_order_id : 0,
			0,
			isset( $this->voucher_type ) ? $this->voucher_type : '',
		);
		$this->vouchers[] = $voucher;
		return $voucher;
	}

	public function set_voucher_type( string $voucher_type ) {
		$this->voucher_type = $voucher_type;
	}

	public function set_parent_order_id( int $parent_order_id ) {
		$this->parent_order_id = absint( $parent_order_id );
	}

	/**
	 *  A method for inserting multiple rows into the specified table
	 *  Updated to include the ability to Update existing rows by primary key
	 *
	 * @return bool
	 *
	 * Borrowed from:
	 * @author  Ugur Mirza ZEYREK
	 * @contributor Travis Grenell
	 * @source http://stackoverflow.com/a/12374838/1194797
	 */
	public function save() : bool {
		global $wpdb;
		$wp_table_name = "{$wpdb->prefix}woo_vouchers";

		// Prepare variables from object.
		$row_arrays = array();

		$table_columns = array(
			'voucher_key',
			'order_id',
			'parent_order_id',
			'user_id',
			'voucher_type',
		);

		foreach ( $this->vouchers as $voucher ) {
			$row_arrays[] = array_combine(
				$table_columns,
				array(
					$voucher->get_voucher_code(),
					$voucher->get_order_id(),
					$voucher->get_parent_order_id(),
					$voucher->get_user_id(),
					$voucher->get_voucher_type(),
				)
			);
		}
		// Setup arrays for Actual Values, and Placeholders.
		$values        = array();
		$place_holders = array();
		$query         = '';
		$query_columns = '';

		$query .= "INSERT INTO `{$wp_table_name}` (";

		foreach ( $row_arrays as $count => $row_array ) {
			foreach ( $row_array as $key => $value ) {
				if ( 0 == $count ) {
					if ( $query_columns ) {
						$query_columns .= ', ' . $key . '';
					} else {
						$query_columns .= '' . $key . '';
					}
				}

				$values[] = $value;

				$symbol = '%s';
				if ( is_numeric( $value ) ) {
					if ( is_float( $value ) ) {
						$symbol = '%f';
					} else {
						$symbol = '%d';
					}
				}
				if ( isset( $place_holders[ $count ] ) ) {
					$place_holders[ $count ] .= ", '$symbol'";
				} else {
					$place_holders[ $count ] = "( '$symbol'";
				}
			}
			// mind closing the GAP.
			$place_holders[ $count ] .= ')';
		}

		$query .= " $query_columns ) VALUES ";

		$query .= implode( ', ', $place_holders );

		if ( $wpdb->query( $wpdb->prepare( $query, $values ) ) ) { // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			return true;
		} else {
			return false;
		}
	}
}
