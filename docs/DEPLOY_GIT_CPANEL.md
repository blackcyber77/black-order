# Deploy Laravel ke cPanel via Fitur Git cPanel

Panduan lengkap untuk deploy project Laravel ke hosting cPanel menggunakan fitur **Git Version Control** bawaan cPanel.  
Tidak menggunakan GitHub Actions — semua dilakukan langsung dari cPanel.

---

## Daftar Isi

1. [Prasyarat](#1-prasyarat)
2. [Persiapkan Repository GitHub](#2-persiapkan-repository-github)
3. [Setup Git Version Control di cPanel](#3-setup-git-version-control-di-cpanel)
4. [Setup Subdomain](#4-setup-subdomain)
5. [Konfigurasi .cpanel.yml](#5-konfigurasi-cpanelyml)
6. [Deploy Pertama Kali](#6-deploy-pertama-kali)
7. [Setup Laravel di Server](#7-setup-laravel-di-server)
8. [Update & Deploy Berikutnya](#8-update--deploy-berikutnya)
9. [Troubleshooting](#9-troubleshooting)
10. [Tips & Best Practices](#10-tips--best-practices)

---

## 1) Prasyarat

Sebelum mulai, pastikan kamu punya:

- ✅ Akun hosting cPanel aktif (pastikan fitur **Git Version Control** tersedia)
- ✅ Repository GitHub yang sudah berisi project Laravel (`blackcyber77/black-order`)
- ✅ Akses ke **Terminal cPanel** atau **SSH** ke server
- ✅ PHP versi 8.1+ terinstall di server
- ✅ Composer terinstall di server (cek via Terminal: `composer --version`)
- ✅ Database MySQL sudah dibuat di cPanel

> **Catatan:** Jika hosting kamu tidak punya Composer, lihat bagian [Troubleshooting](#composer-tidak-tersedia-di-server) di bawah.

---

## 2) Persiapkan Repository GitHub

### 2a) Pastikan repo sudah ter-push

```bash
# Di komputer lokal
cd /path/to/webmakanan
git add .
git commit -m "siap deploy ke cpanel"
git push origin main
```

### 2b) Catat Clone URL repository

Buka repository di GitHub, klik tombol **Code**, lalu copy URL HTTPS:

```
https://github.com/blackcyber77/black-order.git
```

> **Penting:** Jika repo **private**, kamu perlu membuat **Personal Access Token (PAT)** di GitHub:
>
> 1. Buka GitHub → `Settings` → `Developer settings` → `Personal access tokens` → `Tokens (classic)`
> 2. Klik **Generate new token**
> 3. Beri nama, misal: `cpanel-deploy`
> 4. Centang scope: `repo` (Full control of private repositories)
> 5. Klik **Generate token**
> 6. **Copy token** (hanya muncul sekali!)
>
> Clone URL untuk repo private:
> ```
> https://<TOKEN>@github.com/blackcyber77/black-order.git
> ```
> Ganti `<TOKEN>` dengan token yang sudah di-copy.

---

## 3) Setup Git Version Control di cPanel

### Langkah-langkah:

1. **Login ke cPanel** → cari menu **Git™ Version Control**

2. Klik tombol **Create** (Buat repository baru)

3. Isi form:

   | Field                    | Nilai                                                  |
   | ------------------------ | ------------------------------------------------------ |
   | **Clone URL**            | `https://github.com/blackcyber77/black-order.git`      |
   | **Repository Path**      | `/home/USERNAME/scan`                                  |
   | **Repository Name**      | `black-order`                                          |

   > **Catatan:**
   > - Ganti `USERNAME` dengan username cPanel kamu (bisa dilihat di sidebar kiri cPanel)
   > - Path `/home/USERNAME/scan` adalah lokasi repo sekaligus folder web subdomain

4. Klik **Create**

5. Tunggu hingga proses clone selesai (progress bar akan muncul)

### Verifikasi:

Setelah berhasil, repository akan muncul di daftar **Git Version Control** dengan informasi:
- Repository name
- Repository path
- Branch yang aktif (seharusnya `main`)
- Last updated

---

## 4) Setup Subdomain

Karena project di-deploy di subdomain dengan document root `/scan`, pastikan pengaturan subdomain sudah benar:

1. Buka cPanel → **Domains** atau **Subdomains**
2. Cari subdomain kamu
3. **Ubah Document Root** menjadi: `/home/USERNAME/scan/public`

   > **Kenapa harus `/scan/public`?**  
   > Laravel mengharuskan web server mengarah ke folder `public/` di dalam project.  
   > Jika document root hanya `/scan`, maka file-file sensitif (`.env`, `config/`, dll) bisa diakses publik.

**Alternatif — jika tidak bisa ubah Document Root:**

Buat file `.htaccess` di root folder `/scan/`:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

---

## 5) Konfigurasi .cpanel.yml

File `.cpanel.yml` mengontrol apa yang terjadi setiap kali kamu melakukan **deploy** dari Git cPanel.  
File ini **harus ada di root project** (sejajar dengan `composer.json`).

Karena repo Git dan folder web adalah folder yang sama (`/scan`), konfigurasi menjadi sederhana — **tidak perlu rsync**:

```yaml
---
deployment:
  tasks:
    # Deploy Laravel — repo & web di folder yang sama (/scan)
    - export PROJECTPATH=/home/USERNAME/scan

    # Install composer dependencies (production only)
    - cd $PROJECTPATH && /usr/local/bin/composer install --no-dev --optimize-autoloader --no-interaction 2>&1

    # Jalankan database migrations
    - cd $PROJECTPATH && /usr/local/bin/php artisan migrate --force 2>&1

    # Clear & rebuild cache Laravel
    - cd $PROJECTPATH && /usr/local/bin/php artisan optimize:clear 2>&1
    - cd $PROJECTPATH && /usr/local/bin/php artisan config:cache 2>&1
    - cd $PROJECTPATH && /usr/local/bin/php artisan route:cache 2>&1
    - cd $PROJECTPATH && /usr/local/bin/php artisan view:cache 2>&1
```

> ### ⚠️ SESUAIKAN:
>
> - Ganti `USERNAME` dengan username cPanel kamu
> - Cek path `composer` dan `php` di server:
>   ```bash
>   which composer
>   which php
>   ```
>   Contoh umum:
>   - `/usr/local/bin/composer` atau `/usr/bin/composer` atau `~/bin/composer`
>   - `/usr/local/bin/php` atau `/usr/bin/php` atau `/usr/local/bin/ea-php82`

### Commit dan push `.cpanel.yml`:

```bash
git add .cpanel.yml
git commit -m "update konfigurasi deploy cpanel"
git push origin main
```

---

## 6) Deploy Pertama Kali

### 6a) Pull update di cPanel

1. Buka **Git™ Version Control** di cPanel
2. Klik **Manage** pada repository `black-order`
3. Klik tab **Pull or Deploy**
4. Klik tombol **Update from Remote** (untuk pull perubahan terbaru dari GitHub)
5. Setelah update selesai, klik tombol **Deploy HEAD Commit**

> Deploy akan menjalankan semua task yang ada di `.cpanel.yml` secara berurutan.
> Kamu bisa melihat log output di halaman yang sama.

### 6b) Verifikasi deploy berhasil

Cek log deploy di halaman **Pull or Deploy**. Pastikan semua task berstatus sukses (tidak ada error merah).

---

## 7) Setup Laravel di Server

Setelah deploy pertama berhasil, ada beberapa hal yang perlu di-setup **sekali saja** melalui **Terminal cPanel**:

### 7a) Buat file `.env`

```bash
cd ~/scan
cp .env.example .env
```

### 7b) Edit `.env` untuk production

```bash
nano .env
```

Sesuaikan nilai-nilai berikut:

```env
APP_NAME="Black Order"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://subdomain.namadomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=nama_database
DB_USERNAME=user_database
DB_PASSWORD=password_database

# Sesuaikan setting lainnya (MAIL, QUEUE, dll)
```

Simpan: `Ctrl+O` → `Enter` → `Ctrl+X`

### 7c) Generate APP_KEY

```bash
cd ~/scan
php artisan key:generate
```

### 7d) Buat symbolic link storage

```bash
cd ~/scan
php artisan storage:link
```

### 7e) Set permission folder

```bash
cd ~/scan
chmod -R 775 storage bootstrap/cache
```

### 7f) Jalankan migrasi + seeder (jika ada)

```bash
cd ~/scan
php artisan migrate --force
php artisan db:seed --force   # opsional, jika ada seeder
```

### 7g) Build cache Laravel

```bash
cd ~/scan
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 8) Update & Deploy Berikutnya

Setiap kali ada perubahan kode:

### Di komputer lokal:

```bash
# 1. Commit perubahan
git add .
git commit -m "deskripsi perubahan"

# 2. Push ke GitHub
git push origin main
```

### Di cPanel:

1. Buka **Git™ Version Control**
2. Klik **Manage** pada repository
3. Klik tab **Pull or Deploy**
4. Klik **Update from Remote**
5. Klik **Deploy HEAD Commit**

> **Tips:** Kamu juga bisa pull & deploy via Terminal cPanel:
>
> ```bash
> cd ~/scan
> git pull origin main
>
> # Lalu jalankan manual:
> composer install --no-dev --optimize-autoloader --no-interaction
> php artisan migrate --force
> php artisan optimize:clear
> php artisan config:cache
> php artisan route:cache
> php artisan view:cache
> ```

---

## 9) Troubleshooting

### Deploy gagal: "repository path already exists"
- Hapus folder repository yang sudah ada via **File Manager** cPanel
- Buat ulang repository di **Git Version Control**

### Deploy gagal: "Authentication failed"
- Repository GitHub kemungkinan private
- Gunakan URL dengan Personal Access Token:
  ```
  https://<TOKEN>@github.com/blackcyber77/black-order.git
  ```

### Composer tidak tersedia di server
Jika perintah `composer` tidak ditemukan, install manual:

```bash
cd ~
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"
mkdir -p ~/bin
mv composer.phar ~/bin/composer
chmod +x ~/bin/composer
```

Lalu ubah path di `.cpanel.yml`:
```yaml
- cd $PROJECTPATH && ~/bin/composer install --no-dev --optimize-autoloader --no-interaction 2>&1
```

### Error "PHP artisan" command not found
Cek versi PHP yang tersedia:

```bash
ls /usr/local/bin/ea-php*
# atau
ls /opt/cpanel/ea-php*/root/usr/bin/php
```

Gunakan path lengkap, contoh:
```yaml
- cd $PROJECTPATH && /usr/local/bin/ea-php82 artisan migrate --force 2>&1
```

### Halaman web error 500 setelah deploy
1. Cek file `.env` sudah benar
2. Cek `APP_KEY` sudah di-generate
3. Cek permission folder `storage` dan `bootstrap/cache`
4. Cek log error: `cat ~/scan/storage/logs/laravel.log | tail -50`

### Halaman web 403 Forbidden
- Pastikan Document Root subdomain mengarah ke `/home/USERNAME/scan/public`
- Pastikan `public/index.php` ada

### File `.env` hilang setelah deploy
- `.env` ada di `.gitignore` jadi tidak ikut ter-push/pull — ini aman
- Jika hilang, kemungkinan terhapus manual. Buat ulang dari `.env.example`

### Perubahan tidak muncul setelah deploy
```bash
cd ~/scan
php artisan optimize:clear
php artisan config:cache
```

Atau clear cache browser (Ctrl+Shift+R).

---

## 10) Tips & Best Practices

### ✅ Yang HARUS dilakukan:
- Selalu test di lokal sebelum push
- Backup database sebelum deploy yang ada migration besar
- Pastikan `.env` tidak ikut ter-push ke GitHub (sudah ada di `.gitignore`)
- Gunakan `--force` untuk `artisan migrate` di production
- Monitor log Laravel setelah deploy: `tail -f ~/scan/storage/logs/laravel.log`

### ❌ Yang JANGAN dilakukan:
- Jangan edit kode langsung di server — selalu edit di lokal, push, lalu deploy
- Jangan simpan kredensial (password, token) di kode — gunakan `.env`
- Jangan set `APP_DEBUG=true` di production
- Jangan biarkan Document Root mengarah ke `/scan` saja (harus `/scan/public`)

### 🔄 Struktur folder di server:

```
/home/USERNAME/
└── scan/                    ← Repo Git + folder web (subdomain)
    ├── app/
    ├── bootstrap/
    ├── config/
    ├── database/
    ├── public/              ← Document Root subdomain (harus ke sini!)
    │   ├── index.php
    │   └── ...
    ├── resources/
    ├── routes/
    ├── storage/
    ├── vendor/              ← Diinstall oleh composer (tidak dari git)
    ├── .cpanel.yml          ← Konfigurasi deploy
    ├── .env                 ← File environment (TIDAK dari git)
    ├── artisan
    └── composer.json
```

---

## Ringkasan Alur Deploy

```
┌─────────────┐     git push      ┌──────────┐
│  Lokal/IDE  │ ─────────────────► │  GitHub  │
└─────────────┘                    └────┬─────┘
                                        │
                                        │ Pull (Manual dari cPanel)
                                        ▼
                              ┌──────────────────┐
                              │  cPanel Git Repo  │
                              │     ~/scan/       │
                              │                   │
                              │  composer install  │
                              │  artisan migrate   │
                              │  artisan cache     │
                              │                   │
                              │  = Live Website   │
                              └──────────────────┘
```

**Selesai!** 🎉
