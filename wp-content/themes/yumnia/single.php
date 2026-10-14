<?php
/**
 * Tulisan tunggal.
 *
 * @package Yumnia
 */

defined( 'ABSPATH' ) || exit;

get_header();
while ( have_posts() ) :
	the_post();
	?>
	<article <?php post_class( 'page' ); ?>>
		<header class="page-hero">
			<div class="container">
				<p class="eyebrow"><?php echo esc_html( get_the_date() ); ?></p>
				<h1 class="page-hero__title" data-split="now"><?php echo yumnia_split( get_the_title() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h1>
			</div>
		</header>
		<div class="entry-content"><?php the_content(); ?></div>
	</article>
	<?php
endwhile;
get_footer();
