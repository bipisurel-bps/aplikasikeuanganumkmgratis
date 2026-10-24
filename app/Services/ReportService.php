<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\CashMutation;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /** Ringkasan laba rugi riil: Laba Bersih = Omzet - HPP - Beban Operasional. */
    public function summary(Carbon $from, Carbon $to): array
    {
        $from = $from->copy()->startOfDay();
        $to = $to->copy()->endOfDay();

        $omzet = (int) Sale::whereBetween('sold_at', [$from, $to])->sum('total');
        $hpp = (int) Sale::whereBetween('sold_at', [$from, $to])->sum('cogs');
        $beban = (int) Expense::whereBetween('spent_on', [$from->toDateString(), $to->toDateString()])->sum('amount');

        $cashIn = (int) CashMutation::where('direction', 'in')->whereBetween('occurred_at', [$from, $to])->sum('amount');
        $cashOut = (int) CashMutation::where('direction', 'out')->whereBetween('occurred_at', [$from, $to])->sum('amount');

        return [
            'omzet' => $omzet,
            'hpp' => $hpp,
            'laba_kotor' => $omzet - $hpp,
            'beban' => $beban,
            'laba_bersih' => $omzet - $hpp - $beban,
            'transaksi' => Sale::whereBetween('sold_at', [$from, $to])->count(),
            'cash_in' => $cashIn,
            'cash_out' => $cashOut,
            'saldo_kas' => $this->cashBalance(),
            'low_stock' => Product::active()->lowStock()->count(),
        ];
    }

    public function cashBalance(): int
    {
        $in = (int) CashMutation::where('direction', 'in')->sum('amount');
        $out = (int) CashMutation::where('direction', 'out')->sum('amount');

        return $in - $out;
    }

    /** Omzet & laba kotor per hari untuk grafik batang. */
    public function dailySeries(Carbon $from, Carbon $to): array
    {
        $rows = Sale::query()
            ->whereBetween('sold_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->select(DB::raw('date(sold_at) as day'), DB::raw('sum(total) as omzet'), DB::raw('sum(cogs) as hpp'))
            ->groupBy('day')
            ->get()
            ->keyBy('day');

        $series = [];
        for ($d = $from->copy()->startOfDay(); $d->lte($to); $d->addDay()) {
            $key = $d->toDateString();
            $row = $rows->get($key);
            $series[] = [
                'date' => $key,
                'label' => $d->format('d/m'),
                'omzet' => (int) ($row->omzet ?? 0),
                'laba_kotor' => (int) (($row->omzet ?? 0) - ($row->hpp ?? 0)),
            ];
        }

        return $series;
    }

    public function topProducts(Carbon $from, Carbon $to, int $limit = 5)
    {
        return SaleItem::query()
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->whereBetween('sales.sold_at', [$from->copy()->startOfDay(), $to->copy()->endOfDay()])
            ->groupBy('sale_items.product_name')
            ->select(
                'sale_items.product_name as name',
                DB::raw('sum(sale_items.qty) as qty'),
                DB::raw('sum(sale_items.line_total) as revenue'),
                DB::raw('sum(sale_items.line_total - sale_items.unit_cost * sale_items.qty) as profit'),
            )
            ->orderByDesc('qty')
            ->limit($limit)
            ->get();
    }

    /** Pemakaian anggaran per kategori untuk periode (YYYY-MM). */
    public function budgetUsage(string $period): array
    {
        $start = Carbon::createFromFormat('Y-m', $period)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $budgets = Budget::where('period', $period)->pluck('amount', 'category');
        $spent = Expense::query()
            ->whereBetween('spent_on', [$start->toDateString(), $end->toDateString()])
            ->groupBy('category')
            ->select('category', DB::raw('sum(amount) as total'))
            ->pluck('total', 'category');

        $rows = [];
        foreach (config('umkm.expense_categories') as $category) {
            $budget = (int) ($budgets[$category] ?? 0);
            $used = (int) ($spent[$category] ?? 0);
            $pct = $budget > 0 ? (int) round($used / $budget * 100) : null;

            $rows[] = [
                'category' => $category,
                'budget' => $budget,
                'spent' => $used,
                'remaining' => $budget - $used,
                'percent' => $pct,
                'status' => match (true) {
                    $pct === null => 'none',
                    $pct >= 100 => 'over',
                    $pct >= 80 => 'warn',
                    default => 'ok',
                },
            ];
        }

        return $rows;
    }
}
