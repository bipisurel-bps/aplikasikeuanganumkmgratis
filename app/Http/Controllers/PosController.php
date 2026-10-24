<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Services\CheckoutService;
use Illuminate\Http\JsonResponse;

class PosController extends Controller
{
    public function index()
    {
        $drawer = auth()->user()->openDrawer();

        $products = Product::active()
            ->with('category:id,name')
            ->orderBy('name')
            ->get(['id', 'category_id', 'sku', 'name', 'stock', 'sell_price'])
            ->map(fn ($p) => [
                'id' => $p->id,
                'sku' => $p->sku,
                'name' => $p->name,
                'stock' => $p->stock,
                'price' => $p->sell_price,
                'category_id' => $p->category_id,
                'category' => $p->category?->name,
            ]);

        return view('pos.index', [
            'drawer' => $drawer,
            'products' => $products,
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'methods' => config('umkm.payment_methods'),
        ]);
    }

    public function checkout(CheckoutRequest $request, CheckoutService $checkout): JsonResponse
    {
        $data = $request->validated();

        $sale = $checkout->checkout(
            $request->user(),
            $data['items'],
            (int) ($data['discount'] ?? 0),
            (int) ($data['paid'] ?? 0),
            $data['payment_method'],
        );

        return response()->json(['sale' => self::receipt($sale)], 201);
    }

    /** Data struk yang dipakai modal POS dan halaman cetak. */
    public static function receipt(Sale $sale): array
    {
        $sale->loadMissing('items', 'user');

        return [
            'id' => $sale->id,
            'invoice_no' => $sale->invoice_no,
            'sold_at' => $sale->sold_at->translatedFormat('d M Y H:i'),
            'cashier' => $sale->user->name,
            'method' => config('umkm.payment_methods')[$sale->payment_method] ?? $sale->payment_method,
            'subtotal' => $sale->subtotal,
            'discount' => $sale->discount,
            'total' => $sale->total,
            'paid' => $sale->paid,
            'change' => $sale->change_amount,
            'store' => [
                'name' => config('umkm.store_name'),
                'address' => config('umkm.store_address'),
                'phone' => config('umkm.store_phone'),
            ],
            'items' => $sale->items->map(fn ($i) => [
                'name' => $i->product_name,
                'qty' => $i->qty,
                'price' => $i->unit_price,
                'total' => $i->line_total,
            ])->all(),
        ];
    }
}
