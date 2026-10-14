<?php
/**
 * Checkout lewat WhatsApp (Tahap 1).
 *
 * Alur: pembeli mengisi nama, nomor WA, alamat, dan catatan → pesanan tercatat di admin
 * dengan status "Baru" → pembeli diarahkan ke WhatsApp admin dengan ringkasan pesanan.
 * Admin mengonfirmasi ongkir, pembeli membayar manual (transfer/QRIS), lalu admin
 * mengubah status: Baru → Dibayar → Dikirim → Selesai. Stok berkurang saat status Dibayar.
 *
 * @package Yumnia
 */

defined( 'ABSPATH' ) || exit;

/* ---------------------------------------------------------------- status pesanan */

add_filter(
	'woocommerce_register_shop_order_post_statuses',
	function ( $statuses ) {
		$statuses['wc-baru']    = array(
			'label'                     => _x( 'Baru', 'Order status', 'yumnia' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			/* translators: %s: jumlah pesanan */
			'label_count'               => _n_noop( 'Baru <span class="count">(%s)</span>', 'Baru <span class="count">(%s)</span>', 'yumnia' ),
		);
		$statuses['wc-dikirim'] = array(
			'label'                     => _x( 'Dikirim', 'Order status', 'yumnia' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			/* translators: %s: jumlah pesanan */
			'label_count'               => _n_noop( 'Dikirim <span class="count">(%s)</span>', 'Dikirim <span class="count">(%s)</span>', 'yumnia' ),
		);
		if ( isset( $statuses['wc-processing'] ) ) {
			$statuses['wc-processing']['label'] = _x( 'Dibayar', 'Order status', 'yumnia' );
			/* translators: %s: jumlah pesanan */
			$statuses['wc-processing']['label_count'] = _n_noop( 'Dibayar <span class="count">(%s)</span>', 'Dibayar <span class="count">(%s)</span>', 'yumnia' );
		}
		if ( isset( $statuses['wc-completed'] ) ) {
			$statuses['wc-completed']['label'] = _x( 'Selesai', 'Order status', 'yumnia' );
			/* translators: %s: jumlah pesanan */
			$statuses['wc-completed']['label_count'] = _n_noop( 'Selesai <span class="count">(%s)</span>', 'Selesai <span class="count">(%s)</span>', 'yumnia' );
		}
		return $statuses;
	}
);

add_filter(
	'wc_order_statuses',
	function ( $statuses ) {
		$out = array();
		foreach ( $statuses as $key => $label ) {
			if ( 'wc-processing' === $key ) {
				$label = _x( 'Dibayar', 'Order status', 'yumnia' );
			} elseif ( 'wc-completed' === $key ) {
				$label = _x( 'Selesai', 'Order status', 'yumnia' );
			}
			if ( 'wc-processing' === $key ) {
				$out['wc-baru'] = _x( 'Baru', 'Order status', 'yumnia' );
			}
			$out[ $key ] = $label;
			if ( 'wc-processing' === $key ) {
				$out['wc-dikirim'] = _x( 'Dikirim', 'Order status', 'yumnia' );
			}
		}
		return $out;
	}
);

// Pesanan "Baru" & "Dikirim" boleh diubah oleh admin dan dihitung di laporan.
add_filter(
	'woocommerce_reports_order_statuses',
	function ( $statuses ) {
		if ( is_array( $statuses ) ) {
			$statuses[] = 'dikirim';
		}
		return $statuses;
	}
);
add_filter(
	'woocommerce_order_is_paid_statuses',
	function ( $statuses ) {
		$statuses[] = 'dikirim';
		return $statuses;
	}
);

// Stok berkurang saat Dibayar (bawaan WooCommerce untuk status processing) dan juga
// bila admin langsung melompat ke Dikirim. Pesanan Baru tidak memotong stok.
add_action( 'woocommerce_order_status_dikirim', 'wc_maybe_reduce_stock_levels' );
// Lepas stok yang ditahan sementara saat checkout.
add_action(
	'woocommerce_order_status_baru',
	function ( $order_id ) {
		if ( function_exists( 'wc_release_stock_for_order' ) ) {
			wc_release_stock_for_order( $order_id );
		}
	}
);

// Email "pesanan baru" ke admin (jika email server tersedia).
add_filter(
	'woocommerce_email_actions',
	function ( $actions ) {
		$actions[] = 'woocommerce_order_status_pending_to_baru';
		return $actions;
	}
);
add_action(
	'woocommerce_email',
	function ( $mailer ) {
		if ( isset( $mailer->emails['WC_Email_New_Order'] ) ) {
			add_action( 'woocommerce_order_status_pending_to_baru_notification', array( $mailer->emails['WC_Email_New_Order'], 'trigger' ), 10, 2 );
		}
	}
);

// Warna status di daftar pesanan admin.
add_action(
	'admin_head',
	function () {
		echo '<style>.order-status.status-baru{background:#f8e1e4;color:#7b1e3a}.order-status.status-dikirim{background:#e5eef9;color:#2f3a57}</style>';
	}
);

/* ---------------------------------------------------------------- metode "Pesan via WhatsApp" */

// Tema dimuat setelah plugin, jadi kelas WooCommerce sudah tersedia di sini.
yumnia_register_wa_gateway();

/**
 * Daftarkan kelas gateway (dipanggil sekali begitu WooCommerce siap).
 */
function yumnia_register_wa_gateway() {
	if ( class_exists( 'Yumnia_WA_Gateway' ) || ! class_exists( 'WC_Payment_Gateway' ) ) {
		return;
	}

	/**
	 * Gateway tanpa pembayaran online: pesanan diteruskan ke WhatsApp admin.
	 */
	class Yumnia_WA_Gateway extends WC_Payment_Gateway {

		/**
		 * Konstruktor.
		 */
		public function __construct() {
			$this->id                 = 'yumnia_whatsapp';
			$this->method_title       = __( 'Pesan via WhatsApp', 'yumnia' );
			$this->method_description = __( 'Pesanan tercatat dengan status Baru, lalu pembeli diarahkan ke WhatsApp admin dengan ringkasan pesanan. Pembayaran (transfer/QRIS) dikonfirmasi manual.', 'yumnia' );
			$this->has_fields         = false;
			$this->order_button_text  = __( 'Pesan via WhatsApp', 'yumnia' );
			$this->supports           = array( 'products' );

			$this->init_form_fields();
			$this->init_settings();

			$this->title       = $this->get_option( 'title' );
			$this->description = $this->get_option( 'description' );
			$this->enabled     = $this->get_option( 'enabled', 'yes' );

			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		}

		/**
		 * Pengaturan di WooCommerce → Pengaturan → Pembayaran.
		 */
		public function init_form_fields() {
			$this->form_fields = array(
				'enabled'     => array(
					'title'   => __( 'Aktif', 'yumnia' ),
					'type'    => 'checkbox',
					'label'   => __( 'Aktifkan pesan via WhatsApp', 'yumnia' ),
					'default' => 'yes',
				),
				'title'       => array(
					'title'   => __( 'Judul', 'yumnia' ),
					'type'    => 'text',
					'default' => __( 'Pesan via WhatsApp', 'yumnia' ),
				),
				'description' => array(
					'title'   => __( 'Keterangan untuk pembeli', 'yumnia' ),
					'type'    => 'textarea',
					'default' => __( 'Setelah klik Pesan, WhatsApp akan terbuka dengan ringkasan pesananmu. Admin akan membalas dengan total ongkir dan cara bayar (transfer bank atau QRIS).', 'yumnia' ),
				),
			);
		}

		/**
		 * Proses pesanan.
		 *
		 * @param int $order_id ID pesanan.
		 * @return array
		 */
		public function process_payment( $order_id ) {
			$order = wc_get_order( $order_id );
			$order->update_status( 'baru', __( 'Pesanan masuk lewat WhatsApp. Menunggu konfirmasi ongkir & pembayaran.', 'yumnia' ) );
			WC()->cart->empty_cart();
			return array(
				'result'   => 'success',
				'redirect' => $this->get_return_url( $order ),
			);
		}
	}

	add_filter(
		'woocommerce_payment_gateways',
		function ( $gateways ) {
			$gateways[] = 'Yumnia_WA_Gateway';
			return $gateways;
		}
	);
}

/* ---------------------------------------------------------------- isian checkout */

add_filter(
	'woocommerce_checkout_fields',
	function ( $fields ) {
		$keep = array(
			'billing_first_name' => array(
				'label'       => __( 'Nama lengkap', 'yumnia' ),
				'placeholder' => __( 'Nama penerima', 'yumnia' ),
				'required'    => true,
				'class'       => array( 'form-row-wide' ),
				'priority'    => 10,
				'autocomplete' => 'name',
			),
			'billing_phone'      => array(
				'label'       => __( 'Nomor WhatsApp', 'yumnia' ),
				'placeholder' => '08xx xxxx xxxx',
				'required'    => true,
				'type'        => 'tel',
				'class'       => array( 'form-row-wide' ),
				'priority'    => 20,
				'validate'    => array( 'phone' ),
				'autocomplete' => 'tel',
			),
			'billing_address_1'  => array(
				'label'       => __( 'Alamat lengkap', 'yumnia' ),
				'placeholder' => __( 'Jalan, nomor rumah, RT/RW, desa/kelurahan, kecamatan, kota/kabupaten, kode pos', 'yumnia' ),
				'required'    => true,
				'type'        => 'textarea',
				'class'       => array( 'form-row-wide' ),
				'priority'    => 30,
				'autocomplete' => 'street-address',
				'custom_attributes' => array( 'rows' => 3 ),
			),
		);
		$fields['billing']  = $keep;
		$fields['shipping'] = array();
		if ( isset( $fields['order']['order_comments'] ) ) {
			$fields['order']['order_comments']['label']       = __( 'Catatan (opsional)', 'yumnia' );
			$fields['order']['order_comments']['placeholder'] = __( 'Contoh: kirim sore, warna cadangan, atau titip ke tetangga', 'yumnia' );
		}
		return $fields;
	},
	20
);

// Simpan negara Indonesia di pesanan agar format alamat rapi.
add_action(
	'woocommerce_checkout_create_order',
	function ( WC_Order $order ) {
		if ( ! $order->get_billing_country() ) {
			$order->set_billing_country( 'ID' );
		}
	}
);

// Ongkir dihitung manual oleh admin, jadi keranjang tidak butuh metode pengiriman.
add_filter( 'woocommerce_cart_needs_shipping', '__return_false' );
add_filter( 'woocommerce_cart_needs_shipping_address', '__return_false' );
add_filter( 'woocommerce_enable_order_notes_field', '__return_true' );

add_filter(
	'woocommerce_order_button_text',
	function () {
		return __( 'Pesan via WhatsApp', 'yumnia' );
	}
);

// Pembeli tidak perlu membuat akun.
add_filter( 'woocommerce_checkout_registration_enabled', '__return_false' );
add_filter( 'woocommerce_checkout_registration_required', '__return_false' );

/* ---------------------------------------------------------------- pesan WhatsApp */

/**
 * Ringkasan pesanan terformat untuk WhatsApp.
 *
 * @param WC_Order $order Pesanan.
 * @return string
 */
function yumnia_wa_order_message( WC_Order $order ) {
	$lines   = array();
	$lines[] = __( 'Halo Admin Yumnia, saya mau pesan:', 'yumnia' );
	$lines[] = '';
	$lines[] = sprintf( '*%s #%s*', __( 'Pesanan', 'yumnia' ), $order->get_order_number() );
	$n       = 0;
	foreach ( $order->get_items() as $item ) {
		/** @var WC_Order_Item_Product $item */
		$product = $item->get_product();
		$name    = $item->get_name();
		$variant = '';
		if ( $product && $product->is_type( 'variation' ) ) {
			$parent  = wc_get_product( $product->get_parent_id() );
			$name    = $parent ? $parent->get_name() : $name;
			$variant = wc_get_formatted_variation( $product, true, false, false );
		}
		++$n;
		$lines[] = sprintf(
			'%d. %s%s × %d = %s',
			$n,
			$name,
			$variant ? ' (' . $variant . ')' : '',
			$item->get_quantity(),
			yumnia_rupiah( $order->get_line_subtotal( $item, true ) )
		);
	}
	$lines[] = '';
	$lines[] = sprintf( '%s: *%s*', __( 'Subtotal', 'yumnia' ), yumnia_rupiah( $order->get_subtotal() ) );
	$lines[] = __( 'Ongkir: mohon diinfokan', 'yumnia' );
	$lines[] = '';
	$lines[] = '*' . __( 'Data pengiriman', 'yumnia' ) . '*';
	$lines[] = __( 'Nama', 'yumnia' ) . ': ' . $order->get_billing_first_name();
	$lines[] = __( 'No. WA', 'yumnia' ) . ': ' . $order->get_billing_phone();
	$lines[] = __( 'Alamat', 'yumnia' ) . ': ' . preg_replace( '/\s+/', ' ', $order->get_billing_address_1() );
	if ( $order->get_customer_note() ) {
		$lines[] = __( 'Catatan', 'yumnia' ) . ': ' . preg_replace( '/\s+/', ' ', $order->get_customer_note() );
	}
	$lines[] = '';
	$lines[] = __( 'Terima kasih!', 'yumnia' );
	return implode( "\n", $lines );
}

/**
 * Di layar pesanan admin: tombol chat pembeli & kirim ulang ringkasan.
 */
add_action(
	'woocommerce_admin_order_data_after_billing_address',
	function ( $order ) {
		$phone = yumnia_normalize_phone( $order->get_billing_phone() );
		if ( ! $phone ) {
			return;
		}
		$text = sprintf( __( 'Halo %1$s, terima kasih sudah memesan di Yumnia (pesanan #%2$s).', 'yumnia' ), $order->get_billing_first_name(), $order->get_order_number() );
		printf(
			'<p><a class="button" target="_blank" rel="noopener" href="%s">%s</a></p>',
			esc_url( yumnia_wa_link( $text, $phone ) ),
			esc_html__( 'Chat pembeli di WhatsApp', 'yumnia' )
		);
	}
);
