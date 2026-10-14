# Menjalankan Website di Komputer Sendiri (localhost)

Panduan ini untuk melihat dan mencoba website lengkap (termasuk booking dan panel admin) di komputer Anda sebelum dipasang di hosting. Tidak perlu internet setelah instalasi selesai.

## 1. Pasang program yang dibutuhkan (sekali saja)

Cara termudah: **Laravel Herd** (gratis). Herd sudah berisi PHP dan Composer.

- Windows: https://herd.laravel.com/windows
- Mac: https://herd.laravel.com

Unduh, pasang, lalu buka Herd sekali. Setelah itu **tutup dan buka lagi** jendela PowerShell/Terminal agar perintah `php` dan `composer` dikenali.

Cek di PowerShell/Terminal:

```bash
php -v        # harus PHP 8.3 atau lebih baru
composer -V
```

> Node.js **tidak** diperlukan untuk sekadar menjalankan website; tampilan (CSS/JS) sudah dibangun dan ikut di dalam kode.

## 2. Ambil kode website

**Pilihan A: unduh ZIP (tanpa Git)**
1. Buka repo `imagiive-dk-group/website-sukajadi` di GitHub.
2. Pilih branch **`claude/youthful-clarke-kfoday`**.
3. Klik **Code → Download ZIP**, lalu ekstrak, misalnya ke `C:\Users\NamaAnda\Herd\website-sukajadi`.

**Pilihan B: dengan Git**

```bash
git clone -b claude/youthful-clarke-kfoday https://github.com/imagiive-dk-group/website-sukajadi.git
```

## 3. Siapkan website (sekali saja)

Buka PowerShell/Terminal **di dalam folder website**, lalu jalankan:

```bash
composer run setup
```

Perintah ini memasang paket PHP, membuat file `.env`, membuat database (SQLite, berupa satu file), lalu mengisi semua data awal: paket dari brosur, destinasi, UMKM, profil desa, dan akun admin. Proses pertama bisa memakan beberapa menit.

## 4. Jalankan

```bash
composer run preview
```

Lalu buka di browser:

| Halaman | Alamat |
|---|---|
| Website | http://127.0.0.1:8000 |
| Versi English | http://127.0.0.1:8000/en |
| Panel admin | http://127.0.0.1:8000/admin |

Login admin: **admin@desawisatasukajadi.com** / **ubah-password-ini**

Untuk berhenti, tekan **Ctrl + C** di jendela PowerShell/Terminal. Besok-besok cukup jalankan `composer run preview` lagi (tidak perlu setup ulang).

## Yang bisa dicoba

- Booking lengkap: pilih paket → isi formulir → dapat kode booking → cek status di **Cek Booking**. Booking langsung muncul di **Admin → Daftar Booking** dan **Kalender Booking**.
- Unggah foto dari admin (misalnya foto sampul paket) dan lihat placeholder berganti di website.
- Ganti teks, harga, kontak, atau tambah UMKM dari admin, lalu muat ulang halaman website.
- Email notifikasi booking belum benar-benar dikirim di komputer lokal; isinya dicatat di file `storage/logs/laravel.log`.

## Mengulang dari awal

Untuk menghapus semua data percobaan dan kembali ke data awal:

```bash
php artisan migrate:fresh --seed
```

## Jika ada masalah

| Pesan | Solusi |
|---|---|
| `php` / `composer` tidak dikenali | Tutup dan buka lagi PowerShell/Terminal setelah memasang Herd. |
| `Address already in use` / port 8000 dipakai | Jalankan `php artisan serve --port=8080`, lalu buka http://127.0.0.1:8080 |
| Error soal ekstensi `intl`, `gd`, `zip`, atau `sqlite` (bila memakai XAMPP, bukan Herd) | Aktifkan ekstensi tersebut di `php.ini` (hapus tanda `;` di depan `extension=intl`, `extension=gd`, `extension=zip`, `extension=pdo_sqlite`, `extension=sqlite3`), lalu ulangi. |
| Halaman tampil tanpa warna/gaya | Pastikan folder `public/build` ikut terunduh (ada di dalam ZIP/branch). |
