<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function __invoke(Request $request, ReportService $reports)
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $from = isset($validated['from']) ? Carbon::parse($validated['from']) : now()->startOfMonth();
        $to = isset($validated['to']) ? Carbon::parse($validated['to']) : now();

        // Batasi grafik harian maksimal 62 hari agar ringan.
        $seriesFrom = $to->diffInDays($from, true) > 61 ? $to->copy()->subDays(61) : $from;

        return view('dashboard', [
            'from' => $from,
            'to' => $to,
            'summary' => $reports->summary($from, $to),
            'series' => $reports->dailySeries($seriesFrom, $to),
            'topProducts' => $reports->topProducts($from, $to),
            'budgets' => $reports->budgetUsage($to->format('Y-m')),
            'budgetPeriod' => $to->format('Y-m'),
        ]);
    }
}
