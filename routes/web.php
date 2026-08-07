<?php

use App\Http\Controllers\Account\AccountController;
use App\Http\Controllers\Account\AddressController;
use App\Http\Controllers\Admin\CustomerReviewController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Livewire\Admin\AdminOrders;
use App\Livewire\Customer\CustomerOrders;
use App\Livewire\Customer\OrderDetail;
use App\Livewire\Pages\Home;
use App\Livewire\Storefront\CartIndex;
use App\Livewire\Storefront\Checkout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

use App\Livewire\Storefront\ProductIndex;
use App\Livewire\Storefront\ProductShow;

Route::get('/', Home::class)->name('home');
Route::get('/produk', ProductIndex::class)->name('products.index');
Route::get('/produk/{product}', ProductShow::class)->name('products.show');
Route::get('/cart', CartIndex::class)->name('cart.index');

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
    Route::get('/orders/{order}', OrderDetail::class)->name('orders.show');
});

Route::middleware(['auth', 'active.customer'])->group(function () {
    Route::get('/checkout', Checkout::class)->name('checkout.index');
    Route::get('/orders', CustomerOrders::class)->name('orders.index');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/customers', [CustomerReviewController::class, 'index'])->name('customers.index');
    Route::get('/customers/{customerProfile}', [CustomerReviewController::class, 'show'])->name('customers.show');
    Route::patch('/customers/{customerProfile}', [CustomerReviewController::class, 'update'])->name('customers.update');
    Route::get('/orders', AdminOrders::class)->name('orders.index');
});

Route::get('/health', function () {
    DB::connection()->getPdo();

    return response()->json([
        'status' => 'ok',
        'app' => config('app.name'),
        'database' => 'ok',
    ]);
})->name('health');
