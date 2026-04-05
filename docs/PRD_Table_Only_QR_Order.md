# Product Requirements Document (PRD)
## Proyek: Simplifikasi Lokasi ke Table-Only + QR Order Cashless

## 1. Ringkasan
Dokumen ini mendefinisikan perubahan produk untuk menyederhanakan alur pemesanan makanan berbasis QR dengan dua fokus utama:
1. Menghapus eksposur konsep **Tower** pada alur operasional utama dan mengganti menjadi **Table-only**.
2. Memastikan alur **QR Order** hanya menggunakan pembayaran **cashless QRIS** (tanpa opsi tunai).

Perubahan ditujukan untuk menurunkan friction saat pengguna memesan, mengurangi kesalahan input lokasi, dan menyederhanakan operasional kasir/admin.

## 2. Latar Belakang
### Masalah Saat Ini
- Pelanggan perlu konteks lokasi yang memuat `tower + table`, padahal pada praktik lapangan identifikasi cukup dengan nomor meja.
- Struktur UI dan flow menampilkan tower di banyak titik sehingga menambah kompleksitas pemahaman pengguna.
- Pada alur QR order, kebijakan produk menginginkan cashless saja, sehingga opsi/metafora tunai harus dipastikan tidak muncul di flow pelanggan.

### Peluang
- Alur lebih cepat: scan QR -> meja terdeteksi -> pesan.
- Error operasional menurun (lebih sedikit field lokasi).
- Konsistensi kebijakan pembayaran QR order.

## 3. Tujuan Produk
### Tujuan Utama
- Menjadikan **table_number** sebagai satu-satunya konteks lokasi yang ditampilkan pada flow utama pelanggan dan POS.
- Menjaga QR order tetap **QRIS-only**.

### Tujuan Bisnis
- Meningkatkan conversion dari menu ke checkout.
- Mengurangi order gagal akibat mismatch lokasi.
- Mengurangi beban training staf terkait pemetaan tower.

## 4. Non-Tujuan
- Tidak melakukan redesign total UI/branding.
- Tidak menghapus tabel database `towers` pada fase ini.
- Tidak melakukan integrasi payment gateway baru pada fase ini (tetap kompatibel untuk fase berikutnya).

## 5. Persona
1. Pelanggan QR
- Datang ke meja, scan QR, pesan cepat tanpa kebingungan lokasi.

2. Kasir/Admin
- Membuat order manual/POS dengan pemilihan meja yang sederhana.

3. Supervisor Operasional
- Memantau order dan status pembayaran secara konsisten.

## 6. Scope
### In-Scope
- Session lokasi pelanggan berbasis `table_number` saja.
- QR URL menghasilkan parameter `table` saja (`/menu?table=...`).
- Checkout customer menampilkan lokasi meja saja.
- POS menampilkan dan memilih meja tanpa tower.
- Tampilan order/admin menggunakan lokasi meja (atau walk-in/takeaway bila null).
- Copy UX QR order menegaskan QRIS-only.

### Out-of-Scope (fase ini)
- Drop kolom/relasi `tower_id` dari database.
- Migrasi data historis lama untuk menyamakan seluruh record lama.
- Integrasi Midtrans/Xendit full webhook flow.

## 7. User Flow (To-Be)
### 7.1 Customer QR Order
1. User scan QR meja.
2. Browser buka `/menu?table=<nomor_meja>`.
3. Sistem validasi meja aktif, simpan `table_number` di session.
4. User pilih menu -> add to cart.
5. Checkout menampilkan lokasi: `Meja X`.
6. Payment method default/only: `QRIS`.
7. Order dibuat dengan `payment_status = pending`, `status = pending`.
8. Admin verifikasi pembayaran dan lanjut proses status order.

### 7.2 POS Kasir
1. Kasir pilih item.
2. Kasir opsional pilih meja (tanpa tower).
3. Kasir pilih metode bayar (tunai/QRIS) sesuai kebijakan POS.
4. Order dibuat, status meja berubah `terisi` jika table dipilih.
5. Saat pelunasan/selesai, status meja kembali `kosong`.

## 8. Kebutuhan Fungsional
### FR-01 Lokasi Table-only
- Sistem harus menerima lokasi QR berbasis query param `table`.
- Sistem harus menyimpan session lokasi hanya `table_number`.
- Middleware `location.check` harus memvalidasi `table_number`.

### FR-02 QR URL
- Generator QR meja harus menghasilkan URL `/menu?table=<table_number>`.

### FR-03 Checkout Customer
- Form checkout customer tidak menampilkan input tower.
- Order customer tersimpan dengan `tower_id = null`.
- `delivery_fee` pada flow customer default `0` pada fase ini.

### FR-04 Pembayaran QR Order
- QR order hanya menerima `payment_method = qris`.
- UI checkout tidak menampilkan opsi tunai.

### FR-05 POS Table Selection
- POS menampilkan daftar meja aktif langsung (tanpa tower grouping).
- Saat submit POS, `table_number` opsional dan tervalidasi ke tabel meja.

### FR-06 Lokasi di Tampilan Admin
- Ringkasan lokasi order menampilkan `Meja <table_number>` jika ada.
- Jika tidak ada, tampil `Walk-In / Takeaway`.

## 9. Kebutuhan Non-Fungsional
### NFR-01 Konsistensi
- Terminologi UI harus konsisten menggunakan "Meja" untuk flow utama.

### NFR-02 Backward Compatibility
- URL lama dengan parameter tambahan tidak boleh menyebabkan fatal error.

### NFR-03 Maintainability
- Perubahan dilakukan tanpa memutus struktur model existing secara destruktif.

## 10. Data & Model
### Struktur Data yang Dipakai
- `orders.table_number` menjadi identifier lokasi utama pada flow.
- `orders.tower_id` tetap ada (nullable), diisi null untuk order baru flow ini.
- `dining_tables.table_number` digunakan sebagai referensi validasi lokasi.

### Konsekuensi
- Potensi duplikasi `table_number` antar tower lama perlu diselesaikan di fase normalisasi data berikutnya.

## 11. API/Endpoint Impact
### Endpoint yang Dipengaruhi
- `GET /menu?table=...`
- `POST /checkout`
- `POST /admin/pos`
- `GET /admin/tables/{table}/qr` (QR payload berubah)

### Kontrak Perilaku Baru
- `location.check` hanya mensyaratkan `table_number`.
- `orders.store` tidak lagi mewajibkan `tower_id`.

## 12. Metrik Keberhasilan (KPI)
1. Checkout conversion rate (menu -> order created).
2. Persentase order gagal validasi lokasi.
3. Rata-rata waktu dari scan QR ke order submitted.
4. Jumlah komplain terkait salah lokasi pengantaran.

## 13. Risiko & Mitigasi
1. Risiko: `table_number` tidak unik di data lama.
- Mitigasi: tambahkan kebijakan numbering unik global atau migrasi normalisasi pada fase berikutnya.

2. Risiko: dashboard/report lama masih mengasumsikan tower.
- Mitigasi: gunakan accessor lokasi terstandar (`full_location`) dan audit semua tampilan.

3. Risiko: kebingungan operasional transisi.
- Mitigasi: release note internal + panduan singkat staf.

## 14. Rencana Rollout
### Tahap 1 (Dev/Staging)
- Implementasi kode + QA regresi flow customer, POS, admin order.

### Tahap 2 (Pilot)
- Uji terbatas di sebagian meja/area.

### Tahap 3 (Full Rollout)
- Aktifkan penuh, monitor KPI 1-2 minggu pertama.

## 15. Test Plan (Minimum)
1. Scan QR valid (`/menu?table=...`) menyimpan session.
2. Add to cart tanpa table -> gagal dengan pesan lokasi.
3. Checkout customer sukses dengan `table_number` dan `payment_method=qris`.
4. Checkout customer reject selain qris.
5. POS create dengan meja valid -> meja `terisi`.
6. POS mark paid/complete -> meja `kosong`.
7. Halaman admin (dashboard, pos, detail order, receipt) tidak error saat `tower_id=null`.

## 16. Acceptance Criteria
- Semua halaman flow utama tidak lagi menampilkan tower sebagai konteks lokasi pelanggan.
- QR order hanya menyediakan QRIS pada UI dan validasi backend.
- Order baru dari flow customer tersimpan valid dengan `table_number` dan `tower_id=null`.
- POS tetap berfungsi dengan pemilihan meja langsung.
- Tidak ada error runtime pada halaman utama terkait null tower.

## 17. Future Enhancements
1. Normalisasi `table_number` agar unique global.
2. Penghapusan permanen domain model `tower` jika sudah tidak diperlukan bisnis.
3. Integrasi payment gateway otomatis + webhook idempotent.
4. SLA dashboard berbasis meja (occupancy rate, table turnaround time).

---
Dokumen ini menjadi acuan implementasi teknis, QA, dan evaluasi produk untuk fase simplifikasi lokasi table-only.
