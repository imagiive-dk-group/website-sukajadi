<?php
/**
 * Halaman biasa (Cerita, Bantuan, Kontak) dan halaman WooCommerce (Keranjang, Checkout).
 *
 * @package Yumnia
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	$yumnia_is_shop     = function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || is_account_page() );
	$yumnia_is_received = function_exists( 'is_wc_endpoint_url' ) && is_wc_endpoint_url( 'order-received' );
	?>
	<article <?php post_class( $yumnia_is_shop ? 'page page--shop' : 'page' ); ?>>
		<?php if ( ! $yumnia_is_received ) : ?>
			<header class="page-hero<?php echo $yumnia_is_shop ? ' page-hero--small' : ''; ?>">
				<div class="container">
					<?php if ( $yumnia_is_shop && ( is_cart() || is_checkout() ) ) : ?>
						<ol class="steps" aria-label="<?php esc_attr_e( 'Langkah pemesanan', 'yumnia' ); ?>">
							<li class="<?php echo is_cart() ? 'is-current' : 'is-done'; ?>"><?php esc_html_e( 'Keranjang', 'yumnia' ); ?></li>
							<li class="<?php echo is_checkout() ? 'is-current' : ''; ?>"><?php esc_html_e( 'Data pengiriman', 'yumnia' ); ?></li>
							<li><?php esc_html_e( 'Kirim via WhatsApp', 'yumnia' ); ?></li>
						</ol>
					<?php endif; ?>
					<h1 class="page-hero__title" data-split="now"><?php echo yumnia_split( get_the_title() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h1>
					<?php if ( has_excerpt() && ! $yumnia_is_shop ) : ?>
						<p class="page-hero__text" data-reveal><?php echo esc_html( get_the_excerpt() ); ?></p>
					<?php endif; ?>
				</div>
			</header>
		<?php endif; ?>
		<div class="<?php echo $yumnia_is_shop ? 'container shop-page' : 'entry-content'; ?>">
			<?php the_content(); ?>
		</div>
	</article>
	<?php
endwhile;

get_footer();
