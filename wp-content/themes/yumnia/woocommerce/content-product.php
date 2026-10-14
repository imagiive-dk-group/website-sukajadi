<?php
/**
 * Kartu produk di loop WooCommerce (dipakai shortcode & blok bawaan).
 *
 * @package Yumnia
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
get_template_part( 'template-parts/product-card', null, array( 'product' => $product ) );
