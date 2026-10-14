<?php
/**
 * Header situs.
 *
 * @package Yumnia
 */

defined( 'ABSPATH' ) || exit;
$yumnia_count = function_exists( 'yumnia_cart_count' ) ? yumnia_cart_count() : 0;
?><!doctype html>
<html <?php language_attributes(); ?> class="no-js">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<script>(function(h){h.className=h.className.replace('no-js','js');try{if(!sessionStorage.getItem('yumnia-intro')&&!matchMedia('(prefers-reduced-motion: reduce)').matches){h.classList.add('has-intro');}}catch(e){}})(document.documentElement);</script>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main"><?php esc_html_e( 'Langsung ke konten', 'yumnia' ); ?></a>

<div class="topbar" role="note">
	<div class="topbar__track">
		<?php for ( $i = 0; $i < 4; $i++ ) : ?>
			<span <?php echo $i ? 'aria-hidden="true"' : ''; ?>><?php echo esc_html( yumnia_mod( 'topbar_text' ) ); ?></span>
		<?php endfor; ?>
	</div>
</div>

<header class="site-header" data-header>
	<div class="site-header__inner">
		<div class="site-header__left">
			<button class="icon-btn menu-toggle" type="button" data-open="menu" aria-controls="menu-panel" aria-expanded="false">
				<span class="burger" aria-hidden="true"><i></i><i></i></span>
				<span class="sr-only"><?php esc_html_e( 'Buka menu', 'yumnia' ); ?></span>
			</button>
			<nav class="primary-nav" aria-label="<?php esc_attr_e( 'Menu utama', 'yumnia' ); ?>">
				<?php yumnia_menu( 'primary', 'primary-nav__list' ); ?>
			</nav>
		</div>
		<div class="site-header__brand">
			<?php echo yumnia_logo(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
		<div class="site-header__right">
			<button class="icon-btn" type="button" data-open="search" aria-controls="search-panel" aria-expanded="false">
				<?php echo yumnia_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<span class="sr-only"><?php esc_html_e( 'Cari produk', 'yumnia' ); ?></span>
			</button>
			<button class="icon-btn cart-btn" type="button" data-open="cart" aria-controls="cart-drawer" aria-expanded="false">
				<?php echo yumnia_icon( 'bag' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<span class="cart-count js-cart-count" data-count="<?php echo (int) $yumnia_count; ?>"><?php echo (int) $yumnia_count; ?></span>
				<span class="sr-only"><?php esc_html_e( 'Keranjang', 'yumnia' ); ?></span>
			</button>
		</div>
	</div>
</header>

<main id="main" class="site-main">
