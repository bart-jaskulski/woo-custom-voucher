<?php
/**
 * Plugin Name: Vouchers for WooCommerce
 * Plugin URI: http://woocommerce.com/products/woocommerce-extension/
 * Description: Custom voucher extensions for WooCommerce.
 * Version: 1.0.5
 * Author: Dentonet
 * Author URI: https://dentonet.pl
 * Developer: Bart Jaskulski
 * Developer URI: https://github.com/bart-jaskulski
 * Text Domain: woocommerce-extension
 * Domain Path: /languages
 *
 * WC requires at least: 5.0
 * WC tested up to: 5.0
 *
 * @package dentonet
 */

namespace Dentonet\WP;

use Dentonet\WP\{ Voucher_Option, WC_Payment_Complete, Voucher_Form };
use Dentonet\WP\Component_Interface;

defined( 'ABSPATH' ) || exit;

/**
 * Main plugin class responsible for lauching all components.
 */
class Woo_Custom_Voucher {

	/**
	 * Array of injected components
	 *
	 * @var array
	 */
	protected $components = array();

	/**
	 * Create object loading files and inserting components.
	 */
	public function __construct() {
		// Check if WooCommerce is active.
		if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			$this->loader();
			$this->components = $this->get_components();
		}
	}

	/**
	 * Start each component respecitively.
	 */
	public function initialize() {
		array_walk(
			$this->components,
			function ( Component_Interface $component ) {
				$component->initialize();
			}
		);
	}

	/**
	 * Load all files.
	 * TODO: Switch to autoload or sth later.
	 */
	public function loader() {
		require_once( __DIR__ . '/inc/component-interface.php' );
		require_once( __DIR__ . '/inc/class-voucher-option.php' );
		require_once( __DIR__ . '/inc/class-wc-payment-complete.php' );
		require_once( __DIR__ . '/inc/class-voucher-form.php' );
		require_once( __DIR__ . '/inc/class-voucher.php' );
		require_once( __DIR__ . '/inc/class-voucher-manager.php' );
	}

	/**
	 * List and instantiate all used components here.
	 *
	 * @return array List of components
	 */
	private function get_components() : array {
		return array(
			new Voucher_Option(),
			new WC_Payment_Complete(),
			new Voucher_Form(),
		);
	}

	/**
	 * Install custom table for holding vouchers with associated data.
	 */
	public static function install_db_table() {
		global $wpdb;

		$table_name = "{$wpdb->prefix}woo_vouchers";
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$table_name} (
			voucher_key VARCHAR(19) NOT NULL,
			order_id BIGINT UNSIGNED DEFAULT 0,
			parent_order_id BIGINT UNSIGNED NOT NULL,
			user_id BIGINT UNSIGNED DEFAULT 0,
			voucher_type VARCHAR(255) NOT NULL,
			PRIMARY KEY  (voucher_key)
		) {$charset_collate} ENGINE=INNODB";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

}

/**
 * Start plugin when WooCommerce is up and running.
 */
add_action(
	'woocommerce_loaded',
	function () {
		( new Woo_Custom_Voucher() )->initialize();
	}
);

register_activation_hook( __FILE__, array( 'Dentonet\WP\Woo_Custom_Voucher', 'install_db_table' ) );
