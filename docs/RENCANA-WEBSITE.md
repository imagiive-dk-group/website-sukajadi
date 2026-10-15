# Rencana Website Desa Wisata Sukajadi

> Status: **Rencana v4 (final untuk mulai Fase 1)**. Disusun dari 5 brosur/flyer yang ada dan konfirmasi pengelola.

---

## 0. Revisi v2 (catatan pengelola, 15 Okt 2026)

| No | Catatan | Penerapan di website |
|---|---|---|
| 1 | Daftar seluruh kebutuhan bahan | `docs/KEBUTUHAN-BAHAN.md` |
| 2 | Beranda jangan langsung jualan; ceritakan desa dan apa saja yang ada | Urutan beranda baru: hero → **Cerita Sukajadi** → pita foto → **Ada apa di Sukajadi** → destinasi desa → galeri → Jelajah Tamansari → UMKM → tim penggiat → baru **paket kunjungan** di bagian bawah |
| 3 | Pembuka berupa video/gerak, perbanyak gambar | Hero memakai **video latar** (unggah di Pengaturan) atau **slideshow foto bergerak** bila video belum ada; pita foto berjalan; kolase & bento galeri |
| 4 | Identitas = penggiat desa wisata (bukan kades/perangkat); ada pengelolaan & monitoring leads | Halaman **Tim Penggiat** menggantikan perangkat desa; data pemerintahan dihapus dari profil. Menu admin **Prospek (Leads)** + halaman **Monitoring** |
| 5 | Paket dari tempat lain (Highland Tamansari, Curug Nangka) dengan komisi 10% (model Traveloka) | Menu **Jelajah Tamansari** berisi **Mitra Bisnis**, bisa dipesan lewat formulir booking; komisi dihitung otomatis per booking |
| 6 | Berlaku juga untuk penginapan/vila, kafe, dan tempat lain | Kategori mitra: Wisata Sekitar, Penginapan & Vila, Kafe & Resto, Lainnya; harga per orang/tiket/malam/unit/rombongan |
| 7 | UMKM = fasilitasi pemasaran; pebisnis/PT = komisi | **UMKM**: tanpa komisi, kontak langsung ke pemilik (dengan izin). **Mitra Bisnis**: penjualan lewat admin desa wisata, komisi default 10% per mitra (bisa diubah) |

### Model bisnis
- **UMKM warga** → direktori & katalog gratis; klik WhatsApp ke UMKM tetap tercatat untuk laporan dampak pemasaran.
- **Mitra bisnis** → pengunjung memesan lewat website, admin mengonfirmasi ke mitra, pengunjung membayar sesuai kesepakatan; website mencatat **nilai penjualan & komisi** (status: belum ditagih → sudah ditagih → sudah dibayar) dan bisa diekspor per periode.

### Prospek (leads) & monitoring
- Setiap **booking** dan **pesan formulir kontak** otomatis menjadi prospek. Admin juga bisa menambah prospek manual (mis. dari DM Instagram/telepon).
- Status prospek: Baru → Sudah dihubungi → Negosiasi → Berhasil / Batal (otomatis berubah saat booking dikonfirmasi/dibatalkan); bisa ditugaskan ke admin tertentu dengan tanggal follow-up.
- Setiap **klik tombol WhatsApp** di website tercatat (anonim): dari halaman/paket/UMKM/mitra mana minat datang.
- Halaman **Monitoring**: corong prospek & tingkat konversi, sumber prospek, klik WhatsApp per konten, penjualan per jenis, dan komisi per mitra.

> Asumsi: "Leads" diartikan sebagai prospek/calon pelanggan. Bila yang dimaksud adalah lembaga pendamping bernama Leads, cukup tambahkan nama & logonya di identitas pengelola.

## 1. Tujuan Website

1. **Showcase**: menampilkan keindahan alam, budaya, edukasi, dan kuliner Desa Wisata Sukajadi dengan banyak foto berkualitas.
2. **Booking**: calon pengunjung (sekolah, komunitas, instansi, keluarga) dapat memilih paket, tanggal, dan jumlah peserta, lalu mengirim reservasi.
3. **Mandiri**: pengurus desa/Pokdarwis dapat mengelola paket, harga, foto, dan reservasi sendiri lewat panel admin, tanpa harus memanggil programmer.
4. **Dua bahasa sejak awal**: Bahasa Indonesia (utama) dan English.
5. **Biaya**: biaya rutin **hanya domain + hosting**. Semua perangkat lunak yang dipakai gratis/open source.

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
| WhatsApp | **+62 857-7519-4575** (`wa.me/6285775194575`) |
| Instagram | **`@desawisatasukajadi`** |
| TikTok | Tidak ada (tidak ditampilkan di website) |
| Domain | **desawisatasukajadi.com** |
| Bahasa | Indonesia (utama) + English |
| Logo | `assets/brand/logo-desa-wisata-sukajadi.webp` (lencana bundar: gunung, curug, rumah, tulisan "Desa Wisata Sukajadi · Kec. Tamansari – Kab. Bogor") |
| Pilar wisata | Edukasi, Alam, Budaya, Kuliner Lokal |

> Nomor WA & akun Instagram di atas menggantikan yang tertulis di brosur lama. Semua kontak disimpan di menu Pengaturan admin, jadi cukup diubah di satu tempat.

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

**A. Paket Edukasi Budidaya Kelinci**: harga **per pax**, minimal **15 pax** (bisa menyesuaikan)

| Tingkat | Durasi | Harga | Isi |
|---|---|---|---|
| Hemat | 2–3 jam | Rp65.000/pax | Edukasi budidaya kelinci, keliling desa, snack lokal |
| Standar | 4–5 jam | Rp95.000/pax | + makan siang, souvenir |
| Premium ⭐ Best Seller | Seharian | Rp125.000/pax | Edukasi, workshop interaktif, keliling desa, makan siang, oleh-oleh khas desa, dokumentasi |

Materi edukasi: (1) Pengenalan kelinci, (2) Pemberian pakan, (3) Perawatan kandang, (4) Reproduksi & perkembangbiakan, (5) Panen & pemanfaatan.
Fasilitas: transportasi (area desa), makan & snack, tiket masuk objek, pemandu berpengalaman, asuransi kegiatan.

**B. Paket Edukasi Pertanian Ubi Jalar**: **Rp125.000/pax**, minimal **30 pax**
- Edukasi pembuatan pupuk organik
- Edukasi penanaman ubi jalar
- Edukasi panen ubi jalar
- Menu spesial: nasi liwet

**C. Paket Edukasi Budidaya Padi**: **Rp125.000/pax**, minimal **30 pax** (satu paket; dua panel di brosur adalah paket yang sama)
- Edukasi budidaya padi
- Edukasi stroberi (petik stroberi langsung dari kebun)
- Air terjun Curug Aman
- Permainan tradisional Sunda (egrang, gasing, kelom geulis)
- Edukasi UMKM kue tradisional (lapis, onde-onde, kue talam)
- Edukasi tempe

**D. Paket Edukasi UMKM**: 🧪 **harga dummy** Rp100.000/pax, minimal 20 pax (akan diganti dari admin)
- Pembuatan minuman sari buah pala
- Pembuatan kue tradisional "kue noga"
- Wisata alam & petualangan Curug Aman

**E. Agro Eduwisata Anggrek Bulan**: 🧪 **harga dummy** Rp75.000/pax, minimal 15 pax (akan diganti dari admin)
- Belajar budidaya langsung dari petani
- Melihat proses perkembangbiakan anggrek bulan

> Catatan: **semua harga dihitung per pax** (per orang). Yang berbeda antarpaket hanya tingkatan (Hemat/Standar/Premium) dan minimal peserta, keduanya diatur dari admin (lihat bagian 6).

---

## 3. Struktur Halaman (Sitemap)

```
Beranda
├── Tentang
│   ├── Profil Desa           (data resmi pemerintah: wilayah, penduduk, pemerintahan; lihat 3.2)
│   └── Desa Wisata           (cerita desa wisata, Pokdarwis, visi, peta lokasi)
├── Paket Wisata              (daftar + filter: edukasi/alam/budaya/kuliner, durasi, harga)
│   └── Detail Paket          (galeri, rundown, tingkat harga, fasilitas, tombol Booking)
├── Destinasi & Aktivitas     (Curug Aman, kelinci, stroberi, anggrek, dll.)
│   └── Detail Destinasi      (galeri foto, deskripsi, paket terkait)
├── Galeri                    (album per kategori, lightbox, bisa geser di HP)
├── UMKM Sukajadi             (direktori UMKM: overview satu per satu + kontak; lihat 3.3)
│   └── Detail UMKM           (profil usaha, produk, foto, kontak WA/IG, lokasi)
├── Booking / Reservasi       (form multi-langkah)
│   └── Cek Status Booking    (pakai kode booking + no. HP)
├── FAQ                       (cara booking, yang perlu dibawa, akses lokasi)
└── Kontak                    (WA, Instagram, Google Maps, jam operasional)

Semua halaman publik tersedia dalam dua bahasa:
  /            → Bahasa Indonesia (utama)
  /en/...      → English (misal /en/packages/rabbit-farming)

Admin (/admin, hanya pengurus, Bahasa Indonesia)
├── Dashboard (booking baru, jadwal minggu ini)
├── Paket & Tingkat Harga
├── Destinasi/Aktivitas
├── UMKM & Produk
├── Profil Desa (data resmi + sumber)
├── Galeri & Album
├── Booking
├── Kalender (tanggal penuh/libur, kuota opsional)
├── Testimoni, FAQ
├── Pengguna admin (2 akun)
└── Pengaturan (kontak WA/IG, teks halaman)
```

### 3.1 Isi Beranda (urutan section)
1. **Hero** foto besar/slideshow pemandangan desa + tagline "Belajar • Berwisata • Berdaya" + tombol "Lihat Paket" & "Booking Sekarang".
2. **4 Pilar**: Edukasi, Alam, Budaya, Kuliner Lokal (ikon + foto).
3. **Paket Unggulan**: kartu paket dengan harga "mulai dari", badge "Best Seller".
4. **Destinasi Populer**: Curug Aman, kebun stroberi, anggrek bulan, peternakan kelinci.
5. **Cocok Untuk**: sekolah, komunitas, instansi, keluarga.
6. **Fasilitas**: transportasi, makan, tiket, pemandu, asuransi.
7. **Galeri cuplikan**: grid foto (masonry) → tautan ke Galeri.
8. **UMKM Sukajadi**: 3–6 kartu UMKM (foto produk, nama usaha, satu kalimat overview, tombol WA) → tautan ke halaman UMKM.
9. **Testimoni** pengunjung/sekolah.
10. **Cara Booking** dalam 4 langkah.
11. **Lokasi** dengan peta Google Maps.
12. **CTA penutup** "Datang, Belajar, Berkesan!" + tombol WhatsApp.

Tombol **WhatsApp melayang** (floating) tampil di semua halaman.

### 3.2 Halaman Profil Desa (data resmi pemerintah)

Halaman berisi data resmi Desa Sukajadi. **Setiap angka ditampilkan bersama sumber & tahunnya** (misal *"Sumber: BPS, Kecamatan Tamansari Dalam Angka 2025"*), dan semua dapat diperbarui dari admin tanpa programmer.

| Bagian | Isi |
|---|---|
| Identitas | Nama desa, kode wilayah (Kemendagri), kecamatan, kabupaten, provinsi, kode pos |
| Sejarah singkat | Asal-usul nama & sejarah desa |
| Visi & misi | Visi-misi pemerintah desa (dari RPJMDes) |
| Geografi | Luas wilayah, ketinggian, batas wilayah (utara/selatan/barat/timur), jumlah dusun/RW/RT, peta wilayah |
| Kependudukan | Jumlah penduduk (laki-laki/perempuan), jumlah KK, kepadatan, mata pencaharian utama |
| Pemerintahan | Kepala desa, perangkat desa, BPD, struktur organisasi (bagan) |
| Kelembagaan | Pokdarwis, BUMDes, PKK, Karang Taruna |
| Status & prestasi | Status Indeks Desa (Kemendesa), SK/Perda penetapan desa wisata, penghargaan |
| Potensi desa | Pertanian, peternakan, wisata, UMKM (menautkan ke halaman terkait) |
| Kantor desa | Alamat, jam layanan, kontak, peta |

**Data awal yang sudah ditemukan** (perlu dicocokkan dengan data kantor desa):

| Data | Nilai | Sumber |
|---|---|---|
| Kode wilayah | 32.01.31.2008 | Wikipedia (mengutip BPS/Kemendagri) |
| Luas wilayah | ± 3,04 km² | Wikipedia (mengutip BPS) |
| Jumlah penduduk | ± 10.569 jiwa (perkiraan pertengahan 2024) | Wikipedia, mengutip BPS *Kecamatan Tamansari Dalam Angka 2025* |
| Kecamatan | Tamansari, 8 desa; Kawasan Strategis Pariwisata Kab. Bogor | BPS Kab. Bogor; jurnal Poltekpar NHI |

**Sumber resmi yang dipakai** (urut prioritas):
1. **Kantor Desa Sukajadi**: profil/monografi desa, RPJMDes, struktur pemerintahan, sejarah. Ini sumber utama; mohon minta salinannya.
2. **BPS Kabupaten Bogor**: publikasi *Kecamatan Tamansari Dalam Angka* (luas, penduduk, dll.).
3. **Kemendagri**: kode & nama wilayah administrasi resmi.
4. **Kemendesa**: status Indeks Desa.
5. **Pemkab Bogor / Kecamatan Tamansari** (`kecamatantamansari.bogorkab.go.id`): SK/Perda terkait desa wisata.

### 3.3 Halaman UMKM Sukajadi

Direktori UMKM yang menampilkan **overview tiap UMKM satu per satu beserta kontaknya**, agar pengunjung bisa memesan produk langsung ke pelaku usaha.

**Halaman daftar**: kartu per UMKM, filter kategori (Makanan, Minuman, Kerajinan, Pertanian, Lainnya).

**Halaman detail tiap UMKM:**
- Nama usaha, nama pemilik, kategori
- Overview singkat (cerita usaha, sejak kapan, keunikan)
- Produk unggulan (foto, nama, kisaran harga opsional)
- Galeri foto usaha/proses produksi
- **Kontak**: tombol WhatsApp (pesan otomatis *"Halo, saya melihat [nama UMKM] di website Desa Wisata Sukajadi…"*), Instagram, alamat + tautan Google Maps, jam buka
- Info layanan: terima pesanan, bisa dikunjungi rombongan, ikut dalam paket wisata (ditautkan ke paket terkait, misal Paket UMKM)

**UMKM awal (dari brosur, data kontak masih dummy):**

| UMKM | Kategori | Produk |
|---|---|---|
| Sari Buah Pala | Minuman | Minuman sari buah pala |
| Kue Noga | Makanan | Kue tradisional noga |
| Kue Tradisional | Makanan | Kue lapis, onde-onde, kue talam |
| Tempe | Makanan | Tempe |
| Anggrek Bulan | Pertanian | Tanaman anggrek bulan |
| Stroberi | Pertanian | Stroberi segar |

> ⚠️ Nomor WA dan alamat pelaku UMKM adalah data pribadi. Mintalah **izin tiap pemilik** sebelum kontaknya ditampilkan. Jika ada yang tidak berkenan, tombol kontak bisa diarahkan ke WA admin desa.

---

## 4. Desain & Identitas Visual

- **Warna** (diambil dari logo resmi):
  - Hijau logo `#277721`: warna utama (header, judul, tombol sekunder)
  - Oranye logo `#FA6700`: aksen & tombol CTA ("Booking Sekarang", badge "Best Seller")
  - Biru langit `#91D7E6`: aksen ringan (latar section alam/curug)
  - Krem kertas `#F5EFE3`: latar halaman agar terasa hangat
  - Hijau tua `#1B4D17`: teks judul & footer
- **Gaya**: alami, hangat, nuansa daun & papan kayu seperti brosur. Foto menjadi elemen utama.
- **Font** (Google Fonts, gratis): judul tebal *Poppins*/*Baloo 2*, aksen tulisan tangan *Kalam*/*Caveat* (untuk kalimat seperti "Datang, Belajar, Berkesan!"), teks isi *Inter*/*Nunito*.
- **Logo**: memakai logo lencana bundar yang sudah dikirim (`assets/brand/logo-desa-wisata-sukajadi.webp`). File saat ini berupa gambar raster (1080×1440 px, ada ruang kosong atas-bawah). Untuk hasil paling tajam (header, favicon, cetak), idealnya dibuat ulang versi **SVG** (vektor) dari logo ini. Logo pada brosur lama tidak dipakai.
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
| UMKM (×6+) | 4–8/UMKM | 1:1 & 3:2 | Foto produk berlatar bersih, pemilik, proses produksi, tempat usaha |
| Profil desa | 5–10 | 3:2 | Kantor desa, perangkat desa, peta wilayah, kegiatan warga |
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
     ─► Dapat KODE BOOKING (mis. SKJ-261014-0042) + tombol "Lanjutkan via WhatsApp"
          (pesan WA otomatis terisi: kode, paket, tanggal, jumlah peserta, estimasi total)
     ─► Konfirmasi jadwal & pembayaran dilanjutkan di WhatsApp dengan admin
```

Validasi otomatis:
- Jumlah peserta ≥ minimal paket (15 pax / 30 pax, dsb.).
- Tanggal tidak boleh di masa lalu atau tanggal yang ditutup admin.
- **Kuota harian belum ditentukan**: fitur kuota disiapkan tetapi defaultnya *tidak dibatasi*. Admin bisa mengisinya kapan saja nanti.
- Batas pemesanan minimal H-x (misal H-3), diatur admin.

### 6.2 Alur di sisi pengelola

```
Baru ─► Dikonfirmasi ─► Selesai
   └──────────┴──────► Dibatalkan
```

- Notifikasi **email** ke admin setiap ada booking baru (gratis, pakai email bawaan hosting).
- Di admin ada tombol **"Kirim WA"** yang membuka WhatsApp dengan pesan siap kirim ke pemesan (gratis, memakai `wa.me`, tidak perlu WhatsApp API berbayar).
- Admin cukup mengubah status & menambah catatan internal (misal "sudah transfer DP" atau "ganti tanggal").
- Kalender jadwal: melihat rombongan per tanggal.

### 6.3 Pembayaran
- **Tidak ditangani di website.** Aturan DP, pelunasan, pembatalan, dan rekening dibahas langsung lewat **WhatsApp** dengan admin.
- Website hanya menampilkan **estimasi harga** (harga × jumlah peserta) sebagai acuan.
- Halaman FAQ cukup menulis: *"Pembayaran & ketentuan lainnya akan dijelaskan oleh admin melalui WhatsApp."*
- Jika suatu saat volume booking sudah tinggi, payment gateway bisa ditambahkan tanpa mengubah struktur data booking.

### 6.4 Model data (dirancang untuk berkembang)

| Tabel | Isi utama |
|---|---|
| `packages` | nama, slug, kategori, deskripsi, durasi, foto sampul, aktif/tidak, unggulan |
| `package_tiers` | tingkat (Hemat/Standar/Premium), harga per pax, minimal pax, isi/fasilitas, badge, penanda **harga dummy** (tampil label "harga sementara" di admin) |
| `attractions` | destinasi/aktivitas: nama, kategori, deskripsi, lokasi |
| `package_attraction` | relasi paket ↔ aktivitas |
| `albums`, `photos` | galeri, keterangan foto, urutan, terkait paket/destinasi |
| `bookings` | kode, paket, tingkat, tanggal, jumlah pax, data pemesan, jenis rombongan, **bahasa pemesan**, estimasi total, status, catatan internal |
| `blocked_dates` / `capacity` | tanggal tutup & kuota harian per paket (kosong = tidak dibatasi) |
| `addons` (Fase 2) | tambahan opsional: dokumentasi, oleh-oleh, menu tambahan |
| `umkms` | nama usaha, slug, pemilik, kategori, overview, logo/foto sampul, WA, Instagram, alamat, link Maps, jam buka, terima pesanan/kunjungan, tampilkan kontak (ya/tidak), urutan |
| `umkm_products` | UMKM, nama produk, foto, kisaran harga (opsional) |
| `village_profile` | isian halaman Profil Desa per bagian, termasuk **sumber & tahun data** |
| `testimonials`, `faqs`, `settings` | konten pendukung & pengaturan (kontak WA/IG, teks halaman) |
| `users` | akun admin (lihat 6.5) |

Dengan struktur ini, paket baru cukup ditambahkan dari admin.

Kolom teks yang tampil ke pengunjung (nama, deskripsi, isi paket, keterangan foto, FAQ, dll.) disimpan **dalam dua bahasa** (lihat bagian 6.6).

### 6.5 Pengguna admin
- **2 akun** sejak awal:
  - **Super Admin** (Anda): semua menu, termasuk Pengguna & Pengaturan.
  - **Admin** (pengurus berikutnya, menyusul): kelola paket, galeri, destinasi, dan booking; tidak bisa mengubah pengguna & pengaturan inti.
- Akun admin kedua dibuat dari menu Pengguna kapan saja.
- Notifikasi booking baru dikirim ke email semua admin aktif.

### 6.6 Dua bahasa (Indonesia + English)
- **URL**: Indonesia di `/`, English di `/en/` dengan slug sendiri (misal `/paket/budidaya-kelinci` ↔ `/en/packages/rabbit-farming`) agar bagus di Google.
- **Tombol ganti bahasa** (ID | EN) di header, tetap di halaman yang sama.
- **Teks tetap** (menu, tombol, label form) memakai file terjemahan Laravel.
- **Konten dari admin**: setiap kolom teks punya tab **Indonesia** dan **English** di form admin. Jika versi English belum diisi, otomatis tampil versi Indonesia (tidak ada halaman kosong).
- **Booking**: pesan WhatsApp otomatis & email konfirmasi mengikuti bahasa pemesan. Harga tetap dalam Rupiah.
- **SEO**: tag `hreflang`, sitemap dua bahasa, judul & deskripsi per bahasa.
- Terjemahan awal English untuk semua konten bisa saya siapkan, nanti tinggal dikoreksi.

---

## 7. Teknologi (semua gratis / open source)

### 7.1 Rekomendasi: **Laravel + Filament** di shared hosting

| Komponen | Pilihan | Alasan |
|---|---|---|
| Backend | **Laravel** (PHP) | Gratis, stabil, sangat umum di Indonesia (mudah cari programmer pengganti), jalan di shared hosting murah |
| Panel admin | **Filament** | Gratis, admin panel modern siap pakai (kelola paket, foto, booking) tanpa membangun dari nol |
| Database | MySQL/MariaDB | Sudah termasuk di paket hosting |
| Tampilan | Blade + **Tailwind CSS** + **Alpine.js** | Ringan, cepat, cocok untuk HP |
| Dua bahasa | Localization bawaan Laravel + **spatie/laravel-translatable** + plugin translatable Filament | Gratis, konten per bahasa bisa diisi dari admin |
| Pengolahan foto | Intervention Image / Spatie Media Library | Otomatis resize + WebP |
| Galeri | **PhotoSwipe** atau **GLightbox** | Lightbox gratis, nyaman di HP |
| Peta | Embed Google Maps | Gratis (embed, bukan API berbayar) |
| Email | SMTP bawaan hosting | Gratis |
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
| Domain `desawisatasukajadi.com` | ± Rp150–250 rb/tahun | Cek juga harga perpanjangan |
| Shared hosting (spek di atas) | ± Rp300–800 rb/tahun | Cek harga perpanjangan, bukan hanya harga promo tahun pertama |
| Perangkat lunak, font, ikon, peta, analitik | **Rp0** | Semua open source / gratis |
| **Total** | **± Rp450 rb – 1 jt/tahun** | |

> Harga di atas adalah kisaran perkiraan dan perlu dicek ulang ke penyedia saat membeli.

Domain terpilih: **`desawisatasukajadi.com`**. Sekalian buat email resmi gratis dari hosting, misal `info@desawisatasukajadi.com` (untuk notifikasi booking & kontak).

---

## 8. SEO & Pemasaran (gratis)

- Judul & deskripsi tiap halaman berisi kata kunci: "desa wisata Bogor", "wisata edukasi Bogor", "study tour Bogor", "Curug Aman Tamansari", "petik stroberi Bogor", dll.
- Versi English menyasar kata kunci seperti "Bogor village tour", "educational tour Bogor", "Bogor waterfall", "farm tour near Jakarta".
- **Structured data** (schema.org `TouristAttraction`, `Product`/`Offer` untuk paket) agar tampil menarik di Google.
- Sitemap XML otomatis + daftar ke **Google Search Console**.
- Buat/klaim **Google Business Profile** "Desa Wisata Sukajadi" dan tautkan ke website (sangat berpengaruh untuk pencarian lokal & Google Maps).
- Tombol bagikan ke WhatsApp/Facebook; tampilan pratinjau tautan (Open Graph) yang menarik saat dibagikan di grup WA.
- Tautkan website di bio Instagram.

---

## 9. Tahapan Pengembangan

### Fase 0: Persiapan konten (oleh pengelola desa)
- [x] Kontak resmi (WA & Instagram), logo, kejelasan Paket Padi
- [x] Domain, jumlah admin, satuan harga (per pax), dua bahasa
- [ ] Harga asli Paket UMKM & Anggrek Bulan (sementara pakai dummy)
- [ ] Teks "Tentang Desa" (sejarah singkat, Pokdarwis, keunikan)
- [ ] Rundown kegiatan per paket
- [ ] Data resmi profil desa dari kantor desa (monografi/profil desa, RPJMDes, struktur pemerintahan)
- [ ] Daftar UMKM + overview + kontak, **beserta izin pemilik** untuk menampilkan kontak
- [ ] Mulai pengambilan foto sesuai shot list
- [ ] Beli domain `desawisatasukajadi.com` & hosting

### Fase 1: MVP (showcase + booking dasar) ✅ selesai dibangun
- [x] Setup Laravel 13 + Filament 5 + Tailwind CSS 4 (deploy ke hosting: lihat `docs/PANDUAN-DEPLOY.md`)
- [x] Halaman: Beranda, Profil Desa, Desa Wisata, Paket (+detail), Destinasi (+detail), UMKM (+detail), Galeri, Berita, FAQ, Kontak
- [x] Placeholder foto berlabel di semua posisi foto (otomatis berganti saat foto diunggah)
- [x] Dua bahasa (Indonesia + English): URL `/en`, tombol ganti bahasa, konten admin per bahasa, terjemahan awal
- [x] Admin: kelola paket, tingkat harga, destinasi, UMKM, profil desa, galeri, pengaturan, 2 peran (Super Admin & Admin)
- [x] Form booking + kode booking + notifikasi email + tombol WA
- [x] Admin: daftar & status booking + tombol "Kirim WA"
- [x] SEO dasar: meta, Open Graph, hreflang, schema.org, `sitemap.xml`, `robots.txt`; kolom Google Analytics di Pengaturan

### Fase 2: Booking lengkap ✅ selesai dibangun
- [x] Kalender booking & kuota harian (global / per paket; kosong = tidak dibatasi) + tanggal ditutup
- [x] Halaman Cek Status Booking
- [x] Add-on (dokumentasi, oleh-oleh, menu tambahan)
- [x] Ulasan pengunjung (dengan moderasi admin)
- [x] Laporan per bulan + ekspor CSV (bisa dibuka di Excel)

### Fase 3: Pengembangan lanjutan ✅ selesai dibangun
- [x] Paket custom (pemesan memilih sendiri aktivitas → harga dihitung otomatis)
- [x] Berita/blog kegiatan
- [x] Katalog produk UMKM dengan pemesanan via WhatsApp (tanpa pembayaran online)

---

## 10. Keputusan yang Sudah Dikonfirmasi

| Topik | Keputusan |
|---|---|
| Instagram | `@desawisatasukajadi` |
| WhatsApp | +62 857-7519-4575 |
| TikTok | Tidak ada |
| Logo | Lencana bundar (`assets/brand/logo-desa-wisata-sukajadi.webp`) |
| Paket Budidaya Padi | Satu paket (dua panel di brosur = paket yang sama) |
| Satuan harga | Semua **per pax** |
| Harga UMKM & Anggrek Bulan | Dummy dulu, diisi kemudian |
| Pembayaran | Tidak diatur di website, cukup lewat WhatsApp |
| Kuota harian | Belum ditentukan → default tidak dibatasi, bisa diatur nanti |
| Admin | 2 akun: Anda (Super Admin) + 1 pengurus menyusul |
| Domain | `desawisatasukajadi.com` |
| Bahasa | Indonesia + English sejak awal |
| UMKM | Satu section/halaman direktori: overview tiap UMKM + kontak |
| Profil Desa | Halaman khusus berisi data resmi pemerintah, lengkap dengan sumber |

### Masih menunggu (tidak menghambat pengembangan)
- Harga asli Paket UMKM & Anggrek Bulan
- Kuota harian
- Nama & email admin kedua
- Foto asli & teks "Tentang Desa"
- Data resmi profil desa dari kantor desa
- Daftar UMKM, kontak, dan izin pemiliknya
