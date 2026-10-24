<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Services\PurchaseService;
use Illuminate\Http\RedirectResponse;

class PurchaseController extends Controller
{
    public function index()
    {
        return view('purchases.index', [
            'purchases' => Purchase::with('supplier:id,name', 'user:id,name')
                ->withCount('items')
                ->latest('purchased_on')->latest('id')
                ->paginate(15),
        ]);
    }

    public function create()
    {
        return view('purchases.create', [
            'suppliers' => Supplier::orderBy('name')->get(['id', 'name']),
            'products' => Product::active()->orderBy('name')
                ->get(['id', 'sku', 'name', 'stock', 'cost_price'])
                ->map(fn ($p) => [
                    'id' => $p->id, 'sku' => $p->sku, 'name' => $p->name,
                    'stock' => $p->stock, 'cost' => $p->cost_price,
                ]),
            'methods' => config('umkm.payment_methods'),
        ]);
    }

    public function store(PurchaseRequest $request, PurchaseService $service): RedirectResponse
    {
        $data = $request->validated();

        $purchase = $service->receive(
            $request->user(),
            $data['supplier_id'] ?? null,
            $data['items'],
            $data['payment_method'],
            $data['purchased_on'],
            $data['note'] ?? null,
        );

        return redirect()->route('purchases.index')
            ->with('status', "Restock {$purchase->reference_no} tersimpan, stok & HPP diperbarui.");
    }

    public function show(Purchase $purchase)
    {
        return view('purchases.show', ['purchase' => $purchase->load('items.product', 'supplier', 'user')]);
    }
}
