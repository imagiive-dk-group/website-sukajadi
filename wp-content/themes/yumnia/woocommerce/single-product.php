<?php
/**
 * Halaman detail produk.
 *
 * @package Yumnia
 * @version 1.6.4
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	$product = wc_get_product( get_the_ID() );
	if ( ! $product ) {
		continue;
	}
	$GLOBALS['product'] = $product;
	// Data terstruktur produk (Google) dari WooCommerce.
	if ( isset( WC()->structured_data ) && is_object( WC()->structured_data ) ) {
		WC()->structured_data->generate_product_data( $product );
	}

	$yumnia_payload  = yumnia_product_payload( $product );
	$yumnia_is_var   = $product->is_type( 'variable' );
	$yumnia_in_stock = $product->is_in_stock();
	$yumnia_cats     = wc_get_product_terms( $product->get_id(), 'product_cat', array( 'fields' => 'all' ) );
	$yumnia_cat      = $yumnia_cats ? $yumnia_cats[0] : null;
	$yumnia_is_sandal = $yumnia_cat && false !== strpos( $yumnia_cat->slug, 'sandal' );

	// Foto: utama (berganti sesuai warna yang dipilih) lalu galeri.
	$yumnia_images = array_filter( array_merge( array( $product->get_image_id() ), $product->get_gallery_image_ids() ) );
	$yumnia_images = array_values( array_unique( array_map( 'intval', $yumnia_images ) ) );

	$yumnia_maker    = $product->get_meta( '_yumnia_pembuat' );
	$yumnia_bahan    = $product->get_meta( '_yumnia_bahan' );
	$yumnia_size     = $product->get_meta( '_yumnia_ukuran_info' );
	$yumnia_table    = yumnia_size_table( $product );
	$yumnia_heel     = $product->get_meta( '_yumnia_tinggi_hak' );
	$yumnia_sole     = $product->get_meta( '_yumnia_jenis_sol' );
	$yumnia_story    = $product->get_meta( '_yumnia_cerita_nama' );
	$yumnia_shopee   = $product->get_meta( '_yumnia_shopee' );
	$yumnia_tokped   = $product->get_meta( '_yumnia_tokopedia' );
	$yumnia_help     = get_page_by_path( 'bantuan' );
	$yumnia_max      = $product->managing_stock() && ! $product->backorders_allowed() ? (int) $product->get_stock_quantity() : 99;
	?>
	<div class="pdp" data-pdp <?php echo yumnia_payload_attr( $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
		<div class="container pdp__crumbs"><?php woocommerce_breadcrumb(); ?></div>
		<div class="container"><?php wc_print_notices(); ?></div>

		<div class="container pdp__grid">
			<div class="pdp__gallery gallery" data-gallery>
				<div class="gallery__track" data-gallery-track tabindex="0" aria-label="<?php esc_attr_e( 'Foto produk', 'yumnia' ); ?>">
					<?php foreach ( $yumnia_images as $yumnia_i => $yumnia_img ) : ?>
						<figure class="gallery__item<?php echo 0 === $yumnia_i ? ' is-hero' : ''; ?>" data-image-id="<?php echo (int) $yumnia_img; ?>">
							<?php
							echo wp_get_attachment_image(
								$yumnia_img,
								'yumnia-large',
								false,
								array(
									'class'         => 'gallery__img',
									'loading'       => 0 === $yumnia_i ? 'eager' : 'lazy',
									'fetchpriority' => 0 === $yumnia_i ? 'high' : 'auto',
									'sizes'         => '(min-width: 1000px) 58vw, 100vw',
									'alt'           => sprintf( __( '%1$s, foto %2$d', 'yumnia' ), $product->get_name(), $yumnia_i + 1 ),
									'data-full'     => wp_get_attachment_image_url( $yumnia_img, 'full' ),
								)
							);
							?>
						</figure>
					<?php endforeach; ?>
				</div>
				<?php if ( count( $yumnia_images ) > 1 ) : ?>
					<div class="gallery__ui">
						<span class="gallery__count"><span data-gallery-index>1</span> / <?php echo count( $yumnia_images ); ?></span>
						<div class="gallery__dots">
							<?php foreach ( $yumnia_images as $yumnia_i => $yumnia_img ) : ?>
								<button type="button" class="gallery__dot<?php echo 0 === $yumnia_i ? ' is-active' : ''; ?>" data-gallery-go="<?php echo (int) $yumnia_i; ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Foto %d', 'yumnia' ), $yumnia_i + 1 ) ); ?>"></button>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>
			</div>

			<div class="pdp__info">
				<div class="pdp__sticky">
					<?php echo yumnia_badges( $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php if ( $yumnia_cat ) : ?>
						<a class="eyebrow pdp__cat" href="<?php echo esc_url( get_term_link( $yumnia_cat ) ); ?>"><?php echo esc_html( $yumnia_cat->name ); ?></a>
					<?php endif; ?>
					<h1 class="pdp__title" data-split="now"><?php echo yumnia_split( $product->get_name() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h1>
					<p class="pdp__price" data-price><?php echo wp_kses_post( $product->get_price_html() ); ?></p>
					<?php if ( $product->get_short_description() ) : ?>
						<div class="pdp__short"><?php echo wp_kses_post( wpautop( $product->get_short_description() ) ); ?></div>
					<?php endif; ?>

					<form class="pdp-form" method="post" action="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>" data-pdp-form>
						<?php foreach ( $yumnia_payload['attrs'] as $yumnia_attr ) : ?>
							<?php $yumnia_is_color = 'pa_warna' === $yumnia_attr['t']; ?>
							<fieldset class="option" data-attr="<?php echo esc_attr( $yumnia_attr['k'] ); ?>">
								<legend class="option__legend">
									<span><?php echo esc_html( $yumnia_attr['l'] ); ?>: <strong data-attr-value><?php echo 1 === count( $yumnia_attr['o'] ) ? esc_html( $yumnia_attr['o'][0]['n'] ) : esc_html__( 'belum dipilih', 'yumnia' ); ?></strong></span>
									<?php if ( ! $yumnia_is_color && $yumnia_table ) : ?>
										<button type="button" class="option__guide" data-open-details="ukuran"><?php echo yumnia_icon( 'ruler' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Panduan ukuran', 'yumnia' ); ?></button>
									<?php endif; ?>
								</legend>
								<div class="option__list">
									<?php foreach ( $yumnia_attr['o'] as $yumnia_opt ) : ?>
										<label class="opt<?php echo $yumnia_is_color ? ' opt--swatch' : ''; ?>">
											<input type="radio" name="<?php echo esc_attr( $yumnia_attr['k'] ); ?>" value="<?php echo esc_attr( $yumnia_opt['s'] ); ?>" required <?php checked( 1 === count( $yumnia_attr['o'] ) ); ?>>
											<?php if ( $yumnia_is_color ) : ?>
												<span class="opt__swatch" style="--c:<?php echo esc_attr( $yumnia_opt['h'] ); ?>"></span>
												<span class="opt__name"><?php echo esc_html( $yumnia_opt['n'] ); ?></span>
											<?php else : ?>
												<span class="opt__text"><?php echo esc_html( $yumnia_opt['n'] ); ?></span>
											<?php endif; ?>
										</label>
									<?php endforeach; ?>
								</div>
							</fieldset>
						<?php endforeach; ?>

						<p class="pdp__stock" data-stock>
							<?php
							if ( ! $yumnia_in_stock ) {
								esc_html_e( 'Stok sedang habis', 'yumnia' );
							} elseif ( $yumnia_is_var ) {
								esc_html_e( 'Pilih varian untuk melihat stok', 'yumnia' );
							} else {
								esc_html_e( 'Tersedia', 'yumnia' );
							}
							?>
						</p>

						<div class="pdp__buy">
							<div class="qty qty--lg" data-qty-input>
								<button type="button" class="qty__btn" data-step="-1" aria-label="<?php esc_attr_e( 'Kurangi', 'yumnia' ); ?>"><?php echo yumnia_icon( 'minus' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
								<label class="sr-only" for="pdp-qty"><?php esc_html_e( 'Jumlah', 'yumnia' ); ?></label>
								<input class="qty__input" id="pdp-qty" type="number" name="quantity" value="1" min="1" max="<?php echo (int) $yumnia_max; ?>" inputmode="numeric">
								<button type="button" class="qty__btn" data-step="1" aria-label="<?php esc_attr_e( 'Tambah', 'yumnia' ); ?>"><?php echo yumnia_icon( 'plus' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
							</div>
							<button class="btn btn--block pdp__add" type="submit" name="add-to-cart" value="<?php echo (int) $product->get_id(); ?>" data-add-btn <?php disabled( ! $yumnia_in_stock ); ?>>
								<span class="btn__label"><?php echo $yumnia_in_stock ? esc_html__( 'Tambah ke keranjang', 'yumnia' ) : esc_html__( 'Habis', 'yumnia' ); ?></span>
							</button>
						</div>
						<?php if ( $yumnia_is_var ) : ?>
							<input type="hidden" name="product_id" value="<?php echo (int) $product->get_id(); ?>">
							<input type="hidden" name="variation_id" value="0" data-variation-id>
						<?php endif; ?>
					</form>

					<?php if ( ! $yumnia_in_stock ) : ?>
						<a class="btn btn--ghost btn--block" href="<?php echo esc_url( yumnia_wa_link( sprintf( __( 'Halo Yumnia, apakah %s akan restok?', 'yumnia' ), $product->get_name() ) ) ); ?>" target="_blank" rel="noopener"><span class="btn__label"><?php echo yumnia_icon( 'whatsapp' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Tanya jadwal restok', 'yumnia' ); ?></span></a>
					<?php endif; ?>

					<?php if ( $yumnia_shopee || $yumnia_tokped ) : ?>
						<div class="pdp__market">
							<span><?php esc_html_e( 'Juga tersedia di', 'yumnia' ); ?></span>
							<?php if ( $yumnia_shopee ) : ?>
								<a class="market market--shopee" href="<?php echo esc_url( $yumnia_shopee ); ?>" target="_blank" rel="noopener">Shopee<?php echo yumnia_icon( 'arrow-up-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
							<?php endif; ?>
							<?php if ( $yumnia_tokped ) : ?>
								<a class="market market--tokopedia" href="<?php echo esc_url( $yumnia_tokped ); ?>" target="_blank" rel="noopener">Tokopedia<?php echo yumnia_icon( 'arrow-up-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<?php if ( $yumnia_maker ) : ?>
						<p class="pdp__maker"><?php echo yumnia_icon( 'hand' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php echo esc_html( $yumnia_maker ); ?></span></p>
					<?php endif; ?>

					<div class="accordion">
						<?php if ( $yumnia_story ) : ?>
							<details class="acc" open>
								<summary class="acc__head"><?php esc_html_e( 'Cerita di balik nama', 'yumnia' ); ?><?php echo yumnia_icon( 'plus', 'acc__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></summary>
								<div class="acc__body"><p><?php echo esc_html( $yumnia_story ); ?></p></div>
							</details>
						<?php endif; ?>
						<?php if ( get_the_content() ) : ?>
							<details class="acc" <?php echo $yumnia_story ? '' : 'open'; ?>>
								<summary class="acc__head"><?php esc_html_e( 'Deskripsi', 'yumnia' ); ?><?php echo yumnia_icon( 'plus', 'acc__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></summary>
								<div class="acc__body"><?php the_content(); ?></div>
							</details>
						<?php endif; ?>
						<?php if ( $yumnia_bahan ) : ?>
							<details class="acc">
								<summary class="acc__head"><?php esc_html_e( 'Bahan & perawatan', 'yumnia' ); ?><?php echo yumnia_icon( 'plus', 'acc__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></summary>
								<div class="acc__body"><?php echo wp_kses_post( wpautop( esc_html( $yumnia_bahan ) ) ); ?></div>
							</details>
						<?php endif; ?>
						<?php if ( $yumnia_size || $yumnia_table || $yumnia_heel || $yumnia_sole ) : ?>
							<details class="acc" data-details="ukuran">
								<summary class="acc__head"><?php esc_html_e( 'Ukuran', 'yumnia' ); ?><?php echo yumnia_icon( 'plus', 'acc__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></summary>
								<div class="acc__body">
									<?php if ( $yumnia_size ) : ?>
										<p><?php echo esc_html( $yumnia_size ); ?></p>
									<?php endif; ?>
									<?php if ( $yumnia_table ) : ?>
										<table class="size-table">
											<thead><tr><th scope="col"><?php esc_html_e( 'Ukuran', 'yumnia' ); ?></th><th scope="col"><?php esc_html_e( 'Panjang telapak', 'yumnia' ); ?></th></tr></thead>
											<tbody>
												<?php foreach ( $yumnia_table as $yumnia_row ) : ?>
													<tr><td><?php echo esc_html( $yumnia_row[0] ); ?></td><td><?php echo esc_html( $yumnia_row[1] ); ?></td></tr>
												<?php endforeach; ?>
											</tbody>
										</table>
										<p class="size-table__tip"><?php esc_html_e( 'Ukur telapak kaki dari tumit ke ujung jari terpanjang, lalu pilih ukuran yang sama atau sedikit lebih besar.', 'yumnia' ); ?></p>
									<?php endif; ?>
									<?php if ( $yumnia_heel || $yumnia_sole ) : ?>
										<dl class="specs">
											<?php if ( $yumnia_heel ) : ?>
												<div><dt><?php esc_html_e( 'Tinggi hak', 'yumnia' ); ?></dt><dd><?php echo esc_html( $yumnia_heel ); ?></dd></div>
											<?php endif; ?>
											<?php if ( $yumnia_sole ) : ?>
												<div><dt><?php esc_html_e( 'Jenis sol', 'yumnia' ); ?></dt><dd><?php echo esc_html( $yumnia_sole ); ?></dd></div>
											<?php endif; ?>
										</dl>
									<?php endif; ?>
								</div>
							</details>
						<?php endif; ?>
						<details class="acc">
							<summary class="acc__head"><?php esc_html_e( 'Pengiriman & retur', 'yumnia' ); ?><?php echo yumnia_icon( 'plus', 'acc__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></summary>
							<div class="acc__body">
								<p><?php esc_html_e( 'Dikirim dari Desa Sukajadi, Bogor. Ongkir dihitung admin sesuai alamatmu dan dikonfirmasi lewat WhatsApp sebelum pembayaran.', 'yumnia' ); ?></p>
								<?php if ( $yumnia_help ) : ?>
									<p><a href="<?php echo esc_url( get_permalink( $yumnia_help ) . '#retur' ); ?>"><?php esc_html_e( 'Baca kebijakan retur & tukar ukuran', 'yumnia' ); ?></a></p>
								<?php endif; ?>
							</div>
						</details>
					</div>
				</div>
			</div>
		</div>

		<?php
		$yumnia_pairs = array_filter( array_map( 'wc_get_product', $product->get_cross_sell_ids() ) );
		$yumnia_pair_title = __( 'Cocok dengan…', 'yumnia' );
		if ( ! $yumnia_pairs ) {
			$yumnia_pairs      = array_filter( array_map( 'wc_get_product', wc_get_related_products( $product->get_id(), 4 ) ) );
			$yumnia_pair_title = __( 'Kamu mungkin suka', 'yumnia' );
		}
		$yumnia_pairs = array_filter(
			$yumnia_pairs,
			function ( $p ) {
				return $p && $p->is_visible();
			}
		);
		?>
		<?php if ( $yumnia_pairs ) : ?>
			<section class="section pairs">
				<div class="container section-head section-head--row">
					<div>
						<p class="eyebrow" data-reveal><?php esc_html_e( 'Padukan', 'yumnia' ); ?></p>
						<h2 class="section-title" data-split><?php echo esc_html( $yumnia_pair_title ); ?></h2>
					</div>
				</div>
				<div class="container">
					<div class="product-grid product-grid--pairs">
						<?php
						foreach ( array_values( $yumnia_pairs ) as $yumnia_i => $yumnia_pair ) {
							get_template_part( 'template-parts/product-card', null, array( 'product' => $yumnia_pair, 'index' => $yumnia_i ) );
						}
						?>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<div class="pdp-bar" data-pdp-bar aria-hidden="true">
			<img class="pdp-bar__img" src="<?php echo esc_url( $yumnia_payload['img'] ); ?>" alt="" width="48" height="60" data-pdp-bar-img>
			<div class="pdp-bar__info">
				<span class="pdp-bar__name"><?php echo esc_html( $product->get_name() ); ?></span>
				<span class="pdp-bar__price" data-price><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
			</div>
			<button class="btn btn--sm" type="button" data-pdp-bar-btn tabindex="-1" <?php disabled( ! $yumnia_in_stock ); ?>><span class="btn__label"><?php echo $yumnia_in_stock ? esc_html__( 'Tambah', 'yumnia' ) : esc_html__( 'Habis', 'yumnia' ); ?></span></button>
		</div>
	</div>
	<?php
endwhile;

get_footer();
