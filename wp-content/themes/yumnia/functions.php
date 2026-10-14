<?php
/**
 * Tema Yumnia.
 *
 * Fitur dipecah per berkas di folder inc/ supaya mudah dirawat.
 *
 * @package Yumnia
 */

defined( 'ABSPATH' ) || exit;

define( 'YUMNIA_VERSION', '1.0.0' );
define( 'YUMNIA_DIR', get_template_directory() );
define( 'YUMNIA_URI', get_template_directory_uri() );

require YUMNIA_DIR . '/inc/helpers.php';
require YUMNIA_DIR . '/inc/setup.php';
require YUMNIA_DIR . '/inc/customizer.php';
require YUMNIA_DIR . '/inc/seo.php';
require YUMNIA_DIR . '/inc/padu-padan.php';
require YUMNIA_DIR . '/inc/shortcodes.php';

if ( class_exists( 'WooCommerce' ) ) {
	require YUMNIA_DIR . '/inc/woocommerce.php';
	require YUMNIA_DIR . '/inc/product-fields.php';
	require YUMNIA_DIR . '/inc/cart.php';
	require YUMNIA_DIR . '/inc/checkout-whatsapp.php';
} else {
	add_action(
		'admin_notices',
		function () {
			echo '<div class="notice notice-warning"><p>' . esc_html__( 'Tema Yumnia membutuhkan plugin WooCommerce. Silakan pasang dan aktifkan WooCommerce.', 'yumnia' ) . '</p></div>';
		}
	);
}
