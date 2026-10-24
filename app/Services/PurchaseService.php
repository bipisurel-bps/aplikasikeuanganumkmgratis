<?php

namespace App\Services;

use App\Events\PurchaseReceived;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    /**
     * Catat restock dari supplier: tambah stok dan hitung ulang HPP rata-rata bergerak
     * (moving average) per produk.
     *
     * @param  array<int, array{product_id:int, qty:int, unit_cost:int}>  $items
     */
    public function receive(User $user, ?int $supplierId, array $items, string $method, string $date, ?string $note): Purchase
    {
        return DB::transaction(function () use ($user, $supplierId, $items, $method, $date, $note) {
            $total = collect($items)->sum(fn ($i) => (int) $i['qty'] * (int) $i['unit_cost']);

            $purchase = Purchase::create([
                'reference_no' => $this->nextReference($date),
                'supplier_id' => $supplierId,
                'user_id' => $user->id,
                'total' => $total,
                'payment_method' => $method,
                'purchased_on' => $date,
                'note' => $note,
            ]);

            foreach ($items as $row) {
                $qty = (int) $row['qty'];
                $unitCost = (int) $row['unit_cost'];

                /** @var Product $product */
                $product = Product::lockForUpdate()->findOrFail($row['product_id']);

                $purchase->items()->create([
                    'product_id' => $product->id,
                    'qty' => $qty,
                    'unit_cost' => $unitCost,
                    'line_total' => $qty * $unitCost,
                ]);

                $product->update([
                    'cost_price' => self::movingAverage($product->stock, $product->cost_price, $qty, $unitCost),
                    'stock' => $product->stock + $qty,
                ]);
            }

            PurchaseReceived::dispatch($purchase);

            return $purchase->load('items.product', 'supplier');
        });
    }

    /** HPP baru = (stok lama × HPP lama + qty baru × harga beli baru) / total stok. */
    public static function movingAverage(int $oldStock, int $oldCost, int $newQty, int $newCost): int
    {
        $totalQty = $oldStock + $newQty;
        if ($totalQty <= 0) {
            return $newCost;
        }

        return (int) round(($oldStock * $oldCost + $newQty * $newCost) / $totalQty);
    }

    private function nextReference(string $date): string
    {
        $prefix = 'PO-'.Carbon::parse($date)->format('ymd').'-';
        $count = Purchase::where('reference_no', 'like', $prefix.'%')->count() + 1;

        return $prefix.str_pad((string) $count, 3, '0', STR_PAD_LEFT);
    }
}
