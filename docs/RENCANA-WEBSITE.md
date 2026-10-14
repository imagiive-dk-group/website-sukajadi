# Rencana Website Desa Wisata Sukajadi

> Status: **Draf rencana v1**. Dokumen ini disusun dari 5 brosur/flyer yang ada. Bagian bertanda ⚠️ perlu dikonfirmasi pengelola desa sebelum pengembangan dimulai.

---

## 1. Tujuan Website

1. **Showcase**: menampilkan keindahan alam, budaya, edukasi, dan kuliner Desa Wisata Sukajadi dengan banyak foto berkualitas.
2. **Booking**: calon pengunjung (sekolah, komunitas, instansi, keluarga) dapat memilih paket, tanggal, dan jumlah peserta, lalu mengirim reservasi.
3. **Mandiri**: pengurus desa/Pokdarwis dapat mengelola paket, harga, foto, dan reservasi sendiri lewat panel admin, tanpa harus memanggil programmer.
4. **Biaya**: biaya rutin **hanya domain + hosting**. Semua perangkat lunak yang dipakai gratis/open source.

### Target pengunjung (dari brosur)
- Sekolah & study tour
- Komunitas & organisasi
- Instansi & perusahaan
- Keluarga & umum

---

## 2. Informasi Dasar (diambil dari brosur)

| Item | Isi |
|---|---|
| Nama | Desa Wisata Sukajadi |
| Lokasi | Desa Sukajadi, Kec. Tamansari, Kab. Bogor, Jawa Barat |
| Tagline | "Belajar • Berwisata • Berdaya", "Alam, Budaya, Edukasi", "Wisata Ramah! Eta Pisan!", "Datang, Belajar, Berkesan!" |
| WhatsApp | 0889-7644-8447 |
| Instagram | ⚠️ Ada dua versi: `@desa_wisata.sukajadi` dan `@DesaWisataSukajadi`. Mana yang benar? |
| TikTok | ⚠️ "Desa Wisata Sukajadi" / `@DesaWisataSukajadi`. Mohon konfirmasi handle-nya. |
| Pilar wisata | Edukasi, Alam, Budaya, Kuliner Lokal |

> ⚠️ Salah satu brosur (Paket Budidaya Padi) mencantumkan WA **0849**76448447. Kemungkinan salah ketik dari 0889…, mohon dicek.

### 2.1 Destinasi / Atraksi

| Atraksi | Kategori | Keterangan singkat |
|---|---|---|
| Curug Aman | Alam | Air terjun alami, tempat bersantai, wisata alam & petualangan |
| Peternakan Kelinci | Edukasi | Budidaya kelinci dari peternak berpengalaman |
| Kebun Stroberi | Edukasi/Agro | Memetik stroberi langsung dari kebun, belajar budidaya |
| Kebun Anggrek Bulan | Edukasi/Agro | Agro eduwisata, budidaya & perkembangbiakan anggrek bulan |
| Sawah / Budidaya Padi | Edukasi/Agro | Edukasi budidaya padi |
| Kebun Ubi Jalar | Edukasi/Agro | Pupuk organik, tanam, dan panen ubi jalar |
| UMKM Sari Buah Pala | Kuliner/UMKM | Mengolah buah pala menjadi minuman khas |
| UMKM Kue Noga | Kuliner/UMKM | Membuat kue tradisional "kue noga" |
| UMKM Kue Tradisional | Kuliner/UMKM | Kue lapis, onde-onde, kue talam bersama perajin lokal |
| Tempe | Kuliner/UMKM | Sejarah & proses pembuatan tempe |
| Permainan Tradisional Sunda | Budaya | Egrang, gasing, kelom geulis |
| Nasi Liwet | Kuliner | Menu spesial khas desa |

### 2.2 Paket Wisata

**A. Paket Edukasi Budidaya Kelinci**: harga **per orang**, minimal **15 orang** (bisa menyesuaikan)

| Tingkat | Durasi | Harga | Isi |
|---|---|---|---|
| Hemat | 2–3 jam | Rp65.000/orang | Edukasi budidaya kelinci, keliling desa, snack lokal |
| Standar | 4–5 jam | Rp95.000/orang | + makan siang, souvenir |
| Premium ⭐ Best Seller | Seharian | Rp125.000/orang | Edukasi, workshop interaktif, keliling desa, makan siang, oleh-oleh khas desa, dokumentasi |

Materi edukasi: (1) Pengenalan kelinci, (2) Pemberian pakan, (3) Perawatan kandang, (4) Reproduksi & perkembangbiakan, (5) Panen & pemanfaatan.
Fasilitas: transportasi (area desa), makan & snack, tiket masuk objek, pemandu berpengalaman, asuransi kegiatan.

**B. Paket Edukasi Pertanian Ubi Jalar**: **Rp125.000/pack**, minimal **30 pack**
- Edukasi pembuatan pupuk organik
- Edukasi penanaman ubi jalar
- Edukasi panen ubi jalar
- Menu spesial: nasi liwet

**C. Paket Edukasi Budidaya Padi**: **Rp125.000/paket**, minimal **30 paket**
- Pendamping/atraksi pada brosur: Edukasi Stroberi, Curug Aman, Permainan Tradisional Sunda, Edukasi UMKM Kue Tradisional, Tentang Tempe
- ⚠️ Brosur ini menampilkan dua panel berlabel "Paket Edukasi Budidaya Padi". Apakah sebenarnya ada dua paket berbeda (misal Paket Padi + Paket Budaya/UMKM)? Mana saja aktivitas yang termasuk paket?

**D. Paket Edukasi UMKM**: ⚠️ harga & minimal peserta belum ada
- Pembuatan minuman sari buah pala
- Pembuatan kue tradisional "kue noga"
- Wisata alam & petualangan Curug Aman

**E. Agro Eduwisata Anggrek Bulan**: ⚠️ harga & minimal peserta belum ada
- Belajar budidaya langsung dari petani
- Melihat proses perkembangbiakan anggrek bulan

> Catatan: model harga berbeda-beda (per orang vs per pack, ada tingkatan, ada minimal peserta). Sistem booking dirancang agar semua model ini bisa diatur dari admin (lihat bagian 6).

---

## 3. Struktur Halaman (Sitemap)

```
Beranda
├── Tentang Desa              (sejarah, visi, Pokdarwis, peta lokasi)
├── Paket Wisata              (daftar + filter: edukasi/alam/budaya/kuliner, durasi, harga)
│   └── Detail Paket          (galeri, rundown, tingkat harga, fasilitas, tombol Booking)
├── Destinasi & Aktivitas     (Curug Aman, kelinci, stroberi, anggrek, dll.)
│   └── Detail Destinasi      (galeri foto, deskripsi, paket terkait)
├── Galeri                    (album per kategori, lightbox, bisa geser di HP)
├── UMKM & Oleh-oleh          (sari pala, kue noga, tempe, kue tradisional)
├── Booking / Reservasi       (form multi-langkah)
│   └── Cek Status Booking    (pakai kode booking + no. HP)
├── FAQ                       (cara booking, pembayaran, pembatalan, yang perlu dibawa)
└── Kontak                    (WA, IG, TikTok, Google Maps, jam operasional)

Admin (/admin, hanya pengurus)
├── Dashboard (booking baru, jadwal minggu ini, pendapatan)
├── Paket & Tingkat Harga
├── Destinasi/Aktivitas
├── Galeri & Album
├── Booking & Pembayaran
├── Kalender (tanggal penuh/libur, kuota)
├── Testimoni, FAQ
└── Pengaturan (kontak, rekening, teks halaman)
```

### 3.1 Isi Beranda (urutan section)
1. **Hero** foto besar/slideshow pemandangan desa + tagline "Belajar • Berwisata • Berdaya" + tombol "Lihat Paket" & "Booking Sekarang".
2. **4 Pilar**: Edukasi, Alam, Budaya, Kuliner Lokal (ikon + foto).
3. **Paket Unggulan**: kartu paket dengan harga "mulai dari", badge "Best Seller".
4. **Destinasi Populer**: Curug Aman, kebun stroberi, anggrek bulan, peternakan kelinci.
5. **Cocok Untuk**: sekolah, komunitas, instansi, keluarga.
6. **Fasilitas**: transportasi, makan, tiket, pemandu, asuransi.
7. **Galeri cuplikan**: grid foto (masonry) → tautan ke Galeri.
8. **Testimoni** pengunjung/sekolah.
9. **Cara Booking** dalam 4 langkah.
10. **Lokasi** dengan peta Google Maps.
11. **CTA penutup** "Datang, Belajar, Berkesan!" + tombol WhatsApp.

Tombol **WhatsApp melayang** (floating) tampil di semua halaman.

---

## 4. Desain & Identitas Visual

- **Warna** (diambil dari brosur): hijau tua daun `#2F5D2A` (utama), hijau muda `#8DB255`, krem kertas `#F5EFE3` (latar), cokelat kayu `#7A5233` (aksen), oranye `#E8742A` (tombol CTA/badge).
- **Gaya**: alami, hangat, nuansa papan kayu & daun seperti brosur. Foto menjadi elemen utama.
- **Font** (Google Fonts, gratis): judul tebal *Poppins*/*Baloo 2*, aksen tulisan tangan *Kalam*/*Caveat* (untuk kalimat seperti "Datang, Belajar, Berkesan!"), teks isi *Inter*/*Nunito*.
- **Logo**: ⚠️ ada dua versi logo di brosur (rumah+sungai dan air terjun+lencana). Pilih satu sebagai logo resmi, sediakan file PNG/SVG resolusi tinggi.
- **Mobile-first**: mayoritas pengunjung Indonesia membuka dari HP. Semua halaman dirancang untuk layar HP terlebih dahulu.

---

## 5. Strategi Foto (website ini sangat foto-sentris)

### 5.1 Placeholder selama pengembangan
- Dibuat **placeholder lokal** (SVG/gambar abu-abu) bertuliskan **nama foto yang dibutuhkan + rasio**, misal: `FOTO: Panen ubi jalar, rombongan sekolah, 3:2`.
- Dengan begitu tim lapangan tahu persis foto apa yang harus diambil, dan foto asli tinggal diunggah lewat admin untuk menggantikan placeholder.
- Tidak menautkan gambar dari layanan luar (hotlink) agar tidak bergantung pada pihak ketiga.

### 5.2 Daftar foto yang perlu disiapkan (shot list awal)

| Bagian | Jumlah | Rasio | Contoh isi |
|---|---|---|---|
| Hero beranda | 3–5 | 16:9 (min. 1920px) | Panorama desa, sawah, Curug Aman, rombongan berjalan di jalan desa |
| Per paket (×5) | 6–10/paket | 3:2 | Aktivitas inti, peserta, pemandu, makanan |
| Per destinasi (×12) | 5–10/destinasi | 3:2 & 4:5 | Suasana, detail, orang beraktivitas |
| Galeri umum | 50–150 | campuran | Kegiatan, budaya, kuliner, alam |
| UMKM/produk | 2–4/produk | 1:1 | Foto produk dengan latar bersih |
| Tim/pemandu | 1/orang | 1:1 | Foto wajah pemandu & pengurus |

Total kebutuhan awal: kira-kira **150–300 foto**.

> 💡 Gunakan **foto asli** desa, bukan gambar buatan AI seperti di brosur. Foto asli jauh lebih meyakinkan calon pengunjung dan lebih baik untuk Google.

### 5.3 Optimasi foto (otomatis, gratis)
- Saat admin mengunggah foto, sistem otomatis membuat beberapa ukuran (thumbnail 400px, sedang 1000px, besar 1920px) dalam format **WebP**.
- *Lazy loading* (foto dimuat saat di-scroll) dan *responsive images* (`srcset`) agar cepat di HP dan hemat kuota.
- Galeri dengan *lightbox* (geser, zoom, layar penuh).
- Perkiraan ruang: 300 foto × 3 ukuran × ~150 KB ≈ **±150 MB**, cukup untuk hosting paket kecil.

---

## 6. Sistem Booking

### 6.1 Alur booking pengunjung (Fase 1)

```
Pilih paket ─► Pilih tingkat (Hemat/Standar/Premium) ─► Pilih tanggal
     ─► Isi jumlah peserta + data pemesan (nama, HP/WA, email, asal instansi, jenis rombongan)
     ─► Lihat ringkasan & estimasi harga ─► Kirim
     ─► Dapat KODE BOOKING (mis. SKJ-261014-0042) + tombol "Konfirmasi via WhatsApp"
          (pesan WA otomatis terisi: kode, paket, tanggal, jumlah peserta)
```

Validasi otomatis:
- Jumlah peserta ≥ minimal paket (15 orang / 30 pack, dsb.).
- Tanggal tidak boleh di masa lalu, tanggal yang ditutup admin, atau melebihi kuota harian.
- Batas pemesanan minimal H-x (misal H-3), diatur admin.

### 6.2 Alur di sisi pengelola

```
Baru ─► Dikonfirmasi ─► Menunggu DP ─► DP Diterima ─► Lunas ─► Selesai
                 └──────────────► Dibatalkan
```

- Notifikasi **email** ke admin setiap ada booking baru (gratis, pakai email bawaan hosting).
- Di admin ada tombol **"Kirim WA"** yang membuka WhatsApp dengan pesan konfirmasi siap kirim ke pemesan (gratis, memakai `wa.me`, tidak perlu WhatsApp API berbayar).
- Admin mencatat pembayaran (DP/pelunasan), nominal, dan bukti.
- Kalender jadwal: melihat rombongan per tanggal.

### 6.3 Pembayaran (tanpa biaya bulanan)
- **Fase 1–2**: transfer bank / **QRIS statis** milik desa/BUMDes, lalu pemesan **mengunggah bukti bayar** di halaman Cek Status Booking. Admin memverifikasi manual. Biaya: Rp0.
- **Fase 3 (opsional)**: payment gateway (Midtrans/Xendit/Tripay). Tidak ada biaya bulanan, tetapi **ada potongan per transaksi** (bisa dibebankan ke pemesan). Hanya diaktifkan jika volume booking sudah tinggi.

### 6.4 Model data (dirancang untuk berkembang)

| Tabel | Isi utama |
|---|---|
| `packages` | nama, slug, kategori, deskripsi, durasi, foto sampul, aktif/tidak, unggulan |
| `package_tiers` | tingkat (Hemat/Standar/Premium), harga, **satuan harga** (per orang / per pack / per rombongan), minimal peserta, isi/fasilitas, badge |
| `attractions` | destinasi/aktivitas: nama, kategori, deskripsi, lokasi |
| `package_attraction` | relasi paket ↔ aktivitas |
| `albums`, `photos` | galeri, keterangan foto, urutan, terkait paket/destinasi |
| `bookings` | kode, paket, tingkat, tanggal, jumlah peserta, data pemesan, jenis rombongan, total, status, catatan |
| `payments` | booking, jenis (DP/pelunasan), nominal, bukti, diverifikasi oleh, tanggal |
| `blocked_dates` / `capacity` | tanggal tutup & kuota harian per paket |
| `addons` (Fase 2) | tambahan opsional: dokumentasi, oleh-oleh, menu tambahan |
| `testimonials`, `faqs`, `settings` | konten pendukung & pengaturan (kontak, rekening, kebijakan) |

Dengan struktur ini, paket baru dengan model harga apa pun cukup ditambahkan dari admin.

---

## 7. Teknologi (semua gratis / open source)

### 7.1 Rekomendasi: **Laravel + Filament** di shared hosting

| Komponen | Pilihan | Alasan |
|---|---|---|
| Backend | **Laravel** (PHP) | Gratis, stabil, sangat umum di Indonesia (mudah cari programmer pengganti), jalan di shared hosting murah |
| Panel admin | **Filament** | Gratis, admin panel modern siap pakai (kelola paket, foto, booking) tanpa membangun dari nol |
| Database | MySQL/MariaDB | Sudah termasuk di paket hosting |
| Tampilan | Blade + **Tailwind CSS** + **Alpine.js** | Ringan, cepat, cocok untuk HP |
| Pengolahan foto | Intervention Image / Spatie Media Library | Otomatis resize + WebP |
| Galeri | **PhotoSwipe** atau **GLightbox** | Lightbox gratis, nyaman di HP |
| Peta | Embed Google Maps | Gratis (embed, bukan API berbayar) |
| Email | SMTP bawaan hosting | Gratis |
| Invoice PDF (Fase 2) | DomPDF | Gratis |
| Analitik | Google Analytics 4 + Google Search Console | Gratis |
| SSL (https) | Let's Encrypt (biasanya sudah termasuk hosting) | Gratis |
| CDN & keamanan (opsional) | Cloudflare paket Free | Gratis, mempercepat foto |

**Kenapa bukan WordPress?** Bisa, tetapi plugin booking yang mampu menangani tingkat harga + minimal peserta + kuota umumnya berbayar (premium). Laravel + Filament memberi sistem booking sesuai kebutuhan tanpa lisensi berbayar.

**Kenapa bukan situs statis gratis (Netlify/Cloudflare Pages)?** Cocok untuk showcase, tetapi booking butuh database & admin. Menggabungkan layanan gratis pihak ketiga menambah kerumitan dan risiko (batas kuota, proyek dibekukan jika tidak aktif). Satu hosting berbayar yang menampung semuanya lebih sederhana untuk dikelola desa.

### 7.2 Syarat hosting yang harus dipilih
- PHP **8.2+**, MySQL/MariaDB
- **Akses SSH** + Composer (penting untuk Laravel)
- **Cron job** (untuk pengingat/email terjadwal)
- Penyimpanan SSD **≥ 5–10 GB**, SSL gratis, akun email
- Server/datacenter di Indonesia atau Singapura (lebih cepat untuk pengunjung lokal)
- Backup otomatis (atau minimal backup manual via cPanel)

### 7.3 Perkiraan biaya per tahun

| Item | Perkiraan | Catatan |
|---|---|---|
| Domain `.com` / `.id` | ± Rp150–300 rb/tahun | `.id` butuh KTP. Opsi `desa.id` khusus pemerintah desa & butuh surat resmi |
| Shared hosting (spek di atas) | ± Rp300–800 rb/tahun | Cek harga perpanjangan, bukan hanya harga promo tahun pertama |
| Perangkat lunak, font, ikon, peta, analitik | **Rp0** | Semua open source / gratis |
| **Total** | **± Rp450 rb – 1,1 jt/tahun** | |

> Harga di atas adalah kisaran perkiraan dan perlu dicek ulang ke penyedia saat membeli.

Saran nama domain: `desawisatasukajadi.com`, `sukajadi.id`, `wisatasukajadi.id` ⚠️ (pilih & cek ketersediaan).

---

## 8. SEO & Pemasaran (gratis)

- Judul & deskripsi tiap halaman berisi kata kunci: "desa wisata Bogor", "wisata edukasi Bogor", "study tour Bogor", "Curug Aman Tamansari", "petik stroberi Bogor", dll.
- **Structured data** (schema.org `TouristAttraction`, `Product`/`Offer` untuk paket) agar tampil menarik di Google.
- Sitemap XML otomatis + daftar ke **Google Search Console**.
- Buat/klaim **Google Business Profile** "Desa Wisata Sukajadi" dan tautkan ke website (sangat berpengaruh untuk pencarian lokal & Google Maps).
- Tombol bagikan ke WhatsApp/Facebook; tampilan pratinjau tautan (Open Graph) yang menarik saat dibagikan di grup WA.
- Tautkan website di bio Instagram & TikTok.

---

## 9. Tahapan Pengembangan

### Fase 0: Persiapan konten (oleh pengelola desa)
- [ ] Jawab pertanyaan ⚠️ di dokumen ini (kontak, logo, paket, harga)
- [ ] Teks "Tentang Desa" (sejarah singkat, Pokdarwis, keunikan)
- [ ] Rundown kegiatan per paket
- [ ] Kebijakan DP, pelunasan, pembatalan, dan reschedule
- [ ] Rekening/QRIS resmi penerima pembayaran
- [ ] Mulai pengambilan foto sesuai shot list
- [ ] Beli domain & hosting

### Fase 1: MVP (showcase + booking dasar)
- [ ] Setup Laravel + Filament + Tailwind, deploy ke hosting
- [ ] Halaman: Beranda, Tentang, Paket (+detail), Destinasi (+detail), Galeri, FAQ, Kontak
- [ ] Placeholder foto berlabel di semua posisi foto
- [ ] Admin: kelola paket, tingkat harga, destinasi, galeri, pengaturan
- [ ] Form booking + kode booking + notifikasi email + tombol WA
- [ ] Admin: daftar & status booking
- [ ] SEO dasar, Google Analytics, Search Console

### Fase 2: Booking lengkap
- [ ] Kalender ketersediaan & kuota harian
- [ ] Halaman Cek Status Booking + unggah bukti bayar
- [ ] Pencatatan DP/pelunasan, invoice PDF
- [ ] Add-on (dokumentasi, oleh-oleh, menu tambahan)
- [ ] Testimoni pengunjung, halaman UMKM & oleh-oleh
- [ ] Laporan sederhana (jumlah pengunjung & pendapatan per bulan, ekspor Excel)

### Fase 3: Pengembangan lanjutan (opsional)
- [ ] Payment gateway otomatis
- [ ] Versi Bahasa Inggris (wisatawan asing)
- [ ] Paket custom (pemesan memilih sendiri aktivitas → harga dihitung otomatis)
- [ ] Blog/berita kegiatan (bagus untuk SEO)
- [ ] Penjualan produk UMKM online

---

## 10. Pertanyaan untuk Pengelola Desa (⚠️ ringkasan)

1. Akun Instagram & TikTok resmi yang benar?
2. Nomor WA 0889-7644-8447 untuk semua paket? (Ada brosur bertuliskan 0849…)
3. Logo mana yang dipakai sebagai logo resmi?
4. Paket Budidaya Padi: aktivitas apa saja yang termasuk? Apakah ada dua paket berbeda di brosur tersebut?
5. Harga & minimal peserta untuk **Paket UMKM** dan **Anggrek Bulan**?
6. Apakah "per pack/per paket" sama dengan "per orang"?
7. Kebijakan DP (berapa %), batas pelunasan, dan pembatalan?
8. Berapa rombongan/peserta maksimal per hari?
9. Siapa yang akan mengelola admin website (berapa orang)?
10. Nama domain yang diinginkan?
11. Perlu versi Bahasa Inggris sejak awal atau nanti?
