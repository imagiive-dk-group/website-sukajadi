<?php
/**
 * Halaman setelah pesan: buka WhatsApp admin dengan ringkasan pesanan.
 *
 * @package Yumnia
 * @version 8.1.0
 *
 * @var WC_Order|false $order
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="woocommerce-order">
	<?php
	if ( $order ) :
		do_action( 'woocommerce_before_thankyou', $order->get_id() );

		if ( $order->has_status( 'failed' ) ) :
			?>
			<div class="thanks thanks--failed">
				<h1 class="thanks__title"><?php esc_html_e( 'Maaf, pesanan belum berhasil.', 'yumnia' ); ?></h1>
				<p><?php esc_html_e( 'Silakan coba lagi atau hubungi admin lewat WhatsApp.', 'yumnia' ); ?></p>
				<a class="btn" href="<?php echo esc_url( wc_get_checkout_url() ); ?>"><span class="btn__label"><?php esc_html_e( 'Coba lagi', 'yumnia' ); ?></span></a>
			</div>
			<?php
		elseif ( 'yumnia_whatsapp' === $order->get_payment_method() && function_exists( 'yumnia_wa_order_message' ) ) :
			$yumnia_wa = yumnia_wa_link( yumnia_wa_order_message( $order ) );
			?>
			<section class="thanks" data-thanks data-wa="<?php echo esc_url( $yumnia_wa ); ?>" data-order="<?php echo esc_attr( $order->get_order_key() ); ?>">
				<div class="thanks__check" aria-hidden="true">
					<svg viewBox="0 0 80 80"><circle class="thanks__circle" cx="40" cy="40" r="36"/><path class="thanks__tick" d="M25 41l10 10 20-22"/></svg>
				</div>
				<p class="eyebrow"><?php echo esc_html( sprintf( __( 'Pesanan #%s tercatat', 'yumnia' ), $order->get_order_number() ) ); ?></p>
				<h1 class="thanks__title"><?php esc_html_e( 'Satu langkah lagi: kirim pesananmu lewat WhatsApp.', 'yumnia' ); ?></h1>
				<p class="thanks__text" data-countdown-text>
					<?php
					printf(
						/* translators: %s: hitung mundur */
						esc_html__( 'WhatsApp terbuka otomatis dalam %s detik. Kalau tidak terbuka, tekan tombol di bawah.', 'yumnia' ),
						'<strong data-countdown>4</strong>'
					);
					?>
				</p>
				<a class="btn btn--wa btn--lg" href="<?php echo esc_url( $yumnia_wa ); ?>" data-wa-btn>
					<span class="btn__label"><?php echo yumnia_icon( 'whatsapp' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Kirim ke WhatsApp admin', 'yumnia' ); ?></span>
				</a>

				<div class="thanks__summary">
					<ul class="thanks__items">
						<?php foreach ( $order->get_items() as $yumnia_item ) : ?>
							<?php
							$yumnia_p    = $yumnia_item->get_product();
							$yumnia_meta = $yumnia_p && $yumnia_p->is_type( 'variation' ) ? wc_get_formatted_variation( $yumnia_p, true, false, false ) : '';
							$yumnia_name = $yumnia_p && $yumnia_p->is_type( 'variation' ) ? get_the_title( $yumnia_p->get_parent_id() ) : $yumnia_item->get_name();
							?>
							<li>
								<?php if ( $yumnia_p ) : ?>
									<?php echo $yumnia_p->get_image( 'thumbnail', array( 'class' => 'thanks__img' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<?php endif; ?>
								<span class="thanks__name"><?php echo esc_html( $yumnia_name ); ?><?php if ( $yumnia_meta ) : ?><small><?php echo esc_html( str_replace( ', ', ' · ', $yumnia_meta ) ); ?></small><?php endif; ?></span>
								<span class="thanks__qty">× <?php echo (int) $yumnia_item->get_quantity(); ?></span>
								<span class="thanks__price"><?php echo esc_html( yumnia_rupiah( $order->get_line_subtotal( $yumnia_item, true ) ) ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
					<p class="thanks__total"><span><?php esc_html_e( 'Subtotal', 'yumnia' ); ?></span><strong><?php echo esc_html( yumnia_rupiah( $order->get_subtotal() ) ); ?></strong></p>
					<p class="thanks__note"><?php esc_html_e( 'Belum termasuk ongkir. Admin akan menginfokan total dan cara bayar (transfer bank atau QRIS).', 'yumnia' ); ?></p>
				</div>

				<ol class="thanks__steps">
					<li><strong><?php esc_html_e( 'Kirim pesan', 'yumnia' ); ?></strong><span><?php esc_html_e( 'Ringkasan pesanan sudah terisi otomatis di WhatsApp.', 'yumnia' ); ?></span></li>
					<li><strong><?php esc_html_e( 'Konfirmasi ongkir', 'yumnia' ); ?></strong><span><?php esc_html_e( 'Admin membalas total belanja + ongkir.', 'yumnia' ); ?></span></li>
					<li><strong><?php esc_html_e( 'Bayar & dikirim', 'yumnia' ); ?></strong><span><?php esc_html_e( 'Transfer atau QRIS, lalu pesanan dikirim dari Sukajadi.', 'yumnia' ); ?></span></li>
				</ol>
				<a class="link-arrow" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Lanjut belanja', 'yumnia' ); ?><?php echo yumnia_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
			</section>
			<?php
			// Ringkasan bawaan WooCommerce tidak perlu ditampilkan dua kali.
			remove_action( 'woocommerce_thankyou', 'woocommerce_order_details_table', 10 );
		else :
			wc_get_template( 'checkout/order-received.php', array( 'order' => $order ) );
		endif;

		do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() );
		do_action( 'woocommerce_thankyou', $order->get_id() );
	else :
		wc_get_template( 'checkout/order-received.php', array( 'order' => false ) );
	endif;
	?>
</div>
