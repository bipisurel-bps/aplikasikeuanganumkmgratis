<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CashDrawerController;
use App\Http\Controllers\CashMutationController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

// Halaman Pembuka / Web Opener Produk (Landing Page)
Route::get('/', function () {
    return view('landing');
})->name('landing');

// Unduh Panduan Uji Coba Resmi (Word .docx)
Route::get('/download/panduan-uji-coba.docx', function () {
    $filePath = public_path('Panduan_Ujicoba_Aplikasi_Keuangan_UMKM.docx');
    if (!file_exists($filePath)) {
        abort(404, 'Dokumen panduan belum tersedia.');
    }
    return response()->download($filePath, 'Panduan_Ujicoba_Aplikasi_Keuangan_UMKM.docx', [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ]);
})->name('download.guide');


// Autentikasi Pengguna & Demo 1-Klik
Route::redirect('/login', '/masuk');
Route::middleware('guest')->group(function () {
    Route::get('/masuk', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/masuk', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.attempt');
    Route::post('/demo-masuk', [AuthController::class, 'demoLogin'])->name('login.demo');
});

// Area Aplikasi Terproteksi
Route::middleware('auth')->group(function () {
    Route::post('/keluar', [AuthController::class, 'logout'])->name('logout');

    // Akses bersama: Pemilik (Owner) & Kasir
    Route::middleware('role:owner,kasir')->group(function () {
        Route::get('/kasir', [PosController::class, 'index'])->name('pos');
        Route::post('/kasir/checkout', [PosController::class, 'checkout'])->name('pos.checkout');

        Route::get('/shift', [CashDrawerController::class, 'index'])->name('drawers.index');
        Route::post('/shift/buka', [CashDrawerController::class, 'open'])->name('drawers.open');
        Route::post('/shift/{drawer}/tutup', [CashDrawerController::class, 'close'])->name('drawers.close');

        Route::get('/penjualan', [SaleController::class, 'index'])->name('sales.index');
        Route::get('/penjualan/{sale}', [SaleController::class, 'show'])->name('sales.show');
    });

    // Khusus Pemilik (Owner)
    Route::middleware('role:owner')->group(function () {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');

        Route::get('/produk', [ProductController::class, 'index'])->name('products.index');
        Route::post('/produk', [ProductController::class, 'store'])->name('products.store');
        Route::put('/produk/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/produk/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

        Route::post('/kategori', [CategoryController::class, 'store'])->name('categories.store');
        Route::delete('/kategori/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        Route::get('/supplier', [SupplierController::class, 'index'])->name('suppliers.index');
        Route::post('/supplier', [SupplierController::class, 'store'])->name('suppliers.store');
        Route::put('/supplier/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
        Route::delete('/supplier/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');

        Route::get('/restock', [PurchaseController::class, 'index'])->name('purchases.index');
        Route::get('/restock/baru', [PurchaseController::class, 'create'])->name('purchases.create');
        Route::post('/restock', [PurchaseController::class, 'store'])->name('purchases.store');
        Route::get('/restock/{purchase}', [PurchaseController::class, 'show'])->name('purchases.show');

        Route::get('/beban', [ExpenseController::class, 'index'])->name('expenses.index');
        Route::post('/beban', [ExpenseController::class, 'store'])->name('expenses.store');
        Route::delete('/beban/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');
        Route::get('/beban/{expense}/nota', [ExpenseController::class, 'receipt'])->name('expenses.receipt');
        Route::post('/anggaran', [BudgetController::class, 'store'])->name('budgets.store');

        Route::get('/buku-kas', [CashMutationController::class, 'index'])->name('cash.index');
    });
});
