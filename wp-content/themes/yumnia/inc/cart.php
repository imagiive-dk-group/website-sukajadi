<?php
/**
 * Keranjang geser (drawer) dan endpoint AJAX: tambah (satu produk atau satu set), ubah jumlah, hapus.
 *
 * @package Yumnia
 */

defined( 'ABSPATH' ) || exit;

/**
 * Jumlah barang di keranjang.
 */
function yumnia_cart_count() {
	return WC()->cart ? (int) WC()->cart->get_cart_contents_count() : 0;
}

/**
 * Isi drawer keranjang.
 *
 * @return string HTML.
 */
function yumnia_cart_drawer_content() {
	ob_start();
	$cart = WC()->cart;
	echo '<div class="drawer__content js-drawer-content">';
	if ( ! $cart || $cart->is_empty() ) {
		?>
		<div class="drawer__empty">
			<?php echo yumnia_monogram( 'drawer__empty-mark' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<p class="drawer__empty-title"><?php esc_html_e( 'Keranjangmu masih kosong', 'yumnia' ); ?></p>
			<p class="drawer__empty-text"><?php esc_html_e( 'Yuk, lihat koleksi hijab dan sandal dari Sukajadi.', 'yumnia' ); ?></p>
			<a class="btn" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><span class="btn__label"><?php esc_html_e( 'Mulai belanja', 'yumnia' ); ?></span></a>
		</div>
		<?php
	} else {
		echo '<ul class="mini-cart">';
		$i = 0;
		foreach ( $cart->get_cart() as $key => $item ) {
			$product = $item['data'];
			if ( ! $product || ! $product->exists() || $item['quantity'] <= 0 ) {
				continue;
			}
			$parent = $product->is_type( 'variation' ) ? wc_get_product( $product->get_parent_id() ) : $product;
			$name   = $parent ? $parent->get_name() : $product->get_name();
			$meta   = $product->is_type( 'variation' ) ? wc_get_formatted_variation( $product, true, false, false ) : '';
			$max    = $product->managing_stock() && ! $product->backorders_allowed() ? (int) $product->get_stock_quantity() : 99;
			$link   = $product->is_visible() ? $product->get_permalink( $item ) : '';
			?>
			<li class="mini-item" data-key="<?php echo esc_attr( $key ); ?>" style="--i:<?php echo (int) $i++; ?>">
				<a class="mini-item__img" href="<?php echo esc_url( $link ); ?>" tabindex="-1">
					<?php echo $product->get_image( 'yumnia-card', array( 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
				<div class="mini-item__info">
					<a class="mini-item__name" href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $name ); ?></a>
					<?php if ( $meta ) : ?>
						<p class="mini-item__meta"><?php echo esc_html( str_replace( ', ', ' · ', $meta ) ); ?></p>
					<?php endif; ?>
					<div class="mini-item__row">
						<div class="qty" data-key="<?php echo esc_attr( $key ); ?>" data-max="<?php echo (int) $max; ?>">
							<button type="button" class="qty__btn" data-step="-1" aria-label="<?php esc_attr_e( 'Kurangi', 'yumnia' ); ?>"><?php echo yumnia_icon( 'minus' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
							<span class="qty__val" aria-live="polite"><?php echo (int) $item['quantity']; ?></span>
							<button type="button" class="qty__btn" data-step="1" aria-label="<?php esc_attr_e( 'Tambah', 'yumnia' ); ?>" <?php disabled( $item['quantity'] >= $max ); ?>><?php echo yumnia_icon( 'plus' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
						</div>
						<span class="mini-item__price"><?php echo wp_kses_post( $cart->get_product_subtotal( $product, $item['quantity'] ) ); ?></span>
					</div>
				</div>
				<button type="button" class="mini-item__remove" data-remove="<?php echo esc_attr( $key ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Hapus %s', 'yumnia' ), $name ) ); ?>"><?php echo yumnia_icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
			</li>
			<?php
		}
		echo '</ul>';
		?>
		<div class="drawer__foot">
			<div class="drawer__total">
				<span><?php esc_html_e( 'Subtotal', 'yumnia' ); ?></span>
				<strong><?php echo wp_kses_post( $cart->get_cart_subtotal() ); ?></strong>
			</div>
			<p class="drawer__note"><?php echo yumnia_icon( 'truck' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Ongkir dihitung admin dan dikonfirmasi lewat WhatsApp.', 'yumnia' ); ?></p>
			<a class="btn btn--block btn--wa" href="<?php echo esc_url( wc_get_checkout_url() ); ?>">
				<span class="btn__label"><?php echo yumnia_icon( 'whatsapp' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Lanjut pesan', 'yumnia' ); ?></span>
			</a>
			<a class="drawer__link" href="<?php echo esc_url( wc_get_cart_url() ); ?>"><?php esc_html_e( 'Lihat keranjang', 'yumnia' ); ?></a>
		</div>
		<?php
	}
	echo '</div>';
	return (string) ob_get_clean();
}

/**
 * Potongan HTML yang diperbarui setelah keranjang berubah.
 */
function yumnia_cart_fragments() {
	return array(
		'count'  => yumnia_cart_count(),
		'drawer' => yumnia_cart_drawer_content(),
	);
}

/**
 * Kompatibel dengan tombol AJAX bawaan WooCommerce / plugin lain.
 */
add_filter(
	'woocommerce_add_to_cart_fragments',
	function ( $fragments ) {
		$fragments['.js-drawer-content'] = yumnia_cart_drawer_content();
		$fragments['span.js-cart-count'] = '<span class="cart-count js-cart-count" data-count="' . yumnia_cart_count() . '">' . yumnia_cart_count() . '</span>';
		return $fragments;
	}
);

/**
 * Pesan error WooCommerce terakhir, lalu bersihkan supaya tidak muncul lagi di halaman berikutnya.
 *
 * @return string[]
 */
function yumnia_take_errors() {
	$errors = array();
	foreach ( wc_get_notices( 'error' ) as $notice ) {
		$errors[] = wp_strip_all_tags( is_array( $notice ) ? $notice['notice'] : $notice );
	}
	wc_clear_notices();
	return $errors;
}

/**
 * Tambah ke keranjang: items[i][id] (ID produk atau varian) + items[i][qty].
 */
add_action(
	'wc_ajax_yumnia_add',
	function () {
		check_ajax_referer( 'yumnia-cart', 'nonce' );
		$items = isset( $_POST['items'] ) ? (array) wp_unslash( $_POST['items'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$added = 0;
		foreach ( array_slice( $items, 0, 10 ) as $row ) {
			$product = wc_get_product( absint( $row['id'] ?? 0 ) );
			$qty     = max( 1, absint( $row['qty'] ?? 1 ) );
			if ( ! $product || 'publish' !== get_post_status( $product->get_id() ) ) {
				continue;
			}
			$product_id   = $product->get_id();
			$variation_id = 0;
			$variation    = array();
			if ( $product->is_type( 'variation' ) ) {
				$variation_id = $product_id;
				$product_id   = $product->get_parent_id();
				$variation    = $product->get_variation_attributes();
			} elseif ( $product->is_type( 'variable' ) ) {
				wc_add_notice( sprintf( __( 'Pilih varian %s dulu.', 'yumnia' ), $product->get_name() ), 'error' );
				continue;
			}
			if ( ! apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $qty, $variation_id, $variation ) ) {
				continue;
			}
			if ( false !== WC()->cart->add_to_cart( $product_id, $qty, $variation_id, $variation ) ) {
				++$added;
				do_action( 'woocommerce_ajax_added_to_cart', $product_id );
			}
		}
		$errors = yumnia_take_errors();
		wp_send_json(
			array(
				'ok'        => $added > 0,
				'added'     => $added,
				'errors'    => $errors,
				'fragments' => yumnia_cart_fragments(),
			)
		);
	}
);

/**
 * Ubah jumlah (0 = hapus).
 */
add_action(
	'wc_ajax_yumnia_qty',
	function () {
		check_ajax_referer( 'yumnia-cart', 'nonce' );
		$key  = isset( $_POST['key'] ) ? sanitize_text_field( wp_unslash( $_POST['key'] ) ) : '';
		$qty  = isset( $_POST['qty'] ) ? absint( $_POST['qty'] ) : 0;
		$item = WC()->cart->get_cart_item( $key );
		$msg  = array();
		if ( $item ) {
			$product = $item['data'];
			if ( $qty > 0 && $product->managing_stock() && ! $product->backorders_allowed() && $qty > $product->get_stock_quantity() ) {
				$qty   = (int) $product->get_stock_quantity();
				$msg[] = sprintf( __( 'Stok tersisa %d.', 'yumnia' ), $qty );
			}
			WC()->cart->set_quantity( $key, $qty, true );
		}
		wp_send_json(
			array(
				'ok'        => (bool) $item,
				'errors'    => array_merge( $msg, yumnia_take_errors() ),
				'fragments' => yumnia_cart_fragments(),
			)
		);
	}
);

/**
 * Ambil isi keranjang terbaru (dipakai saat halaman dibuka dari tombol kembali).
 */
add_action(
	'wc_ajax_yumnia_fragments',
	function () {
		wp_send_json( array( 'fragments' => yumnia_cart_fragments() ) );
	}
);
