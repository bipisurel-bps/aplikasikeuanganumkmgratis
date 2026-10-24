<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::query()
            ->with('category:id,name')
            ->search($request->query('q'))
            ->when($request->filled('category'), fn ($q) => $q->where('category_id', $request->query('category')))
            ->when($request->boolean('low'), fn ($q) => $q->lowStock())
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('products.index', [
            'products' => $products,
            'categories' => Category::withCount('products')->orderBy('name')->get(),
        ]);
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        Product::create($request->validated() + ['is_active' => $request->boolean('is_active', true)]);

        return back()->with('status', 'Produk ditambahkan.');
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $product->update($request->validated() + ['is_active' => $request->boolean('is_active')]);

        return back()->with('status', 'Produk diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        // Produk yang pernah direstock tidak dihapus permanen demi integritas riwayat.
        if ($product->purchaseItems()->exists()) {
            $product->update(['is_active' => false]);

            return back()->with('status', 'Produk punya riwayat restock, jadi dinonaktifkan (bukan dihapus).');
        }

        $product->delete();

        return back()->with('status', 'Produk dihapus.');
    }
}
