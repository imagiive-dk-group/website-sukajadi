<?php
/**
 * Isi WordPress + WooCommerce dengan data contoh Yumnia.
 *
 * Jalankan lewat WP-CLI (aman dijalankan berulang, data yang sudah ada dilewati):
 *   wp eval-file scripts/seed.php contoh-data
 *
 * Argumen pertama = folder contoh-data (berisi produk.json, gambar/, halaman/).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( "Jalankan lewat: wp eval-file scripts/seed.php contoh-data\n" );
}
if ( ! class_exists( 'WooCommerce' ) ) {
	WP_CLI::error( 'WooCommerce belum aktif.' );
}

require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

$dir  = rtrim( isset( $args[0] ) ? $args[0] : dirname( __DIR__ ) . '/contoh-data', '/' );
$data = json_decode( (string) file_get_contents( $dir . '/produk.json' ), true );
if ( ! $data ) {
	WP_CLI::error( "Tidak bisa membaca {$dir}/produk.json" );
}

/* ------------------------------------------------------------ bantuan */

/**
 * Unggah gambar contoh sekali saja; panggilan berikutnya memakai lampiran yang sama.
 */
function yumnia_seed_image( $dir, $file ) {
	static $cache = array();
	if ( isset( $cache[ $file ] ) ) {
		return $cache[ $file ];
	}
	$found = get_posts(
		array(
			'post_type'   => 'attachment',
			'post_status' => 'inherit',
			'meta_key'    => '_yumnia_seed_file', // phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_value'  => $file, // phpcs:ignore WordPress.DB.SlowDBQuery
			'numberposts' => 1,
			'fields'      => 'ids',
		)
	);
	if ( $found ) {
		return $cache[ $file ] = (int) $found[0];
	}
	$path = $dir . '/gambar/' . $file;
	if ( ! file_exists( $path ) ) {
		WP_CLI::warning( "Gambar tidak ada: {$file}" );
		return $cache[ $file ] = 0;
	}
	$upload = wp_upload_bits( $file, null, (string) file_get_contents( $path ) );
	if ( ! empty( $upload['error'] ) ) {
		WP_CLI::warning( $upload['error'] );
		return $cache[ $file ] = 0;
	}
	$id = wp_insert_attachment(
		array(
			'post_mime_type' => 'image/jpeg',
			'post_title'     => preg_replace( '/\.\w+$/', '', $file ) . ' (foto contoh)',
			'post_status'    => 'inherit',
		),
		$upload['file']
	);
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $upload['file'] ) );
	update_post_meta( $id, '_yumnia_seed_file', $file );
	update_post_meta( $id, '_wp_attachment_image_alt', 'Foto contoh' );
	return $cache[ $file ] = (int) $id;
}

/**
 * Buat atau perbarui halaman berdasarkan slug.
 */
function yumnia_seed_page( $slug, $title, $content = '', $excerpt = '' ) {
	$page = get_page_by_path( $slug );
	$args = array(
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_name'    => $slug,
		'post_title'   => $title,
		'post_content' => $content,
		'post_excerpt' => $excerpt,
	);
	if ( $page ) {
		$args['ID'] = $page->ID;
		return wp_update_post( $args );
	}
	return wp_insert_post( $args );
}

/**
 * Pastikan atribut global (Warna, Ukuran) ada dan terdaftar di request ini.
 */
function yumnia_seed_attribute( $slug, $name ) {
	$id = wc_attribute_taxonomy_id_by_name( $slug );
	if ( ! $id ) {
		$id = wc_create_attribute(
			array(
				'name'         => $name,
				'slug'         => $slug,
				'type'         => 'select',
				'order_by'     => 'menu_order',
				'has_archives' => false,
			)
		);
		if ( is_wp_error( $id ) ) {
			WP_CLI::error( $id->get_error_message() );
		}
	}
	$tax = wc_attribute_taxonomy_name( $slug );
	if ( ! taxonomy_exists( $tax ) ) {
		register_taxonomy( $tax, array( 'product' ), array( 'hierarchical' => false, 'show_ui' => false, 'query_var' => true, 'rewrite' => false ) );
	}
	return (int) $id;
}

function yumnia_seed_term( $tax, $slug, $name, $order = 0 ) {
	$term = get_term_by( 'slug', $slug, $tax );
	if ( ! $term ) {
		$res = wp_insert_term( $name, $tax, array( 'slug' => $slug ) );
		if ( is_wp_error( $res ) ) {
			WP_CLI::error( $res->get_error_message() );
		}
		$term = get_term( $res['term_id'], $tax );
	}
	update_term_meta( $term->term_id, 'order', $order );
	return $term;
}

/* ------------------------------------------------------------ 1. pengaturan toko */
WP_CLI::log( '1/7 Pengaturan toko…' );
$options = array(
	'timezone_string'                            => 'Asia/Jakarta',
	'date_format'                                => 'j F Y',
	'blogname'                                   => 'Yumnia',
	'blogdescription'                            => 'For a better you — hijab & sandal dari Desa Sukajadi',
	'woocommerce_currency'                       => 'IDR',
	'woocommerce_currency_pos'                   => 'left',
	'woocommerce_price_thousand_sep'             => '.',
	'woocommerce_price_decimal_sep'              => ',',
	'woocommerce_price_num_decimals'             => 0,
	'woocommerce_default_country'                => 'ID:JB',
	'woocommerce_store_address'                  => 'Desa Sukajadi',
	'woocommerce_store_city'                     => 'Kab. Bogor',
	'woocommerce_store_postcode'                 => '16610',
	'woocommerce_allowed_countries'              => 'specific',
	'woocommerce_specific_allowed_countries'     => array( 'ID' ),
	'woocommerce_ship_to_countries'              => 'disabled',
	'woocommerce_calc_taxes'                     => 'no',
	'woocommerce_enable_coupons'                 => 'no',
	'woocommerce_manage_stock'                   => 'yes',
	'woocommerce_hold_stock_minutes'             => 60,
	'woocommerce_notify_low_stock_amount'        => 2,
	'woocommerce_hide_out_of_stock_items'        => 'no',
	'woocommerce_enable_guest_checkout'          => 'yes',
	'woocommerce_enable_checkout_login_reminder' => 'no',
	'woocommerce_enable_signup_and_login_from_checkout' => 'no',
	'woocommerce_enable_myaccount_registration'  => 'no',
	'woocommerce_cart_redirect_after_add'        => 'no',
	'woocommerce_enable_ajax_add_to_cart'        => 'yes',
	'woocommerce_default_catalog_orderby'        => 'date',
	'woocommerce_coming_soon'                    => 'no',
	'woocommerce_store_pages_only'               => 'no',
	'woocommerce_task_list_hidden'               => 'yes',
	'woocommerce_onboarding_profile'             => array( 'skipped' => true ),
	'woocommerce_bacs_settings'                  => array( 'enabled' => 'no' ),
	'woocommerce_cheque_settings'                => array( 'enabled' => 'no' ),
	'woocommerce_cod_settings'                   => array( 'enabled' => 'no' ),
	'woocommerce_yumnia_whatsapp_settings'       => array(
		'enabled'     => 'yes',
		'title'       => 'Pesan via WhatsApp',
		'description' => 'Setelah klik Pesan, WhatsApp akan terbuka dengan ringkasan pesananmu. Admin akan membalas dengan total ongkir dan cara bayar (transfer bank atau QRIS).',
	),
);
foreach ( $options as $key => $value ) {
	update_option( $key, $value );
}

/* ------------------------------------------------------------ 2. halaman */
WP_CLI::log( '2/7 Halaman…' );
if ( ! wc_get_page_id( 'shop' ) || wc_get_page_id( 'shop' ) < 1 ) {
	WC_Install::create_pages();
}
wp_update_post( array( 'ID' => wc_get_page_id( 'shop' ), 'post_title' => 'Semua produk', 'post_name' => 'toko' ) );
wp_update_post( array( 'ID' => wc_get_page_id( 'cart' ), 'post_title' => 'Keranjang', 'post_name' => 'keranjang', 'post_content' => "<!-- wp:shortcode -->\n[woocommerce_cart]\n<!-- /wp:shortcode -->" ) );
wp_update_post( array( 'ID' => wc_get_page_id( 'checkout' ), 'post_title' => 'Pesan sekarang', 'post_name' => 'pesan', 'post_content' => "<!-- wp:shortcode -->\n[woocommerce_checkout]\n<!-- /wp:shortcode -->" ) );

$home = yumnia_seed_page( 'beranda', 'Beranda' );
update_option( 'show_on_front', 'page' );
update_option( 'page_on_front', $home );

$shop_url = get_permalink( wc_get_page_id( 'shop' ) );
$pages    = array(
	'cerita'  => array( 'Cerita Yumnia', 'Dari kampung di kaki Gunung Salak, untuk dirimu yang lebih baik.' ),
	'bantuan' => array( 'Bantuan', 'Cara order, ongkir, pembayaran, retur, dan tanya jawab.' ),
	'kontak'  => array( 'Kontak', '' ),
);
foreach ( $pages as $slug => $meta ) {
	$html = (string) file_get_contents( "{$dir}/halaman/{$slug}.html" );
	$html = preg_replace_callback(
		'/IMG:([\w\-\.]+)/',
		function ( $m ) use ( $dir ) {
			$id = yumnia_seed_image( $dir, $m[1] );
			return $id ? wp_get_attachment_image_url( $id, 'large' ) : '';
		},
		$html
	);
	$html = str_replace( 'LINK:shop', $shop_url, $html );
	yumnia_seed_page( $slug, $meta[0], $html, $meta[1] );
}

/* ------------------------------------------------------------ 3. atribut & kategori */
WP_CLI::log( '3/7 Atribut warna & ukuran, kategori…' );
$attr_ids = array(
	'pa_warna'  => yumnia_seed_attribute( 'warna', 'Warna' ),
	'pa_ukuran' => yumnia_seed_attribute( 'ukuran', 'Ukuran' ),
);
$terms = array( 'pa_warna' => array(), 'pa_ukuran' => array() );
foreach ( $data['warna'] as $i => $c ) {
	$t = yumnia_seed_term( 'pa_warna', $c['slug'], $c['nama'], $i );
	update_term_meta( $t->term_id, 'yumnia_hex', $c['hex'] );
	$terms['pa_warna'][ $c['slug'] ] = $t;
}
foreach ( $data['ukuran'] as $i => $c ) {
	$terms['pa_ukuran'][ $c['slug'] ] = yumnia_seed_term( 'pa_ukuran', $c['slug'], $c['nama'], $i );
}
$cats = array();
foreach ( $data['kategori'] as $i => $c ) {
	$t = yumnia_seed_term( 'product_cat', $c['slug'], $c['nama'], $i );
	wp_update_term( $t->term_id, 'product_cat', array( 'description' => $c['deskripsi'] ) );
	update_term_meta( $t->term_id, 'thumbnail_id', yumnia_seed_image( $dir, $c['gambar'] ) );
	$cats[ $c['slug'] ] = $t->term_id;
}

/* ------------------------------------------------------------ 4. produk & varian */
WP_CLI::log( '4/7 Produk & varian (butuh beberapa menit karena mengolah foto)…' );
$ids   = array();
$table = implode( "\n", array_map( function ( $r ) { return $r[0] . ' | ' . $r[1]; }, $data['tabel_ukuran_sandal'] ) );
$total = count( $data['produk'] );
foreach ( array_reverse( $data['produk'] ) as $n => $p ) {
	$existing = get_page_by_path( $p['slug'], OBJECT, 'product' );
	if ( $existing ) {
		$ids[ $p['slug'] ] = $existing->ID;
		WP_CLI::log( "   - {$p['nama']} sudah ada, dilewati" );
		continue;
	}
	$product = new WC_Product_Variable();
	$product->set_name( $p['nama'] );
	$product->set_slug( $p['slug'] );
	$product->set_status( 'publish' );
	$product->set_description( $p['deskripsi'] );
	$product->set_short_description( $p['ringkas'] );
	$product->set_category_ids( array( $cats[ $p['kategori'] ] ) );
	$product->set_featured( ! empty( $p['unggulan'] ) );
	$product->set_total_sales( (int) $p['terjual'] );
	$product->set_image_id( yumnia_seed_image( $dir, $p['slug'] . '-' . $p['warna'][0] . '.jpg' ) );
	$gallery = 'hijab' === $p['jenis']
		? array( $p['slug'] . '-pakai.jpg', $p['slug'] . '-detail.jpg' )
		: array( $p['slug'] . '-samping.jpg' );
	$product->set_gallery_image_ids( array_filter( array_map( function ( $f ) use ( $dir ) { return yumnia_seed_image( $dir, $f ); }, $gallery ) ) );

	$attributes = array();
	foreach ( array( 'pa_warna' => $p['warna'], 'pa_ukuran' => $p['ukuran'] ) as $tax => $slugs ) {
		$a = new WC_Product_Attribute();
		$a->set_id( $attr_ids[ $tax ] );
		$a->set_name( $tax );
		$a->set_options( array_map( function ( $s ) use ( $terms, $tax ) { return $terms[ $tax ][ $s ]->term_id; }, $slugs ) );
		$a->set_visible( true );
		$a->set_variation( true );
		$attributes[] = $a;
	}
	$product->set_attributes( $attributes );

	$meta = array(
		'_yumnia_baru'        => ! empty( $p['baru'] ) ? 'yes' : 'no',
		'_yumnia_pembuat'     => $p['pembuat'] ?? '',
		'_yumnia_bahan'       => $p['bahan'] ?? '',
		'_yumnia_ukuran_info' => $p['ukuran_info'] ?? '',
		'_yumnia_cerita_nama' => $p['cerita_nama'] ?? '',
		'_yumnia_tinggi_hak'  => $p['tinggi_hak'] ?? '',
		'_yumnia_jenis_sol'   => $p['jenis_sol'] ?? '',
		'_yumnia_tabel_ukuran' => 'sandal' === $p['jenis'] ? $table : '',
		'_yumnia_shopee'      => '',
		'_yumnia_tokopedia'   => '',
	);
	foreach ( $meta as $k => $v ) {
		$product->update_meta_data( $k, $v );
	}
	$id = $product->save();

	foreach ( $p['warna'] as $w ) {
		foreach ( $p['ukuran'] as $u ) {
			$v = new WC_Product_Variation();
			$v->set_parent_id( $id );
			$v->set_attributes( array( 'pa_warna' => $w, 'pa_ukuran' => $u ) );
			$v->set_regular_price( (string) $p['harga'] );
			$v->set_manage_stock( true );
			$v->set_stock_quantity( (int) ( $p['stok'][ "{$w}/{$u}" ] ?? $p['stok']['*'] ) );
			$v->set_image_id( yumnia_seed_image( $dir, $p['slug'] . '-' . $w . '.jpg' ) );
			$v->set_status( 'publish' );
			$v->save();
		}
	}
	WC_Product_Variable::sync( $id );
	wc_delete_product_transients( $id );
	$ids[ $p['slug'] ] = $id;
	WP_CLI::log( sprintf( '   + %s (%d/%d)', $p['nama'], $n + 1, $total ) );
}

// Produk pasangan ("Cocok dengan…").
foreach ( $data['produk'] as $p ) {
	$product = wc_get_product( $ids[ $p['slug'] ] ?? 0 );
	if ( $product && ! $product->get_cross_sell_ids() ) {
		$product->set_cross_sell_ids( array_values( array_filter( array_map( function ( $s ) use ( $ids ) { return $ids[ $s ] ?? 0; }, $p['cocok'] ?? array() ) ) ) );
		$product->save();
	}
}

/* ------------------------------------------------------------ 5. padu-padan */
WP_CLI::log( '5/7 Set padu-padan…' );
foreach ( $data['padu_padan'] as $i => $s ) {
	if ( get_page_by_path( $s['slug'], OBJECT, 'yumnia_set' ) ) {
		continue;
	}
	$post_id = wp_insert_post(
		array(
			'post_type'    => 'yumnia_set',
			'post_status'  => 'publish',
			'post_title'   => $s['nama'],
			'post_name'    => $s['slug'],
			'post_excerpt' => $s['deskripsi'],
			'menu_order'   => $i,
		)
	);
	set_post_thumbnail( $post_id, yumnia_seed_image( $dir, $s['gambar'] ) );
	update_post_meta(
		$post_id,
		'_yumnia_set_items',
		array_map( function ( $it ) use ( $ids ) { return array( 'product' => $ids[ $it[0] ] ?? 0, 'warna' => $it[1] ); }, $s['item'] )
	);
}

/* ------------------------------------------------------------ 6. tampilan */
WP_CLI::log( '6/7 Foto beranda…' );
set_theme_mod( 'yumnia_hero_image', yumnia_seed_image( $dir, 'hero.jpg' ) );
set_theme_mod( 'yumnia_hero_image_hp', yumnia_seed_image( $dir, 'hero-hp.jpg' ) );
set_theme_mod( 'yumnia_story_image', yumnia_seed_image( $dir, 'pengrajin-1.jpg' ) );
set_theme_mod( 'yumnia_og_image', yumnia_seed_image( $dir, 'og-default.jpg' ) );

/* ------------------------------------------------------------ 7. selesai */
WP_CLI::log( '7/7 Membersihkan cache…' );
if ( function_exists( 'wc_update_product_lookup_tables' ) ) {
	wc_update_product_lookup_tables();
}
delete_transient( 'wc_term_counts' );
wc_delete_product_transients();
flush_rewrite_rules();
WP_CLI::success( 'Data contoh Yumnia siap.' );
