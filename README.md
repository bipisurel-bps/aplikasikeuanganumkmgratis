# Aplikasi Keuangan UMKM — Sistem Kasir (POS) & Manajemen Keuangan Terpadu

[![Laravel 12](https://img.shields.io/badge/Laravel-12.x-red.svg)](https://laravel.com)
[![Tailwind CSS](https://img.shields.io/badge/TailwindCSS-v4-38bdf8.svg)](https://tailwindcss.com)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-v3-77c1d8.svg)](https://alpinejs.dev)
[![License: MIT](https://img.shields.io/badge/License-MIT-amber.svg)](LICENSE)

Aplikasi web kasir (*Point of Sales*) dan manajemen keuangan terpadu untuk usaha mikro, kecil, dan menengah (UMKM). Dirancang mandiri dari nol (*clean-room*) tanpa dependensi berbayar atau langganan bulanan.

Aplikasi ini tidak hanya mencatat transaksi di meja kasir, melainkan otomatis menghubungkannya dengan **kalkulasi HPP (*moving average*)**, **jurnal arus kas atomik**, **pencatatan beban operasional dengan bukti nota**, dan **laporan laba bersih riil**.

---

## 🌟 Fitur Utama

1. **Layar Kasir (POS) Responsif & Cepat**:
   - Pencarian instan dan pemindaian barcode/SKU produk (tekan `Enter` langsung masuk keranjang).
   - Tombol pecahan cepat & uang pas (Rp 5.000, Rp 10.000, Rp 50.000, Rp 100.000).
   - Multi-metode pembayaran: Tunai, QRIS, dan Transfer Bank.
   - Modal struk belanja bergaya kertas *thermal receipt* yang siap langsung dicetak (`window.print()`).

2. **Otomasi HPP (Moving Average) & Restock Supplier**:
   - Harga pokok penjualan (HPP) otomatis dihitung ulang setiap kali mencatat faktur pembelian restock:
     $$\text{HPP Baru} = \frac{(\text{Stok Lama} \times \text{HPP Lama}) + (\text{Qty Baru} \times \text{Harga Beli Baru})}{\text{Total Stok Baru}}$$
   - Peringatan stok menipis jika stok berada di bawah ambang batas minimum toko.
   - Manajemen katalog produk, kategori, dan direktori supplier pemasok.

3. **Beban Operasional (OPEX) & Plafon Anggaran**:
   - Pencatatan biaya sewa, gaji karyawan, listrik, pulsa, transportasi, dan kemasan.
   - Unggah dan arsip berkas bukti nota/kuitansi (JPG, PNG, PDF) tersimpan privat.
   - Pemantau sisa pagu anggaran bulanan per kategori dengan *progress bar* visual (aman, waspada, over-budget).

4. **Buku Kas Terpadu (Zero-Input Journal)**:
   - Setiap penjualan kasir dicatat sebagai uang masuk (Debit).
   - Setiap restock supplier dan pengeluaran beban dicatat sebagai uang keluar (Kredit).
   - Atomik: Pembatalan/penghapusan beban otomatis membatalkan mutasi kas terkait.

5. **Laporan Keuangan & Laba Bersih Riil**:
   - Formula Laba Bersih Riil:
     $$\text{Laba Bersih} = \text{Omzet Penjualan} - \text{HPP Terjual} - \text{Beban Operasional}$$
   - Grafik batang pergerakan omzet harian.
   - Peringkat 5 produk terlaris dengan kontribusi keuntungan tertinggi.

6. **Web Opener (Landing Page) & Demo 1-Klik**:
   - Halaman pembuka publik yang menjelaskan alur bisnis dan perbandingan dengan POS biasa.
   - Tombol sandbox 1-klik untuk langsung menguji mode **Pemilik (Owner)** atau mode **Kasir**.

---

## 🚀 Panduan Instalasi & Menjalankan

### Persyaratan Sistem:
- PHP 8.2 atau lebih baru
- Ekstensi PHP: `pdo_sqlite` (atau `pdo_mysql`), `mbstring`, `xml`, `fileinfo`, `ctype`, `tokenizer`, `curl`
- Composer

### Langkah Instalasi:

```bash
# 1. Klon repositori
git clone https://github.com/bipisurel-bps/aplikasikeuanganumkmgratis.git
cd aplikasikeuanganumkmgratis

# 2. Pasang dependensi PHP
composer install

# 3. Siapkan file konfigurasi lingkungan
cp .env.example .env
php artisan key:generate

# 4. Buat database SQLite dan jalankan migrasi beserta data demo
touch database/database.sqlite
php artisan migrate --seed

# 5. Jalankan server lokal
php artisan serve
```

Aset frontend (Tailwind CSS v4 & Alpine.js) sudah dikompilasi di direktori `public/build/`, sehingga aplikasi dapat langsung dijalankan tanpa perlu menginstal Node.js/npm.

Buka browser di: **`http://localhost:8000`** *(atau `http://127.0.0.1:8000`)*.

---

## 🔑 Akun Demo Bawaan

Database telah dilengkapi dengan data simulasi 14 hari dan 400+ transaksi penjualan:

| Peran Akun | Alamat Email | Kata Sandi | Akses Hak |
|---|---|---|---|
| **Pemilik (Owner)** | `owner@umkm.test` | `password` | Seluruh dashboard keuangan, laba rugi, kontrol anggaran, buku kas, HPP, produk & restock |
| **Kasir Toko** | `kasir@umkm.test` | `password` | Layar POS kasir, buka/tutup shift laci kas, riwayat struk penjualan sendiri |

Untuk skenario pengujian langkah-demi-langkah, alur transaksi kasir, kalkulasi HPP, dan lembar evaluasi, silakan baca **[Panduan Lengkap Uji Coba (PANDUAN_UJICOBA.md)](PANDUAN_UJICOBA.md)**.

---

## 🏛️ Arsitektur & Struktur Direktori

```text
app/
├── Events/              # Event transaksi: SaleCompleted, PurchaseReceived, ExpenseRecorded
├── Http/
│   ├── Controllers/    # Auth, Dashboard, POS, Products, Purchases, Expenses, Drawers, Cash
│   ├── Middleware/     # EnsureRole (Owner vs Kasir)
│   └── Requests/       # Form requests dengan validasi ketat
├── Listeners/          # PostCashJournal (otomasi jurnal arus kas ke cash_mutations)
├── Models/             # Eloquent Models & Relasi (Sale, Purchase, Expense, CashDrawer, dll.)
└── Services/           # Business logic: CheckoutService, PurchaseService (HPP), ExpenseService, ReportService
```

---

## 🧪 Pengujian Otomatis (Unit & Feature Tests)

Seluruh alur bisnis, pemotongan stok, kalkulasi HPP, dan otorisasi peran telah diuji:

```bash
php artisan test
```

---

## 📄 Lisensi & Atribusi

Proyek ini dirilis sebagai perangkat lunak sumber terbuka di bawah lisensi **[MIT License](LICENSE)**.

Dikembangkan oleh **bipi (jaddadeveloper)** untuk kemanfaatan bersama seluas-luasnya bagi para pelaku UMKM Indonesia.
