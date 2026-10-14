# Panduan admin Yumnia

Untuk tim inti desa dan siswa SMK. Semua pekerjaan dilakukan di **wp-admin** tanpa menyentuh kode.

## 1. Memproses pesanan harian

Pesanan masuk lewat dua jalur sekaligus: **chat WhatsApp** dari pembeli dan **WooCommerce → Pesanan** dengan status **Baru**.

| Langkah | Yang dilakukan admin | Status di admin |
| --- | --- | --- |
| 1 | Balas chat: total belanja + ongkir + rekening/QRIS | Baru |
| 2 | Bukti bayar diterima dan dicek | ubah ke **Dibayar** (stok otomatis berkurang) |
| 3 | Paket dikirim, kirim nomor resi ke pembeli | ubah ke **Dikirim** |
| 4 | Paket sampai | ubah ke **Selesai** |

- Pembeli batal: ubah ke **Dibatalkan**. Jika sudah Dibayar, stok dikembalikan otomatis.
- Di halaman detail pesanan ada tombol **Chat pembeli di WhatsApp** di bawah alamat.
- Ongkir bisa dicatat di pesanan: **Tambah item → Tambah biaya**, isi nominal ongkir.

## 2. Menambah produk baru

**Produk → Tambah Baru**

1. **Nama produk** dan **Deskripsi** (kotak besar).
2. **Deskripsi singkat produk** (kotak bawah) tampil di dekat harga, 1–2 kalimat.
3. Di kotak **Data produk** pilih **Produk bervariasi**.
4. Tab **Atribut**: tambahkan **Warna** dan **Ukuran**, pilih nilainya, centang *Digunakan untuk variasi*, lalu **Simpan atribut**.
5. Tab **Variasi**: pilih *Buat variasi dari semua atribut*. Untuk tiap variasi isi:
   - Harga normal
   - Centang *Kelola stok?* dan isi jumlah stok
   - Foto variasi: foto produk dengan warna tersebut
6. Tab **Yumnia**:
   - Tandai Baru
   - Dibuat oleh
   - Bahan & perawatan
   - Ukuran (hijab), atau tabel ukuran, tinggi hak, dan jenis sol (sandal)
   - Cerita nama model
   - Tautan Shopee dan Tokopedia
7. Tab **Produk tertaut → Cross-sells**: pilih produk pasangan. Ini yang tampil di bagian "Cocok dengan…".
8. Kanan bawah:
   - **Gambar produk**: foto utama, latar polos
   - **Galeri produk**: foto **pertama** dipakai sebagai foto saat produk dipakai, yang muncul ketika kartu produk disentuh
9. **Kategori produk**: Hijab atau Sandal. Tekan **Terbitkan**.

**Bintang** di daftar Produk menandai produk **unggulan**. Produk unggulan tampil di "Koleksi peluncuran" di beranda.

## 3. Mengubah harga & stok

**Produk → Semua Produk** → buka produk → tab **Variasi** → ubah harga atau stok per variasi → **Simpan perubahan**.
Stok 0 otomatis tampil sebagai **Habis**.

> Stok di website, Shopee, dan Tokopedia dicatat terpisah. Setiap ada penjualan di marketplace, kurangi juga stok di website (dan sebaliknya). Lihat [USULAN-TEKNIS.md](USULAN-TEKNIS.md#sinkron-stok-dengan-marketplace).

## 4. Warna baru

**Produk → Atribut → Warna → Konfigurasikan istilah**. Tambahkan nama warna dan pilih **Kode warna**. Kode ini dipakai untuk bulatan warna di toko.

## 5. Set padu-padan

Menu **Padu-padan → Tambah set**:

1. Judul dan **Ringkasan** (1 kalimat).
2. **Foto look** di kanan bawah: foto model memakai hijab + sandal.
3. **Produk dalam set**: pilih 2–4 produk dan warnanya.
4. **Urutan** (kotak *Atribut halaman*) menentukan urutan tampil. Tiga set teratas tampil di beranda.

## 6. Mengganti teks & foto beranda, kontak, logo

**Tampilan → Sesuaikan → Pengaturan Yumnia**

| Bagian | Isi |
| --- | --- |
| Kontak & WhatsApp | Nomor WA admin (semua pesanan masuk ke sini), media sosial, alamat, jam layanan, teks pita atas |
| Beranda | Foto hero desktop & HP, judul, teks, paragraf perkenalan, angka sorotan, foto & kalimat "Dari Sukajadi" |
| Liputan media | Nama media + tautan artikel |
| Logo mitra | Logo Desa Wisata, RATU, Leads (tampil kecil di footer) |
| Bagikan & analitik | Gambar saat link dibagikan, ID Google Analytics |

Logo Yumnia: **Tampilan → Sesuaikan → Identitas Situs → Logo**.

## 7. Halaman Cerita, Bantuan, Kontak

**Halaman → Semua Halaman** → buka halaman → edit langsung seperti mengetik dokumen.
Tanya jawab memakai blok **Detail**: satu blok untuk satu pertanyaan.
Untuk foto berbentuk lengkung, pilih blok gambar → **Gaya → Lengkung**.

## 8. Tips foto

- Rasio **4:5** (contoh 1200×1500 px), latar polos terang, cahaya siang.
- Satu foto per warna, plus foto saat dipakai.
- Ukuran file sebaiknya di bawah 500 KB. WordPress otomatis membuat versi kecil untuk HP.
