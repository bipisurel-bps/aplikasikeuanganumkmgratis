<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\CashMutation;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Services\CashDrawerService;
use App\Services\CheckoutService;
use App\Services\ExpenseService;
use App\Services\PurchaseService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

/**
 * Data dummy warung/toko kelontong. Seluruh transaksi dibuat lewat service yang sama
 * dengan aplikasi, sehingga stok, HPP rata-rata, dan buku kas konsisten.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        mt_srand(2025); // data demo deterministik

        $owner = User::create([
            'name' => 'Bu Rina (Owner)', 'email' => 'owner@umkm.test',
            'password' => 'password', 'role' => User::ROLE_OWNER,
        ]);
        $kasir = User::create([
            'name' => 'Dimas (Kasir)', 'email' => 'kasir@umkm.test',
            'password' => 'password', 'role' => User::ROLE_KASIR,
        ]);

        $cats = collect(['Minuman', 'Makanan Ringan', 'Sembako', 'Kebutuhan Rumah', 'Rokok & Lainnya'])
            ->mapWithKeys(fn ($n) => [$n => Category::create(['name' => $n])->id]);

        $suppliers = [
            Supplier::create(['name' => 'CV Sumber Pangan', 'phone' => '0812-1111-2222', 'address' => 'Pasar Induk Caringin']),
            Supplier::create(['name' => 'UD Berkah Grosir', 'phone' => '0813-3333-4444', 'address' => 'Jl. Kopo No. 88']),
            Supplier::create(['name' => 'PT Segar Minuman', 'phone' => '021-555-0101', 'address' => 'Kawasan Industri Jababeka']),
        ];

        // [sku, nama, kategori, harga beli awal, harga jual, min stok, supplier idx]
        $catalog = [
            ['MNM-001', 'Air Mineral 600ml', 'Minuman', 2200, 3500, 24, 2],
            ['MNM-002', 'Teh Botol 450ml', 'Minuman', 3800, 5500, 24, 2],
            ['MNM-003', 'Kopi Sachet Hitam', 'Minuman', 1100, 2000, 30, 1],
            ['MNM-004', 'Susu UHT Cokelat 200ml', 'Minuman', 4600, 6500, 12, 2],
            ['MNM-005', 'Minuman Isotonik 500ml', 'Minuman', 5200, 7500, 12, 2],
            ['SNK-001', 'Keripik Singkong 150g', 'Makanan Ringan', 6500, 10000, 10, 1],
            ['SNK-002', 'Biskuit Kelapa 200g', 'Makanan Ringan', 7800, 11500, 10, 1],
            ['SNK-003', 'Wafer Cokelat', 'Makanan Ringan', 1700, 3000, 20, 1],
            ['SNK-004', 'Kacang Atom 180g', 'Makanan Ringan', 8200, 12500, 8, 0],
            ['SMB-001', 'Beras Premium 5kg', 'Sembako', 66000, 76000, 6, 0],
            ['SMB-002', 'Minyak Goreng 1L', 'Sembako', 15500, 18500, 10, 0],
            ['SMB-003', 'Gula Pasir 1kg', 'Sembako', 14200, 17000, 10, 0],
            ['SMB-004', 'Telur Ayam 1kg', 'Sembako', 26000, 30000, 8, 0],
            ['SMB-005', 'Mi Instan Goreng', 'Sembako', 2700, 3800, 40, 1],
            ['SMB-006', 'Tepung Terigu 1kg', 'Sembako', 10500, 13000, 8, 0],
            ['RMH-001', 'Sabun Cuci Piring 400ml', 'Kebutuhan Rumah', 8800, 12500, 8, 1],
            ['RMH-002', 'Deterjen Bubuk 800g', 'Kebutuhan Rumah', 17500, 23000, 6, 1],
            ['RMH-003', 'Sabun Mandi Batang', 'Kebutuhan Rumah', 3000, 4500, 15, 1],
            ['RMH-004', 'Tisu Gulung', 'Kebutuhan Rumah', 5500, 8000, 10, 1],
            ['RKK-001', 'Korek Api Gas', 'Rokok & Lainnya', 2800, 4500, 10, 1],
        ];

        $products = [];
        foreach ($catalog as [$sku, $name, $cat, $cost, $sell, $min]) {
            $products[$sku] = Product::create([
                'category_id' => $cats[$cat], 'sku' => $sku, 'name' => $name,
                'stock' => 0, 'min_stock' => $min, 'cost_price' => 0, 'sell_price' => $sell,
            ]);
        }

        $purchases = app(PurchaseService::class);
        $checkout = app(CheckoutService::class);
        $drawers = app(CashDrawerService::class);
        $expenses = app(ExpenseService::class);

        $days = 14;
        $base = Carbon::today();
        $start = $base->copy()->subDays($days + 1);

        // Modal awal pemilik
        Carbon::setTestNow($start->copy()->setHour(8));
        CashMutation::create([
            'direction' => 'in', 'amount' => 25_000_000, 'method' => 'cash',
            'description' => 'Modal awal pemilik', 'user_id' => $owner->id, 'occurred_at' => now(),
        ]);

        // Stok awal: dikelompokkan per supplier
        foreach ($suppliers as $idx => $supplier) {
            $items = [];
            foreach ($catalog as [$sku, , , $cost, , $min, $sup]) {
                if ($sup !== $idx) {
                    continue;
                }
                $items[] = ['product_id' => $products[$sku]->id, 'qty' => $min * 14 + 40, 'unit_cost' => $cost];
            }
            $purchases->receive($owner, $supplier->id, $items, 'cash', $start->toDateString(), 'Stok awal toko');
        }

        // Penjualan harian oleh kasir
        for ($d = $days; $d >= 0; $d--) {
            $day = $base->copy()->subDays($d);
            Carbon::setTestNow($day->copy()->setHour(8)->setMinute(0));
            $drawer = $drawers->open($kasir, 200_000);

            // Restock di tengah periode dengan harga beli naik sedikit -> HPP rata-rata bergerak
            if ($d === 7) {
                $items = [];
                foreach (['MNM-001', 'MNM-002', 'SMB-002', 'SMB-003', 'SMB-005'] as $sku) {
                    $items[] = [
                        'product_id' => $products[$sku]->id, 'qty' => 150,
                        'unit_cost' => (int) round($products[$sku]->fresh()->cost_price * 1.06),
                    ];
                }
                $purchases->receive($owner, $suppliers[2]->id, $items, 'transfer', $day->toDateString(), 'Restock mingguan');
            }

            $trx = mt_rand(30, 50);
            for ($t = 0; $t < $trx; $t++) {
                Carbon::setTestNow($day->copy()->setHour(8 + (int) ($t * 12 / $trx))->setMinute(mt_rand(0, 59)));

                $skus = array_rand($products, mt_rand(2, 5));
                $skus = (array) $skus;
                $lines = array_map(fn ($sku) => ['product_id' => $products[$sku]->id, 'qty' => mt_rand(1, 4)], $skus);

                $method = ['cash', 'cash', 'cash', 'qris', 'transfer'][mt_rand(0, 4)];
                $subtotal = collect($lines)->sum(fn ($l) => $products[array_search($l['product_id'], array_map(fn ($p) => $p->id, $products))]->sell_price * $l['qty']);
                $paid = (int) (ceil($subtotal / 5000) * 5000);

                try {
                    $checkout->checkout($kasir, $lines, 0, $paid, $method);
                } catch (ValidationException) {
                    // stok habis: lewati transaksi demo ini
                }
            }

            Carbon::setTestNow($day->copy()->setHour(21));
            if ($d > 0) {
                $drawers->close($drawer, $drawer->computeExpectedCash() - (mt_rand(0, 3) === 0 ? 2000 : 0), null);
            } else {
                $drawers->close($drawer, $drawer->computeExpectedCash(), 'Demo: shift tutup, silakan buka shift baru.');
            }
        }

        // Beban operasional bulan berjalan & bulan lalu
        Carbon::setTestNow();
        $thisMonth = now()->startOfMonth();
        $lastMonth = now()->subMonthNoOverflow()->startOfMonth();
        $today = now()->startOfDay();

        $expenseRows = [
            ['Sewa Tempat', 'Sewa kios bulanan', 2_500_000, $lastMonth->copy()->addDays(1)],
            ['Gaji Karyawan', 'Gaji kasir', 2_200_000, $lastMonth->copy()->addDays(26)],
            ['Listrik & Air', 'Token listrik', 450_000, $lastMonth->copy()->addDays(10)],
            ['Sewa Tempat', 'Sewa kios bulanan', 2_500_000, $thisMonth->copy()],
            ['Gaji Karyawan', 'Gaji kasir', 2_200_000, $thisMonth->copy()->min($today)],
            ['Listrik & Air', 'Token listrik', 480_000, $thisMonth->copy()->addDays(2)->min($today)],
            ['Internet & Pulsa', 'Paket internet toko', 350_000, $thisMonth->copy()->addDays(1)->min($today)],
            ['Kemasan & Perlengkapan', 'Kantong plastik & kertas struk', 180_000, $thisMonth->copy()->addDays(3)->min($today)],
            ['Transportasi', 'Ongkos angkut belanja grosir', 120_000, $thisMonth->copy()->addDays(4)->min($today)],
            ['Pemasaran', 'Cetak spanduk promo', 250_000, $thisMonth->copy()->addDays(5)->min($today)],
        ];

        foreach ($expenseRows as [$cat, $desc, $amount, $date]) {
            Carbon::setTestNow($date->copy()->setHour(10));
            $expenses->record($owner, [
                'category' => $cat, 'description' => $desc, 'amount' => $amount,
                'spent_on' => $date->toDateString(),
            ], null);
        }
        Carbon::setTestNow();

        // Anggaran bulanan
        foreach ([$lastMonth, $thisMonth] as $m) {
            foreach ([
                'Sewa Tempat' => 2_500_000, 'Gaji Karyawan' => 2_400_000, 'Listrik & Air' => 500_000,
                'Internet & Pulsa' => 400_000, 'Transportasi' => 150_000,
                'Kemasan & Perlengkapan' => 200_000, 'Pemasaran' => 200_000,
            ] as $cat => $amt) {
                Budget::create(['category' => $cat, 'period' => $m->format('Y-m'), 'amount' => $amt]);
            }
        }
    }
}
