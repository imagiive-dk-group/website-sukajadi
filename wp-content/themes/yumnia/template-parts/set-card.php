<?php
/**
 * Kartu set padu-padan.
 *
 * Argumen: post (WP_Post), index (int), layout ('feature' | 'grid').
 *
 * @package Yumnia
 */

defined( 'ABSPATH' ) || exit;

$set    = isset( $args['post'] ) ? $args['post'] : get_post();
$index  = isset( $args['index'] ) ? (int) $args['index'] : 0;
$layout = isset( $args['layout'] ) ? $args['layout'] : 'grid';
$items  = function_exists( 'yumnia_set_items' ) ? yumnia_set_items( $set->ID ) : array();
if ( ! $items ) {
	return;
}

$payload = array();
$total   = 0;
foreach ( $items as $item ) {
	$data        = yumnia_product_payload( $item['product'] );
	$data['pre'] = $item['warna'] ? array( 'attribute_pa_warna' => $item['warna'] ) : new stdClass();
	if ( $item['warna'] ) {
		$data['img'] = $item['image'];
	}
	$payload[] = $data;
	$total    += (float) $item['product']->get_price();
}
$color_name = function ( $slug ) {
	$term = $slug && taxonomy_exists( 'pa_warna' ) ? get_term_by( 'slug', $slug, 'pa_warna' ) : false;
	return $term ? $term->name : '';
};
?>
<article class="look look--<?php echo esc_attr( $layout ); ?>" id="set-<?php echo (int) $set->ID; ?>" data-set="<?php echo esc_attr( wp_json_encode( $payload ) ); ?>" style="--i:<?php echo (int) $index; ?>">
	<div class="look__media" data-reveal="clip">
		<?php
		if ( has_post_thumbnail( $set ) ) {
			echo get_the_post_thumbnail(
				$set,
				'yumnia-large',
				array(
					'class'   => 'look__img',
					'loading' => 'lazy',
					'sizes'   => '(min-width: 900px) 45vw, 100vw',
				)
			);
		} else {
			echo '<img class="look__img" src="' . esc_url( $items[0]['image'] ) . '" alt="" loading="lazy">';
		}
		?>
		<span class="look__num" aria-hidden="true"><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
	</div>
	<div class="look__body">
		<p class="eyebrow" data-reveal><?php esc_html_e( 'Padu-padan', 'yumnia' ); ?></p>
		<h3 class="look__title" data-reveal><?php echo esc_html( get_the_title( $set ) ); ?></h3>
		<?php if ( has_excerpt( $set ) ) : ?>
			<p class="look__text" data-reveal><?php echo esc_html( get_the_excerpt( $set ) ); ?></p>
		<?php endif; ?>
		<ul class="look__items">
			<?php foreach ( $items as $n => $item ) : ?>
				<li class="look-item" data-reveal style="--d:<?php echo esc_attr( (string) ( 0.08 * $n ) ); ?>s">
					<a class="look-item__img" href="<?php echo esc_url( get_permalink( $item['product']->get_id() ) ); ?>" tabindex="-1"><img src="<?php echo esc_url( $item['image'] ); ?>" alt="" loading="lazy" width="120" height="150"></a>
					<div class="look-item__info">
						<a class="look-item__name" href="<?php echo esc_url( get_permalink( $item['product']->get_id() ) ); ?>"><?php echo esc_html( $item['product']->get_name() ); ?></a>
						<?php if ( $color_name( $item['warna'] ) ) : ?>
							<span class="look-item__meta"><?php echo esc_html( sprintf( __( 'Warna %s', 'yumnia' ), $color_name( $item['warna'] ) ) ); ?></span>
						<?php endif; ?>
					</div>
					<span class="look-item__price"><?php echo esc_html( yumnia_rupiah( $item['product']->get_price() ) ); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>
		<div class="look__foot" data-reveal>
			<p class="look__total"><span><?php esc_html_e( 'Total set', 'yumnia' ); ?></span> <strong><?php echo esc_html( yumnia_rupiah( $total ) ); ?></strong></p>
			<button class="btn" type="button" data-add-set data-magnetic><span class="btn__label"><?php esc_html_e( 'Ambil set ini', 'yumnia' ); ?><?php echo yumnia_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span></button>
		</div>
	</div>
</article>
