<?php
/**
 * Panel yang muncul di atas halaman: menu, pencarian, keranjang, pilih varian, notifikasi.
 *
 * @package Yumnia
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="scrim" data-scrim hidden></div>

<div class="panel panel--menu" id="menu-panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Menu', 'yumnia' ); ?>" data-panel="menu" hidden>
	<div class="panel__bg" aria-hidden="true"><?php echo yumnia_monogram( 'panel__mono' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
	<div class="panel__top">
		<span class="panel__label"><?php esc_html_e( 'Menu', 'yumnia' ); ?></span>
		<button class="icon-btn" type="button" data-close aria-label="<?php esc_attr_e( 'Tutup menu', 'yumnia' ); ?>"><?php echo yumnia_icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
	</div>
	<nav class="menu-big" aria-label="<?php esc_attr_e( 'Menu utama', 'yumnia' ); ?>">
		<?php yumnia_menu( 'primary', 'menu-big__list' ); ?>
	</nav>
	<div class="panel__foot">
		<a class="btn btn--light btn--sm" href="<?php echo esc_url( yumnia_wa_link( yumnia_mod( 'wa_greeting' ) ) ); ?>" target="_blank" rel="noopener"><span class="btn__label"><?php echo yumnia_icon( 'whatsapp' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Chat admin', 'yumnia' ); ?></span></a>
		<div class="panel__socials">
			<?php foreach ( array( 'instagram' => 'Instagram', 'tiktok' => 'TikTok', 'facebook' => 'Facebook' ) as $yumnia_key => $yumnia_label ) : ?>
				<?php if ( yumnia_mod( $yumnia_key ) ) : ?>
					<a href="<?php echo esc_url( yumnia_mod( $yumnia_key ) ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $yumnia_label ); ?></a>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
	</div>
</div>

<div class="panel panel--search" id="search-panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Cari produk', 'yumnia' ); ?>" data-panel="search" hidden>
	<div class="search-box">
		<form class="search-form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php echo yumnia_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<label class="sr-only" for="search-input"><?php esc_html_e( 'Cari produk', 'yumnia' ); ?></label>
			<input id="search-input" class="search-form__input" type="search" name="s" placeholder="<?php esc_attr_e( 'Cari pashmina, sandal, warna…', 'yumnia' ); ?>" autocomplete="off" data-search-input>
			<input type="hidden" name="post_type" value="product">
			<button class="icon-btn" type="button" data-close aria-label="<?php esc_attr_e( 'Tutup pencarian', 'yumnia' ); ?>"><?php echo yumnia_icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
		</form>
		<div class="search-suggest" data-search-suggest>
			<p class="search-suggest__title"><?php esc_html_e( 'Pencarian populer', 'yumnia' ); ?></p>
			<div class="chips">
				<?php foreach ( array( 'Pashmina', 'Bergo', 'Sandal hak', 'Maroon', 'Blush' ) as $yumnia_term ) : ?>
					<button type="button" class="chip" data-search-term="<?php echo esc_attr( $yumnia_term ); ?>"><?php echo esc_html( $yumnia_term ); ?></button>
				<?php endforeach; ?>
			</div>
		</div>
		<div class="search-results" data-search-results aria-live="polite"></div>
	</div>
</div>

<aside class="drawer" id="cart-drawer" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Keranjang', 'yumnia' ); ?>" data-panel="cart" hidden>
	<div class="drawer__head">
		<p class="drawer__title"><?php esc_html_e( 'Keranjang', 'yumnia' ); ?> <span class="drawer__count js-cart-count" data-count="<?php echo function_exists( 'yumnia_cart_count' ) ? (int) yumnia_cart_count() : 0; ?>"><?php echo function_exists( 'yumnia_cart_count' ) ? (int) yumnia_cart_count() : 0; ?></span></p>
		<button class="icon-btn" type="button" data-close aria-label="<?php esc_attr_e( 'Tutup keranjang', 'yumnia' ); ?>"><?php echo yumnia_icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
	</div>
	<?php
	if ( function_exists( 'yumnia_cart_drawer_content' ) ) {
		echo yumnia_cart_drawer_content(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	?>
</aside>

<div class="sheet" id="variant-sheet" role="dialog" aria-modal="true" aria-labelledby="sheet-title" data-panel="sheet" hidden>
	<div class="sheet__grip" aria-hidden="true"></div>
	<div class="sheet__head">
		<p class="sheet__title" id="sheet-title"><?php esc_html_e( 'Pilih varian', 'yumnia' ); ?></p>
		<button class="icon-btn" type="button" data-close aria-label="<?php esc_attr_e( 'Tutup', 'yumnia' ); ?>"><?php echo yumnia_icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
	</div>
	<div class="sheet__body" data-sheet-body></div>
	<div class="sheet__foot">
		<button class="btn btn--block" type="button" data-sheet-submit disabled><span class="btn__label"><?php esc_html_e( 'Tambah ke keranjang', 'yumnia' ); ?></span></button>
	</div>
</div>

<div class="toasts" aria-live="polite" data-toasts></div>
<div class="cursor" aria-hidden="true"><span class="cursor__label"></span></div>
