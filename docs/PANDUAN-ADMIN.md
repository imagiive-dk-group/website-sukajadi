# Panduan Admin Desa Wisata Sukajadi

Panel admin ada di **`https://desawisatasukajadi.com/admin`**. Semua menu berbahasa Indonesia.

## Peran pengguna

| Peran | Bisa mengakses |
|---|---|
| **Super Admin** | Semua menu, termasuk **Pengguna Admin** dan **Pengaturan Website** |
| **Admin** | Booking, paket, destinasi, UMKM, galeri, berita, profil desa (tanpa Pengguna & Pengaturan) |

Menambah admin kedua: **Pengaturan → Pengguna Admin → Buat**, pilih peran *Admin*.

## Mengisi dua bahasa

Setiap isian teks punya tab **Indonesia** dan **English**.
- Tab Indonesia wajib diisi.
- Tab English boleh dikosongkan; website versi English otomatis menampilkan teks Indonesia sampai terjemahannya diisi.
- Slug (alamat URL) dibuat otomatis dari nama; biasanya tidak perlu diubah.

## Foto

- Unggah foto dalam format JPG/PNG/WebP (maks. 10 MB). Sistem otomatis membuat versi kecil, sedang, dan besar (WebP) agar website tetap cepat di HP.
- Selama foto belum diunggah, website menampilkan **placeholder** berisi keterangan foto yang dibutuhkan (kolom *Keterangan foto yang dibutuhkan*). Gunakan keterangan itu sebagai daftar foto yang perlu diambil.
- Galeri foto ada di tab/bagian **Foto** pada Paket, Destinasi, UMKM, dan Album. Tombol **Unggah banyak foto** untuk mengunggah sekaligus; urutan bisa diatur dengan seret (drag).
- Foto latar beranda (hero) diubah di **Pengaturan Website**.

## Booking

1. Booking baru masuk ke **Booking → Daftar Booking** (angka oranye = booking baru) dan dikirim ke email semua admin aktif.
2. Klik **Kirim WA** untuk membuka WhatsApp dengan pesan konfirmasi yang sudah terisi.
3. Setelah sepakat jadwal & pembayaran di WhatsApp, klik **Konfirmasi**. Setelah kunjungan, klik **Selesai**. Jika batal, klik **Batalkan** (booking batal tidak memakai kuota).
4. Catatan internal (misal "DP sudah transfer") ditulis di kolom *Catatan internal admin*. Pembayaran **tidak** diproses di website.
5. **Booking manual**: untuk pesanan yang masuk langsung lewat WhatsApp/telepon.

Menu lain di grup Booking:
- **Kalender Booking**: jadwal rombongan per bulan beserta total pax per hari.
- **Tanggal Ditutup**: tutup tanggal tertentu (untuk semua paket atau satu paket), misalnya hari besar atau acara desa.
- **Laporan**: rekap per bulan (booking, pax, estimasi pendapatan) dan tombol **Ekspor CSV (Excel)**.

Kuota harian: isi di **Pengaturan Website → Booking** (berlaku untuk semua paket) atau di masing-masing **Paket Wisata** (kolom *Kuota harian (pax)*). Kosong = tidak dibatasi.

## Paket & harga

- **Wisata → Paket Wisata**: setiap paket punya satu atau lebih **tingkat harga** (misal Hemat/Standar/Premium). Harga selalu **per pax**, dengan minimal peserta per tingkat.
- Tandai **Harga sementara (dummy)** bila harga belum final. Paket UMKM dan Anggrek Bulan saat ini masih memakai harga dummy.
- **Destinasi & Aktivitas**: kolom *Harga paket custom (per pax)* menentukan harga per pax bila aktivitas dipilih di halaman **Paket Custom**. Biaya dasar & minimal pax paket custom diatur di Pengaturan Website.
- **Tambahan Booking**: add-on seperti dokumentasi atau oleh-oleh (per pax atau per rombongan).

## UMKM

- **UMKM → Direktori UMKM**: profil usaha, kontak, dan produk (tab *Produk*).
- Aktifkan **Tampilkan kontak UMKM di website** hanya jika pemilik sudah memberi izin. Jika nonaktif, tombol WhatsApp di halaman UMKM diarahkan ke WA admin desa.

## Profil Desa

- **Data Profil**: angka resmi (luas, penduduk, dll.) beserta **sumber** dan **tahun**. Matikan *Perlu verifikasi* setelah data dicocokkan dengan Kantor Desa.
- **Bagian Profil**: teks sejarah, visi & misi, geografi, potensi, kelembagaan.
- **Perangkat Desa**: nama, jabatan, dan foto.

## Konten lain

- **Berita**: artikel kegiatan desa (bagus untuk Google). Hapus artikel contoh setelah ada berita asli.
- **FAQ**: pertanyaan umum, urutan bisa diseret.
- **Ulasan**: ulasan dari pengunjung masuk dengan status menunggu; klik **Setujui** agar tampil. Hapus 3 ulasan contoh setelah ada ulasan asli.
- **Pesan Masuk**: pesan dari formulir Kontak, dengan tombol balas via WhatsApp/email.

## Pengaturan Website (Super Admin)

Nama & slogan, teks beranda dan "Tentang", foto hero, nomor WhatsApp, Instagram, email, alamat, jam operasional, peta Google Maps, aturan booking (batas H-, kuota, paket custom), email notifikasi tambahan, dan Google Analytics.

## Yang perlu diganti sebelum diluncurkan

- [ ] Ganti password Super Admin
- [ ] Unggah foto asli (ikuti keterangan placeholder)
- [ ] Harga asli Paket UMKM & Anggrek Bulan (matikan *Harga sementara*)
- [ ] Data resmi profil desa, nama perangkat desa
- [ ] Kontak UMKM (dengan izin pemilik)
- [ ] Hapus berita & ulasan contoh
- [ ] Cek ulang teks "Tentang" dan rundown paket
