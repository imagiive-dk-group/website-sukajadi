<?php
/**
 * Pengaturan tema di Tampilan → Sesuaikan (Customizer).
 * Tim desa bisa mengubah teks, foto, kontak, dan tautan tanpa menyentuh kode.
 *
 * @package Yumnia
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'customize_register',
	function ( WP_Customize_Manager $wp_customize ) {
		$d = yumnia_defaults();

		$wp_customize->add_panel(
			'yumnia',
			array(
				'title'    => __( 'Pengaturan Yumnia', 'yumnia' ),
				'priority' => 20,
			)
		);

		$add_text = function ( $section, $key, $label, $type = 'text', $description = '' ) use ( $wp_customize, $d ) {
			$sanitize = 'sanitize_text_field';
			if ( 'url' === $type ) {
				$sanitize = 'esc_url_raw';
			} elseif ( 'textarea' === $type ) {
				$sanitize = 'sanitize_textarea_field';
			}
			$wp_customize->add_setting(
				'yumnia_' . $key,
				array(
					'default'           => isset( $d[ $key ] ) ? $d[ $key ] : '',
					'sanitize_callback' => $sanitize,
				)
			);
			$wp_customize->add_control(
				'yumnia_' . $key,
				array(
					'section'     => $section,
					'label'       => $label,
					'type'        => $type,
					'description' => $description,
				)
			);
		};

		$add_image = function ( $section, $key, $label, $description = '' ) use ( $wp_customize ) {
			$wp_customize->add_setting(
				'yumnia_' . $key,
				array(
					'default'           => 0,
					'sanitize_callback' => 'absint',
				)
			);
			$wp_customize->add_control(
				new WP_Customize_Media_Control(
					$wp_customize,
					'yumnia_' . $key,
					array(
						'section'     => $section,
						'label'       => $label,
						'mime_type'   => 'image',
						'description' => $description,
					)
				)
			);
		};

		// Kontak & toko.
		$wp_customize->add_section( 'yumnia_contact', array( 'title' => __( 'Kontak & WhatsApp', 'yumnia' ), 'panel' => 'yumnia' ) );
		$add_text( 'yumnia_contact', 'wa_number', __( 'Nomor WhatsApp admin', 'yumnia' ), 'text', __( 'Contoh: 081234567890. Semua pesanan dikirim ke nomor ini.', 'yumnia' ) );
		$add_text( 'yumnia_contact', 'wa_greeting', __( 'Pesan awal tombol WhatsApp', 'yumnia' ) );
		$add_text( 'yumnia_contact', 'instagram', __( 'Tautan Instagram', 'yumnia' ), 'url' );
		$add_text( 'yumnia_contact', 'tiktok', __( 'Tautan TikTok', 'yumnia' ), 'url' );
		$add_text( 'yumnia_contact', 'facebook', __( 'Tautan Facebook', 'yumnia' ), 'url' );
		$add_text( 'yumnia_contact', 'address', __( 'Alamat', 'yumnia' ), 'textarea' );
		$add_text( 'yumnia_contact', 'maps_url', __( 'Tautan Google Maps', 'yumnia' ), 'url' );
		$add_text( 'yumnia_contact', 'hours', __( 'Jam layanan', 'yumnia' ) );
		$add_text( 'yumnia_contact', 'topbar_text', __( 'Teks pita atas', 'yumnia' ) );

		// Beranda.
		$wp_customize->add_section( 'yumnia_home', array( 'title' => __( 'Beranda', 'yumnia' ), 'panel' => 'yumnia' ) );
		$add_image( 'yumnia_home', 'hero_image', __( 'Foto hero (desktop)', 'yumnia' ), __( 'Foto model memakai hijab dan sandal Yumnia. Ukuran ideal 2000×1125 px.', 'yumnia' ) );
		$add_image( 'yumnia_home', 'hero_image_hp', __( 'Foto hero (HP)', 'yumnia' ), __( 'Versi tegak untuk layar HP. Ukuran ideal 1080×1440 px. Boleh dikosongkan.', 'yumnia' ) );
		$add_text( 'yumnia_home', 'hero_title', __( 'Judul hero', 'yumnia' ) );
		$add_text( 'yumnia_home', 'hero_text', __( 'Teks hero', 'yumnia' ), 'textarea' );
		$add_text( 'yumnia_home', 'hero_cta', __( 'Teks tombol hero', 'yumnia' ) );
		$add_text( 'yumnia_home', 'intro_text', __( 'Paragraf perkenalan', 'yumnia' ), 'textarea' );
		$add_text( 'yumnia_home', 'intro_stat', __( 'Angka sorotan', 'yumnia' ) );
		$add_text( 'yumnia_home', 'intro_stat_lbl', __( 'Keterangan angka sorotan', 'yumnia' ) );
		$add_image( 'yumnia_home', 'story_image', __( 'Foto "Dari Sukajadi"', 'yumnia' ), __( 'Foto pengrajin atau suasana desa.', 'yumnia' ) );
		$add_text( 'yumnia_home', 'story_quote', __( 'Kalimat "Dari Sukajadi"', 'yumnia' ), 'textarea' );
		$add_text( 'yumnia_home', 'story_caption', __( 'Keterangan foto', 'yumnia' ) );

		// Liputan media.
		$wp_customize->add_section(
			'yumnia_press',
			array(
				'title'       => __( 'Liputan media', 'yumnia' ),
				'panel'       => 'yumnia',
				'description' => __( 'Kosongkan nama untuk menyembunyikan. Tautan diarahkan ke artikel aslinya.', 'yumnia' ),
			)
		);
		foreach ( array( 'Radar Bogor', 'Jurnal Bogor', 'Tribun Bogor', 'Jurnal Inspirasi', 'Jurnalissatunews' ) as $i => $name ) {
			$n = $i + 1;
			$wp_customize->add_setting( "yumnia_press_{$n}_name", array( 'default' => $name, 'sanitize_callback' => 'sanitize_text_field' ) );
			$wp_customize->add_control( "yumnia_press_{$n}_name", array( 'section' => 'yumnia_press', 'label' => sprintf( __( 'Media %d: nama', 'yumnia' ), $n ) ) );
			$wp_customize->add_setting( "yumnia_press_{$n}_url", array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
			$wp_customize->add_control( "yumnia_press_{$n}_url", array( 'section' => 'yumnia_press', 'label' => sprintf( __( 'Media %d: tautan artikel', 'yumnia' ), $n ), 'type' => 'url' ) );
		}

		// Mitra.
		$wp_customize->add_section(
			'yumnia_partners',
			array(
				'title'       => __( 'Logo mitra (footer)', 'yumnia' ),
				'panel'       => 'yumnia',
				'description' => __( 'Logo Desa Wisata, RATU, dan Leads tampil kecil di footer dan halaman Cerita.', 'yumnia' ),
			)
		);
		$add_text( 'yumnia_partners', 'partners_title', __( 'Judul', 'yumnia' ) );
		foreach ( array( 'Desa Wisata Sukajadi', 'PT Raharja Energi Cepu Tbk (RATU)', 'Yayasan Leads Indonesia Bisa' ) as $i => $name ) {
			$n = $i + 1;
			$wp_customize->add_setting( "yumnia_partner_{$n}_name", array( 'default' => $name, 'sanitize_callback' => 'sanitize_text_field' ) );
			$wp_customize->add_control( "yumnia_partner_{$n}_name", array( 'section' => 'yumnia_partners', 'label' => sprintf( __( 'Mitra %d: nama', 'yumnia' ), $n ) ) );
			$add_image( 'yumnia_partners', "partner_{$n}_logo", sprintf( __( 'Mitra %d: logo', 'yumnia' ), $n ) );
		}

		// SEO & analitik.
		$wp_customize->add_section( 'yumnia_seo', array( 'title' => __( 'Bagikan & analitik', 'yumnia' ), 'panel' => 'yumnia' ) );
		$add_image( 'yumnia_seo', 'og_image', __( 'Gambar saat link dibagikan', 'yumnia' ), __( 'Tampil saat link beranda dibagikan di WhatsApp/Instagram. Ukuran 1200×630 px.', 'yumnia' ) );
		$add_text( 'yumnia_seo', 'ga4_id', __( 'ID Google Analytics 4', 'yumnia' ), 'text', __( 'Contoh: G-XXXXXXX. Kosongkan bila belum ada.', 'yumnia' ) );
	}
);
