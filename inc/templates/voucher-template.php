<?php
defined( 'ABSPATH' ) || exit;

get_header();
?>

<form id="voucher_form" name="voucher" method="post" class="" action="">

  <input type="hidden" name="action" value="woo_voucher">
	<input type="text" name="user[name]" required>
	<input type="text" name="user[surname]" required>
	<input type="email" name="user[email]" required>
	<input type="text" name="voucher-key" required>
  <input type="password" name="user[password]" required>
  <?php wp_nonce_field( 'claim-voucher', '_voucher_nonce' ); ?>
  <input type="submit">
</form>
<?php
get_footer();
