<?php
/**
 * This class add checkbox for WooCommerce variable products.
 *
 * @package dentonet
 */

defined( 'ABSPATH' ) || exit;

/**
 * Add WooCommerce option and save it to database.
 */
class Voucher_Option implements Component_Interface {

	/**
	 * Hook necesary actions and filters.
	 */
	public function initialize() {
		add_filter( 'product_type_options', array( $this, 'filter_add_voucher_checkbox' ) );
		add_action( 'woocommerce_process_product_meta_variable', array( $this, 'action_save_voucher_checkbox' ) );
	}

	/**
	 * Insert checkbox shown only for variable product.
	 *
	 * @param  array $options array of product type options
	 * @return array modified product type options
	 */
	public function filter_add_voucher_checkbox( array $options ) : array {
		$options['voucher'] = array(
			'id'            => '_voucher',
			'wrapper_class' => 'show_if_variable',
			'label'         => __( 'Voucher', 'woocommerce' ),
			'description'   => __( 'Virtual products are intangible and are not shipped.', 'woocommerce' ),
			'default'       => 'no',
		);
		return $options;
	}

	/**
	 * Save checkbox's state to database.
	 *
	 * @param  int $post_id Current product ID.
	 */
	public function action_save_voucher_checkbox( int $post_id ) {
		update_post_meta( $post_id, '_voucher', isset( $_POST['_voucher'] ) ? 'yes' : 'no' );
	}
}
