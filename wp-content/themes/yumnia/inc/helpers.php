<?php
/**
 * Fungsi bantu umum.
 *
 * @package Yumnia
 */

defined( 'ABSPATH' ) || exit;

/**
 * Nilai bawaan pengaturan tema (Customizer).
 */
function yumnia_defaults() {
	return array(
		'wa_number'      => '6281234567890',
		'wa_greeting'    => 'Halo Yumnia, saya mau tanya produk.',
		'instagram'      => 'https://instagram.com/',
		'tiktok'         => 'https://tiktok.com/',
		'facebook'       => 'https://facebook.com/',
		'address'        => 'Desa Sukajadi, Kec. Tamansari, Kab. Bogor, Jawa Barat',
		'maps_url'       => 'https://maps.google.com/?q=Desa+Sukajadi+Tamansari+Bogor',
		'hours'          => 'Senin–Sabtu, 08.00–17.00 WIB',
		'topbar_text'    => 'Pesan mudah lewat WhatsApp · Dibuat oleh pengrajin Desa Sukajadi, Bogor',
		'hero_title'     => 'For a better you.',
		'hero_text'      => 'Hijab dan sandal wanita yang dibuat dengan teliti oleh pengrajin Desa Sukajadi, di kaki Gunung Salak.',
		'hero_cta'       => 'Lihat koleksi',
		'hero_image'     => 0,
		'hero_image_hp'  => 0,
		'intro_text'     => 'Yumnia adalah merek milik Desa Wisata Sukajadi. Setiap hijab dijahit oleh UMKM konveksi desa, dan setiap sandal dirakit oleh pengrajin alas kaki dari kampung yang sama.',
		'intro_stat'     => '100%',
		'intro_stat_lbl' => 'Dibuat di Desa Sukajadi',
		'story_image'    => 0,
		'story_quote'    => 'Setiap jahitan membawa cerita dari kampung kami di kaki Gunung Salak.',
		'story_caption'  => 'Pengrajin Yumnia, Desa Sukajadi',
		'og_image'       => 0,
		'ga4_id'         => '',
		'partners_title' => 'Didampingi oleh',
	);
}

/**
 * Ambil pengaturan tema dengan nilai bawaan.
 *
 * @param string $key Kunci pengaturan.
 * @return mixed
 */
function yumnia_mod( $key ) {
	$defaults = yumnia_defaults();
	return get_theme_mod( 'yumnia_' . $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
}

/**
 * Daftar liputan media (nama + tautan).
 */
function yumnia_press() {
	$names = array( 'Radar Bogor', 'Jurnal Bogor', 'Tribun Bogor', 'Jurnal Inspirasi', 'Jurnalissatunews' );
	$items = array();
	foreach ( $names as $i => $name ) {
		$n     = $i + 1;
		$label = get_theme_mod( "yumnia_press_{$n}_name", $name );
		if ( '' === trim( (string) $label ) ) {
			continue;
		}
		$items[] = array(
			'name' => $label,
			'url'  => get_theme_mod( "yumnia_press_{$n}_url", '' ),
		);
	}
	return $items;
}

/**
 * Daftar logo mitra untuk footer dan halaman Cerita.
 */
function yumnia_partners() {
	$names = array( 'Desa Wisata Sukajadi', 'PT Raharja Energi Cepu Tbk (RATU)', 'Yayasan Leads Indonesia Bisa' );
	$items = array();
	foreach ( $names as $i => $name ) {
		$n       = $i + 1;
		$items[] = array(
			'name'  => get_theme_mod( "yumnia_partner_{$n}_name", $name ),
			'logo'  => (int) get_theme_mod( "yumnia_partner_{$n}_logo", 0 ),
		);
	}
	return $items;
}

/**
 * Normalisasi nomor HP Indonesia ke format internasional tanpa tanda plus (628xxx).
 *
 * @param string $phone Nomor mentah.
 * @return string
 */
function yumnia_normalize_phone( $phone ) {
	$digits = preg_replace( '/\D+/', '', (string) $phone );
	if ( '' === $digits ) {
		return '';
	}
	if ( 0 === strpos( $digits, '0' ) ) {
		$digits = '62' . substr( $digits, 1 );
	} elseif ( 0 === strpos( $digits, '8' ) ) {
		$digits = '62' . $digits;
	}
	return $digits;
}

/**
 * Tautan WhatsApp ke admin toko.
 *
 * @param string $text Pesan awal.
 * @param string $to   Nomor tujuan (opsional, bawaan nomor admin).
 * @return string
 */
function yumnia_wa_link( $text = '', $to = '' ) {
	$number = yumnia_normalize_phone( $to ? $to : yumnia_mod( 'wa_number' ) );
	$url    = 'https://wa.me/' . $number;
	if ( '' !== $text ) {
		$url .= '?text=' . rawurlencode( $text );
	}
	return $url;
}

/**
 * Format rupiah sederhana: Rp79.000.
 *
 * @param float $amount Nominal.
 * @return string
 */
function yumnia_rupiah( $amount ) {
	return 'Rp' . number_format( (float) $amount, 0, ',', '.' );
}

/**
 * URL gambar dari ID lampiran, dengan cadangan.
 *
 * @param int    $id       ID lampiran.
 * @param string $size     Ukuran gambar.
 * @param string $fallback URL cadangan.
 * @return string
 */
function yumnia_image_url( $id, $size = 'large', $fallback = '' ) {
	$url = $id ? wp_get_attachment_image_url( (int) $id, $size ) : '';
	return $url ? $url : $fallback;
}

/**
 * Ikon SVG inline.
 *
 * @param string $name Nama ikon.
 * @param string $class Kelas tambahan.
 * @return string
 */
function yumnia_icon( $name, $class = '' ) {
	$paths = array(
		'search'      => '<circle cx="11" cy="11" r="6.5"/><path d="m20 20-4.2-4.2"/>',
		'bag'         => '<path d="M5 8h14l-1.2 12H6.2z"/><path d="M9 8V6.5a3 3 0 0 1 6 0V8"/>',
		'menu'        => '<path d="M4 8h16M4 16h10"/>',
		'close'       => '<path d="M6 6l12 12M18 6 6 18"/>',
		'arrow-right' => '<path d="M5 12h14M13 6l6 6-6 6"/>',
		'arrow-left'  => '<path d="M19 12H5M11 6l-6 6 6 6"/>',
		'arrow-up-right' => '<path d="M7 17 17 7M8 7h9v9"/>',
		'arrow-down'  => '<path d="M12 5v14M6 13l6 6 6-6"/>',
		'plus'        => '<path d="M12 5v14M5 12h14"/>',
		'minus'       => '<path d="M5 12h14"/>',
		'check'       => '<path d="m5 12.5 4.5 4.5L19 7.5"/>',
		'chevron-down' => '<path d="m6 9 6 6 6-6"/>',
		'filter'      => '<path d="M4 7h10M18 7h2M4 17h4M12 17h8"/><circle cx="16" cy="7" r="2"/><circle cx="10" cy="17" r="2"/>',
		'pin'         => '<path d="M12 21s-6.5-5.6-6.5-11a6.5 6.5 0 0 1 13 0c0 5.4-6.5 11-6.5 11z"/><circle cx="12" cy="10" r="2.3"/>',
		'hand'        => '<path d="M8 13V5.5a1.5 1.5 0 0 1 3 0V11m0-1.5V4.5a1.5 1.5 0 0 1 3 0V11m0-4.5a1.5 1.5 0 0 1 3 0V14a7 7 0 0 1-7 7h-.6a6 6 0 0 1-4.8-2.4L3.4 16a1.6 1.6 0 0 1 2.5-2l2.1 2"/>',
		'truck'       => '<path d="M3 6h11v10H3zM14 10h4l3 3v3h-7"/><circle cx="7" cy="17.5" r="1.8"/><circle cx="17" cy="17.5" r="1.8"/>',
		'swap'        => '<path d="M7 7h12l-3-3M17 17H5l3 3"/>',
		'sparkle'     => '<path d="M12 3v4M12 17v4M3 12h4M17 12h4M6 6l2.5 2.5M15.5 15.5 18 18M6 18l2.5-2.5M15.5 8.5 18 6"/>',
		'trash'       => '<path d="M5 7h14M10 7V5h4v2M7 7l1 12h8l1-12"/>',
		'ruler'       => '<path d="M3 15 15 3l6 6L9 21z"/><path d="m7 11 2 2M10 8l2 2M13 5l2 2"/>',
		'instagram'   => '<rect x="3.5" y="3.5" width="17" height="17" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.2" cy="6.8" r=".6" fill="currentColor"/>',
		'tiktok'      => '<path d="M14 3v11.5a3.5 3.5 0 1 1-3.5-3.5"/><path d="M14 3c.4 2.6 2.2 4.4 5 4.6"/>',
		'facebook'    => '<path d="M14.5 8H17V4.5h-2.5A3.5 3.5 0 0 0 11 8v2.5H8.5V14H11v7h3.5v-7H17l.5-3.5h-3V8.5a.5.5 0 0 1 .5-.5z"/>',
		'whatsapp'    => '<path d="M4 20l1.2-4.1A8 8 0 1 1 8.4 19z"/><path d="M9.2 8.6c.2-.4.5-.4.8-.4h.5c.2 0 .4.1.5.4l.7 1.6c.1.2 0 .5-.1.6l-.5.6c-.1.2-.1.4 0 .5a6 6 0 0 0 2.6 2.4c.2.1.4.1.5-.1l.6-.7c.2-.2.4-.2.6-.1l1.6.8c.2.1.3.3.3.5 0 1-.8 1.9-1.9 1.9-3 0-6.6-3.4-6.6-6.4 0-.5.2-1.1.4-1.6z" fill="currentColor" stroke="none"/>',
	);
	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}
	return sprintf(
		'<svg class="icon icon-%1$s %2$s" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%3$s</svg>',
		esc_attr( $name ),
		esc_attr( $class ),
		$paths[ $name ]
	);
}

/**
 * Monogram Y Yumnia (placeholder sampai file logo resmi tersedia).
 *
 * @param string $class Kelas tambahan.
 * @return string
 */
function yumnia_monogram( $class = '' ) {
	return '<svg class="monogram ' . esc_attr( $class ) . '" viewBox="0 0 100 120" aria-hidden="true" focusable="false">'
		. '<path class="monogram__hood" d="M22 16C33 3 67 3 78 16" fill="none" stroke="currentColor" stroke-width="5" stroke-linecap="round"/>'
		. '<path class="monogram__arm" d="M50 68C32 62 19 44 22 16" fill="none" stroke="currentColor" stroke-width="8" stroke-linecap="round"/>'
		. '<path class="monogram__arm" d="M50 68C68 62 81 44 78 16" fill="none" stroke="currentColor" stroke-width="8" stroke-linecap="round"/>'
		. '<path class="monogram__stem" d="M50 68V114" fill="none" stroke="currentColor" stroke-width="8" stroke-linecap="round"/>'
		. '</svg>';
}

/**
 * Logo situs: logo unggahan (Customizer) atau monogram + wordmark.
 */
function yumnia_logo() {
	$name = get_bloginfo( 'name' );
	if ( has_custom_logo() ) {
		$logo_id = get_theme_mod( 'custom_logo' );
		return '<a class="brand" href="' . esc_url( home_url( '/' ) ) . '" rel="home" aria-label="' . esc_attr( $name ) . '">'
			. wp_get_attachment_image( $logo_id, 'medium', false, array( 'class' => 'brand__img', 'alt' => $name ) )
			. '</a>';
	}
	return '<a class="brand" href="' . esc_url( home_url( '/' ) ) . '" rel="home" aria-label="' . esc_attr( $name ) . '">'
		. yumnia_monogram( 'brand__mark' )
		. '<span class="brand__word">YUMNIA</span>'
		. '</a>';
}

/**
 * Pecah teks per kata untuk animasi (fallback tetap terbaca tanpa JS).
 *
 * @param string $text Teks.
 * @return string HTML.
 */
function yumnia_split( $text ) {
	$words = preg_split( '/\s+/', trim( wp_strip_all_tags( $text ) ) );
	$out   = array();
	foreach ( $words as $i => $word ) {
		$out[] = '<span class="w"><span class="w__i" style="--i:' . (int) $i . '">' . esc_html( $word ) . '</span></span>';
	}
	return implode( ' ', $out );
}
