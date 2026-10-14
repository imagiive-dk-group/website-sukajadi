# Website Yumnia — Desa Wisata Sukajadi

Toko online **Yumnia**, merek hijab dan sandal wanita milik Desa Wisata Sukajadi, Kec. Tamansari, Kab. Bogor.
Dibangun dengan **WordPress + WooCommerce**, memakai tema custom `yumnia`. Pesanan dikirim lewat **WhatsApp**.

> **Status:** tema sudah lengkap. Produk, harga, foto, dan teks cerita masih **data contoh**. Semua foto contoh bertanda "FOTO CONTOH".
> Daftar bahan yang masih ditunggu ada di [docs/BAHAN-YANG-DIBUTUHKAN.md](docs/BAHAN-YANG-DIBUTUHKAN.md).

## Lihat pratinjau tanpa memasang apa pun

Folder `preview/` berisi halaman HTML hasil render template tema yang asli dengan data contoh.

```bash
python3 -m http.server 8000      # jalankan di folder repo ini
# lalu buka http://localhost:8000/preview/
```

Keranjang dan pencarian di pratinjau memakai data tiruan (`preview/mock.js`). Di WordPress sungguhan, keduanya terhubung ke WooCommerce.
Untuk membuat ulang pratinjau setelah mengubah tema: `php preview/build.php`.

## Menjalankan WordPress di komputer sendiri

Butuh [Docker Desktop](https://www.docker.com/products/docker-desktop/).

```bash
docker compose up -d
docker compose run --rm cli sh /scripts/setup.sh
```

Buka http://localhost:8080. Halaman admin ada di http://localhost:8080/wp-admin (user `admin`, sandi `admin`).
Panduan lengkap, termasuk cara tanpa Docker (LocalWP), ada di [docs/PANDUAN-LOKAL.md](docs/PANDUAN-LOKAL.md).

## Fitur

| Area | Isi |
| --- | --- |
| Beranda | Hero foto penuh, pita berjalan, dua pintu kategori, koleksi peluncuran (carousel), angka sorotan, padu-padan, potongan cerita "Dari Sukajadi", liputan media |
| Katalog | Tab Semua / Hijab / Sandal / Padu-padan, filter warna, ukuran, dan harga, urutan terbaru/terlaris/harga, label Baru & Habis |
| Detail produk | Galeri (geser di HP, grid di desktop, zoom), pilihan warna & ukuran dengan status stok, tabel ukuran sandal, tinggi hak, jenis sol, cerita nama model, pembuat, Shopee/Tokopedia, "Cocok dengan…", bilah beli menempel di HP |
| Keranjang | Laci keranjang (ubah jumlah, hapus), tambah cepat dari kartu, ambil satu set padu-padan sekaligus |
| Pemesanan | Form singkat (nama, WA, alamat, catatan) → pesanan tercatat di admin dengan status **Baru** → WhatsApp admin terbuka dengan ringkasan pesanan |
| Admin pesanan | Status Baru → Dibayar → Dikirim → Selesai. Stok berkurang saat Dibayar. Tombol "Chat pembeli di WhatsApp" |
| Halaman | Cerita Yumnia, Bantuan (cara order, ongkir, retur, FAQ), Kontak. Semua diedit lewat editor blok |
| Lainnya | Pencarian langsung, tombol WhatsApp mengambang, meta SEO + Open Graph, sitemap, Google Analytics (opsional), siap terjemahan |

**Animasi:** intro monogram di kunjungan pertama, judul muncul per kata, foto terbuka dengan efek tirai dan lengkung, parallax hero, pita yang bereaksi pada kecepatan gulir, tombol magnet, kursor "Lihat", foto terbang ke keranjang, transisi antarhalaman (View Transitions), dan akordeon halus.
Semuanya tanpa library tambahan, dan otomatis dimatikan bila perangkat meminta gerak dikurangi (*reduced motion*).

## Struktur folder

```
wp-content/themes/yumnia/   Tema WordPress (ini yang dipasang di hosting)
  inc/                      Fitur: WooCommerce, keranjang, checkout WhatsApp, padu-padan, SEO, Customizer
  woocommerce/              Template katalog, produk, checkout, terima kasih
  template-parts/           Kartu produk, kartu set, panel (menu, cari, keranjang)
  assets/                   CSS, JS, font Jost
contoh-data/                Data & foto contoh (produk.json, gambar/, halaman/)
scripts/                    setup.sh + seed.php (isi data contoh lewat WP-CLI)
preview/                    Pratinjau statis + pembuatnya
tools/                      Generator foto placeholder
docs/                       Panduan & usulan teknis
```

## Dokumen

- [Usulan teknis, biaya, dan alur penjualan](docs/USULAN-TEKNIS.md)
- [Panduan menjalankan di komputer lokal](docs/PANDUAN-LOKAL.md)
- [Panduan admin untuk tim desa](docs/PANDUAN-ADMIN.md)
- [Bahan yang masih dibutuhkan](docs/BAHAN-YANG-DIBUTUHKAN.md)

Font: [Jost](https://github.com/indestructible-type/Jost) (SIL Open Font License).
