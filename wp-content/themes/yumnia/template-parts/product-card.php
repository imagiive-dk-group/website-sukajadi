<?php
/**
 * Kartu produk.
 *
 * Argumen: product (WC_Product), index (int), sizes (string), eager (bool).
 *
 * @package Yumnia
 */

defined( 'ABSPATH' ) || exit;

$product = isset( $args['product'] ) ? $args['product'] : ( isset( $GLOBALS['product'] ) ? $GLOBALS['product'] : null );
if ( ! $product instanceof WC_Product ) {
	return;
}
$index   = isset( $args['index'] ) ? (int) $args['index'] : 0;
$sizes   = isset( $args['sizes'] ) ? $args['sizes'] : '(min-width: 1100px) 24vw, (min-width: 700px) 32vw, 48vw';
$eager   = ! empty( $args['eager'] );
$url     = get_permalink( $product->get_id() );
$name    = $product->get_name();
$colors  = yumnia_product_colors( $product );
$alt_id  = yumnia_secondary_image_id( $product );
$in_stock = $product->is_in_stock();
?>
<article class="card<?php echo $in_stock ? '' : ' is-soldout'; ?>" data-card <?php echo yumnia_payload_attr( $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> style="--i:<?php echo (int) $index; ?>">
	<div class="card__media">
		<a class="card__link" href="<?php echo esc_url( $url ); ?>" data-cursor="<?php esc_attr_e( 'Lihat', 'yumnia' ); ?>" aria-label="<?php echo esc_attr( $name ); ?>">
			<?php
			if ( $product->get_image_id() ) {
				echo wp_get_attachment_image(
					$product->get_image_id(),
					'yumnia-card',
					false,
					array(
						'class'   => 'card__img card__img--main',
						'sizes'   => $sizes,
						'alt'     => $name,
						'loading' => $eager ? 'eager' : 'lazy',
					)
				);
			} else {
				echo '<img class="card__img card__img--main" src="' . esc_url( wc_placeholder_img_src( 'yumnia-card' ) ) . '" alt="' . esc_attr( $name ) . '" loading="lazy">';
			}
			if ( $alt_id ) {
				echo wp_get_attachment_image(
					$alt_id,
					'yumnia-card',
					false,
					array(
						'class'   => 'card__img card__img--alt',
						'sizes'   => $sizes,
						'alt'     => '',
						'loading' => 'lazy',
					)
				);
			}
			?>
		</a>
		<?php echo yumnia_badges( $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php if ( $in_stock ) : ?>
			<button class="card__add" type="button" data-quick-add aria-label="<?php echo esc_attr( sprintf( __( 'Tambah %s ke keranjang', 'yumnia' ), $name ) ); ?>">
				<?php echo yumnia_icon( 'plus' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<span class="card__add-label"><?php esc_html_e( 'Tambah', 'yumnia' ); ?></span>
			</button>
		<?php endif; ?>
	</div>
	<div class="card__body">
		<h3 class="card__title"><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $name ); ?></a></h3>
		<p class="card__price"><?php echo wp_kses_post( $product->get_price_html() ); ?></p>
		<?php if ( $colors ) : ?>
			<div class="swatches" role="group" aria-label="<?php esc_attr_e( 'Warna', 'yumnia' ); ?>">
				<?php foreach ( array_slice( $colors, 0, 5 ) as $color ) : ?>
					<button type="button" class="swatch" style="--c:<?php echo esc_attr( $color['hex'] ); ?>" data-swatch="<?php echo esc_attr( $color['slug'] ); ?>" data-img="<?php echo esc_url( yumnia_product_image_for_color( $product, $color['slug'], 'yumnia-card' ) ); ?>" aria-label="<?php echo esc_attr( $color['name'] ); ?>" title="<?php echo esc_attr( $color['name'] ); ?>"></button>
				<?php endforeach; ?>
				<?php if ( count( $colors ) > 5 ) : ?>
					<span class="swatches__more">+<?php echo (int) ( count( $colors ) - 5 ); ?></span>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</article>
