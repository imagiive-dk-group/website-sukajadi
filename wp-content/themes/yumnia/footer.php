<?php
/**
 * Footer situs.
 *
 * @package Yumnia
 */

defined( 'ABSPATH' ) || exit;

$yumnia_help = get_page_by_path( 'bantuan' );
$yumnia_help = $yumnia_help ? get_permalink( $yumnia_help ) : home_url( '/bantuan/' );
$yumnia_socials = array(
	'instagram' => array( 'Instagram', yumnia_mod( 'instagram' ) ),
	'tiktok'    => array( 'TikTok', yumnia_mod( 'tiktok' ) ),
	'facebook'  => array( 'Facebook', yumnia_mod( 'facebook' ) ),
);
?>
</main>

<footer class="site-footer">
	<div class="container">
		<div class="footer-cta">
			<div>
				<p class="eyebrow eyebrow--light" data-reveal><?php esc_html_e( 'Ada pertanyaan?', 'yumnia' ); ?></p>
				<h2 class="footer-cta__title" data-split><?php esc_html_e( 'Ngobrol langsung dengan admin Yumnia.', 'yumnia' ); ?></h2>
			</div>
			<a class="btn btn--light" data-magnetic data-reveal href="<?php echo esc_url( yumnia_wa_link( yumnia_mod( 'wa_greeting' ) ) ); ?>" target="_blank" rel="noopener">
				<span class="btn__label"><?php echo yumnia_icon( 'whatsapp' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Chat WhatsApp', 'yumnia' ); ?></span>
			</a>
		</div>

		<div class="footer-grid">
			<div class="footer-col footer-col--brand" data-reveal>
				<?php echo yumnia_monogram( 'footer-mark' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<p class="footer-tagline"><?php esc_html_e( 'For a better you', 'yumnia' ); ?></p>
				<p class="footer-address"><?php echo yumnia_icon( 'pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><a href="<?php echo esc_url( yumnia_mod( 'maps_url' ) ); ?>" target="_blank" rel="noopener"><?php echo esc_html( yumnia_mod( 'address' ) ); ?></a></p>
			</div>
			<div class="footer-col" data-reveal style="--d:.08s">
				<h3 class="footer-title"><?php esc_html_e( 'Belanja', 'yumnia' ); ?></h3>
				<?php
				if ( has_nav_menu( 'footer_shop' ) ) {
					yumnia_menu( 'footer_shop', 'footer-links' );
				} else {
					echo '<ul class="footer-links">';
					if ( function_exists( 'wc_get_page_permalink' ) ) {
						echo '<li><a href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '">' . esc_html__( 'Semua produk', 'yumnia' ) . '</a></li>';
					}
					foreach ( yumnia_default_menu() as $item ) {
						if ( ! is_wp_error( $item['url'] ) && $item['url'] && in_array( $item['label'], array( 'Hijab', 'Sandal', 'Padu-padan' ), true ) ) {
							echo '<li><a href="' . esc_url( $item['url'] ) . '">' . esc_html( $item['label'] ) . '</a></li>';
						}
					}
					echo '</ul>';
				}
				?>
			</div>
			<div class="footer-col" data-reveal style="--d:.16s">
				<h3 class="footer-title"><?php esc_html_e( 'Bantuan', 'yumnia' ); ?></h3>
				<?php if ( has_nav_menu( 'footer_help' ) ) : ?>
					<?php yumnia_menu( 'footer_help', 'footer-links' ); ?>
				<?php else : ?>
					<ul class="footer-links">
						<li><a href="<?php echo esc_url( $yumnia_help . '#cara-order' ); ?>"><?php esc_html_e( 'Cara order', 'yumnia' ); ?></a></li>
						<li><a href="<?php echo esc_url( $yumnia_help . '#ongkir' ); ?>"><?php esc_html_e( 'Ongkos kirim', 'yumnia' ); ?></a></li>
						<li><a href="<?php echo esc_url( $yumnia_help . '#retur' ); ?>"><?php esc_html_e( 'Retur & tukar ukuran', 'yumnia' ); ?></a></li>
						<li><a href="<?php echo esc_url( $yumnia_help . '#faq' ); ?>"><?php esc_html_e( 'Tanya jawab', 'yumnia' ); ?></a></li>
						<?php $yumnia_contact = get_page_by_path( 'kontak' ); ?>
						<?php if ( $yumnia_contact ) : ?>
							<li><a href="<?php echo esc_url( get_permalink( $yumnia_contact ) ); ?>"><?php esc_html_e( 'Kontak', 'yumnia' ); ?></a></li>
						<?php endif; ?>
					</ul>
				<?php endif; ?>
			</div>
			<div class="footer-col" data-reveal style="--d:.24s">
				<h3 class="footer-title"><?php esc_html_e( 'Ikuti kami', 'yumnia' ); ?></h3>
				<ul class="socials">
					<?php foreach ( $yumnia_socials as $yumnia_key => $yumnia_social ) : ?>
						<?php if ( $yumnia_social[1] ) : ?>
							<li><a class="social" href="<?php echo esc_url( $yumnia_social[1] ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( $yumnia_social[0] ); ?>"><?php echo yumnia_icon( $yumnia_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
				<p class="footer-hours"><?php echo esc_html( yumnia_mod( 'hours' ) ); ?></p>
			</div>
		</div>

		<div class="partners" data-reveal>
			<p class="partners__title"><?php echo esc_html( yumnia_mod( 'partners_title' ) ); ?></p>
			<ul class="partners__list">
				<?php foreach ( yumnia_partners() as $yumnia_partner ) : ?>
					<li class="partners__item">
						<?php if ( $yumnia_partner['logo'] ) : ?>
							<?php echo wp_get_attachment_image( $yumnia_partner['logo'], 'medium', false, array( 'alt' => $yumnia_partner['name'], 'loading' => 'lazy' ) ); ?>
						<?php else : ?>
							<span><?php echo esc_html( $yumnia_partner['name'] ); ?></span>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>

	<div class="footer-word" aria-hidden="true" data-reveal="letters">
		<?php foreach ( str_split( 'YUMNIA' ) as $yumnia_i => $yumnia_letter ) : ?>
			<span style="--i:<?php echo (int) $yumnia_i; ?>"><?php echo esc_html( $yumnia_letter ); ?></span>
		<?php endforeach; ?>
	</div>

	<div class="footer-bottom container">
		<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> Yumnia · <?php esc_html_e( 'Merek milik Desa Wisata Sukajadi, Tamansari, Bogor', 'yumnia' ); ?></p>
		<button type="button" class="to-top" data-to-top><?php esc_html_e( 'Kembali ke atas', 'yumnia' ); ?> <?php echo yumnia_icon( 'arrow-up-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
	</div>
</footer>

<a class="wa-float" href="<?php echo esc_url( yumnia_wa_link( yumnia_mod( 'wa_greeting' ) ) ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Chat WhatsApp admin', 'yumnia' ); ?>">
	<?php echo yumnia_icon( 'whatsapp' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<span class="wa-float__label"><?php esc_html_e( 'Tanya admin', 'yumnia' ); ?></span>
</a>

<?php get_template_part( 'template-parts/overlays' ); ?>
<?php wp_footer(); ?>
</body>
</html>
