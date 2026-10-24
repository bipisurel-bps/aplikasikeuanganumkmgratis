# UMKM Kasir — POS & Manajemen Keuangan

Aplikasi POS dan keuangan terpadu untuk UMKM. Dibuat dari nol dengan Laravel 12, Blade, Tailwind CSS 4, dan Alpine.js.

## Menjalankan

```bash
composer install
cp .env.example .env && php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
npm install && npm run build
php artisan serve
```

Buka http://127.0.0.1:8000 — akun demo (sandi `password`):

| Peran | Email |
|-------|-------|
| Owner | owner@umkm.test |
| Kasir | kasir@umkm.test |

## Fitur
- **POS kasir**: katalog cepat (cari/scan SKU), keranjang, diskon, kembalian, QRIS/transfer, struk cetak. Wajib membuka shift.
- **Shift kasir**: modal awal, hitung uang laci, selisih kas saat tutup shift.
- **Produk & stok**, kategori, supplier, **restock** dengan HPP rata-rata bergerak.
- **Beban operasional** + unggah nota (disimpan privat) + **anggaran bulanan** per kategori dengan bar sisa anggaran.
- **Buku kas**: jurnal otomatis dari penjualan, restock, dan beban.
- **Laporan**: Omzet, HPP, Beban, dan **Laba Bersih = Omzet − HPP − Beban**.

## Arsitektur
- `app/Services` — `CheckoutService` (potong stok + HPP), `PurchaseService`, `ExpenseService`, `CashDrawerService`, `ReportService`.
- `app/Events` + `app/Listeners/PostCashJournal` — pencatatan otomatis ke `cash_mutations`.
- Nominal disimpan sebagai integer rupiah. Peran: `owner` (semua) dan `kasir` (POS, shift, riwayat sendiri).

Tes: `php artisan test`
