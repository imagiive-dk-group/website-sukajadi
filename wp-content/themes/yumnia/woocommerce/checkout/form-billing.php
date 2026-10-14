<?php
/**
 * Isian data pengiriman di checkout.
 *
 * @package Yumnia
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="woocommerce-billing-fields">
	<h2 class="checkout-step"><span class="checkout-step__num">1</span><?php esc_html_e( 'Data pengiriman', 'yumnia' ); ?></h2>
	<?php do_action( 'woocommerce_before_checkout_billing_form', $checkout ); ?>
	<div class="woocommerce-billing-fields__field-wrapper">
		<?php
		foreach ( $checkout->get_checkout_fields( 'billing' ) as $key => $field ) {
			woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
		}
		?>
	</div>
	<?php do_action( 'woocommerce_after_checkout_billing_form', $checkout ); ?>
</div>
