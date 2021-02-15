<?php
defined( 'ABSPATH' ) || exit;

get_header();
?>

<form id="voucher_form" name="voucher" method="post" class="" action="">

  <input type="hidden" name="action" value="woo_voucher">
	<input type="text" name="user-name">
	<input type="email" name="email">
	<input type="text" name="voucher-key">
  <?php echo wp_nonce_field('claim-voucher', '_voucher-nonce'); ?>
  <input type="submit">
</form>
<?php //var_dump(get_order_by_meta_key( '2835bbb695f2' )[0]); ?>
<script>
const form = document.getElementById('voucher_form');
form.addEventListener('submit', e => {
  e.preventDefault();
  const formData = new URLSearchParams( [...(new FormData( form ) )] );
  const data = {
    method: 'POST',
    body: formData,
    credentials: 'same-origin',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
  };
  fetch(wooVoucher.ajaxUrl, data)
  .then(response => response.text())
  .then(data => console.log(data))
})
</script>
<?php get_footer();
