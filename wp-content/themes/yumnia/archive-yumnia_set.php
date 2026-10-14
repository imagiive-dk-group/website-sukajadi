<?php
/**
 * Daftar set padu-padan.
 *
 * @package Yumnia
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<header class="page-hero">
	<div class="container">
		<p class="eyebrow" data-reveal><?php esc_html_e( 'Padu-padan', 'yumnia' ); ?></p>
		<h1 class="page-hero__title" data-split="now"><?php echo yumnia_split( __( 'Serasi dari kepala sampai kaki.', 'yumnia' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h1>
		<p class="page-hero__text" data-reveal><?php esc_html_e( 'Set hijab dan sandal yang warnanya sudah kami padukan. Ambil satu set sekaligus, pilih ukuran sandalmu, selesai.', 'yumnia' ); ?></p>
		<?php if ( function_exists( 'wc_get_page_permalink' ) ) : ?>
			<nav class="tabs tabs--links" aria-label="<?php esc_attr_e( 'Kategori', 'yumnia' ); ?>" data-reveal>
				<a class="tab" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Semua', 'yumnia' ); ?></a>
				<?php foreach ( array( 'hijab', 'sandal' ) as $yumnia_slug ) : ?>
					<?php $yumnia_cat = get_term_by( 'slug', $yumnia_slug, 'product_cat' ); ?>
					<?php if ( $yumnia_cat ) : ?>
						<a class="tab" href="<?php echo esc_url( get_term_link( $yumnia_cat ) ); ?>"><?php echo esc_html( $yumnia_cat->name ); ?></a>
					<?php endif; ?>
				<?php endforeach; ?>
				<a class="tab is-active" aria-current="page" href="<?php echo esc_url( get_post_type_archive_link( 'yumnia_set' ) ); ?>"><?php esc_html_e( 'Padu-padan', 'yumnia' ); ?></a>
			</nav>
		<?php endif; ?>
	</div>
</header>
<div class="container looks-list">
	<?php if ( have_posts() ) : ?>
		<?php
		$yumnia_i = 0;
		while ( have_posts() ) {
			the_post();
			get_template_part(
				'template-parts/set-card',
				null,
				array(
					'post'   => get_post(),
					'index'  => $yumnia_i++,
					'layout' => 'feature',
				)
			);
		}
		?>
	<?php else : ?>
		<p><?php esc_html_e( 'Belum ada set padu-padan.', 'yumnia' ); ?></p>
	<?php endif; ?>
</div>
<?php
get_footer();
