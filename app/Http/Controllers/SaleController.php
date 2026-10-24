<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $sales = Sale::query()
            ->with('user:id,name')
            ->withCount('items')
            ->when(! $user->isOwner(), fn ($q) => $q->where('user_id', $user->id))
            ->when($request->filled('q'), fn ($q) => $q->where('invoice_no', 'like', '%'.$request->query('q').'%'))
            ->latest('sold_at')
            ->paginate(20)
            ->withQueryString();

        return view('sales.index', ['sales' => $sales]);
    }

    public function show(Request $request, Sale $sale)
    {
        abort_unless($request->user()->isOwner() || $sale->user_id === $request->user()->id, 403);

        return view('sales.show', ['receipt' => PosController::receipt($sale)]);
    }
}
