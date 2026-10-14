<?php
/**
 * Dasar SEO: meta description, Open Graph (pratinjau link di WhatsApp/IG), dan Google Analytics.
 * Otomatis nonaktif jika plugin SEO (Yoast / Rank Math) dipasang.
 *
 * @package Yumnia
 */

defined( 'ABSPATH' ) || exit;

/**
 * Apakah plugin SEO lain sedang aktif.
 */
function yumnia_has_seo_plugin() {
	return defined( 'WPSEO_VERSION' ) || class_exists( 'RankMath' ) || defined( 'SEOPRESS_VERSION' ) || defined( 'AIOSEO_VERSION' );
}

/**
 * Data meta untuk halaman saat ini.
 *
 * @return array{title:string,description:string,image:string,type:string,url:string}
 */
function yumnia_meta() {
	$meta = array(
		'title'       => wp_get_document_title(),
		'description' => get_bloginfo( 'description' ),
		'image'       => '',
		'type'        => 'website',
		'url'         => home_url( add_query_arg( array() ) ),
	);

	if ( is_front_page() ) {
		$meta['description'] = yumnia_mod( 'hero_text' );
		$meta['url']         = home_url( '/' );
	} elseif ( function_exists( 'is_product' ) && is_product() ) {
		$product = wc_get_product( get_queried_object_id() );
		if ( $product ) {
			$meta['description'] = $product->get_short_description() ? $product->get_short_description() : $product->get_description();
			$meta['image']       = wp_get_attachment_image_url( $product->get_image_id(), 'yumnia-large' );
			$meta['type']        = 'product';
			$meta['url']         = get_permalink( $product->get_id() );
		}
	} elseif ( is_singular() ) {
		$post                = get_queried_object();
		$meta['description'] = has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_trim_words( $post->post_content, 30, '…' );
		$meta['url']         = get_permalink( $post );
		if ( has_post_thumbnail( $post ) ) {
			$meta['image'] = get_the_post_thumbnail_url( $post, 'yumnia-wide' );
		}
	} elseif ( is_tax() || is_category() ) {
		$term = get_queried_object();
		if ( $term && ! empty( $term->description ) ) {
			$meta['description'] = $term->description;
		}
		if ( $term && 'product_cat' === $term->taxonomy ) {
			$thumb = get_term_meta( $term->term_id, 'thumbnail_id', true );
			if ( $thumb ) {
				$meta['image'] = wp_get_attachment_image_url( $thumb, 'yumnia-large' );
			}
		}
		$meta['url'] = get_term_link( $term );
	}

	if ( ! $meta['image'] ) {
		$meta['image'] = yumnia_image_url( yumnia_mod( 'og_image' ), 'full', YUMNIA_URI . '/assets/img/og-default.jpg' );
	}
	$meta['description'] = wp_trim_words( wp_strip_all_tags( (string) $meta['description'] ), 32, '…' );

	return $meta;
}

add_action(
	'wp_head',
	function () {
		if ( yumnia_has_seo_plugin() ) {
			return;
		}
		$m = yumnia_meta();
		if ( is_wp_error( $m['url'] ) ) {
			$m['url'] = home_url( '/' );
		}
		echo "\n<!-- Yumnia SEO -->\n";
		if ( $m['description'] ) {
			printf( '<meta name="description" content="%s">' . "\n", esc_attr( $m['description'] ) );
		}
		printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
		printf( '<meta property="og:locale" content="%s">' . "\n", esc_attr( str_replace( '-', '_', get_bloginfo( 'language' ) ) ) );
		printf( '<meta property="og:type" content="%s">' . "\n", esc_attr( $m['type'] ) );
		printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $m['title'] ) );
		printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $m['description'] ) );
		printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $m['url'] ) );
		if ( $m['image'] ) {
			printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $m['image'] ) );
		}
		echo '<meta name="twitter:card" content="summary_large_image">' . "\n";

		if ( 'product' === $m['type'] && function_exists( 'wc_get_product' ) ) {
			$product = wc_get_product( get_queried_object_id() );
			if ( $product ) {
				printf( '<meta property="product:price:amount" content="%s">' . "\n", esc_attr( $product->get_price() ) );
				printf( '<meta property="product:price:currency" content="%s">' . "\n", esc_attr( get_woocommerce_currency() ) );
			}
		}
	},
	5
);

/**
 * Google Analytics 4 (opsional, isi ID di Customizer).
 */
add_action(
	'wp_head',
	function () {
		$id = trim( (string) yumnia_mod( 'ga4_id' ) );
		if ( '' === $id || is_user_logged_in() && current_user_can( 'edit_posts' ) ) {
			return;
		}
		printf(
			'<script async src="https://www.googletagmanager.com/gtag/js?id=%1$s"></script><script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag("js",new Date());gtag("config","%1$s");</script>' . "\n",
			esc_attr( $id )
		);
	},
	20
);
