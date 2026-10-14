<?php
/**
 * Pengaturan dasar tema, menu, gambar, dan aset.
 *
 * @package Yumnia
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	function () {
		load_theme_textdomain( 'yumnia', YUMNIA_DIR . '/languages' );

		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'align-wide' );
		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'editor-styles' );
		add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 120,
				'width'       => 360,
				'flex-height' => true,
				'flex-width'  => true,
			)
		);
		add_theme_support(
			'editor-color-palette',
			array(
				array( 'name' => 'Maroon', 'slug' => 'maroon', 'color' => '#7B1E3A' ),
				array( 'name' => 'Maroon tua', 'slug' => 'maroon-dark', 'color' => '#4A0F22' ),
				array( 'name' => 'Blush', 'slug' => 'blush', 'color' => '#F3DAD7' ),
				array( 'name' => 'Blush muda', 'slug' => 'blush-soft', 'color' => '#FBEFEC' ),
				array( 'name' => 'Krem', 'slug' => 'cream', 'color' => '#FBF7F2' ),
				array( 'name' => 'Putih', 'slug' => 'white', 'color' => '#FFFFFF' ),
				array( 'name' => 'Tinta', 'slug' => 'ink', 'color' => '#2A1419' ),
			)
		);
		add_editor_style( 'assets/css/editor.css' );

		register_nav_menus(
			array(
				'primary'     => __( 'Menu utama', 'yumnia' ),
				'footer_shop' => __( 'Footer: Belanja', 'yumnia' ),
				'footer_help' => __( 'Footer: Bantuan', 'yumnia' ),
			)
		);

		// Ukuran gambar: kartu produk 4:5, galeri besar, dan lebar penuh.
		add_image_size( 'yumnia-card', 640, 800, true );
		add_image_size( 'yumnia-large', 1200, 1500, true );
		add_image_size( 'yumnia-wide', 2000, 1125, true );
	}
);

/**
 * Aset CSS & JS.
 */
add_action(
	'wp_enqueue_scripts',
	function () {
		$css = YUMNIA_DIR . '/assets/css/main.css';
		$js  = YUMNIA_DIR . '/assets/js/main.js';

		wp_enqueue_style( 'yumnia', YUMNIA_URI . '/assets/css/main.css', array(), file_exists( $css ) ? (string) filemtime( $css ) : YUMNIA_VERSION );
		wp_enqueue_script(
			'yumnia',
			YUMNIA_URI . '/assets/js/main.js',
			array(),
			file_exists( $js ) ? (string) filemtime( $js ) : YUMNIA_VERSION,
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);

		$data = array(
			'home'     => home_url( '/' ),
			'nonce'    => wp_create_nonce( 'yumnia-cart' ),
			'wcAjax'   => class_exists( 'WC_AJAX' ) ? WC_AJAX::get_endpoint( '%%endpoint%%' ) : '',
			'storeApi' => esc_url_raw( rest_url( 'wc/store/v1/products' ) ),
			'search'   => esc_url_raw( add_query_arg( array( 'post_type' => 'product', 's' => '' ), home_url( '/' ) ) ),
			'cart'     => function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : '',
			'checkout' => function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : '',
			'i18n'     => array(
				'added'       => __( 'Masuk ke keranjang', 'yumnia' ),
				'addedSet'    => __( 'Set masuk ke keranjang', 'yumnia' ),
				'error'       => __( 'Maaf, terjadi kesalahan. Coba lagi.', 'yumnia' ),
				'chooseColor' => __( 'Pilih warna', 'yumnia' ),
				'chooseSize'  => __( 'Pilih ukuran', 'yumnia' ),
				'choose'      => __( 'Pilih varian dulu', 'yumnia' ),
				'pick'        => __( 'Pilih varian', 'yumnia' ),
				'pickSet'     => __( 'Ambil set ini', 'yumnia' ),
				'add'         => __( 'Tambah ke keranjang', 'yumnia' ),
				'addAll'      => __( 'Tambah semua ke keranjang', 'yumnia' ),
				'soldOut'     => __( 'Habis', 'yumnia' ),
				'inStock'     => __( 'Tersedia', 'yumnia' ),
				'lowStock'    => __( 'Sisa %d', 'yumnia' ),
				'noResults'   => __( 'Tidak ada produk yang cocok.', 'yumnia' ),
				'seeAll'      => __( 'Lihat semua hasil', 'yumnia' ),
			),
		);
		wp_add_inline_script( 'yumnia', 'window.YUMNIA=' . wp_json_encode( $data ) . ';', 'before' );

		// Gaya blok WooCommerce tidak dipakai (cart & checkout memakai shortcode klasik).
		if ( ! is_admin() ) {
			wp_dequeue_style( 'wc-blocks-style' );
		}
	},
	20
);

/**
 * Preload font utama supaya teks tampil cepat.
 */
add_action(
	'wp_head',
	function () {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( YUMNIA_URI . '/assets/fonts/jost-latin-wght-normal.woff2' )
		);
		echo '<meta name="theme-color" content="#FBF7F2">' . "\n";
	},
	1
);

/**
 * Kelas body tambahan.
 */
add_filter(
	'body_class',
	function ( $classes ) {
		if ( is_front_page() ) {
			$classes[] = 'has-hero';
		}
		return $classes;
	}
);

/**
 * Menu bawaan jika admin belum mengatur menu.
 *
 * @return array<int,array{label:string,url:string}>
 */
function yumnia_default_menu() {
	$items = array();
	foreach ( array( 'hijab' => __( 'Hijab', 'yumnia' ), 'sandal' => __( 'Sandal', 'yumnia' ) ) as $slug => $label ) {
		$term = taxonomy_exists( 'product_cat' ) ? get_term_by( 'slug', $slug, 'product_cat' ) : false;
		if ( $term && ! is_wp_error( $term ) ) {
			$items[] = array( 'label' => $label, 'url' => get_term_link( $term ) );
		}
	}
	$items[] = array( 'label' => __( 'Padu-padan', 'yumnia' ), 'url' => get_post_type_archive_link( 'yumnia_set' ) );
	foreach ( array( 'cerita' => __( 'Cerita', 'yumnia' ), 'bantuan' => __( 'Bantuan', 'yumnia' ) ) as $slug => $label ) {
		$page = get_page_by_path( $slug );
		if ( $page ) {
			$items[] = array( 'label' => $label, 'url' => get_permalink( $page ) );
		}
	}
	return $items;
}

/**
 * Cetak menu: menu dari admin bila ada, selain itu menu bawaan.
 *
 * @param string $location Lokasi menu.
 * @param string $class    Kelas <ul>.
 */
function yumnia_menu( $location, $class ) {
	if ( has_nav_menu( $location ) ) {
		wp_nav_menu(
			array(
				'theme_location' => $location,
				'container'      => false,
				'menu_class'     => $class,
				'depth'          => 1,
				'fallback_cb'    => false,
			)
		);
		return;
	}
	echo '<ul class="' . esc_attr( $class ) . '">';
	foreach ( yumnia_default_menu() as $item ) {
		if ( empty( $item['url'] ) || is_wp_error( $item['url'] ) ) {
			continue;
		}
		echo '<li><a href="' . esc_url( $item['url'] ) . '">' . esc_html( $item['label'] ) . '</a></li>';
	}
	echo '</ul>';
}

/**
 * Batasi panjang ringkasan.
 */
add_filter(
	'excerpt_length',
	function () {
		return 24;
	}
);
