<?php
/**
 * Halaman tidak ditemukan.
 *
 * @package Yumnia
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="not-found">
	<div class="container">
		<div class="not-found__mark" aria-hidden="true"><?php echo yumnia_monogram(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
		<p class="eyebrow">404</p>
		<h1 class="page-hero__title" data-split="now"><?php echo yumnia_split( __( 'Halaman ini tidak ditemukan.', 'yumnia' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h1>
		<p><?php esc_html_e( 'Mungkin tautannya sudah berubah. Yuk kembali ke koleksi.', 'yumnia' ); ?></p>
		<div class="not-found__actions">
			<a class="btn" href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ) ); ?>"><span class="btn__label"><?php esc_html_e( 'Lihat koleksi', 'yumnia' ); ?></span></a>
			<a class="btn btn--ghost" href="<?php echo esc_url( home_url( '/' ) ); ?>"><span class="btn__label"><?php esc_html_e( 'Ke beranda', 'yumnia' ); ?></span></a>
		</div>
	</div>
</section>
<?php
get_footer();
