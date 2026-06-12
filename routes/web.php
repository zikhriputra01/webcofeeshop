<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/menu');
});

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Authenticated routes
Route::middleware(['auth', 'nocache'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    // POS Menu & Cart
    Route::get('/menu', [MenuController::class, 'index'])->name('menu');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
    
    // Transactions
    Route::post('/transaksi', [TransactionController::class, 'store'])->name('transaction.store');
    
    // History & Print
    Route::get('/history', [HistoryController::class, 'index'])->name('history');
    Route::get('/history/{trx_id}/print', [HistoryController::class, 'print'])->name('history.print');
    
    // Setting Akun (semua role)
    Route::get('/setting/akun', [SettingController::class, 'akun'])->name('setting.akun');
    Route::put('/setting/akun', [SettingController::class, 'updateAkun'])->name('setting.akun.update');
    
    // Setting Toko & Menu (admin only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/setting/toko', [SettingController::class, 'toko'])->name('setting.toko');
        Route::put('/setting/toko', [SettingController::class, 'updateToko'])->name('setting.toko.update');
        
        Route::get('/setting/menu', [SettingController::class, 'menu'])->name('setting.menu');
        Route::post('/setting/menu', [SettingController::class, 'storeMenu'])->name('setting.menu.store');
        Route::put('/setting/menu/{id}', [SettingController::class, 'updateMenu'])->name('setting.menu.update');
        Route::delete('/setting/menu/{id}', [SettingController::class, 'destroyMenu'])->name('setting.menu.destroy');
    });
});
