<?php
/**
 * Integrasi WooCommerce: katalog, kartu produk, warna, label, dan data varian untuk JavaScript.
 *
 * @package Yumnia
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	function () {
		add_theme_support(
			'woocommerce',
			array(
				'thumbnail_image_width' => 640,
				'single_image_width'    => 1200,
				'product_grid'          => array(
					'default_columns' => 4,
					'default_rows'    => 3,
				),
			)
		);
	}
);

// Semua tampilan diatur oleh tema.
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

// Katalog: 12 produk per halaman, urutan bawaan "Terbaru".
add_filter(
	'loop_shop_per_page',
	function () {
		return 12;
	}
);
add_filter(
	'woocommerce_default_catalog_orderby',
	function () {
		return 'date';
	}
);
add_filter(
	'woocommerce_catalog_orderby',
	function () {
		return array(
			'date'       => __( 'Terbaru', 'yumnia' ),
			'popularity' => __( 'Terlaris', 'yumnia' ),
			'price'      => __( 'Harga terendah', 'yumnia' ),
			'price-desc' => __( 'Harga tertinggi', 'yumnia' ),
		);
	}
);

// Breadcrumb ringkas.
add_filter(
	'woocommerce_breadcrumb_defaults',
	function ( $args ) {
		$args['delimiter']   = '<span class="crumbs__sep" aria-hidden="true">/</span>';
		$args['wrap_before'] = '<nav class="crumbs" aria-label="' . esc_attr__( 'Navigasi', 'yumnia' ) . '">';
		$args['wrap_after']  = '</nav>';
		$args['home']        = __( 'Beranda', 'yumnia' );
		return $args;
	}
);

// Kupon & produk terkait bawaan tidak dipakai di keranjang.
remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );

/* ---------------------------------------------------------------- warna */

/**
 * Warna cadangan bila admin belum mengisi kode warna di atribut.
 */
function yumnia_color_fallbacks() {
	return array(
		'maroon' => '#7B1E3A',
		'blush'  => '#E6B4B0',
		'pink'   => '#E6B4B0',
		'mocca'  => '#A47E66',
		'coklat' => '#7A5240',
		'sage'   => '#9AAA8F',
		'hijau'  => '#6F8B6A',
		'hitam'  => '#2B2426',
		'putih'  => '#FFFFFF',
		'krem'   => '#E6D7BF',
		'cream'  => '#E6D7BF',
		'lilac'  => '#B5A1BC',
		'ungu'   => '#8E6F9A',
		'navy'   => '#2F3A57',
		'biru'   => '#4F6D8F',
		'abu'    => '#9C9696',
		'nude'   => '#D9B9A0',
	);
}

/**
 * Kode warna untuk term atribut warna.
 *
 * @param WP_Term|string $term Term atau slug.
 * @return string
 */
function yumnia_color_hex( $term ) {
	if ( is_string( $term ) ) {
		$found = taxonomy_exists( 'pa_warna' ) ? get_term_by( 'slug', $term, 'pa_warna' ) : false;
		$slug  = $term;
		$term  = $found;
	} else {
		$slug = $term->slug;
	}
	if ( $term instanceof WP_Term ) {
		$hex = get_term_meta( $term->term_id, 'yumnia_hex', true );
		if ( $hex ) {
			return $hex;
		}
	}
	$map = yumnia_color_fallbacks();
	foreach ( $map as $key => $hex ) {
		if ( false !== strpos( (string) $slug, $key ) ) {
			return $hex;
		}
	}
	return '#D9C7C2';
}

// Isian kode warna di Produk → Atribut → Warna.
add_action(
	'pa_warna_add_form_fields',
	function () {
		echo '<div class="form-field"><label for="yumnia_hex">' . esc_html__( 'Kode warna', 'yumnia' ) . '</label><input type="color" name="yumnia_hex" id="yumnia_hex" value="#7B1E3A"><p>' . esc_html__( 'Dipakai untuk bulatan warna di toko.', 'yumnia' ) . '</p></div>';
	}
);
add_action(
	'pa_warna_edit_form_fields',
	function ( $term ) {
		$hex = get_term_meta( $term->term_id, 'yumnia_hex', true );
		echo '<tr class="form-field"><th><label for="yumnia_hex">' . esc_html__( 'Kode warna', 'yumnia' ) . '</label></th><td><input type="color" name="yumnia_hex" id="yumnia_hex" value="' . esc_attr( $hex ? $hex : yumnia_color_hex( $term ) ) . '"><p class="description">' . esc_html__( 'Dipakai untuk bulatan warna di toko.', 'yumnia' ) . '</p></td></tr>';
	}
);
$yumnia_save_hex = function ( $term_id ) {
	if ( isset( $_POST['yumnia_hex'] ) && current_user_can( 'manage_product_terms' ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce diperiksa oleh layar term WordPress.
		$hex = sanitize_hex_color( wp_unslash( $_POST['yumnia_hex'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( $hex ) {
			update_term_meta( $term_id, 'yumnia_hex', $hex );
		}
	}
};
add_action( 'created_pa_warna', $yumnia_save_hex );
add_action( 'edited_pa_warna', $yumnia_save_hex );

/* ---------------------------------------------------------------- produk */

/**
 * Produk ditandai "Baru" oleh admin.
 *
 * @param WC_Product $product Produk.
 */
function yumnia_is_new( $product ) {
	return 'yes' === $product->get_meta( '_yumnia_baru' );
}

/**
 * Label Baru / Habis.
 *
 * @param WC_Product $product Produk.
 * @return string
 */
function yumnia_badges( $product ) {
	$out = '';
	if ( ! $product->is_in_stock() ) {
		$out .= '<span class="badge badge--soldout">' . esc_html__( 'Habis', 'yumnia' ) . '</span>';
	} elseif ( yumnia_is_new( $product ) ) {
		$out .= '<span class="badge badge--new">' . esc_html__( 'Baru', 'yumnia' ) . '</span>';
	}
	return $out ? '<div class="badges">' . $out . '</div>' : '';
}

/**
 * Warna yang tersedia pada produk.
 *
 * @param WC_Product $product Produk.
 * @return array<int,array{slug:string,name:string,hex:string}>
 */
function yumnia_product_colors( $product ) {
	$colors = array();
	if ( ! taxonomy_exists( 'pa_warna' ) ) {
		return $colors;
	}
	$terms = wc_get_product_terms( $product->get_id(), 'pa_warna', array( 'fields' => 'all' ) );
	foreach ( $terms as $term ) {
		$colors[] = array(
			'slug' => $term->slug,
			'name' => $term->name,
			'hex'  => yumnia_color_hex( $term ),
		);
	}
	return $colors;
}

/**
 * Varian yang aktif.
 *
 * @param WC_Product $product Produk.
 * @return WC_Product_Variation[]
 */
function yumnia_variations( $product ) {
	static $cache = array();
	$id = $product->get_id();
	if ( isset( $cache[ $id ] ) ) {
		return $cache[ $id ];
	}
	$list = array();
	if ( $product->is_type( 'variable' ) ) {
		foreach ( $product->get_children() as $child_id ) {
			$v = wc_get_product( $child_id );
			if ( $v && $v->exists() && 'publish' === $v->get_status() ) {
				$list[] = $v;
			}
		}
	}
	$cache[ $id ] = $list;
	return $list;
}

/**
 * Foto produk untuk warna tertentu (foto varian bila ada).
 *
 * @param WC_Product $product Produk.
 * @param string     $warna   Slug warna.
 * @param string     $size    Ukuran gambar.
 * @return string URL.
 */
function yumnia_product_image_for_color( $product, $warna, $size = 'yumnia-card' ) {
	if ( $warna ) {
		foreach ( yumnia_variations( $product ) as $v ) {
			$attrs = $v->get_attributes();
			if ( isset( $attrs['pa_warna'] ) && $attrs['pa_warna'] === $warna && $v->get_image_id() && $v->get_image_id() !== $product->get_image_id() ) {
				return (string) wp_get_attachment_image_url( $v->get_image_id(), $size );
			}
		}
	}
	$url = wp_get_attachment_image_url( $product->get_image_id(), $size );
	return $url ? $url : wc_placeholder_img_src( $size );
}

/**
 * Foto kedua (foto saat dipakai) = foto pertama di galeri produk.
 *
 * @param WC_Product $product Produk.
 * @param string     $size    Ukuran gambar.
 * @return int ID lampiran atau 0.
 */
function yumnia_secondary_image_id( $product ) {
	$gallery = $product->get_gallery_image_ids();
	return $gallery ? (int) $gallery[0] : 0;
}

/**
 * Data produk untuk JavaScript (pilih varian, tambah cepat, padu-padan).
 *
 * @param WC_Product $product Produk.
 * @return array
 */
function yumnia_product_payload( $product ) {
	$attrs = array();
	if ( $product->is_type( 'variable' ) ) {
		foreach ( $product->get_variation_attributes() as $taxonomy => $values ) {
			$options = array();
			if ( taxonomy_exists( $taxonomy ) ) {
				$terms = wc_get_product_terms( $product->get_id(), $taxonomy, array( 'fields' => 'all' ) );
				foreach ( $terms as $term ) {
					if ( ! in_array( $term->slug, $values, true ) ) {
						continue;
					}
					$opt = array(
						's' => $term->slug,
						'n' => $term->name,
					);
					if ( 'pa_warna' === $taxonomy ) {
						$opt['h'] = yumnia_color_hex( $term );
					}
					$options[] = $opt;
				}
			} else {
				foreach ( $values as $value ) {
					$options[] = array( 's' => $value, 'n' => $value );
				}
			}
			$attrs[] = array(
				'k' => 'attribute_' . sanitize_title( $taxonomy ),
				't' => $taxonomy,
				'l' => wc_attribute_label( $taxonomy, $product ),
				'o' => $options,
			);
		}
	}

	$vars = array();
	foreach ( yumnia_variations( $product ) as $v ) {
		$a = array();
		foreach ( $v->get_attributes() as $tax => $val ) {
			$a[ 'attribute_' . sanitize_title( $tax ) ] = $val;
		}
		$img = $v->get_image_id() && $v->get_image_id() !== $product->get_image_id() ? wp_get_attachment_image_url( $v->get_image_id(), 'yumnia-card' ) : '';
		$big = $img ? wp_get_attachment_image_url( $v->get_image_id(), 'yumnia-large' ) : '';
		$vars[] = array(
			'id'  => $v->get_id(),
			'a'   => $a,
			'p'   => (float) $v->get_price(),
			's'   => $v->is_in_stock(),
			'q'   => $v->managing_stock() ? (int) $v->get_stock_quantity() : null,
			'img' => $img ? $img : '',
			'big' => $big ? $big : '',
			'gi'  => $v->get_image_id() ? (int) $v->get_image_id() : 0,
		);
	}

	return array(
		'id'    => $product->get_id(),
		'name'  => $product->get_name(),
		'url'   => get_permalink( $product->get_id() ),
		'type'  => $product->get_type(),
		'price' => (float) $product->get_price(),
		'img'   => yumnia_product_image_for_color( $product, '', 'yumnia-card' ),
		'stock' => $product->is_in_stock(),
		'q'     => $product->managing_stock() ? (int) $product->get_stock_quantity() : null,
		'attrs' => $attrs,
		'vars'  => $vars,
	);
}

/**
 * Atribut data-product untuk elemen HTML.
 *
 * @param WC_Product $product Produk.
 * @return string
 */
function yumnia_payload_attr( $product ) {
	return 'data-product="' . esc_attr( wp_json_encode( yumnia_product_payload( $product ) ) ) . '"';
}

/**
 * Produk unggulan (bintang di daftar produk) untuk "Koleksi peluncuran";
 * jika belum ada yang ditandai, pakai produk terbaru.
 *
 * @param int $limit Jumlah.
 * @return WC_Product[]
 */
function yumnia_launch_products( $limit = 8 ) {
	$args     = array(
		'status'     => 'publish',
		'limit'      => $limit,
		'orderby'    => 'date',
		'order'      => 'DESC',
		'visibility' => 'catalog',
	);
	$featured = wc_get_products( $args + array( 'featured' => true ) );
	return $featured ? $featured : wc_get_products( $args );
}

/* ---------------------------------------------------------------- filter katalog */

/**
 * Pilihan rentang harga untuk filter.
 */
function yumnia_price_ranges() {
	return array(
		array( 'min' => '', 'max' => 75000, 'label' => __( 'Di bawah Rp75rb', 'yumnia' ) ),
		array( 'min' => 75000, 'max' => 125000, 'label' => __( 'Rp75rb – Rp125rb', 'yumnia' ) ),
		array( 'min' => 125000, 'max' => 175000, 'label' => __( 'Rp125rb – Rp175rb', 'yumnia' ) ),
		array( 'min' => 175000, 'max' => '', 'label' => __( 'Di atas Rp175rb', 'yumnia' ) ),
	);
}

/**
 * Nilai filter yang sedang aktif dari URL.
 *
 * @param string $key Kunci (warna/ukuran).
 * @return string[]
 */
function yumnia_active_filter( $key ) {
	$raw = isset( $_GET[ 'filter_' . $key ] ) ? sanitize_text_field( wp_unslash( $_GET[ 'filter_' . $key ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	return array_filter( array_map( 'sanitize_title', explode( ',', $raw ) ) );
}

/**
 * Jumlah filter aktif (untuk lencana tombol Filter).
 */
function yumnia_active_filter_count() {
	$n = count( yumnia_active_filter( 'warna' ) ) + count( yumnia_active_filter( 'ukuran' ) );
	if ( isset( $_GET['min_price'] ) || isset( $_GET['max_price'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		++$n;
	}
	return $n;
}

/**
 * URL katalog saat ini tanpa parameter filter.
 */
function yumnia_catalog_base_url() {
	if ( is_product_category() || is_product_tag() ) {
		$link = get_term_link( get_queried_object() );
		return is_wp_error( $link ) ? wc_get_page_permalink( 'shop' ) : $link;
	}
	return wc_get_page_permalink( 'shop' );
}

/**
 * Filter katalog tanpa JavaScript: ubah checkbox (filter_warna[]=a&filter_warna[]=b)
 * dan pilihan harga (harga=1) menjadi format yang dipahami WooCommerce.
 */
add_action(
	'init',
	function () {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		foreach ( array( 'warna', 'ukuran' ) as $key ) {
			$param = 'filter_' . $key;
			if ( isset( $_GET[ $param ] ) && is_array( $_GET[ $param ] ) ) {
				$_GET[ $param ] = implode( ',', array_filter( array_map( 'sanitize_title', wp_unslash( $_GET[ $param ] ) ) ) );
			}
			if ( isset( $_GET[ $param ] ) && '' === $_GET[ $param ] ) {
				unset( $_GET[ $param ] );
			}
			if ( ! empty( $_GET[ $param ] ) ) {
				$_GET[ 'query_type_' . $key ] = 'or';
			}
		}
		if ( isset( $_GET['harga'] ) && '' !== $_GET['harga'] ) {
			$ranges = yumnia_price_ranges();
			$range  = $ranges[ absint( $_GET['harga'] ) ] ?? null;
			if ( $range ) {
				if ( '' !== $range['min'] ) {
					$_GET['min_price'] = $range['min'];
				}
				if ( '' !== $range['max'] ) {
					$_GET['max_price'] = $range['max'];
				}
			}
		}
		// phpcs:enable
	},
	0
);

/**
 * URL katalog dengan filter tertentu.
 *
 * @param array $override Nilai yang diganti (null = hapus).
 * @return string
 */
function yumnia_filter_url( $override = array() ) {
	// phpcs:disable WordPress.Security.NonceVerification.Recommended
	$args = array(
		'filter_warna'  => implode( ',', yumnia_active_filter( 'warna' ) ),
		'filter_ukuran' => implode( ',', yumnia_active_filter( 'ukuran' ) ),
		'harga'         => isset( $_GET['harga'] ) ? absint( $_GET['harga'] ) : '',
		'orderby'       => isset( $_GET['orderby'] ) ? sanitize_key( $_GET['orderby'] ) : '',
	);
	if ( is_search() ) {
		$args['s']         = get_search_query( false );
		$args['post_type'] = 'product';
	}
	// phpcs:enable
	$args = array_merge( $args, $override );
	$args = array_filter(
		$args,
		function ( $v ) {
			return null !== $v && '' !== $v;
		}
	);
	return add_query_arg( array_map( 'rawurlencode', $args ), is_search() ? home_url( '/' ) : yumnia_catalog_base_url() );
}
