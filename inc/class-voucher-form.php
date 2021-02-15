<?php
defined( 'ABSPATH' ) || exit;

class Voucher_Form implements Component_Interface {
	public function initialize() {
		add_filter( 'template_include', array( $this, 'wpdocs_include_template_files_on_page' ) );
		add_action( 'wp_ajax_woo_voucher', array( $this, 'action_ajax_validate_voucher' ) );
		add_action( 'wp_ajax_nopriv_woo_voucher', array( $this, 'action_ajax_validate_voucher' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'action_ajax_add_global_variables' ) );
	}

	public static function action_ajax_validate_voucher() {
		try {
			$customer_id = wc_create_new_customer( $_POST['email'] );

			$order = get_order_by_meta_key( $_POST['voucher-key'] )[0];

			$order->update_status( 'completed' );
			$order->add_order_note( 'hey, you did it' );
			$order->set_customer_id( $customer_id );
			$order->save();
			echo home_url();
		}
		catch ( Exception $e ) {
			echo $e->getMessage();
		}
		die();
	}

	public function action_ajax_add_global_variables() {
		$vars = array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' )
		);
		wp_localize_script( 'jquery', 'wooVoucher', $vars );
	}

	public function wpdocs_include_template_files_on_page( $template ) {
    if ( is_page( 'voucher' ) ) {
      return $this->get_voucher_form();
    }

		return $template;
	}

	public function get_voucher_form() {
		include __DIR__ . '/templates/voucher-template.php';
	}
}
