<?php
/**
 * Pembuat pratinjau statis.
 *
 * Menjalankan template tema Yumnia yang asli dengan data contoh (contoh-data/produk.json)
 * tanpa WordPress, lalu menyimpan hasilnya sebagai HTML di folder preview/.
 * Fungsi WordPress/WooCommerce di bawah ini hanya tiruan sederhana untuk pratinjau.
 *
 * Jalankan: php preview/build.php
 */

error_reporting( E_ALL );
ini_set( 'display_errors', '1' );

define( 'ABSPATH', __DIR__ . '/' );
$ROOT  = dirname( __DIR__ );
$THEME = $ROOT . '/wp-content/themes/yumnia';
define( 'YUMNIA_DIR', $THEME );
define( 'YUMNIA_URI', '../wp-content/themes/yumnia' );
define( 'YUMNIA_VERSION', '1.0.0' );
define( 'IMG_BASE', '../contoh-data/gambar/' );
define( 'IMG_DIR', $ROOT . '/contoh-data/gambar/' );

/* =========================================================== hooks & i18n */
$GLOBALS['__hooks']      = array();
$GLOBALS['__shortcodes'] = array();
function add_filter( $tag, $cb, $prio = 10, $args = 1 ) { $GLOBALS['__hooks'][ $tag ][] = array( $cb, $args ); return true; }
function add_action( $tag, $cb, $prio = 10, $args = 1 ) { return add_filter( $tag, $cb, $prio, $args ); }
function remove_action( $tag, $cb, $prio = 10 ) { unset( $GLOBALS['__hooks'][ $tag ] ); return true; }
function remove_filter( $tag, $cb, $prio = 10 ) { return remove_action( $tag, $cb ); }
function apply_filters( $tag, $value, ...$rest ) {
	foreach ( $GLOBALS['__hooks'][ $tag ] ?? array() as $h ) {
		if ( is_callable( $h[0] ) ) { $value = call_user_func_array( $h[0], array_slice( array_merge( array( $value ), $rest ), 0, max( 1, $h[1] ) ) ); }
	}
	return $value;
}
function do_action( $tag, ...$args ) {
	if ( in_array( $tag, array( 'init', 'after_setup_theme', 'wp_enqueue_scripts', 'customize_register', 'admin_head' ), true ) ) { return; }
	foreach ( $GLOBALS['__hooks'][ $tag ] ?? array() as $h ) { if ( is_callable( $h[0] ) ) { call_user_func_array( $h[0], $args ); } }
}
function add_shortcode( $tag, $cb ) { $GLOBALS['__shortcodes'][ $tag ] = $cb; }
function do_shortcode( $content ) {
	return preg_replace_callback( '/\[(\w+)\]/', function ( $m ) {
		return isset( $GLOBALS['__shortcodes'][ $m[1] ] ) ? call_user_func( $GLOBALS['__shortcodes'][ $m[1] ] ) : $m[0];
	}, $content );
}
function __( $s, $d = null ) { return $s; }
function _x( $s, $c, $d = null ) { return $s; }
function _e( $s, $d = null ) { echo $s; }
function esc_html__( $s, $d = null ) { return esc_html( $s ); }
function esc_attr__( $s, $d = null ) { return esc_attr( $s ); }
function esc_html_e( $s, $d = null ) { echo esc_html( $s ); }
function esc_attr_e( $s, $d = null ) { echo esc_attr( $s ); }
function _n( $a, $b, $n, $d = null ) { return 1 === (int) $n ? $a : $b; }
function _n_noop( $a, $b, $d = null ) { return array( $a, $b ); }
function load_theme_textdomain() {}
function esc_html( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' ); }
function esc_attr( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' ); }
function esc_url( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' ); }
function esc_url_raw( $s ) { return (string) $s; }
function wp_kses_post( $s ) { return (string) $s; }
function sanitize_title( $s ) { return trim( preg_replace( '/[^a-z0-9_]+/', '-', strtolower( (string) $s ) ), '-' ); }
function sanitize_key( $s ) { return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string) $s ) ); }
function sanitize_text_field( $s ) { return trim( strip_tags( (string) $s ) ); }
function sanitize_textarea_field( $s ) { return trim( strip_tags( (string) $s ) ); }
function sanitize_hex_color( $s ) { return preg_match( '/^#[0-9a-f]{6}$/i', $s ) ? $s : null; }
function wp_unslash( $s ) { return $s; }
function absint( $n ) { return abs( (int) $n ); }
function wp_strip_all_tags( $s ) { return trim( strip_tags( (string) $s ) ); }
function wp_json_encode( $d ) { return json_encode( $d, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); }
function wp_parse_url( $u, $c = -1 ) { return parse_url( $u, $c ); }
function wpautop( $s ) { $s = trim( (string) $s ); if ( '' === $s ) { return ''; } return '<p>' . implode( "</p>\n<p>", preg_split( '/\n\s*\n/', $s ) ) . '</p>'; }
function wp_trim_words( $s, $n = 55, $more = '…' ) { $w = preg_split( '/\s+/', trim( strip_tags( $s ) ) ); return count( $w ) > $n ? implode( ' ', array_slice( $w, 0, $n ) ) . $more : implode( ' ', $w ); }
function selected( $a, $b = true, $echo = true ) { $r = (string) $a === (string) $b ? ' selected="selected"' : ''; if ( $echo ) { echo $r; } return $r; }
function checked( $a, $b = true, $echo = true ) { $r = (string) $a === (string) $b ? ' checked="checked"' : ''; if ( $echo ) { echo $r; } return $r; }
function disabled( $a, $b = true, $echo = true ) { $r = (string) $a === (string) $b ? ' disabled="disabled"' : ''; if ( $echo ) { echo $r; } return $r; }
function is_wp_error( $x ) { return false; }
function is_admin() { return false; }
function current_user_can() { return false; }
function is_user_logged_in() { return false; }
function wp_create_nonce() { return 'preview'; }
function add_query_arg( $args, $url ) {
	$q = http_build_query( $args );
	return $q ? $url . ( false === strpos( $url, '?' ) ? '?' : '&' ) . $q : $url;
}
function register_nav_menus() {}
function register_post_type() {}
function register_block_style() {}

/* =========================================================== data contoh */
$DATA = json_decode( file_get_contents( $ROOT . '/contoh-data/produk.json' ), true );

class WP_Term {
	public $term_id; public $slug; public $name; public $taxonomy; public $description = ''; public $count = 0; public $hex = ''; public $thumb = 0;
	public function __construct( $a ) { foreach ( $a as $k => $v ) { $this->$k = $v; } }
}
class WP_Post {
	public $ID; public $post_title; public $post_excerpt = ''; public $post_content = ''; public $post_name = ''; public $post_type = 'page'; public $thumb = 0; public $meta = array();
	public function __construct( $a ) { foreach ( $a as $k => $v ) { $this->$k = $v; } }
}

// Lampiran (gambar).
$GLOBALS['__att']   = array();
$GLOBALS['__attid'] = array();
function att( $file ) {
	if ( ! isset( $GLOBALS['__attid'][ $file ] ) ) {
		$id = 1000 + count( $GLOBALS['__att'] );
		$GLOBALS['__att'][ $id ]     = $file;
		$GLOBALS['__attid'][ $file ] = $id;
	}
	return $GLOBALS['__attid'][ $file ];
}
function wp_get_attachment_image_url( $id, $size = 'large' ) { return isset( $GLOBALS['__att'][ $id ] ) ? IMG_BASE . $GLOBALS['__att'][ $id ] : false; }
function wp_get_attachment_image( $id, $size = 'large', $icon = false, $attr = array() ) {
	if ( ! isset( $GLOBALS['__att'][ $id ] ) ) { return ''; }
	$file = $GLOBALS['__att'][ $id ];
	$dim  = @getimagesize( IMG_DIR . $file );
	$attr = array_merge( array( 'src' => IMG_BASE . $file, 'alt' => '', 'loading' => 'lazy', 'decoding' => 'async' ), $attr );
	if ( $dim ) { $attr['width'] = $dim[0]; $attr['height'] = $dim[1]; }
	unset( $attr['sizes'] );
	$html = '<img';
	foreach ( $attr as $k => $v ) { $html .= ' ' . $k . '="' . esc_attr( $v ) . '"'; }
	return $html . '>';
}

// Istilah: kategori, warna, ukuran.
$GLOBALS['__terms'] = array( 'product_cat' => array(), 'pa_warna' => array(), 'pa_ukuran' => array() );
$tid = 1;
foreach ( $DATA['kategori'] as $c ) {
	$GLOBALS['__terms']['product_cat'][ $c['slug'] ] = new WP_Term( array( 'term_id' => $tid++, 'slug' => $c['slug'], 'name' => $c['nama'], 'taxonomy' => 'product_cat', 'description' => $c['deskripsi'], 'thumb' => att( $c['gambar'] ) ) );
}
foreach ( $DATA['warna'] as $c ) {
	$GLOBALS['__terms']['pa_warna'][ $c['slug'] ] = new WP_Term( array( 'term_id' => $tid++, 'slug' => $c['slug'], 'name' => $c['nama'], 'taxonomy' => 'pa_warna', 'hex' => $c['hex'] ) );
}
foreach ( $DATA['ukuran'] as $c ) {
	$GLOBALS['__terms']['pa_ukuran'][ $c['slug'] ] = new WP_Term( array( 'term_id' => $tid++, 'slug' => $c['slug'], 'name' => $c['nama'], 'taxonomy' => 'pa_ukuran' ) );
}
function taxonomy_exists( $t ) { return isset( $GLOBALS['__terms'][ $t ] ); }
function get_term_by( $field, $value, $tax ) { return $GLOBALS['__terms'][ $tax ][ $value ] ?? false; }
function get_terms( $args ) {
	$list = array_values( $GLOBALS['__terms'][ $args['taxonomy'] ] ?? array() );
	if ( ! empty( $args['hide_empty'] ) ) { $list = array_values( array_filter( $list, function ( $t ) { return $t->count > 0; } ) ); }
	return $list;
}
function get_term_meta( $id, $key, $single = true ) {
	foreach ( $GLOBALS['__terms'] as $terms ) {
		foreach ( $terms as $t ) {
			if ( $t->term_id === $id ) { return 'yumnia_hex' === $key ? $t->hex : ( 'thumbnail_id' === $key ? $t->thumb : '' ); }
		}
	}
	return '';
}
function get_term_link( $term ) { return 'katalog-' . $term->slug . '.html'; }
function wc_attribute_label( $tax ) { return array( 'pa_warna' => 'Warna', 'pa_ukuran' => 'Ukuran' )[ $tax ] ?? $tax; }

// Produk & varian.
class WC_Product {
	public $d; public $id; public $vars = array(); public $parent = 0; public $attrs = array(); public $stock = 0; public $img = 0;
	public function __construct( $d, $id ) { $this->d = $d; $this->id = $id; }
	public function get_id() { return $this->id; }
	public function get_name() { return $this->d['nama']; }
	public function get_type() { return $this->parent ? 'variation' : 'variable'; }
	public function is_type( $t ) { return $this->get_type() === $t; }
	public function get_price() { return $this->d['harga']; }
	public function get_price_html() { return '<span class="amount">' . yumnia_rupiah( $this->d['harga'] ) . '</span>'; }
	public function get_image_id() { return $this->img; }
	public function get_image( $size = 'thumbnail', $attr = array() ) { return wp_get_attachment_image( $this->img, $size, false, $attr ); }
	public function get_gallery_image_ids() { return $this->d['_gallery'] ?? array(); }
	public function is_in_stock() { if ( $this->parent ) { return $this->stock > 0; } foreach ( $this->vars as $v ) { if ( $v->stock > 0 ) { return true; } } return false; }
	public function get_children() { return array_map( function ( $v ) { return $v->id; }, $this->vars ); }
	public function get_attributes() { return $this->attrs; }
	public function get_variation_attributes() {
		if ( $this->parent ) { $o = array(); foreach ( $this->attrs as $k => $v ) { $o[ 'attribute_' . $k ] = $v; } return $o; }
		return array( 'pa_warna' => $this->d['warna'], 'pa_ukuran' => $this->d['ukuran'] );
	}
	public function managing_stock() { return (bool) $this->parent; }
	public function get_stock_quantity() { return $this->stock; }
	public function backorders_allowed() { return false; }
	public function get_meta( $k ) {
		$map = array(
			'_yumnia_baru' => ! empty( $this->d['baru'] ) ? 'yes' : 'no',
			'_yumnia_pembuat' => $this->d['pembuat'] ?? '',
			'_yumnia_bahan' => $this->d['bahan'] ?? '',
			'_yumnia_ukuran_info' => $this->d['ukuran_info'] ?? '',
			'_yumnia_tinggi_hak' => $this->d['tinggi_hak'] ?? '',
			'_yumnia_jenis_sol' => $this->d['jenis_sol'] ?? '',
			'_yumnia_cerita_nama' => $this->d['cerita_nama'] ?? '',
			'_yumnia_shopee' => 'https://shopee.co.id/',
			'_yumnia_tokopedia' => 'https://tokopedia.com/',
			'_yumnia_tabel_ukuran' => 'sandal' === $this->d['jenis'] ? implode( "\n", array_map( function ( $r ) { return $r[0] . ' | ' . $r[1]; }, $GLOBALS['DATA']['tabel_ukuran_sandal'] ) ) : '',
		);
		return $map[ $k ] ?? '';
	}
	public function get_short_description() { return $this->d['ringkas']; }
	public function get_description() { return $this->d['deskripsi']; }
	public function get_cross_sell_ids() { return array_values( array_filter( array_map( function ( $s ) { return $GLOBALS['__slug2id'][ $s ] ?? 0; }, $this->d['cocok'] ?? array() ) ) ); }
	public function get_status() { return 'publish'; }
	public function exists() { return true; }
	public function is_visible() { return true; }
	public function get_parent_id() { return $this->parent; }
	public function get_permalink() { return get_permalink( $this->parent ? $this->parent : $this->id ); }
}
class WC_Product_Variation extends WC_Product {}

$GLOBALS['__products'] = array();
$GLOBALS['__slug2id']  = array();
$pid  = 100;
$vid  = 5000;
foreach ( $DATA['produk'] as $p ) {
	$id   = $pid++;
	$prod = new WC_Product( $p, $id );
	$prod->img = att( $p['slug'] . '-' . $p['warna'][0] . '.jpg' );
	$gallery   = array();
	if ( 'hijab' === $p['jenis'] ) {
		$gallery[] = att( $p['slug'] . '-pakai.jpg' );
		$gallery[] = att( $p['slug'] . '-detail.jpg' );
	} else {
		$gallery[] = att( $p['slug'] . '-samping.jpg' );
	}
	$prod->d['_gallery'] = $gallery;
	foreach ( $p['warna'] as $w ) {
		foreach ( $p['ukuran'] as $u ) {
			$v          = new WC_Product_Variation( $p, $vid++ );
			$v->parent  = $id;
			$v->attrs   = array( 'pa_warna' => $w, 'pa_ukuran' => $u );
			$v->img     = att( $p['slug'] . '-' . $w . '.jpg' );
			$v->stock   = $p['stok'][ "$w/$u" ] ?? $p['stok']['*'];
			$prod->vars[] = $v;
			$GLOBALS['__products'][ $v->id ] = $v;
		}
		$GLOBALS['__terms']['pa_warna'][ $w ]->count++;
	}
	foreach ( $p['ukuran'] as $u ) { $GLOBALS['__terms']['pa_ukuran'][ $u ]->count++; }
	$GLOBALS['__terms']['product_cat'][ $p['kategori'] ]->count++;
	$GLOBALS['__products'][ $id ] = $prod;
	$GLOBALS['__slug2id'][ $p['slug'] ] = $id;
}
function wc_get_product( $id ) { return $GLOBALS['__products'][ is_object( $id ) ? $id->get_id() : (int) $id ] ?? false; }
function parents() { return array_values( array_filter( $GLOBALS['__products'], function ( $p ) { return ! $p->parent; } ) ); }
function wc_get_products( $args ) {
	$list = parents();
	if ( ! empty( $args['featured'] ) ) { $list = array_values( array_filter( $list, function ( $p ) { return ! empty( $p->d['unggulan'] ); } ) ); }
	return array_slice( $list, 0, $args['limit'] ?? 100 );
}
function wc_get_product_terms( $id, $tax, $args = array() ) {
	$p = wc_get_product( $id );
	if ( 'product_cat' === $tax ) { return array( $GLOBALS['__terms']['product_cat'][ $p->d['kategori'] ] ); }
	$key = 'pa_warna' === $tax ? 'warna' : 'ukuran';
	return array_map( function ( $s ) use ( $tax ) { return $GLOBALS['__terms'][ $tax ][ $s ]; }, $p->d[ $key ] );
}
function wc_get_formatted_variation( $v, $flat = true ) { return implode( ', ', array_map( function ( $k, $s ) { return $GLOBALS['__terms'][ $k ][ $s ]->name; }, array_keys( $v->attrs ), $v->attrs ) ); }
function wc_get_related_products( $id, $n = 4 ) {
	$p = wc_get_product( $id );
	return array_slice( array_values( array_map( function ( $x ) { return $x->id; }, array_filter( parents(), function ( $x ) use ( $p ) { return $x->id !== $p->id && $x->d['kategori'] === $p->d['kategori']; } ) ) ), 0, $n );
}
function wc_placeholder_img_src() { return IMG_BASE . 'og-default.jpg'; }
function get_woocommerce_currency() { return 'IDR'; }

// Set padu-padan & halaman.
$GLOBALS['__sets'] = array();
foreach ( $DATA['padu_padan'] as $i => $s ) {
	$GLOBALS['__sets'][] = new WP_Post( array(
		'ID' => 900 + $i, 'post_title' => $s['nama'], 'post_excerpt' => $s['deskripsi'], 'post_type' => 'yumnia_set', 'thumb' => att( $s['gambar'] ),
		'meta' => array( '_yumnia_set_items' => array_map( function ( $it ) { return array( 'product' => $GLOBALS['__slug2id'][ $it[0] ], 'warna' => $it[1] ); }, $s['item'] ) ),
	) );
}
$GLOBALS['__pages'] = array();
foreach ( array( 'cerita' => 'Cerita Yumnia', 'bantuan' => 'Bantuan', 'kontak' => 'Kontak' ) as $slug => $title ) {
	$html = file_get_contents( $ROOT . '/contoh-data/halaman/' . $slug . '.html' );
	$html = preg_replace_callback( '/IMG:([\w\-\.]+)/', function ( $m ) { return IMG_BASE . $m[1]; }, $html );
	$html = str_replace( 'LINK:shop', 'katalog.html', $html );
	$GLOBALS['__pages'][ $slug ] = new WP_Post( array( 'ID' => 800 + count( $GLOBALS['__pages'] ), 'post_title' => $title, 'post_name' => $slug, 'post_content' => $html ) );
}
function get_page_by_path( $slug ) { return $GLOBALS['__pages'][ trim( $slug, '/' ) ] ?? null; }
function get_posts( $args ) { return array_slice( $GLOBALS['__sets'], 0, $args['posts_per_page'] ?? 10 ); }
function get_post_meta( $id, $key, $single = true ) { foreach ( $GLOBALS['__sets'] as $s ) { if ( $s->ID === $id ) { return $s->meta[ $key ] ?? ''; } } return ''; }
function get_post( $p = null ) { return $p ? $p : $GLOBALS['__post']; }
function get_the_title( $p = null ) {
	if ( is_numeric( $p ) ) { $x = wc_get_product( $p ); return $x ? $x->get_name() : ''; }
	$p = $p ? $p : $GLOBALS['__post'];
	return $p instanceof WP_Post ? $p->post_title : $p->get_name();
}
function the_title() { echo esc_html( get_the_title() ); }
function has_excerpt( $p = null ) { $p = $p ? $p : $GLOBALS['__post']; return $p instanceof WP_Post && '' !== $p->post_excerpt; }
function get_the_excerpt( $p = null ) { $p = $p ? $p : $GLOBALS['__post']; return $p instanceof WP_Post ? $p->post_excerpt : ''; }
function has_post_thumbnail( $p = null ) { $p = $p ? $p : $GLOBALS['__post']; return $p instanceof WP_Post && $p->thumb; }
function get_the_post_thumbnail( $p, $size, $attr = array() ) { return wp_get_attachment_image( $p->thumb, $size, false, $attr ); }
function get_the_post_thumbnail_url( $p, $size ) { return wp_get_attachment_image_url( $p->thumb ); }
function get_the_ID() { $p = $GLOBALS['__post']; return $p instanceof WP_Post ? $p->ID : $p->get_id(); }
function get_the_content() { $p = $GLOBALS['__post']; return $p instanceof WP_Post ? $p->post_content : $p->get_description(); }
function the_content() {
	$c = get_the_content();
	$c = preg_replace( '/<!-- \/?wp:[^>]*-->\n?/', '', $c );
	echo do_shortcode( false === strpos( $c, '<' ) ? wpautop( $c ) : $c );
}
function post_class( $c = '' ) { echo 'class="' . esc_attr( trim( 'post ' . ( is_array( $c ) ? implode( ' ', $c ) : $c ) ) ) . '"'; }
function get_post_status() { return 'publish'; }

// Tautan.
function home_url( $path = '/' ) {
	$path = trim( $path, '/' );
	return '' === $path ? 'index.html' : $path . '.html';
}
function get_permalink( $p ) {
	if ( $p instanceof WP_Post ) { return $p->post_name . '.html'; }
	$x = wc_get_product( $p );
	if ( $x ) { $x = $x->parent ? wc_get_product( $x->parent ) : $x; return 'produk-' . $x->d['slug'] . '.html'; }
	return '#';
}
function wc_get_page_permalink( $p ) { return 'shop' === $p ? 'katalog.html' : '#'; }
function wc_get_checkout_url() { return 'terima-kasih.html'; }
function wc_get_cart_url() { return '#'; }
function get_post_type_archive_link() { return 'padu-padan.html'; }
function get_template_directory() { return YUMNIA_DIR; }
function get_template_directory_uri() { return YUMNIA_URI; }

/* =========================================================== konteks halaman */
$GLOBALS['__ctx'] = array();
function ctx( $k ) { return $GLOBALS['__ctx'][ $k ] ?? false; }
function is_front_page() { return ctx( 'front' ); }
function is_shop() { return ctx( 'shop' ); }
function is_search() { return false; }
function is_product_category() { return (bool) ctx( 'cat' ); }
function is_product_tag() { return false; }
function is_product() { return ctx( 'product' ); }
function is_singular( $t = null ) { return false; }
function is_cart() { return false; }
function is_checkout() { return false; }
function is_account_page() { return false; }
function is_wc_endpoint_url( $e = null ) { return ctx( 'received' ); }
function is_tax() { return false; }
function is_category() { return false; }
function get_queried_object() { return ctx( 'cat' ); }
function get_queried_object_id() { return 0; }
function get_search_query( $esc = true ) { return ''; }
function woocommerce_page_title( $echo = true ) { $t = ctx( 'cat' ) ? ctx( 'cat' )->name : 'Semua produk'; if ( $echo ) { echo $t; } return $t; }
function woocommerce_breadcrumb() {
	$parts = array( '<a href="index.html">Beranda</a>' );
	if ( ctx( 'cat' ) ) { $parts[] = '<a href="katalog.html">Toko</a>'; $parts[] = '<span>' . esc_html( ctx( 'cat' )->name ) . '</span>'; }
	elseif ( ctx( 'shop' ) ) { $parts[] = '<span>Toko</span>'; }
	elseif ( ctx( 'product' ) ) { $p = $GLOBALS['__post']; $c = $GLOBALS['__terms']['product_cat'][ $p->d['kategori'] ]; $parts[] = '<a href="' . get_term_link( $c ) . '">' . esc_html( $c->name ) . '</a>'; $parts[] = '<span>' . esc_html( $p->get_name() ) . '</span>'; }
	echo '<nav class="crumbs" aria-label="Navigasi">' . implode( '<span class="crumbs__sep" aria-hidden="true">/</span>', $parts ) . '</nav>';
}
function wc_print_notices() {}
function wc_get_template() {}
function woocommerce_product_loop() { return true; }
function wc_get_loop_prop( $k ) { return array( 'total' => count( $GLOBALS['__loop'] ), 'total_pages' => 1, 'current_page' => 1 )[ $k ] ?? 0; }
function paginate_links() { return ''; }
$GLOBALS['__loop'] = array();
$GLOBALS['__li']   = -1;
function have_posts() { return $GLOBALS['__li'] + 1 < count( $GLOBALS['__loop'] ); }
function the_post() { $GLOBALS['__li']++; $GLOBALS['__post'] = $GLOBALS['__loop'][ $GLOBALS['__li'] ]; }
function set_loop( $items ) { $GLOBALS['__loop'] = $items; $GLOBALS['__li'] = -1; $GLOBALS['wp_query'] = (object) array( 'found_posts' => count( $items ), 'max_num_pages' => 1 ); }
function get_query_var( $k ) { return 0; }

function get_theme_mod( $key, $default = '' ) {
	$mods = array(
		'yumnia_hero_image'    => att( 'hero.jpg' ),
		'yumnia_hero_image_hp' => att( 'hero-hp.jpg' ),
		'yumnia_story_image'   => att( 'pengrajin-1.jpg' ),
		'yumnia_wa_number'     => '6281234567890',
	);
	return $mods[ $key ] ?? $default;
}
function has_custom_logo() { return false; }
function has_nav_menu() { return false; }
function get_bloginfo( $k = 'name' ) { return array( 'name' => 'Yumnia', 'description' => 'For a better you', 'language' => 'id-ID', 'charset' => 'UTF-8' )[ $k ] ?? ''; }
function bloginfo( $k ) { echo esc_html( get_bloginfo( $k ) ); }
function language_attributes() { echo 'lang="id-ID"'; }
function body_class( $extra = '' ) { echo 'class="' . esc_attr( trim( ( ctx( 'front' ) ? 'home has-hero ' : '' ) . ( ctx( 'body' ) ?: '' ) ) ) . '"'; }
function wp_body_open() {}
function wp_head() {
	echo '<title>' . esc_html( ctx( 'title' ) ) . ' · Yumnia (pratinjau)</title>' . "\n";
	echo '<link rel="preload" href="' . YUMNIA_URI . '/assets/fonts/jost-latin-wght-normal.woff2" as="font" type="font/woff2" crossorigin>' . "\n";
	echo '<link rel="stylesheet" href="' . YUMNIA_URI . '/assets/css/main.css">' . "\n";
	echo '<link rel="stylesheet" href="preview.css">' . "\n";
}
function wp_footer() {
	$y = array(
		'home' => 'index.html', 'nonce' => 'preview', 'wcAjax' => '?wc-ajax=%%endpoint%%', 'storeApi' => 'store-api/products',
		'search' => 'katalog.html?s=', 'cart' => '#', 'checkout' => 'terima-kasih.html',
		'i18n' => array( 'added' => 'Masuk ke keranjang', 'addedSet' => 'Set masuk ke keranjang', 'error' => 'Maaf, terjadi kesalahan. Coba lagi.', 'chooseColor' => 'Pilih warna', 'chooseSize' => 'Pilih ukuran', 'choose' => 'Pilih varian dulu', 'pick' => 'Pilih varian', 'pickSet' => 'Ambil set ini', 'add' => 'Tambah ke keranjang', 'addAll' => 'Tambah semua ke keranjang', 'soldOut' => 'Habis', 'inStock' => 'Tersedia', 'lowStock' => 'Sisa %d', 'noResults' => 'Tidak ada produk yang cocok.', 'seeAll' => 'Lihat semua hasil' ),
	);
	echo '<div class="preview-flag">Pratinjau desain · data &amp; foto contoh</div>';
	echo '<script>window.YUMNIA=' . wp_json_encode( $y ) . ';</script>' . "\n";
	echo '<script src="preview-data.js"></script><script src="mock.js"></script>' . "\n";
	echo '<script src="' . YUMNIA_URI . '/assets/js/main.js" defer></script>' . "\n";
}
function get_header() { include YUMNIA_DIR . '/header.php'; }
function get_footer() { include YUMNIA_DIR . '/footer.php'; }
function get_template_part( $slug, $name = null, $args = array() ) { include YUMNIA_DIR . '/' . $slug . '.php'; }

/* =========================================================== keranjang & pesanan tiruan */
class FakeCart {
	public function is_empty() { return true; }
	public function get_cart_contents_count() { return 0; }
	public function get_cart() { return array(); }
}
function WC() { static $wc; if ( ! $wc ) { $wc = new stdClass(); $wc->cart = new FakeCart(); } return $wc; }
class WC_Order_Item_Product {
	public $p; public $q;
	public function __construct( $p, $q ) { $this->p = $p; $this->q = $q; }
	public function get_product() { return $this->p; }
	public function get_name() { return $this->p->get_name(); }
	public function get_quantity() { return $this->q; }
}
class WC_Order {
	public $items;
	public function __construct( $items ) { $this->items = $items; }
	public function get_id() { return 1042; }
	public function has_status( $s ) { return false; }
	public function get_payment_method() { return 'yumnia_whatsapp'; }
	public function get_order_key() { return 'wc_order_preview'; }
	public function get_order_number() { return '1042'; }
	public function get_items() { return $this->items; }
	public function get_line_subtotal( $item ) { return $item->p->get_price() * $item->q; }
	public function get_subtotal() { $t = 0; foreach ( $this->items as $i ) { $t += $this->get_line_subtotal( $i ); } return $t; }
	public function get_billing_first_name() { return 'Siti Aminah'; }
	public function get_billing_phone() { return '0812 3456 7890'; }
	public function get_billing_address_1() { return 'Jl. Raya Sukajadi No. 12, RT 02/RW 03, Tamansari, Kab. Bogor 16610'; }
	public function get_customer_note() { return 'Tolong dikirim sore ya.'; }
}

class WooCommerce {}

/* =========================================================== muat tema */
require YUMNIA_DIR . '/inc/helpers.php';
require YUMNIA_DIR . '/inc/setup.php';
require YUMNIA_DIR . '/inc/padu-padan.php';
require YUMNIA_DIR . '/inc/shortcodes.php';
require YUMNIA_DIR . '/inc/woocommerce.php';
require YUMNIA_DIR . '/inc/product-fields.php';
require YUMNIA_DIR . '/inc/cart.php';
require YUMNIA_DIR . '/inc/checkout-whatsapp.php';

/* =========================================================== render */
function render( $out, $ctx, $fn ) {
	$GLOBALS['__ctx'] = $ctx;
	ob_start();
	$fn();
	$html = ob_get_clean();
	file_put_contents( __DIR__ . '/' . $out, $html );
	echo "  {$out} (" . round( strlen( $html ) / 1024 ) . " KB)\n";
}
$T = YUMNIA_DIR;

render( 'index.html', array( 'front' => true, 'title' => 'Beranda' ), function () use ( $T ) { include $T . '/front-page.php'; } );

render( 'katalog.html', array( 'shop' => true, 'title' => 'Semua produk' ), function () use ( $T ) { set_loop( parents() ); include $T . '/woocommerce/archive-product.php'; } );
foreach ( $GLOBALS['__terms']['product_cat'] as $cat ) {
	render( 'katalog-' . $cat->slug . '.html', array( 'cat' => $cat, 'title' => $cat->name ), function () use ( $T, $cat ) {
		set_loop( array_values( array_filter( parents(), function ( $p ) use ( $cat ) { return $p->d['kategori'] === $cat->slug; } ) ) );
		include $T . '/woocommerce/archive-product.php';
	} );
}
foreach ( parents() as $p ) {
	render( 'produk-' . $p->d['slug'] . '.html', array( 'product' => true, 'title' => $p->get_name(), 'body' => 'single-product' ), function () use ( $T, $p ) { set_loop( array( $p ) ); include $T . '/woocommerce/single-product.php'; } );
}
render( 'padu-padan.html', array( 'title' => 'Padu-padan' ), function () use ( $T ) { set_loop( $GLOBALS['__sets'] ); include $T . '/archive-yumnia_set.php'; } );
foreach ( $GLOBALS['__pages'] as $slug => $page ) {
	render( $slug . '.html', array( 'title' => $page->post_title ), function () use ( $T, $page ) { set_loop( array( $page ) ); include $T . '/page.php'; } );
}
render( 'terima-kasih.html', array( 'received' => true, 'title' => 'Pesanan tercatat' ), function () use ( $T ) {
	$v1    = wc_get_product( 100 )->vars[0];
	$v2    = wc_get_product( $GLOBALS['__slug2id']['sandal-hak-balok'] )->vars[2];
	$order = new WC_Order( array( new WC_Order_Item_Product( $v1, 1 ), new WC_Order_Item_Product( $v2, 1 ) ) );
	get_header();
	echo '<div class="container shop-page">';
	include $T . '/woocommerce/checkout/thankyou.php';
	echo '</div>';
	get_footer();
} );
render( '404.html', array( 'title' => 'Tidak ditemukan' ), function () use ( $T ) { include $T . '/404.php'; } );

/* =========================================================== data untuk tiruan keranjang & pencarian */
$items = array();
foreach ( $GLOBALS['__products'] as $id => $p ) {
	$parent = $p->parent ? wc_get_product( $p->parent ) : $p;
	$items[ $id ] = array(
		'name'  => $parent->get_name(),
		'meta'  => $p->parent ? str_replace( ', ', ' · ', wc_get_formatted_variation( $p ) ) : '',
		'price' => $p->get_price(),
		'img'   => wp_get_attachment_image_url( $p->get_image_id() ),
		'max'   => $p->parent ? $p->stock : 99,
		'url'   => get_permalink( $parent->get_id() ),
	);
}
$search = array_map( function ( $p ) {
	return array( 'name' => $p->get_name(), 'permalink' => get_permalink( $p->get_id() ), 'prices' => array( 'price' => (string) $p->get_price(), 'currency_minor_unit' => 0 ), 'images' => array( array( 'src' => wp_get_attachment_image_url( $p->get_image_id() ) ) ) );
}, parents() );
file_put_contents( __DIR__ . '/preview-data.js', 'window.PREVIEW=' . wp_json_encode( array( 'items' => $items, 'search' => $search ) ) . ';' );
echo "  preview-data.js\n";
