<?php

use App\Http\Controllers\Account\AccountController;
use App\Http\Controllers\Account\AddressController;
use App\Http\Controllers\Admin\CustomerReviewController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Livewire\Pages\Home;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', Home::class)->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/daftar', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/daftar', [RegisteredUserController::class, 'store'])->name('register.store');
    Route::get('/masuk', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/masuk', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/akun', [AccountController::class, 'show'])->name('account.dashboard');
    Route::patch('/akun', [AccountController::class, 'update'])->name('account.update');
    Route::post('/akun/addresses', [AddressController::class, 'store'])->name('account.addresses.store');
    Route::patch('/akun/addresses/{address}', [AddressController::class, 'update'])->name('account.addresses.update');
    Route::delete('/akun/addresses/{address}', [AddressController::class, 'destroy'])->name('account.addresses.destroy');
});

Route::middleware(['auth', 'active.customer'])->group(function () {
    Route::view('/checkout', 'checkout.index')->name('checkout.index');
    Route::view('/orders', 'orders.index')->name('orders.index');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/customers', [CustomerReviewController::class, 'index'])->name('customers.index');
    Route::get('/customers/{customerProfile}', [CustomerReviewController::class, 'show'])->name('customers.show');
    Route::patch('/customers/{customerProfile}', [CustomerReviewController::class, 'update'])->name('customers.update');
});

Route::get('/health', function () {
    DB::connection()->getPdo();

    return response()->json([
        'status' => 'ok',
        'app' => config('app.name'),
        'database' => 'ok',
    ]);
})->name('health');
