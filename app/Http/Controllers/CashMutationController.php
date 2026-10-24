<?php

namespace App\Http\Controllers;

use App\Models\CashMutation;
use App\Services\ReportService;
use Illuminate\Http\Request;

class CashMutationController extends Controller
{
    public function index(Request $request, ReportService $reports)
    {
        $mutations = CashMutation::query()
            ->with('user:id,name')
            ->when(in_array($request->query('direction'), ['in', 'out'], true), fn ($q) => $q->where('direction', $request->query('direction')))
            ->latest('occurred_at')->latest('id')
            ->paginate(25)
            ->withQueryString();

        return view('cash.index', [
            'mutations' => $mutations,
            'balance' => $reports->cashBalance(),
        ]);
    }
}
