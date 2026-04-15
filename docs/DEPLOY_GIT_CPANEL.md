# Deploy Laravel ke cPanel via Fitur Git cPanel

Panduan lengkap untuk deploy project Laravel ke hosting cPanel menggunakan fitur **Git Version Control** bawaan cPanel.  
Tidak menggunakan GitHub Actions — semua dilakukan langsung dari cPanel.

---

## Daftar Isi

1. [Prasyarat](#1-prasyarat)
2. [Persiapkan Repository GitHub](#2-persiapkan-repository-github)
3. [Setup Git Version Control di cPanel](#3-setup-git-version-control-di-cpanel)
4. [Konfigurasi .cpanel.yml](#4-konfigurasi-cpanelyml)
5. [Deploy Pertama Kali](#5-deploy-pertama-kali)
6. [Setup Laravel di Server](#6-setup-laravel-di-server)
7. [Update & Deploy Berikutnya](#7-update--deploy-berikutnya)
8. [Troubleshooting](#8-troubleshooting)
9. [Tips & Best Practices](#9-tips--best-practices)

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
   | **Repository Path**      | `/home/USERNAME/repositories/black-order`               |
   | **Repository Name**      | `black-order`                                          |

   > **PENTING tentang Repository Path:**
   >
   > - Ganti `USERNAME` dengan username cPanel kamu (bisa dilihat di sidebar kiri cPanel)
   > - **JANGAN** clone langsung ke `public_html`! Clone ke folder terpisah
   > - Contoh path: `/home/username/repositories/black-order`
   > - Nanti kita akan copy/deploy file-file yang dibutuhkan ke `public_html` menggunakan `.cpanel.yml`

4. Klik **Create**

5. Tunggu hingga proses clone selesai (progress bar akan muncul)

### Verifikasi:

Setelah berhasil, repository akan muncul di daftar **Git Version Control** dengan informasi:
- Repository name
- Repository path
- Branch yang aktif (seharusnya `main`)
- Last updated

---

## 4) Konfigurasi .cpanel.yml

File `.cpanel.yml` mengontrol apa yang terjadi setiap kali kamu melakukan **deploy** dari Git cPanel.  
File ini **harus ada di root project** (sejajar dengan `composer.json`).

### Buat file `.cpanel.yml` di komputer lokal:

```yaml
---
deployment:
  tasks:
    # 1) Copy semua file project ke public_html (kecuali folder public)
    - export DEPLOYPATH=/home/USERNAME/public_html
    - export REPOPATH=/home/USERNAME/repositories/black-order

    # 2) Sync file project (exclude folder public Laravel)
    - /bin/rsync -a --delete --exclude='.git'
        --exclude='node_modules'
        --exclude='.env'
        --exclude='storage/logs/*.log'
        --exclude='storage/framework/cache/data/*'
        --exclude='storage/framework/sessions/*'
        --exclude='storage/framework/views/*'
        $REPOPATH/ $DEPLOYPATH/

    # 3) Install dependencies composer
    - cd $DEPLOYPATH && /usr/local/bin/composer install --no-dev --optimize-autoloader --no-interaction 2>&1

    # 4) Jalankan migrasi database
    - cd $DEPLOYPATH && /usr/local/bin/php artisan migrate --force 2>&1

    # 5) Clear & rebuild cache Laravel
    - cd $DEPLOYPATH && /usr/local/bin/php artisan optimize:clear 2>&1
    - cd $DEPLOYPATH && /usr/local/bin/php artisan config:cache 2>&1
    - cd $DEPLOYPATH && /usr/local/bin/php artisan route:cache 2>&1
    - cd $DEPLOYPATH && /usr/local/bin/php artisan view:cache 2>&1
```

> ### ⚠️ PENTING — Sesuaikan path berikut:
>
> | Variabel       | Ganti dengan                                      |
> | -------------- | ------------------------------------------------- |
> | `USERNAME`     | Username cPanel kamu                               |
> | `DEPLOYPATH`   | Folder tujuan deploy (biasanya `/home/user/public_html` atau subdomain) |
> | `REPOPATH`     | Path repository yang kamu set di langkah 3         |
>
> ### Cek path `composer` dan `php`:
>
> Login ke **Terminal cPanel** lalu jalankan:
> ```bash
> which composer
> which php
> ```
> Jika hasilnya berbeda, sesuaikan path di `.cpanel.yml`.
> Contoh umum:
> - `/usr/local/bin/composer` atau `/usr/bin/composer` atau `~/bin/composer`
> - `/usr/local/bin/php` atau `/usr/bin/php` atau `/usr/local/bin/ea-php82`

### Commit dan push `.cpanel.yml`:

```bash
git add .cpanel.yml
git commit -m "tambah konfigurasi deploy cpanel"
git push origin main
```

---

## 5) Deploy Pertama Kali

### 5a) Pull update di cPanel

1. Buka **Git™ Version Control** di cPanel
2. Klik **Manage** pada repository `black-order`
3. Klik tab **Pull or Deploy**
4. Klik tombol **Update from Remote** (untuk pull perubahan terbaru dari GitHub)
5. Setelah update selesai, klik tombol **Deploy HEAD Commit**

> Deploy akan menjalankan semua task yang ada di `.cpanel.yml` secara berurutan.
> Kamu bisa melihat log output di halaman yang sama.

### 5b) Verifikasi deploy berhasil

Cek log deploy di halaman **Pull or Deploy**. Pastikan semua task berstatus sukses (tidak ada error merah).

---

## 6) Setup Laravel di Server

Setelah deploy pertama berhasil, ada beberapa hal yang perlu di-setup **sekali saja** melalui **Terminal cPanel**:

### 6a) Buat file `.env`

```bash
cd ~/public_html
cp .env.example .env
```

### 6b) Edit `.env` untuk production

```bash
nano .env
```

Sesuaikan nilai-nilai berikut:

```env
APP_NAME="Black Order"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://namadomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=nama_database
DB_USERNAME=user_database
DB_PASSWORD=password_database

# Sesuaikan setting lainnya (MAIL, QUEUE, dll)
```

Simpan: `Ctrl+O` → `Enter` → `Ctrl+X`

### 6c) Generate APP_KEY

```bash
cd ~/public_html
php artisan key:generate
```

### 6d) Buat symbolic link storage

```bash
cd ~/public_html
php artisan storage:link
```

### 6e) Set permission folder

```bash
cd ~/public_html
chmod -R 775 storage bootstrap/cache
```

### 6f) Jalankan migrasi + seeder (jika ada)

```bash
cd ~/public_html
php artisan migrate --force
php artisan db:seed --force   # opsional, jika ada seeder
```

### 6g) Build cache Laravel

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 7) Update & Deploy Berikutnya

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

> **Tips:** Kamu juga bisa melakukan pull & deploy via Terminal cPanel:
>
> ```bash
> cd ~/repositories/black-order
> git pull origin main
> ```
> 
> Kemudian deploy via cPanel UI, atau jalankan manual:
>
> ```bash
> # Berpindah ke folder deploy
> cd ~/public_html
>
> # Sync file
> rsync -a --delete --exclude='.git' --exclude='node_modules' --exclude='.env' \
>   ~/repositories/black-order/ ~/public_html/
>
> # Install deps & cache
> composer install --no-dev --optimize-autoloader --no-interaction
> php artisan migrate --force
> php artisan optimize:clear
> php artisan config:cache
> php artisan route:cache
> php artisan view:cache
> ```

---

## 8) Troubleshooting

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
mv composer.phar ~/bin/composer
chmod +x ~/bin/composer
```

Lalu ubah path di `.cpanel.yml`:
```yaml
- cd $DEPLOYPATH && ~/bin/composer install --no-dev --optimize-autoloader --no-interaction 2>&1
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
- cd $DEPLOYPATH && /usr/local/bin/ea-php82 artisan migrate --force 2>&1
```

### Halaman web error 500 setelah deploy
1. Cek file `.env` sudah benar
2. Cek `APP_KEY` sudah di-generate
3. Cek permission folder `storage` dan `bootstrap/cache`
4. Cek log error: `cat ~/public_html/storage/logs/laravel.log | tail -50`

### Halaman web 403 Forbidden
- Pastikan `public/index.php` ada di root `public_html`
- Jika menggunakan subdomain, pastikan **Document Root** mengarah ke folder yang benar

### File `.env` tertimpa saat deploy
- Pastikan `.env` sudah ada di daftar `--exclude` pada perintah `rsync` di `.cpanel.yml`
- `.env` sudah ada di `.gitignore`, jadi tidak akan ikut ter-push

### Perubahan tidak muncul setelah deploy
```bash
cd ~/public_html
php artisan optimize:clear
php artisan config:cache
```

Atau clear cache browser (Ctrl+Shift+R).

---

## 9) Tips & Best Practices

### ✅ Yang HARUS dilakukan:
- Selalu test di lokal sebelum push
- Backup database sebelum deploy yang ada migration besar
- Pastikan `.env` tidak ikut ter-push ke GitHub (sudah ada di `.gitignore`)
- Gunakan `--force` untuk `artisan migrate` di production
- Monitor log Laravel setelah deploy: `tail -f storage/logs/laravel.log`

### ❌ Yang JANGAN dilakukan:
- Jangan clone langsung ke `public_html` (gunakan folder terpisah + rsync)
- Jangan edit kode langsung di server — selalu edit di lokal, push, lalu deploy
- Jangan simpan kredensial (password, token) di kode — gunakan `.env`
- Jangan set `APP_DEBUG=true` di production

### 🔄 Struktur folder yang direkomendasikan:

```
/home/USERNAME/
├── public_html/             ← Folder web utama (hasil deploy)
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── public/              ← Document root web
│   │   ├── index.php
│   │   └── ...
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   ├── .env                 ← File environment (TIDAK dari git)
│   ├── artisan
│   └── composer.json
│
└── repositories/
    └── black-order/         ← Clone dari GitHub (bare repo cPanel)
        ├── app/
        ├── .cpanel.yml
        ├── composer.json
        └── ...
```

### 📌 Catatan tentang Document Root:

Secara default, cPanel mengarahkan domain ke `/home/USERNAME/public_html/`.  
Namun Laravel membutuhkan **Document Root** mengarah ke folder `public/` di dalam project.

**Solusi pilihan:**

**Opsi A — Ubah Document Root di cPanel (Rekomendasi):**
1. Buka cPanel → **Domains** atau **Subdomains**
2. Edit domain kamu
3. Ubah **Document Root** menjadi: `/home/USERNAME/public_html/public`

**Opsi B — Gunakan `.htaccess` di root `public_html`:**

Jika tidak bisa ubah Document Root, buat file `.htaccess` di `public_html/`:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
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
                              │  ~/repositories/  │
                              └────────┬─────────┘
                                       │
                                       │ Deploy (.cpanel.yml)
                                       │ rsync + composer + artisan
                                       ▼
                              ┌──────────────────┐
                              │   public_html/    │
                              │   (Live Website)  │
                              └──────────────────┘
```

**Selesai!** 🎉
