<?php
/**
 * Shortcode untuk konten halaman: [yumnia_kontak] dan [yumnia_mitra].
 * Isinya diambil dari Customizer, jadi cukup diubah di satu tempat.
 *
 * @package Yumnia
 */

defined( 'ABSPATH' ) || exit;

/**
 * Nomor WA untuk ditampilkan: 6281234567890 → 0812-3456-7890.
 *
 * @param string $number Nomor.
 * @return string
 */
function yumnia_pretty_phone( $number ) {
	$n = yumnia_normalize_phone( $number );
	if ( 0 === strpos( $n, '62' ) ) {
		$n = '0' . substr( $n, 2 );
	}
	return trim( implode( '-', str_split( $n, 4 ) ), '-' );
}

/**
 * Nama akun dari tautan media sosial: https://instagram.com/yumnia → @yumnia.
 *
 * @param string $url Tautan.
 * @return string
 */
function yumnia_handle( $url ) {
	$path = trim( (string) wp_parse_url( $url, PHP_URL_PATH ), '/' );
	return $path ? '@' . ltrim( $path, '@' ) : __( 'Segera hadir', 'yumnia' );
}

add_shortcode(
	'yumnia_kontak',
	function () {
		$cards = array(
			array( 'whatsapp', __( 'WhatsApp', 'yumnia' ), yumnia_pretty_phone( yumnia_mod( 'wa_number' ) ), yumnia_wa_link( yumnia_mod( 'wa_greeting' ) ), yumnia_mod( 'hours' ) ),
			array( 'instagram', 'Instagram', yumnia_handle( yumnia_mod( 'instagram' ) ), yumnia_mod( 'instagram' ), __( 'Koleksi & cerita terbaru', 'yumnia' ) ),
			array( 'tiktok', 'TikTok', yumnia_handle( yumnia_mod( 'tiktok' ) ), yumnia_mod( 'tiktok' ), __( 'Cara pakai & padu-padan', 'yumnia' ) ),
			array( 'facebook', 'Facebook', yumnia_handle( yumnia_mod( 'facebook' ) ), yumnia_mod( 'facebook' ), __( 'Kabar dari desa', 'yumnia' ) ),
			array( 'pin', __( 'Alamat', 'yumnia' ), yumnia_mod( 'address' ), yumnia_mod( 'maps_url' ), __( 'Buka di Google Maps', 'yumnia' ) ),
		);
		$out = '<div class="contact-cards">';
		foreach ( $cards as $i => $c ) {
			if ( ! $c[3] ) {
				continue;
			}
			$out .= sprintf(
				'<a class="contact-card" href="%1$s" target="_blank" rel="noopener" data-reveal style="--d:%6$ss">%2$s<span class="contact-card__label">%3$s</span><strong class="contact-card__value">%4$s</strong><span class="contact-card__hint">%5$s</span></a>',
				esc_url( $c[3] ),
				yumnia_icon( $c[0] ),
				esc_html( $c[1] ),
				esc_html( $c[2] ),
				esc_html( $c[4] ),
				esc_attr( (string) ( 0.06 * $i ) )
			);
		}
		return $out . '</div>';
	}
);

add_shortcode(
	'yumnia_mitra',
	function () {
		$out = '<ul class="partners-inline">';
		foreach ( yumnia_partners() as $p ) {
			$out .= '<li>' . ( $p['logo'] ? wp_get_attachment_image( $p['logo'], 'medium', false, array( 'alt' => $p['name'] ) ) : '<span>' . esc_html( $p['name'] ) . '</span>' ) . '</li>';
		}
		return $out . '</ul>';
	}
);

/**
 * Gaya "Lengkung" untuk blok gambar (bentuk jendela melengkung khas Yumnia).
 */
add_action(
	'init',
	function () {
		if ( function_exists( 'register_block_style' ) ) {
			register_block_style( 'core/image', array( 'name' => 'arch', 'label' => __( 'Lengkung', 'yumnia' ) ) );
		}
	}
);
