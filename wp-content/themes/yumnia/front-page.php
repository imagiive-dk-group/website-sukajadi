<?php
/**
 * Beranda.
 *
 * @package Yumnia
 */

defined( 'ABSPATH' ) || exit;

get_header();

$yumnia_has_wc  = class_exists( 'WooCommerce' );
$yumnia_shop    = $yumnia_has_wc ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
$yumnia_sets    = get_posts(
	array(
		'post_type'      => 'yumnia_set',
		'posts_per_page' => 3,
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
	)
);
$yumnia_story   = get_page_by_path( 'cerita' );
$yumnia_hero    = (int) yumnia_mod( 'hero_image' );
$yumnia_hero_hp = (int) yumnia_mod( 'hero_image_hp' );
?>

<section class="hero" data-hero>
	<div class="hero__media" data-hero-media>
		<?php if ( $yumnia_hero ) : ?>
			<picture>
				<?php if ( $yumnia_hero_hp ) : ?>
					<source media="(max-width: 700px)" srcset="<?php echo esc_url( wp_get_attachment_image_url( $yumnia_hero_hp, 'full' ) ); ?>">
				<?php endif; ?>
				<?php
				echo wp_get_attachment_image(
					$yumnia_hero,
					'full',
					false,
					array(
						'class'         => 'hero__img',
						'loading'       => 'eager',
						'fetchpriority' => 'high',
						'sizes'         => '100vw',
						'alt'           => esc_attr__( 'Model memakai hijab dan sandal Yumnia', 'yumnia' ),
					)
				);
				?>
			</picture>
		<?php else : ?>
			<div class="hero__art" aria-hidden="true"><?php echo yumnia_monogram( 'hero__art-mono' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
		<?php endif; ?>
	</div>
	<div class="hero__shade" aria-hidden="true"></div>
	<div class="hero__content">
		<p class="eyebrow eyebrow--light hero__eyebrow"><?php esc_html_e( 'Hijab & sandal dari Desa Sukajadi', 'yumnia' ); ?></p>
		<h1 class="hero__title" data-split="now"><?php echo yumnia_split( yumnia_mod( 'hero_title' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h1>
		<div class="hero__bottom">
			<p class="hero__text"><?php echo esc_html( yumnia_mod( 'hero_text' ) ); ?></p>
			<div class="hero__actions">
				<a class="btn btn--light" data-magnetic href="<?php echo esc_url( $yumnia_shop ); ?>"><span class="btn__label"><?php echo esc_html( yumnia_mod( 'hero_cta' ) ); ?><?php echo yumnia_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span></a>
				<?php if ( $yumnia_sets ) : ?>
					<a class="link-light" href="#padu-padan"><?php esc_html_e( 'Lihat padu-padan', 'yumnia' ); ?></a>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<a class="hero__scroll" href="#kategori" aria-label="<?php esc_attr_e( 'Gulir ke bawah', 'yumnia' ); ?>">
		<svg class="hero__scroll-ring" viewBox="0 0 100 100" aria-hidden="true"><defs><path id="ring" d="M50 50m-38 0a38 38 0 1 1 76 0a38 38 0 1 1-76 0"/></defs><text><textPath href="#ring"><?php esc_html_e( 'Gulir · Jelajahi koleksi · Gulir · Jelajahi koleksi ·', 'yumnia' ); ?></textPath></text></svg>
		<?php echo yumnia_icon( 'arrow-down' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</a>
</section>

<div class="band" data-marquee aria-hidden="true">
	<div class="band__track">
		<?php for ( $yumnia_i = 0; $yumnia_i < 2; $yumnia_i++ ) : ?>
			<div class="band__group">
				<span>Hijab</span><i>✦</i><span class="is-outline">Sandal</span><i>✦</i><span>Padu-padan</span><i>✦</i><span class="is-outline">Dari Sukajadi</span><i>✦</i><span>For a better you</span><i>✦</i>
			</div>
		<?php endfor; ?>
	</div>
</div>

<?php if ( $yumnia_has_wc ) : ?>
	<section class="section doors" id="kategori">
		<div class="container">
			<div class="section-head">
				<p class="eyebrow" data-reveal><?php esc_html_e( 'Belanja per kategori', 'yumnia' ); ?></p>
				<h2 class="section-title" data-split><?php esc_html_e( 'Dua pintu, satu gaya.', 'yumnia' ); ?></h2>
			</div>
			<div class="doors__grid">
				<?php
				foreach ( array( 'hijab', 'sandal' ) as $yumnia_n => $yumnia_slug ) :
					$yumnia_term = get_term_by( 'slug', $yumnia_slug, 'product_cat' );
					if ( ! $yumnia_term ) {
						continue;
					}
					$yumnia_thumb = (int) get_term_meta( $yumnia_term->term_id, 'thumbnail_id', true );
					?>
					<a class="door" href="<?php echo esc_url( get_term_link( $yumnia_term ) ); ?>" data-cursor="<?php esc_attr_e( 'Buka', 'yumnia' ); ?>" style="--d:<?php echo esc_attr( (string) ( 0.12 * $yumnia_n ) ); ?>s">
						<div class="door__media" data-reveal="clip">
							<?php
							if ( $yumnia_thumb ) {
								echo wp_get_attachment_image(
									$yumnia_thumb,
									'yumnia-large',
									false,
									array(
										'class'   => 'door__img',
										'sizes'   => '(min-width: 900px) 45vw, 50vw',
										'loading' => 'lazy',
										'alt'     => $yumnia_term->name,
									)
								);
							}
							?>
						</div>
						<div class="door__label">
							<span class="door__name"><?php echo esc_html( $yumnia_term->name ); ?></span>
							<span class="door__count"><?php echo esc_html( sprintf( _n( '%d model', '%d model', $yumnia_term->count, 'yumnia' ), $yumnia_term->count ) ); ?></span>
							<span class="door__arrow"><?php echo yumnia_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						</div>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<?php $yumnia_launch = yumnia_launch_products( 8 ); ?>
	<?php if ( $yumnia_launch ) : ?>
		<section class="section launch" id="koleksi">
			<div class="container section-head section-head--row">
				<div>
					<p class="eyebrow" data-reveal><?php esc_html_e( 'Koleksi peluncuran', 'yumnia' ); ?></p>
					<h2 class="section-title" data-split><?php esc_html_e( 'Baru dari Sukajadi.', 'yumnia' ); ?></h2>
				</div>
				<a class="link-arrow" data-reveal href="<?php echo esc_url( $yumnia_shop ); ?>"><?php esc_html_e( 'Lihat semua', 'yumnia' ); ?><?php echo yumnia_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
			</div>
			<div class="rail" data-rail>
				<div class="rail__track" data-rail-track>
					<?php
					foreach ( $yumnia_launch as $yumnia_i => $yumnia_product ) {
						get_template_part(
							'template-parts/product-card',
							null,
							array(
								'product' => $yumnia_product,
								'index'   => $yumnia_i,
								'sizes'   => '(min-width: 1100px) 23vw, (min-width: 700px) 36vw, 72vw',
							)
						);
					}
					?>
				</div>
			</div>
			<div class="container rail__ui">
				<div class="rail__progress"><span data-rail-bar></span></div>
				<div class="rail__nav">
					<button class="round-btn" type="button" data-rail-prev aria-label="<?php esc_attr_e( 'Sebelumnya', 'yumnia' ); ?>"><?php echo yumnia_icon( 'arrow-left' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
					<button class="round-btn" type="button" data-rail-next aria-label="<?php esc_attr_e( 'Berikutnya', 'yumnia' ); ?>"><?php echo yumnia_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
				</div>
			</div>
		</section>
	<?php endif; ?>
<?php endif; ?>

<section class="section intro">
	<div class="container intro__grid">
		<p class="intro__text" data-split><?php echo esc_html( yumnia_mod( 'intro_text' ) ); ?></p>
		<div class="intro__stat" data-reveal>
			<span class="intro__num" data-count><?php echo esc_html( yumnia_mod( 'intro_stat' ) ); ?></span>
			<span class="intro__label"><?php echo esc_html( yumnia_mod( 'intro_stat_lbl' ) ); ?></span>
			<?php if ( $yumnia_story ) : ?>
				<a class="btn btn--ghost btn--sm" href="<?php echo esc_url( get_permalink( $yumnia_story ) ); ?>"><span class="btn__label"><?php esc_html_e( 'Kenali Yumnia', 'yumnia' ); ?></span></a>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php if ( $yumnia_sets && $yumnia_has_wc ) : ?>
	<section class="section looks-home" id="padu-padan">
		<div class="container">
			<div class="section-head section-head--row">
				<div>
					<p class="eyebrow" data-reveal><?php esc_html_e( 'Padu-padan', 'yumnia' ); ?></p>
					<h2 class="section-title" data-split><?php esc_html_e( 'Serasi dari kepala sampai kaki.', 'yumnia' ); ?></h2>
				</div>
				<div class="tabs" role="tablist" data-looks-tabs>
					<?php foreach ( $yumnia_sets as $yumnia_i => $yumnia_set ) : ?>
						<button class="tab<?php echo 0 === $yumnia_i ? ' is-active' : ''; ?>" type="button" role="tab" aria-selected="<?php echo 0 === $yumnia_i ? 'true' : 'false'; ?>" data-look-tab="<?php echo (int) $yumnia_i; ?>"><?php echo esc_html( get_the_title( $yumnia_set ) ); ?></button>
					<?php endforeach; ?>
				</div>
			</div>
			<div class="looks-stage" data-looks>
				<?php
				foreach ( $yumnia_sets as $yumnia_i => $yumnia_set ) {
					echo '<div class="looks-stage__slide' . ( 0 === $yumnia_i ? ' is-active' : '' ) . '" data-look="' . (int) $yumnia_i . '"' . ( 0 === $yumnia_i ? '' : ' hidden' ) . '>';
					get_template_part(
						'template-parts/set-card',
						null,
						array(
							'post'   => $yumnia_set,
							'index'  => $yumnia_i,
							'layout' => 'feature',
						)
					);
					echo '</div>';
				}
				?>
			</div>
		</div>
	</section>
<?php endif; ?>

<section class="story">
	<div class="container story__grid">
		<div class="story__media" data-reveal="arch">
			<?php
			$yumnia_story_img = (int) yumnia_mod( 'story_image' );
			if ( $yumnia_story_img ) {
				echo wp_get_attachment_image(
					$yumnia_story_img,
					'yumnia-large',
					false,
					array(
						'class'   => 'story__img',
						'loading' => 'lazy',
						'sizes'   => '(min-width: 900px) 40vw, 90vw',
						'alt'     => yumnia_mod( 'story_caption' ),
					)
				);
			} else {
				echo '<div class="story__placeholder">' . yumnia_monogram() . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			?>
		</div>
		<div class="story__body">
			<p class="eyebrow" data-reveal><?php esc_html_e( 'Dari Sukajadi', 'yumnia' ); ?></p>
			<blockquote class="story__quote" data-split>&ldquo;<?php echo esc_html( yumnia_mod( 'story_quote' ) ); ?>&rdquo;</blockquote>
			<p class="story__caption" data-reveal><?php echo esc_html( yumnia_mod( 'story_caption' ) ); ?></p>
			<?php if ( $yumnia_story ) : ?>
				<a class="link-arrow" data-reveal href="<?php echo esc_url( get_permalink( $yumnia_story ) ); ?>"><?php esc_html_e( 'Baca cerita Yumnia', 'yumnia' ); ?><?php echo yumnia_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php $yumnia_press = yumnia_press(); ?>
<?php if ( $yumnia_press ) : ?>
	<section class="section press">
		<div class="container">
			<p class="eyebrow" data-reveal><?php esc_html_e( 'Diliput oleh', 'yumnia' ); ?></p>
			<ul class="press__list">
				<?php foreach ( $yumnia_press as $yumnia_i => $yumnia_item ) : ?>
					<li data-reveal style="--d:<?php echo esc_attr( (string) ( 0.06 * $yumnia_i ) ); ?>s">
						<?php if ( $yumnia_item['url'] ) : ?>
							<a class="press__item" href="<?php echo esc_url( $yumnia_item['url'] ); ?>" target="_blank" rel="noopener"><span><?php echo esc_html( $yumnia_item['name'] ); ?></span><?php echo yumnia_icon( 'arrow-up-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
						<?php else : ?>
							<span class="press__item"><span><?php echo esc_html( $yumnia_item['name'] ); ?></span></span>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>
<?php endif; ?>

<?php
get_footer();
