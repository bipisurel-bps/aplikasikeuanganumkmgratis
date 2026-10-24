<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseRequest;
use App\Models\Expense;
use App\Services\ExpenseService;
use App\Services\ReportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function index(Request $request, ReportService $reports)
    {
        $period = $request->query('period', now()->format('Y-m'));
        abort_unless(preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $period) === 1, 404);

        $start = Carbon::createFromFormat('Y-m', $period)->startOfMonth();

        $expenses = Expense::with('user:id,name')
            ->whereBetween('spent_on', [$start->toDateString(), $start->copy()->endOfMonth()->toDateString()])
            ->latest('spent_on')->latest('id')
            ->paginate(20)
            ->withQueryString();

        $budgets = $reports->budgetUsage($period);

        return view('expenses.index', [
            'expenses' => $expenses,
            'period' => $period,
            'periodLabel' => $start->translatedFormat('F Y'),
            'budgets' => $budgets,
            'totalBudget' => collect($budgets)->sum('budget'),
            'totalSpent' => collect($budgets)->sum('spent'),
            'categories' => config('umkm.expense_categories'),
        ]);
    }

    public function store(ExpenseRequest $request, ExpenseService $service): RedirectResponse
    {
        $service->record($request->user(), $request->validated(), $request->file('receipt'));

        return back()->with('status', 'Beban dicatat dan masuk ke buku kas.');
    }

    public function destroy(Expense $expense, ExpenseService $service): RedirectResponse
    {
        $service->delete($expense);

        return back()->with('status', 'Beban dihapus, mutasi kas dibatalkan.');
    }

    public function receipt(Expense $expense)
    {
        abort_unless($expense->receipt_path && Storage::disk('local')->exists($expense->receipt_path), 404);

        return response()->file(Storage::disk('local')->path($expense->receipt_path));
    }
}
