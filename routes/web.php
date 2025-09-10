<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PaymentMethodController;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesExport;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// Grup Rute untuk Admin (Membutuhkan autentikasi & role 'admin')
Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('products', ProductController::class);
    Route::resource('users', UserController::class);
    Route::resource('payment_methods', PaymentMethodController::class);

    // Rute Laporan Penjualan
    Route::get('/kasir/report', [SaleController::class, 'report'])->name('sales.report');
    
    // Rute untuk mengunduh laporan penjualan (Excel)
    Route::get('/kasir/report/export', function (\Illuminate\Http\Request $request) {
        return Excel::download(new SalesExport($request->start_date, $request->end_date), 'laporan-penjualan.xlsx');
    })->name('sales.export');

    // Rute untuk menghapus transaksi satu per satu
    Route::delete('/kasir/history/{sale}', [SaleController::class, 'destroy'])->name('sales.destroy');
    
    // PERBAIKAN DI SINI: Rute untuk menghapus semua transaksi
    // Gunakan metode POST karena lebih stabil untuk form HTML
    Route::post('/kasir/history/all', [SaleController::class, 'destroyAll'])->name('sales.destroyAll');
});

// Grup Rute untuk Pengguna Terautentikasi (Kasir & Admin)
Route::middleware(['auth'])->group(function () {
    // Rute untuk halaman kasir dan riwayat transaksi
    Route::get('/kasir', [SaleController::class, 'index'])->name('sales.index');
    Route::get('/kasir/history', [SaleController::class, 'history'])->name('sales.history');

    // Rute API untuk kasir (pencarian produk dan checkout)
    Route::get('/kasir/search', [SaleController::class, 'search'])->name('sales.search');
    Route::post('/kasir/checkout', [SaleController::class, 'store'])->name('sales.store');
    
    // Rute untuk menampilkan struk pembelian
    Route::get('/kasir/receipt/{sale}', [SaleController::class, 'receipt'])->name('sales.receipt');
});


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');