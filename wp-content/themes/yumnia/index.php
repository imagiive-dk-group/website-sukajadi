<?php
/**
 * Cadangan untuk arsip & tulisan.
 *
 * @package Yumnia
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<header class="page-hero">
	<div class="container">
		<h1 class="page-hero__title" data-split="now"><?php echo yumnia_split( is_search() ? sprintf( __( 'Hasil untuk “%s”', 'yumnia' ), get_search_query() ) : wp_strip_all_tags( get_the_archive_title() ? get_the_archive_title() : get_bloginfo( 'name' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h1>
	</div>
</header>
<div class="container post-list">
	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<article <?php post_class( 'post-item' ); ?> data-reveal>
				<h2 class="post-item__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<p><?php echo esc_html( get_the_excerpt() ); ?></p>
			</article>
		<?php endwhile; ?>
		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<p><?php esc_html_e( 'Belum ada konten.', 'yumnia' ); ?></p>
	<?php endif; ?>
</div>
<?php
get_footer();
