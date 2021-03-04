<?php
/**
 * HTML template for voucher front-end form.
 *
 * @package dentonet
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main id="primary" class="container">
	<h2 class="h3"><?php esc_html_e( 'Wypełnij formularz', 'woo-custom-voucher' ); ?></h2>
	<p><?php _e( 'Pola oznaczone <span>*</span> są wymagane', 'woo-custom-voucher' ); ?></p>
	<div id="js-error-holder"></div>
	<form id="voucher_form" name="voucher" method="post" class="voucher-form" action="">
		<fieldset class="voucher-code-wrapper">
			<legend class="h3 color-primary"><?php esc_html_e( 'Voucher', 'woo-custom-voucher' ); ?></legend>
			<p class="form-group">
				<input id="voucher-code" type="text" name="voucher-key" required>
				<label
				  for="voucher-code"><?php esc_html_e( 'Wpisz kod', 'woo-custom-voucher' ); ?></label>
			</p>
		</fieldset>
		<fieldset class="voucher-user-data-wrapper">
			<legend class="h3 color-primary"><?php esc_html_e( 'Twoje dane', 'woo-custom-voucher' ); ?></legend>
			<p class="form-group">
				<input id="user-name" type="text" name="user[name]" required>
				<label
				  for="user-name"><?php esc_html_e( 'Imię', 'woo-custom-voucher' ); ?></label>
			</p>
			<p class="form-group">
				<input id="user-surname" type="text" name="user[surname]" required>
				<label
				  for="user-surname"><?php esc_html_e( 'Nazwisko', 'woo-customer-voucher' ); ?></label>
			</p>
			<p class="form-group">
				<input id="user-email" type="email" name="user[email]" required>
				<label
				  for="user-email"><?php esc_html_e( 'Email', 'woo-customer-voucher' ); ?></label>
			</p>
			<p class="form-group">
				<input id="user-phone" type="text" name="user[phone]" required>
				<label
				  for="user-phone"><?php esc_html_e( 'Telefon', 'woo-customer-voucher' ); ?></label>
			</p>
			<p class="form-group">
				<input id="user-pass" type="password" name="user[password]"
				  required>
				<label
				  for="user-pass"><?php esc_html_e( 'Hasło', 'woo-customer-voucher' ); ?></label>
			</p>
		</fieldset>
		<fieldset class="voucher-marketing-wrapper is-border">
			<p class="form-group">
					<input id="marketing_content" class="input-checkbox" type="checkbox" required>
					<label for="marketing_content" class="checkbox">Zgoda marketingowa</label>
				</p>
			<p class="form-group">
					<input class="input-checkbox" id="rules_consent" type="checkbox" required>
					<label class="checkbox" for="rules_consent">Akceptuję regulamin</label>
				</p>
		</fieldset>
		<input type="hidden" name="action" value="woo_voucher">
		<?php wp_nonce_field( 'claim-voucher', '_voucher_nonce' ); ?>
		<button type="submit" class="is-full-width voucher-submit"><?php esc_attr_e( 'Zarejestruj', 'woo-custom-voucher' ); ?><i class="icon-chevron-right"></i></button>
	</form>
</main>
<?php
get_footer();
