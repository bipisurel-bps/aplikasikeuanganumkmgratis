<?php

namespace App\Services;

use App\Events\SaleCompleted;
use App\Models\CashDrawer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    /**
     * Proses checkout: kunci produk, validasi stok, potong stok, hitung HPP,
     * simpan transaksi, lalu picu event agar tercatat ke buku kas.
     *
     * @param  array<int, array{product_id:int, qty:int}>  $items
     */
    public function checkout(User $cashier, array $items, int $discount, int $paid, string $method): Sale
    {
        $drawer = $cashier->openDrawer();
        if (! $drawer instanceof CashDrawer) {
            throw ValidationException::withMessages(['shift' => 'Buka shift kasir terlebih dahulu sebelum bertransaksi.']);
        }

        return DB::transaction(function () use ($cashier, $drawer, $items, $discount, $paid, $method) {
            // Gabungkan baris duplikat agar pengecekan stok akurat.
            $qtyByProduct = [];
            foreach ($items as $row) {
                $pid = (int) $row['product_id'];
                $qtyByProduct[$pid] = ($qtyByProduct[$pid] ?? 0) + (int) $row['qty'];
            }

            $products = Product::query()
                ->whereIn('id', array_keys($qtyByProduct))
                ->active()
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $lines = [];
            $subtotal = 0;
            $cogs = 0;

            foreach ($qtyByProduct as $pid => $qty) {
                $product = $products->get($pid);
                if (! $product) {
                    throw ValidationException::withMessages(['items' => 'Ada produk yang tidak tersedia lagi.']);
                }
                if ($product->stock < $qty) {
                    throw ValidationException::withMessages([
                        'items' => "Stok {$product->name} tidak cukup (tersisa {$product->stock}).",
                    ]);
                }

                $lineTotal = $product->sell_price * $qty;
                $subtotal += $lineTotal;
                $cogs += $product->cost_price * $qty;

                $lines[] = [
                    'product' => $product,
                    'qty' => $qty,
                    'line_total' => $lineTotal,
                ];
            }

            if ($discount > $subtotal) {
                throw ValidationException::withMessages(['discount' => 'Diskon tidak boleh melebihi subtotal.']);
            }

            $total = $subtotal - $discount;

            // Non-tunai selalu dibayar pas.
            if ($method !== 'cash') {
                $paid = $total;
            }
            if ($paid < $total) {
                throw ValidationException::withMessages(['paid' => 'Uang yang dibayarkan kurang dari total belanja.']);
            }

            $sale = Sale::create([
                'invoice_no' => $this->nextInvoiceNumber(),
                'user_id' => $cashier->id,
                'cash_drawer_id' => $drawer->id,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total' => $total,
                'cogs' => $cogs,
                'paid' => $paid,
                'change_amount' => $paid - $total,
                'payment_method' => $method,
                'sold_at' => now(),
            ]);

            foreach ($lines as $line) {
                /** @var Product $product */
                $product = $line['product'];

                $sale->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'qty' => $line['qty'],
                    'unit_price' => $product->sell_price,
                    'unit_cost' => $product->cost_price,
                    'line_total' => $line['line_total'],
                ]);

                $product->decrement('stock', $line['qty']);
            }

            SaleCompleted::dispatch($sale);

            return $sale->load('items', 'user');
        });
    }

    private function nextInvoiceNumber(): string
    {
        $prefix = 'INV-'.now()->format('ymd').'-';
        $count = Sale::where('invoice_no', 'like', $prefix.'%')->count() + 1;

        return $prefix.str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }
}
