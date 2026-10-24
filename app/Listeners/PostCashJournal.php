<?php

namespace App\Listeners;

use App\Events\ExpenseRecorded;
use App\Events\PurchaseReceived;
use App\Events\SaleCompleted;
use App\Models\CashMutation;

/**
 * Menulis jurnal arus kas otomatis setiap ada penjualan, pembelian stok, atau beban.
 * Berjalan sinkron di dalam transaksi database pemicunya sehingga atomik.
 */
class PostCashJournal
{
    public function onSale(SaleCompleted $event): void
    {
        $sale = $event->sale;

        $sale->cashMutations()->create([
            'direction' => CashMutation::IN,
            'amount' => $sale->total,
            'method' => $sale->payment_method,
            'description' => "Penjualan {$sale->invoice_no}",
            'user_id' => $sale->user_id,
            'occurred_at' => $sale->sold_at,
        ]);
    }

    public function onPurchase(PurchaseReceived $event): void
    {
        $purchase = $event->purchase;

        $purchase->cashMutations()->create([
            'direction' => CashMutation::OUT,
            'amount' => $purchase->total,
            'method' => $purchase->payment_method,
            'description' => "Restock {$purchase->reference_no}",
            'user_id' => $purchase->user_id,
            'occurred_at' => $purchase->purchased_on->copy()->setTimeFrom(now()),
        ]);
    }

    public function onExpense(ExpenseRecorded $event): void
    {
        $expense = $event->expense;

        $expense->cashMutations()->create([
            'direction' => CashMutation::OUT,
            'amount' => $expense->amount,
            'method' => 'cash',
            'description' => "Beban: {$expense->category} - {$expense->description}",
            'user_id' => $expense->user_id,
            'occurred_at' => $expense->spent_on->copy()->setTimeFrom(now()),
        ]);
    }
}
