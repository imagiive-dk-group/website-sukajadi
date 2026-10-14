<?php
/**
 * Tab "Yumnia" di halaman edit produk: label Baru, pembuat, bahan, ukuran, cerita nama, dan tautan marketplace.
 *
 * @package Yumnia
 */

defined( 'ABSPATH' ) || exit;

/**
 * Daftar isian tambahan produk.
 */
function yumnia_product_fields() {
	return array(
		'_yumnia_baru'         => array( 'type' => 'checkbox', 'label' => __( 'Tandai sebagai Baru', 'yumnia' ), 'desc' => __( 'Menampilkan label "Baru" di kartu produk.', 'yumnia' ) ),
		'_yumnia_pembuat'      => array( 'type' => 'text', 'label' => __( 'Dibuat oleh', 'yumnia' ), 'desc' => __( 'Satu kalimat tentang pembuatnya. Contoh: Dijahit oleh Ibu Siti, UMKM konveksi Sukajadi.', 'yumnia' ) ),
		'_yumnia_bahan'        => array( 'type' => 'textarea', 'label' => __( 'Bahan & perawatan', 'yumnia' ), 'desc' => '' ),
		'_yumnia_ukuran_info'  => array( 'type' => 'text', 'label' => __( 'Ukuran (hijab)', 'yumnia' ), 'desc' => __( 'Contoh: 75 × 180 cm', 'yumnia' ) ),
		'_yumnia_tabel_ukuran' => array( 'type' => 'textarea', 'label' => __( 'Tabel ukuran (sandal)', 'yumnia' ), 'desc' => __( 'Satu baris per ukuran, pisahkan dengan tanda | . Contoh: 38 | 24,5 cm', 'yumnia' ) ),
		'_yumnia_tinggi_hak'   => array( 'type' => 'text', 'label' => __( 'Tinggi hak (sandal)', 'yumnia' ), 'desc' => __( 'Contoh: 5 cm (balok)', 'yumnia' ) ),
		'_yumnia_jenis_sol'    => array( 'type' => 'text', 'label' => __( 'Jenis sol (sandal)', 'yumnia' ), 'desc' => __( 'Contoh: Karet anti-slip', 'yumnia' ) ),
		'_yumnia_cerita_nama'  => array( 'type' => 'textarea', 'label' => __( 'Cerita nama model', 'yumnia' ), 'desc' => __( 'Singkat, 1–2 kalimat tentang tempat di Sukajadi yang jadi nama model.', 'yumnia' ) ),
		'_yumnia_shopee'       => array( 'type' => 'url', 'label' => __( 'Tautan Shopee', 'yumnia' ), 'desc' => '' ),
		'_yumnia_tokopedia'    => array( 'type' => 'url', 'label' => __( 'Tautan Tokopedia', 'yumnia' ), 'desc' => '' ),
	);
}

add_filter(
	'woocommerce_product_data_tabs',
	function ( $tabs ) {
		$tabs['yumnia'] = array(
			'label'    => __( 'Yumnia', 'yumnia' ),
			'target'   => 'yumnia_product_data',
			'priority' => 15,
		);
		return $tabs;
	}
);

add_action(
	'woocommerce_product_data_panels',
	function () {
		global $post;
		echo '<div id="yumnia_product_data" class="panel woocommerce_options_panel hidden"><div class="options_group">';
		foreach ( yumnia_product_fields() as $key => $field ) {
			$value = get_post_meta( $post->ID, $key, true );
			$args  = array(
				'id'          => $key,
				'label'       => $field['label'],
				'description' => $field['desc'],
				'desc_tip'    => false,
				'value'       => $value,
			);
			if ( 'checkbox' === $field['type'] ) {
				woocommerce_wp_checkbox( $args );
			} elseif ( 'textarea' === $field['type'] ) {
				woocommerce_wp_textarea_input( $args + array( 'rows' => 4 ) );
			} else {
				woocommerce_wp_text_input( $args + array( 'type' => $field['type'] ) );
			}
		}
		echo '<p class="form-field"><em>' . esc_html__( 'Tips: foto pertama di "Galeri produk" dipakai sebagai foto saat produk dipakai (muncul saat kartu produk disentuh). Produk pasangan untuk "Cocok dengan…" diatur di tab Produk tertaut → Cross-sells.', 'yumnia' ) . '</em></p>';
		echo '</div></div>';
	}
);

add_action(
	'woocommerce_admin_process_product_object',
	function ( WC_Product $product ) {
		// Nonce sudah diverifikasi oleh WooCommerce sebelum hook ini dijalankan.
		foreach ( yumnia_product_fields() as $key => $field ) {
			if ( 'checkbox' === $field['type'] ) {
				$product->update_meta_data( $key, isset( $_POST[ $key ] ) ? 'yes' : 'no' ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
				continue;
			}
			$raw = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			if ( 'url' === $field['type'] ) {
				$value = esc_url_raw( $raw );
			} elseif ( 'textarea' === $field['type'] ) {
				$value = sanitize_textarea_field( $raw );
			} else {
				$value = sanitize_text_field( $raw );
			}
			$product->update_meta_data( $key, $value );
		}
	}
);

/**
 * Tabel ukuran sandal: [['36', '23 cm'], ...].
 *
 * @param WC_Product $product Produk.
 * @return array
 */
function yumnia_size_table( $product ) {
	$rows = array();
	foreach ( preg_split( '/\r\n|\r|\n/', (string) $product->get_meta( '_yumnia_tabel_ukuran' ) ) as $line ) {
		$parts = array_map( 'trim', explode( '|', $line ) );
		if ( count( $parts ) >= 2 && '' !== $parts[0] ) {
			$rows[] = array( $parts[0], $parts[1] );
		}
	}
	return $rows;
}
