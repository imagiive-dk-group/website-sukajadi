# Usulan teknis Website Yumnia

Platform yang dipakai adalah **WordPress + WooCommerce dengan tema custom**.
Biaya rutinnya hanya **domain + hosting**. Semua fitur memakai software gratis, tanpa langganan plugin atau aplikasi.

## Kenapa WooCommerce

| Kebutuhan brief | Cara dipenuhi |
| --- | --- |
| Dikelola tim non-teknis | Dashboard WordPress yang umum dipakai. Banyak tutorial berbahasa Indonesia untuk siswa SMK |
| Brand guideline & desain premium | Tema dibuat sendiri, bukan template. Warna, font, dan animasi mengikuti brand Yumnia |
| Mobile-first, cepat lewat data seluler | Satu file CSS + satu file JS tanpa library. Gambar otomatis dikecilkan per layar dan dimuat bertahap |
| Pesanan tercatat + status | Panel pesanan WooCommerce dengan status Baru / Dibayar / Dikirim / Selesai |
| Bisa naik ke Tahap 2 tanpa bangun ulang | Payment gateway & ongkir otomatis cukup ditambah lewat plugin, tema tetap |
| Aset atas nama desa | Domain, hosting, dan akun admin bisa didaftarkan atas nama lembaga desa |

## Perkiraan biaya rutin

| Item | Perkiraan | Catatan |
| --- | --- | --- |
| Domain `.com` atau `.id` | ± Rp150–300 rb/tahun | `.id` butuh KTP pendaftar; `.co.id` butuh badan usaha. Harga perlu dicek ke penyedia |
| Hosting WordPress (Indonesia) | ± Rp40–150 rb/bulan | Paket yang menyertakan SSL gratis, backup otomatis harian, dan PHP 8.1+ |
| Plugin & tema | Rp0 | WooCommerce, tema Yumnia, UpdraftPlus (backup cadangan), Google Analytics: semuanya gratis |

> Angka di atas kisaran umum, bukan penawaran. Bandingkan 2–3 penyedia hosting lokal sebelum membeli.

## Alur penjualan (Tahap 1, saat peluncuran)

1. Pembeli mengisi keranjang, lalu form singkat (nama, nomor WA, alamat, catatan).
2. Klik **Pesan via WhatsApp** → pesanan tercatat di admin (status **Baru**) → WhatsApp admin terbuka berisi ringkasan: produk, varian, jumlah, subtotal, alamat.
3. Admin membalas ongkir + rekening/QRIS. Pembeli membayar manual.
4. Admin mengubah status ke **Dibayar** (stok berkurang otomatis) → **Dikirim** → **Selesai**.

Tidak ada payment gateway, sesuai keputusan saat ini. Jika nanti dibutuhkan (Tahap 2), Midtrans/Xendit dan ongkir otomatis bisa ditambah tanpa mengubah tema.
Layanan ini tidak berbiaya bulanan, tetapi memotong biaya per transaksi.

## Sinkron stok dengan marketplace

Opsi paling sederhana tanpa biaya adalah **satu admin, satu sumber stok**:

- Stok fisik dicatat di website (WooCommerce) sebagai acuan.
- Setiap pesanan Shopee/Tokopedia masuk, admin mengurangi stok varian yang sama di website, dan sebaliknya.
- Seminggu sekali stok di tiga tempat dicocokkan dengan stok fisik.
- Agar tidak terjual melebihi stok, sisakan 1–2 buffer per varian di marketplace.

Sinkron otomatis (Jubelio, Ginee, dsb.) bisa dipertimbangkan nanti bila volume pesanan sudah tinggi. Layanan itu berbayar.

## Kualitas & keamanan

- HTTPS dari SSL gratis hosting. Backup harian dari hosting + UpdraftPlus ke Google Drive milik desa.
- Aksesibilitas: teks alternatif gambar, kontras warna maroon di atas krem memenuhi WCAG AA, navigasi keyboard, dan dukungan *reduced motion*.
- SEO dasar: judul & deskripsi otomatis, Open Graph untuk pratinjau link di WhatsApp/IG, sitemap bawaan WordPress (`/wp-sitemap.xml`), data produk terstruktur dari WooCommerce.
- Struktur siap ditambah Bahasa Inggris: semua teks tema bisa diterjemahkan.

## Rencana serah terima

- [ ] Domain, hosting, admin WordPress, dan email didaftarkan atas nama lembaga desa
- [ ] Panduan admin (lihat [PANDUAN-ADMIN.md](PANDUAN-ADMIN.md), bisa dijadikan PDF/video)
- [ ] Satu sesi pelatihan langsung untuk tim desa + siswa SMK
- [ ] Masa perbaikan bug setelah peluncuran: usulan **3 bulan**
- [ ] Rincian biaya tahunan final setelah penyedia hosting dipilih
