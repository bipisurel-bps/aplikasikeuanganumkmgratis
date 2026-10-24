<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;

class CategoryController extends Controller
{
    public function store(CategoryRequest $request): RedirectResponse
    {
        Category::create($request->validated());

        return back()->with('status', 'Kategori ditambahkan.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete(); // produk otomatis menjadi tanpa kategori (nullOnDelete)

        return back()->with('status', 'Kategori dihapus.');
    }
}
