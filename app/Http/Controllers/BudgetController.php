<?php

namespace App\Http\Controllers;

use App\Http\Requests\BudgetRequest;
use App\Models\Budget;
use Illuminate\Http\RedirectResponse;

class BudgetController extends Controller
{
    public function store(BudgetRequest $request): RedirectResponse
    {
        $period = $request->validated('period');

        foreach ($request->validated('budgets') as $category => $amount) {
            $amount = (int) $amount;

            if ($amount > 0) {
                Budget::updateOrCreate(['category' => $category, 'period' => $period], ['amount' => $amount]);
            } else {
                Budget::where(['category' => $category, 'period' => $period])->delete();
            }
        }

        return redirect()->route('expenses.index', ['period' => $period])->with('status', 'Anggaran bulanan disimpan.');
    }
}
