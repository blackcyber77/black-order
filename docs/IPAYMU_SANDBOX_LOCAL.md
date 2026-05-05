# Setup iPaymu Sandbox di Lokal (Step by Step)

Dokumen ini untuk menjalankan pembayaran iPaymu **sandbox** pada project ini saat aplikasi berjalan di lokal.

## 1) Prasyarat

1. Akun iPaymu sandbox dengan:
   - VA (`IPAYMU_VA`)
   - API Key (`IPAYMU_API_KEY`)
2. Aplikasi Laravel bisa jalan di lokal.
3. Ngrok (atau tunnel publik lain) untuk menerima callback iPaymu.

## 2) Konfigurasi environment

Isi `.env`:

```env
PAYMENT_GATEWAY=ipaymu

IPAYMU_SANDBOX=true
IPAYMU_BASE_URL=https://sandbox.ipaymu.com/api/v2
IPAYMU_VA=ISI_DARI_IPAYMU
IPAYMU_API_KEY=ISI_DARI_IPAYMU
IPAYMU_PAYMENT_METHOD=qris
IPAYMU_PAYMENT_CHANNEL=qris
IPAYMU_EXPIRED=24
IPAYMU_EXPIRED_TYPE=hours
```

> Catatan: `APP_URL` harus URL publik saat testing callback (lihat langkah ngrok).

## 3) Jalankan aplikasi lokal

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

## 4) Buka tunnel publik untuk callback

Contoh pakai ngrok:

```bash
ngrok http 8000
```

Ambil URL HTTPS dari ngrok, misalnya:
`https://abcd-1234.ngrok-free.app`

Lalu set `APP_URL` di `.env`:

```env
APP_URL=https://abcd-1234.ngrok-free.app
```

Setelah ubah `.env`, jalankan:

```bash
php artisan optimize:clear
```

## 5) URL callback yang dipakai sistem

Sistem ini sudah menyiapkan endpoint:

- Callback: `POST /payment/callback`
- Notification: `POST /payment/notification`

Keduanya otomatis dikecualikan dari CSRF.

## 6) Alur yang sudah otomatis di sistem

1. Customer checkout.
2. Order tersimpan dengan status `pending`.
3. Sistem memanggil API iPaymu sandbox (`/payment`).
4. Customer otomatis redirect ke URL pembayaran iPaymu.
5. iPaymu callback ke sistem (`/payment/callback`).
6. Jika status sukses, order otomatis di-mark `paid`.

## 7) Cara test cepat

1. Buka menu customer dari URL ngrok.
2. Buat pesanan QR.
3. Pastikan setelah klik **Buat Pesanan**, browser redirect ke iPaymu sandbox.
4. Simulasikan pembayaran di sandbox.
5. Cek:
   - halaman admin pesanan,
   - status `payment_status`,
   - `paid_at` terisi.

## 8) Endpoint penting

- Buat/retry payment: `GET /payment/create/{order}`
- Cek status order: `GET /payment/{order}/status`

## 9) Troubleshooting umum

1. **Gagal redirect iPaymu**  
   Cek `IPAYMU_VA`, `IPAYMU_API_KEY`, dan koneksi internet server lokal.

2. **Callback tidak masuk**  
   Biasanya karena `APP_URL` masih localhost. Gunakan URL ngrok HTTPS.

3. **Status tidak berubah**  
   Cek log:
   - `storage/logs/laravel.log`
   - cari keyword `iPaymu callback`.

4. **419/CSRF saat callback**  
   Sudah di-handle pada `bootstrap/app.php`. Pastikan deploy kode terbaru.
