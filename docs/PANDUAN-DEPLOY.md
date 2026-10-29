# Panduan Deploy ke Hosting

Website ini dibangun dengan **Laravel 13 + Filament 5** (PHP). Semua perangkat lunaknya gratis; biaya hanya **domain + hosting**.

Panduan ini ditulis dari pemasangan nyata di **Hostinger (hPanel, LiteSpeed)**, satu akun dengan website lain. Catatan untuk cPanel ada di bagian 7.

## 1. Syarat hosting

| Kebutuhan | Keterangan |
|---|---|
| PHP **8.3+** | ekstensi: `gd`, `intl`, `mbstring`, `pdo_mysql`, `zip`, `fileinfo`, `exif` |
| MySQL / MariaDB | 1 database |
| **Akses SSH** + Composer + git | untuk instalasi & update |
| SSL gratis | wajib untuk https |
| Akun email | untuk notifikasi booking |
| Penyimpanan ≥ 5–10 GB | foto otomatis dikompres ke WebP |

Node.js **tidak** diperlukan di server: file CSS/JS sudah dibangun dan disimpan di `public/build`.

## 2. Instalasi pertama (hPanel)

Bagian A–C dikerjakan di panel; D dan seterusnya lewat SSH.

**A. Tambahkan domain.** hPanel → *Websites → Add website* → *Empty PHP / Custom website* → isi domain. Folder website dibuat di `~/domains/<domain>/public_html` dan terpisah dari website lain di akun yang sama.

**B. SSL.** *Security → SSL*, pasang SSL gratis (setelah DNS aktif), lalu aktifkan *Force HTTPS*.

**C. Database.** *Databases → Management → Create new database*. hPanel menambahkan **awalan akun** pada nama database dan user, misalnya `u123456789_desasukajadi` dan `u123456789_admin`. Pakai nama lengkap itu di `.env`. Simpan password di password manager, **jangan dikirim lewat chat atau disimpan di repo**.

**D. Ambil kode** (SSH, folder di luar `domains/` supaya tidak terjangkau web):

```bash
cd ~
git clone https://github.com/imagiive-dk-group/website-sukajadi.git sukajadi-app
cd sukajadi-app
php -d disable_functions= /usr/local/bin/composer install --no-dev --optimize-autoloader
```

> Hostinger mematikan `proc_open`, padahal Composer membutuhkannya. Awalan `php -d disable_functions=` mengaktifkannya hanya untuk satu perintah itu. Gunakan juga untuk `artisan` bila ada pesan serupa.

**E. Isi `.env`.** Salin dari `.env.example`, lalu isi:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://desawisatasukajadi.com
LOG_STACK=daily
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=<awalan>_desasukajadi
DB_USERNAME=<awalan>_admin
DB_PASSWORD=

SESSION_SECURE_COOKIE=true

MAIL_MAILER=smtp
MAIL_SCHEME=smtps
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_USERNAME=admin@desawisatasukajadi.com
MAIL_PASSWORD=
MAIL_FROM_ADDRESS="admin@desawisatasukajadi.com"

ADMIN_EMAIL=admin@desawisatasukajadi.com
ADMIN_PASSWORD=
```

Lalu:

```bash
php artisan key:generate --force
chmod 600 .env
nano .env        # isi DB_PASSWORD, MAIL_PASSWORD, ADMIN_PASSWORD sendiri
```

Dari laptop, sekali jalan:

```bash
ssh -t -i ~/.ssh/<kunci> -p <port> <user>@<ip-server> "nano ~/sukajadi-app/.env"
```

Jangan menjalankan `nano` di PowerShell laptop; file `.env` ada di server.

**F. Tabel dan data awal** (hanya sekali, termasuk akun Super Admin):

```bash
php artisan migrate --seed --force
php artisan optimize
mkdir -p public/uploads && chmod -R 775 public/uploads storage bootstrap/cache
```

**G. Document root.** Pada Hostinger, folder `public_html` tidak bisa dipindah, jadi diganti tautan simbolik ke folder `public`:

```bash
cd ~/domains/desawisatasukajadi.com
mv public_html public_html_default
ln -s ~/sukajadi-app/public public_html
```

**H. Uji.** Buka `https://desawisatasukajadi.com`, lalu `/admin` dan login dengan `ADMIN_EMAIL` & `ADMIN_PASSWORD`. Buat satu booking percobaan dan pastikan emailnya masuk.

> Pesan `SQLSTATE[HY000] [1045] Access denied` di `storage/logs` berarti nama/user/password database di `.env` salah (cek awalan akun). Setelah mengubah `.env`, jalankan `php artisan config:clear`.

Website ini **tidak memakai cron**: tidak ada tugas terjadwal.

## 3. Update website

Setiap ada perubahan kode di GitHub:

```bash
cd ~/sukajadi-app
php -d disable_functions= artisan down
git pull
php -d disable_functions= /usr/local/bin/composer install --no-dev --optimize-autoloader
php -d disable_functions= artisan migrate --force
php -d disable_functions= artisan optimize
php -d disable_functions= artisan up
```

> Jangan jalankan `composer run setup`, `migrate:fresh`, atau `db:seed` di server produksi setelah website berjalan: perintah itu menghapus/menimpa data. `.env` dan `public/uploads` tidak ikut git dan tidak tersentuh `git pull`.

## 4. Backup

- **Database**: hPanel → *Backups* (backup harian tersedia), atau unduh lewat *phpMyAdmin → Export*, minimal seminggu sekali.
- **Foto**: folder `~/sukajadi-app/public/uploads` (semua foto yang diunggah dari admin).
- **File `.env`**: simpan salinannya di password manager. File ini tidak ada di GitHub.

## 5. Setelah online

1. Daftarkan website di **Google Search Console** dan kirim sitemap `https://desawisatasukajadi.com/sitemap.xml`.
2. Klaim/buat **Google Business Profile** "Desa Wisata Sukajadi" dan cantumkan website.
3. (Opsional) Isi **Google Analytics 4 Measurement ID** di Admin → Pengaturan Website.
4. Pasang link website di bio Instagram `@desawisatasukajadi`.
5. (Opsional) Hubungkan domain ke **Cloudflare (gratis)** agar foto lebih cepat dimuat.

## 6. Pengembangan di komputer lokal

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
npm run build        # atau: npm run dev (saat mengubah tampilan)
php artisan serve    # buka http://localhost:8000
php artisan test     # menjalankan semua tes
```

Setelah mengubah CSS/JS, jalankan `npm run build` lalu commit folder `public/build`.

## 7. Bila memakai cPanel

- Database: *MySQL Databases*, buat database + user, beri semua hak akses.
- Email: *Email Accounts*; host SMTP biasanya `mail.<domain>`.
- Document root: *Domains* → ubah *Document Root* menjadi `sukajadi-app/public`; jika tidak bisa, gunakan tautan simbolik seperti langkah G.
- SSL: *SSL/TLS Status* → *Run AutoSSL*.
