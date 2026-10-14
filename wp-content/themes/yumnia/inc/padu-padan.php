<?php
/**
 * Padu-padan: set hijab + sandal yang serasi.
 * Dikelola di menu admin "Padu-padan": judul, foto look, dan produk + warna di dalam set.
 *
 * @package Yumnia
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'init',
	function () {
		register_post_type(
			'yumnia_set',
			array(
				'labels'       => array(
					'name'               => __( 'Padu-padan', 'yumnia' ),
					'singular_name'      => __( 'Set padu-padan', 'yumnia' ),
					'add_new'            => __( 'Tambah set', 'yumnia' ),
					'add_new_item'       => __( 'Tambah set padu-padan', 'yumnia' ),
					'edit_item'          => __( 'Ubah set padu-padan', 'yumnia' ),
					'all_items'          => __( 'Semua set', 'yumnia' ),
					'not_found'          => __( 'Belum ada set.', 'yumnia' ),
					'featured_image'     => __( 'Foto look', 'yumnia' ),
					'set_featured_image' => __( 'Pilih foto look', 'yumnia' ),
				),
				'public'       => true,
				'has_archive'  => 'padu-padan',
				'rewrite'      => array( 'slug' => 'padu-padan', 'with_front' => false ),
				'menu_icon'    => 'dashicons-heart',
				'menu_position' => 56,
				'supports'     => array( 'title', 'excerpt', 'thumbnail', 'page-attributes' ),
				'show_in_rest' => true,
			)
		);
	}
);

/**
 * Isi set: [ ['product' => id, 'warna' => slug], ... ].
 *
 * @param int $post_id ID set.
 * @return array
 */
function yumnia_set_raw_items( $post_id ) {
	$items = get_post_meta( $post_id, '_yumnia_set_items', true );
	return is_array( $items ) ? $items : array();
}

/**
 * Isi set yang siap ditampilkan.
 *
 * @param int $post_id ID set.
 * @return array<int,array{product:WC_Product,warna:string,image:string}>
 */
function yumnia_set_items( $post_id ) {
	if ( ! function_exists( 'wc_get_product' ) ) {
		return array();
	}
	$out = array();
	foreach ( yumnia_set_raw_items( $post_id ) as $row ) {
		$product = wc_get_product( absint( $row['product'] ?? 0 ) );
		if ( ! $product || 'publish' !== $product->get_status() ) {
			continue;
		}
		$warna = sanitize_title( $row['warna'] ?? '' );
		$out[] = array(
			'product' => $product,
			'warna'   => $warna,
			'image'   => yumnia_product_image_for_color( $product, $warna, 'yumnia-card' ),
		);
	}
	return $out;
}

/* ---------------------------------------------------------------- admin */

add_action(
	'add_meta_boxes',
	function () {
		add_meta_box( 'yumnia_set_items', __( 'Produk dalam set', 'yumnia' ), 'yumnia_set_metabox', 'yumnia_set', 'normal', 'high' );
	}
);

/**
 * Kotak isian produk dalam set.
 *
 * @param WP_Post $post Set.
 */
function yumnia_set_metabox( $post ) {
	wp_nonce_field( 'yumnia_set_save', 'yumnia_set_nonce' );
	$items    = yumnia_set_raw_items( $post->ID );
	$products = function_exists( 'wc_get_products' ) ? wc_get_products(
		array(
			'status'  => 'publish',
			'limit'   => 200,
			'orderby' => 'title',
			'order'   => 'ASC',
		)
	) : array();
	$colors   = taxonomy_exists( 'pa_warna' ) ? get_terms( array( 'taxonomy' => 'pa_warna', 'hide_empty' => false ) ) : array();

	echo '<p>' . esc_html__( 'Pilih 2–4 produk yang serasi. Warna menentukan foto dan varian yang dipilihkan untuk pembeli.', 'yumnia' ) . '</p>';
	echo '<table class="widefat striped"><thead><tr><th>' . esc_html__( 'Produk', 'yumnia' ) . '</th><th>' . esc_html__( 'Warna', 'yumnia' ) . '</th></tr></thead><tbody>';
	for ( $i = 0; $i < 4; $i++ ) {
		$current = $items[ $i ] ?? array( 'product' => 0, 'warna' => '' );
		echo '<tr><td><select name="yumnia_set_items[' . (int) $i . '][product]" style="width:100%">';
		echo '<option value="">' . esc_html__( '(kosong)', 'yumnia' ) . '</option>';
		foreach ( $products as $p ) {
			printf( '<option value="%d" %s>%s</option>', (int) $p->get_id(), selected( (int) $current['product'], $p->get_id(), false ), esc_html( $p->get_name() ) );
		}
		echo '</select></td><td><select name="yumnia_set_items[' . (int) $i . '][warna]" style="width:100%">';
		echo '<option value="">' . esc_html__( '(bebas)', 'yumnia' ) . '</option>';
		if ( ! is_wp_error( $colors ) ) {
			foreach ( $colors as $c ) {
				printf( '<option value="%s" %s>%s</option>', esc_attr( $c->slug ), selected( $current['warna'], $c->slug, false ), esc_html( $c->name ) );
			}
		}
		echo '</select></td></tr>';
	}
	echo '</tbody></table>';
}

add_action(
	'save_post_yumnia_set',
	function ( $post_id ) {
		if ( ! isset( $_POST['yumnia_set_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['yumnia_set_nonce'] ) ), 'yumnia_set_save' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		$rows  = isset( $_POST['yumnia_set_items'] ) ? (array) wp_unslash( $_POST['yumnia_set_items'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$clean = array();
		foreach ( $rows as $row ) {
			$pid = absint( $row['product'] ?? 0 );
			if ( $pid ) {
				$clean[] = array(
					'product' => $pid,
					'warna'   => sanitize_title( $row['warna'] ?? '' ),
				);
			}
		}
		update_post_meta( $post_id, '_yumnia_set_items', $clean );
	}
);

/**
 * Tampilkan semua set di halaman arsip (tanpa paginasi, urut sesuai "Urutan").
 */
add_action(
	'pre_get_posts',
	function ( $q ) {
		if ( ! is_admin() && $q->is_main_query() && $q->is_post_type_archive( 'yumnia_set' ) ) {
			$q->set( 'posts_per_page', 24 );
			$q->set( 'orderby', array( 'menu_order' => 'ASC', 'date' => 'DESC' ) );
		}
	}
);

/**
 * Halaman tunggal set dialihkan ke arsip padu-padan (semua set tampil di sana).
 */
add_action(
	'template_redirect',
	function () {
		if ( is_singular( 'yumnia_set' ) ) {
			wp_safe_redirect( get_post_type_archive_link( 'yumnia_set' ) . '#set-' . get_queried_object_id(), 301 );
			exit;
		}
	}
);

/**
 * Set tidak punya halaman sendiri, jadi keluarkan dari sitemap.
 */
add_filter(
	'wp_sitemaps_post_types',
	function ( $types ) {
		unset( $types['yumnia_set'] );
		return $types;
	}
);
