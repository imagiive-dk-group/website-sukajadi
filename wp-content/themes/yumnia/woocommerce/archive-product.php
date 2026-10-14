<?php
/**
 * Katalog: Semua produk, kategori Hijab/Sandal, dan hasil pencarian.
 *
 * @package Yumnia
 * @version 8.6.0
 */

defined( 'ABSPATH' ) || exit;

get_header();

// phpcs:disable WordPress.Security.NonceVerification.Recommended
$yumnia_total    = isset( $GLOBALS['wp_query'] ) ? (int) $GLOBALS['wp_query']->found_posts : 0;
$yumnia_term     = is_product_category() ? get_queried_object() : null;
$yumnia_title    = is_search() ? sprintf( __( 'Hasil untuk “%s”', 'yumnia' ), get_search_query() ) : woocommerce_page_title( false );
$yumnia_shop_url = wc_get_page_permalink( 'shop' );
$yumnia_colors   = taxonomy_exists( 'pa_warna' ) ? get_terms( array( 'taxonomy' => 'pa_warna', 'hide_empty' => true ) ) : array();
$yumnia_sizes    = taxonomy_exists( 'pa_ukuran' ) ? get_terms( array( 'taxonomy' => 'pa_ukuran', 'hide_empty' => true, 'orderby' => 'slug' ) ) : array();
$yumnia_sel_c    = yumnia_active_filter( 'warna' );
$yumnia_sel_s    = yumnia_active_filter( 'ukuran' );
$yumnia_sel_p    = isset( $_GET['harga'] ) && '' !== $_GET['harga'] ? absint( $_GET['harga'] ) : null;
$yumnia_orderby  = isset( $_GET['orderby'] ) ? sanitize_key( $_GET['orderby'] ) : 'date';
$yumnia_active   = yumnia_active_filter_count();
// phpcs:enable

$yumnia_tabs = array(
	array(
		'label'  => __( 'Semua', 'yumnia' ),
		'url'    => $yumnia_shop_url,
		'active' => is_shop() && ! is_search(),
	),
);
foreach ( array( 'hijab', 'sandal' ) as $yumnia_slug ) {
	$yumnia_cat = get_term_by( 'slug', $yumnia_slug, 'product_cat' );
	if ( $yumnia_cat ) {
		$yumnia_tabs[] = array(
			'label'  => $yumnia_cat->name,
			'url'    => get_term_link( $yumnia_cat ),
			'active' => $yumnia_term && $yumnia_term->term_id === $yumnia_cat->term_id,
		);
	}
}
$yumnia_tabs[] = array(
	'label'  => __( 'Padu-padan', 'yumnia' ),
	'url'    => get_post_type_archive_link( 'yumnia_set' ),
	'active' => false,
);
?>
<div class="catalog">
	<header class="page-hero">
		<div class="container">
			<?php woocommerce_breadcrumb(); ?>
			<h1 class="page-hero__title" data-split="now"><?php echo yumnia_split( $yumnia_title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h1>
			<?php if ( $yumnia_term && $yumnia_term->description ) : ?>
				<p class="page-hero__text" data-reveal><?php echo esc_html( wp_strip_all_tags( $yumnia_term->description ) ); ?></p>
			<?php endif; ?>
			<nav class="tabs tabs--links" aria-label="<?php esc_attr_e( 'Kategori', 'yumnia' ); ?>" data-reveal>
				<?php foreach ( $yumnia_tabs as $yumnia_tab ) : ?>
					<a class="tab<?php echo $yumnia_tab['active'] ? ' is-active' : ''; ?>" href="<?php echo esc_url( $yumnia_tab['url'] ); ?>" <?php echo $yumnia_tab['active'] ? 'aria-current="page"' : ''; ?>><?php echo esc_html( $yumnia_tab['label'] ); ?></a>
				<?php endforeach; ?>
			</nav>
		</div>
	</header>

	<div class="toolbar" data-toolbar>
		<div class="container toolbar__inner">
			<button class="toolbar__btn" type="button" data-open="filters" aria-controls="filters-panel" aria-expanded="false">
				<?php echo yumnia_icon( 'filter' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php esc_html_e( 'Filter', 'yumnia' ); ?>
				<?php if ( $yumnia_active ) : ?>
					<span class="toolbar__badge"><?php echo (int) $yumnia_active; ?></span>
				<?php endif; ?>
			</button>
			<p class="toolbar__count"><?php echo esc_html( sprintf( _n( '%d produk', '%d produk', $yumnia_total, 'yumnia' ), $yumnia_total ) ); ?></p>
			<form class="sort" method="get" action="<?php echo esc_url( yumnia_filter_url( array( 'orderby' => null ) ) ); ?>">
				<label class="sort__label" for="orderby"><?php esc_html_e( 'Urutkan', 'yumnia' ); ?></label>
				<select class="sort__select" name="orderby" id="orderby" data-autosubmit>
					<?php foreach ( apply_filters( 'woocommerce_catalog_orderby', array() ) as $yumnia_key => $yumnia_label ) : ?>
						<option value="<?php echo esc_attr( $yumnia_key ); ?>" <?php selected( $yumnia_orderby, $yumnia_key ); ?>><?php echo esc_html( $yumnia_label ); ?></option>
					<?php endforeach; ?>
				</select>
				<?php
				foreach ( array( 'filter_warna' => implode( ',', $yumnia_sel_c ), 'filter_ukuran' => implode( ',', $yumnia_sel_s ), 'harga' => null === $yumnia_sel_p ? '' : $yumnia_sel_p ) as $yumnia_k => $yumnia_v ) {
					if ( '' !== $yumnia_v ) {
						echo '<input type="hidden" name="' . esc_attr( $yumnia_k ) . '" value="' . esc_attr( (string) $yumnia_v ) . '">';
					}
				}
				if ( is_search() ) {
					echo '<input type="hidden" name="s" value="' . esc_attr( get_search_query() ) . '"><input type="hidden" name="post_type" value="product">';
				}
				?>
				<noscript><button class="btn btn--sm" type="submit"><?php esc_html_e( 'Terapkan', 'yumnia' ); ?></button></noscript>
			</form>
		</div>
	</div>

	<?php if ( $yumnia_active ) : ?>
		<div class="container active-filters">
			<?php foreach ( $yumnia_sel_c as $yumnia_slug ) : ?>
				<?php $yumnia_t = get_term_by( 'slug', $yumnia_slug, 'pa_warna' ); ?>
				<a class="chip chip--remove" href="<?php echo esc_url( yumnia_filter_url( array( 'filter_warna' => implode( ',', array_diff( $yumnia_sel_c, array( $yumnia_slug ) ) ) ) ) ); ?>">
					<i class="chip__dot" style="--c:<?php echo esc_attr( yumnia_color_hex( $yumnia_t ? $yumnia_t : $yumnia_slug ) ); ?>"></i><?php echo esc_html( $yumnia_t ? $yumnia_t->name : $yumnia_slug ); ?><?php echo yumnia_icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
			<?php endforeach; ?>
			<?php foreach ( $yumnia_sel_s as $yumnia_slug ) : ?>
				<?php $yumnia_t = get_term_by( 'slug', $yumnia_slug, 'pa_ukuran' ); ?>
				<a class="chip chip--remove" href="<?php echo esc_url( yumnia_filter_url( array( 'filter_ukuran' => implode( ',', array_diff( $yumnia_sel_s, array( $yumnia_slug ) ) ) ) ) ); ?>"><?php echo esc_html( sprintf( __( 'Ukuran %s', 'yumnia' ), $yumnia_t ? $yumnia_t->name : $yumnia_slug ) ); ?><?php echo yumnia_icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
			<?php endforeach; ?>
			<?php if ( null !== $yumnia_sel_p && isset( yumnia_price_ranges()[ $yumnia_sel_p ] ) ) : ?>
				<a class="chip chip--remove" href="<?php echo esc_url( yumnia_filter_url( array( 'harga' => null ) ) ); ?>"><?php echo esc_html( yumnia_price_ranges()[ $yumnia_sel_p ]['label'] ); ?><?php echo yumnia_icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
			<?php endif; ?>
			<a class="active-filters__clear" href="<?php echo esc_url( yumnia_filter_url( array( 'filter_warna' => null, 'filter_ukuran' => null, 'harga' => null ) ) ); ?>"><?php esc_html_e( 'Hapus semua', 'yumnia' ); ?></a>
		</div>
	<?php endif; ?>

	<div class="container catalog__body">
		<?php wc_print_notices(); ?>
		<?php if ( woocommerce_product_loop() && have_posts() ) : ?>
			<div class="product-grid" data-stagger>
				<?php
				$yumnia_i = 0;
				while ( have_posts() ) {
					the_post();
					$yumnia_product = wc_get_product( get_the_ID() );
					get_template_part(
						'template-parts/product-card',
						null,
						array(
							'product' => $yumnia_product,
							'index'   => $yumnia_i % 4,
							'eager'   => $yumnia_i < 4,
						)
					);
					++$yumnia_i;
				}
				?>
			</div>
			<?php
			$yumnia_pages = isset( $GLOBALS['wp_query'] ) ? (int) $GLOBALS['wp_query']->max_num_pages : 1;
			if ( $yumnia_pages > 1 ) {
				echo '<nav class="pagination" aria-label="' . esc_attr__( 'Halaman', 'yumnia' ) . '">';
				echo paginate_links( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					array(
						'total'     => $yumnia_pages,
						'current'   => max( 1, (int) get_query_var( 'paged' ) ),
						'prev_text' => yumnia_icon( 'arrow-left' ),
						'next_text' => yumnia_icon( 'arrow-right' ),
						'type'      => 'list',
					)
				);
				echo '</nav>';
			}
			?>
		<?php else : ?>
			<div class="empty-state">
				<?php echo yumnia_monogram( 'empty-state__mark' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<h2 class="empty-state__title"><?php esc_html_e( 'Belum ada produk yang cocok', 'yumnia' ); ?></h2>
				<p><?php esc_html_e( 'Coba hapus beberapa filter atau cari dengan kata lain.', 'yumnia' ); ?></p>
				<a class="btn" href="<?php echo esc_url( $yumnia_shop_url ); ?>"><span class="btn__label"><?php esc_html_e( 'Lihat semua produk', 'yumnia' ); ?></span></a>
			</div>
		<?php endif; ?>
	</div>
</div>

<aside class="drawer drawer--left" id="filters-panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Filter produk', 'yumnia' ); ?>" data-panel="filters" hidden>
	<form class="filters" method="get" action="<?php echo esc_url( is_search() ? home_url( '/' ) : yumnia_catalog_base_url() ); ?>">
		<div class="drawer__head">
			<p class="drawer__title"><?php esc_html_e( 'Filter', 'yumnia' ); ?></p>
			<button class="icon-btn" type="button" data-close aria-label="<?php esc_attr_e( 'Tutup filter', 'yumnia' ); ?>"><?php echo yumnia_icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
		</div>
		<div class="filters__body">
			<?php if ( ! is_product_category() ) : ?>
				<fieldset class="filters__group">
					<legend><?php esc_html_e( 'Kategori', 'yumnia' ); ?></legend>
					<div class="chips">
						<?php foreach ( array_slice( $yumnia_tabs, 1, 2 ) as $yumnia_tab ) : ?>
							<a class="chip" href="<?php echo esc_url( $yumnia_tab['url'] ); ?>"><?php echo esc_html( $yumnia_tab['label'] ); ?></a>
						<?php endforeach; ?>
					</div>
				</fieldset>
			<?php endif; ?>
			<?php if ( $yumnia_colors && ! is_wp_error( $yumnia_colors ) ) : ?>
				<fieldset class="filters__group">
					<legend><?php esc_html_e( 'Warna', 'yumnia' ); ?></legend>
					<div class="chips">
						<?php foreach ( $yumnia_colors as $yumnia_c ) : ?>
							<label class="chip chip--check">
								<input type="checkbox" name="filter_warna[]" value="<?php echo esc_attr( $yumnia_c->slug ); ?>" <?php checked( in_array( $yumnia_c->slug, $yumnia_sel_c, true ) ); ?>>
								<i class="chip__dot" style="--c:<?php echo esc_attr( yumnia_color_hex( $yumnia_c ) ); ?>"></i><?php echo esc_html( $yumnia_c->name ); ?>
							</label>
						<?php endforeach; ?>
					</div>
				</fieldset>
			<?php endif; ?>
			<?php if ( $yumnia_sizes && ! is_wp_error( $yumnia_sizes ) ) : ?>
				<fieldset class="filters__group">
					<legend><?php esc_html_e( 'Ukuran', 'yumnia' ); ?></legend>
					<div class="chips">
						<?php foreach ( $yumnia_sizes as $yumnia_s ) : ?>
							<label class="chip chip--check">
								<input type="checkbox" name="filter_ukuran[]" value="<?php echo esc_attr( $yumnia_s->slug ); ?>" <?php checked( in_array( $yumnia_s->slug, $yumnia_sel_s, true ) ); ?>>
								<?php echo esc_html( $yumnia_s->name ); ?>
							</label>
						<?php endforeach; ?>
					</div>
				</fieldset>
			<?php endif; ?>
			<fieldset class="filters__group">
				<legend><?php esc_html_e( 'Harga', 'yumnia' ); ?></legend>
				<div class="chips">
					<?php foreach ( yumnia_price_ranges() as $yumnia_idx => $yumnia_range ) : ?>
						<label class="chip chip--check">
							<input type="radio" name="harga" value="<?php echo (int) $yumnia_idx; ?>" <?php checked( $yumnia_sel_p, $yumnia_idx ); ?>>
							<?php echo esc_html( $yumnia_range['label'] ); ?>
						</label>
					<?php endforeach; ?>
				</div>
			</fieldset>
			<input type="hidden" name="orderby" value="<?php echo esc_attr( $yumnia_orderby ); ?>">
			<?php if ( is_search() ) : ?>
				<input type="hidden" name="s" value="<?php echo esc_attr( get_search_query() ); ?>">
				<input type="hidden" name="post_type" value="product">
			<?php endif; ?>
		</div>
		<div class="drawer__foot filters__foot">
			<a class="btn btn--ghost" href="<?php echo esc_url( yumnia_filter_url( array( 'filter_warna' => null, 'filter_ukuran' => null, 'harga' => null ) ) ); ?>"><span class="btn__label"><?php esc_html_e( 'Reset', 'yumnia' ); ?></span></a>
			<button class="btn" type="submit"><span class="btn__label"><?php esc_html_e( 'Terapkan', 'yumnia' ); ?></span></button>
		</div>
	</form>
</aside>
<?php
get_footer();
