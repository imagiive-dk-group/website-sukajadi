<?php
/**
 * Formulir checkout: kiri data pengiriman, kanan ringkasan + tombol Pesan via WhatsApp.
 *
 * @package Yumnia
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_checkout_form', $checkout );

if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}
?>
<form name="checkout" method="post" class="checkout woocommerce-checkout checkout-grid" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data" aria-label="<?php esc_attr_e( 'Checkout', 'yumnia' ); ?>">
	<div class="checkout-grid__fields">
		<?php if ( $checkout->get_checkout_fields() ) : ?>
			<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>
			<div id="customer_details">
				<?php do_action( 'woocommerce_checkout_billing' ); ?>
				<?php do_action( 'woocommerce_checkout_shipping' ); ?>
			</div>
			<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>
		<?php endif; ?>
		<ol class="checkout-steps-info">
			<li><?php esc_html_e( 'Klik "Pesan via WhatsApp". Pesananmu tercatat dan WhatsApp terbuka dengan ringkasan pesanan.', 'yumnia' ); ?></li>
			<li><?php esc_html_e( 'Admin membalas dengan total ongkir dan cara bayar (transfer bank atau QRIS).', 'yumnia' ); ?></li>
			<li><?php esc_html_e( 'Setelah pembayaran dikonfirmasi, pesanan dikemas dan dikirim dari Sukajadi.', 'yumnia' ); ?></li>
		</ol>
	</div>
	<div class="checkout-grid__summary">
		<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>
		<h2 class="checkout-step" id="order_review_heading"><span class="checkout-step__num">2</span><?php esc_html_e( 'Ringkasan pesanan', 'yumnia' ); ?></h2>
		<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>
		<div id="order_review" class="woocommerce-checkout-review-order">
			<?php do_action( 'woocommerce_checkout_order_review' ); ?>
		</div>
		<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
	</div>
</form>
<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
