# Menjalankan website Yumnia di komputer sendiri

Ada dua cara. Pilih salah satu.

## Cara A: Docker (disarankan untuk developer)

1. Pasang [Docker Desktop](https://www.docker.com/products/docker-desktop/) lalu jalankan.
2. Buka terminal di folder repo ini, lalu jalankan:

   ```bash
   docker compose up -d
   docker compose run --rm cli sh /scripts/setup.sh
   ```

   Perintah kedua memasang WordPress, Bahasa Indonesia, WooCommerce, mengaktifkan tema Yumnia, lalu mengisi data contoh: 10 produk, 3 set padu-padan, dan halaman Cerita, Bantuan, Kontak. Prosesnya beberapa menit karena WordPress mengolah 60-an foto.
3. Buka:
   - Situs: http://localhost:8080
   - Admin: http://localhost:8080/wp-admin (user `admin`, sandi `admin`)

Perubahan di folder `wp-content/themes/yumnia` langsung terlihat setelah halaman dimuat ulang.

| Perintah | Fungsi |
| --- | --- |
| `docker compose stop` | Matikan sementara |
| `docker compose up -d` | Nyalakan lagi |
| `docker compose run --rm cli wp <perintah>` | Jalankan WP-CLI, mis. `wp plugin list` |
| `docker compose down -v` | Hapus semuanya (database ikut terhapus) |

## Cara B: LocalWP (tanpa Docker, cocok untuk Windows/Mac)

1. Pasang [LocalWP](https://localwp.com/) dan buat situs baru (PHP 8.1 atau lebih baru).
2. Salin folder `wp-content/themes/yumnia` ke folder `app/public/wp-content/themes/` milik situs LocalWP.
3. Masuk ke wp-admin → **Plugin → Tambah Baru** → pasang dan aktifkan **WooCommerce**. Lewati panduan pengaturan WooCommerce.
4. **Tampilan → Tema** → aktifkan **Yumnia**.
5. Di LocalWP klik kanan situs → **Open site shell**, lalu jalankan (sesuaikan lokasi folder repo):

   ```bash
   wp eval-file /lokasi/repo/scripts/seed.php /lokasi/repo/contoh-data
   wp rewrite structure '/%postname%/' && wp rewrite flush
   ```

## Mencoba alur pesan

1. Pilih produk → pilih warna & ukuran → **Tambah ke keranjang**.
2. Di laci keranjang tekan **Lanjut pesan**, isi data, tekan **Pesan via WhatsApp**.
3. Halaman terima kasih membuka WhatsApp dengan ringkasan pesanan. Nomor admin contoh adalah 0812-3456-7890; ganti di **Tampilan → Sesuaikan → Pengaturan Yumnia → Kontak & WhatsApp**.
4. Di **WooCommerce → Pesanan**, pesanan muncul dengan status **Baru**. Ubah ke **Dibayar**, lalu cek stok varian berkurang.

## Membuat ulang pratinjau & foto contoh

```bash
php preview/build.php                    # render ulang preview/*.html dari template tema
pip install pillow numpy                 # sekali saja
python3 tools/generate-placeholders.py   # buat ulang foto contoh
```

## Memasang di hosting

Yang dipasang di hosting cukup folder tema `wp-content/themes/yumnia` (unggah sebagai ZIP di **Tampilan → Tema → Tambah Baru**) plus plugin WooCommerce.
Data contoh **jangan** dipakai di situs sungguhan. Isi produk asli lewat admin; caranya ada di [PANDUAN-ADMIN.md](PANDUAN-ADMIN.md).
Langkah setelah memasang:

1. **Pengaturan → Permalink** → pilih "Nama tulisan".
2. **WooCommerce → Pengaturan → Umum**: mata uang Rupiah, 0 desimal, pemisah ribuan titik.
3. **WooCommerce → Pengaturan → Pembayaran**: aktifkan hanya **Pesan via WhatsApp**.
4. Pastikan halaman Keranjang berisi `[woocommerce_cart]` dan halaman Checkout berisi `[woocommerce_checkout]` (blok *Shortcode*), bukan blok Keranjang/Checkout bawaan.
5. **Tampilan → Sesuaikan → Pengaturan Yumnia**: isi nomor WhatsApp, media sosial, foto hero, tautan liputan media, dan logo mitra.
