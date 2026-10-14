# Panduan Deploy ke Hosting

Website ini dibangun dengan **Laravel 13 + Filament 5** (PHP). Semua perangkat lunaknya gratis; biaya hanya **domain + hosting**.

## 1. Syarat hosting

Pilih paket shared hosting (cPanel) yang memiliki:

| Kebutuhan | Keterangan |
|---|---|
| PHP **8.3+** | ekstensi: `gd`, `intl`, `mbstring`, `pdo_mysql`, `zip`, `fileinfo`, `exif` |
| MySQL / MariaDB | 1 database |
| **Akses SSH** + Composer | untuk instalasi & update |
| SSL gratis (AutoSSL / Let's Encrypt) | wajib untuk https |
| Akun email | untuk notifikasi booking (`info@desawisatasukajadi.com`) |
| Penyimpanan ≥ 5–10 GB | foto otomatis dikompres ke WebP |

Node.js **tidak** diperlukan di server: file CSS/JS sudah dibangun dan disimpan di `public/build`.

## 2. Instalasi pertama

1. **Domain**: arahkan domain `desawisatasukajadi.com` ke hosting (nameserver dari penyedia hosting).
2. **Database**: di cPanel → *MySQL Databases*, buat database + user, beri semua hak akses. Catat nama database, user, dan password.
3. **Email**: di cPanel → *Email Accounts*, buat `info@desawisatasukajadi.com`. Catat host SMTP, port, dan password.
4. **Ambil kode** (via SSH):

   ```bash
   cd ~
   git clone https://github.com/imagiive-dk-group/website-sukajadi.git desawisatasukajadi
   cd desawisatasukajadi
   composer install --no-dev --optimize-autoloader
   cp .env.example .env
   php artisan key:generate
   ```

5. **Isi `.env`** (pakai `nano .env`):

   ```dotenv
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://desawisatasukajadi.com

   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=nama_database
   DB_USERNAME=user_database
   DB_PASSWORD=password_database

   MAIL_MAILER=smtp
   MAIL_HOST=mail.desawisatasukajadi.com
   MAIL_PORT=465
   MAIL_SCHEME=smtps
   MAIL_USERNAME=info@desawisatasukajadi.com
   MAIL_PASSWORD=password_email
   MAIL_FROM_ADDRESS=info@desawisatasukajadi.com

   ADMIN_EMAIL=email-anda@gmail.com
   ADMIN_PASSWORD=password-kuat-anda
   ```

6. **Buat tabel & isi data awal** (paket, destinasi, UMKM, profil desa, akun Super Admin):

   ```bash
   php artisan migrate --seed --force
   php artisan optimize
   ```

7. **Document root**: arahkan domain ke folder `public`.
   - cPanel → *Domains* → ubah *Document Root* menjadi `desawisatasukajadi/public`, **atau**
   - jika tidak bisa diubah:
     ```bash
     mv ~/public_html ~/public_html_lama
     ln -s ~/desawisatasukajadi/public ~/public_html
     ```
8. **Folder foto**: pastikan folder unggahan bisa ditulis:
   ```bash
   mkdir -p public/uploads && chmod -R 775 public/uploads storage bootstrap/cache
   ```
9. **SSL**: cPanel → *SSL/TLS Status* → *Run AutoSSL*.
10. Buka `https://desawisatasukajadi.com/admin`, login dengan `ADMIN_EMAIL` & `ADMIN_PASSWORD`, lalu **ganti password** dari menu profil.

## 3. Update website

Setiap ada perubahan kode di GitHub:

```bash
cd ~/desawisatasukajadi
php artisan down
git pull
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize
php artisan up
```

> Jangan jalankan `migrate:fresh` atau `db:seed` di server produksi setelah website berjalan: perintah itu menghapus/menimpa data.

## 4. Backup

- **Database**: cPanel → *Backup* → unduh backup MySQL, minimal seminggu sekali.
- **Foto**: folder `public/uploads` (semua foto yang diunggah dari admin).
- **File `.env`**: simpan salinannya di tempat aman.

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
