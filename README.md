# Website Desa Wisata Sukajadi

Website showcase & reservasi paket wisata **Desa Wisata Sukajadi**, Kec. Tamansari, Kab. Bogor — dua bahasa (Indonesia & English).

Dibangun dengan **Laravel 13**, panel admin **Filament 5**, **Tailwind CSS 4**, dan **Alpine.js**. Semua perangkat lunak gratis/open source; biaya hanya domain + hosting.

## Fitur

- **Halaman publik (ID + EN)**: Beranda, Profil Desa (data resmi + sumber), Desa Wisata, Paket Wisata, Paket Custom, Destinasi, Direktori UMKM, Produk UMKM, Galeri (lightbox), Berita, FAQ, Ulasan, Kontak.
- **Booking**: formulir 4 langkah, validasi minimal pax / tanggal ditutup / kuota harian / batas hari pemesanan, add-on, estimasi harga, kode booking, lanjut konfirmasi via WhatsApp, email notifikasi, halaman Cek Status Booking.
- **Panel admin** (`/admin`): kelola semua konten dua bahasa, unggah foto (otomatis dikompres ke WebP), booking + tombol "Kirim WA", kalender booking, laporan & ekspor CSV, pengaturan website, 2 peran pengguna (Super Admin & Admin).
- **Placeholder foto berlabel**: setiap posisi foto menjelaskan foto apa yang perlu diambil, dan otomatis berganti begitu foto diunggah dari admin.
- **SEO**: meta & Open Graph, `hreflang`, schema.org, `sitemap.xml`.

## Dokumentasi

- Rencana website: [docs/RENCANA-WEBSITE.md](docs/RENCANA-WEBSITE.md)
- Panduan deploy ke hosting: [docs/PANDUAN-DEPLOY.md](docs/PANDUAN-DEPLOY.md)
- Panduan admin: [docs/PANDUAN-ADMIN.md](docs/PANDUAN-ADMIN.md)

## Menjalankan di komputer lokal

```bash
composer install && npm install
cp .env.example .env && php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
npm run build
php artisan serve        # http://localhost:8000  (admin: /admin)
php artisan test
```

Login admin awal diambil dari `ADMIN_EMAIL` / `ADMIN_PASSWORD` di `.env` (bawaan: `admin@desawisatasukajadi.com` / `ubah-password-ini`). **Ganti password setelah login pertama.**
