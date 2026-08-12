<?php

use App\Http\Controllers\Account\AccountController;
use App\Http\Controllers\Account\AddressController;
use App\Http\Controllers\Admin\CategoryTaxRuleController;
use App\Http\Controllers\Admin\CustomerReviewController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Livewire\Admin\AdminOrders;
use App\Livewire\Customer\CustomerOrders;
use App\Livewire\Customer\OrderDetail;
use App\Livewire\Pages\Home;
use App\Livewire\Storefront\CartIndex;
use App\Livewire\Storefront\Checkout;
use App\Livewire\Storefront\ProductIndex;
use App\Livewire\Storefront\ProductShow;
use App\Models\CustomerProfile;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

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
    Route::get('/akun/profil', [AccountController::class, 'profile'])->name('account.profile');
    Route::patch('/akun/profil', [AccountController::class, 'update'])->name('account.update');
    Route::get('/akun/alamat', [AccountController::class, 'addresses'])->name('account.addresses.index');
    Route::post('/akun/alamat', [AddressController::class, 'store'])->name('account.addresses.store');
    Route::patch('/akun/alamat/{address}', [AddressController::class, 'update'])->name('account.addresses.update');
    Route::delete('/akun/alamat/{address}', [AddressController::class, 'destroy'])->name('account.addresses.destroy');
    Route::get('/akun/pesanan/{order}', OrderDetail::class)->name('orders.show');
    Route::get('/orders/{order}', OrderDetail::class);
});

Route::middleware(['auth', 'active.customer'])->group(function () {
    Route::get('/checkout', Checkout::class)->name('checkout.index');
    Route::get('/akun/pesanan', CustomerOrders::class)->name('orders.index');
    Route::get('/orders', CustomerOrders::class);
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return view('admin.dashboard', [
            'customerCount' => CustomerProfile::count(),
            'pendingCustomerCount' => CustomerProfile::where('verification_status', CustomerProfile::PENDING)->count(),
            'orderCount' => Order::count(),
            'unpaidOrderCount' => Order::whereIn('status', ['unpaid', 'payment_pending'])->count(),
        ]);
    })->name('dashboard');
    Route::get('/customers', [CustomerReviewController::class, 'index'])->name('customers.index');
    Route::get('/customers/{customerProfile}', [CustomerReviewController::class, 'show'])->name('customers.show');
    Route::patch('/customers/{customerProfile}', [CustomerReviewController::class, 'update'])->name('customers.update');
    Route::get('/orders', AdminOrders::class)->name('orders.index');
    Route::get('/orders/{order}', function (Order $order) {
        return view('admin.orders.show', [
            'order' => $order->load(['user', 'items', 'invoice', 'latestPaymentProof', 'paymentProofs.reviewer']),
        ]);
    })->name('orders.show');
    Route::get('/tax-rules', [CategoryTaxRuleController::class, 'index'])->name('tax-rules.index');
    Route::get('/tax-rules/{category}/edit', [CategoryTaxRuleController::class, 'edit'])->name('tax-rules.edit');
    Route::patch('/tax-rules/{category}', [CategoryTaxRuleController::class, 'update'])->name('tax-rules.update');
});

Route::get('/health', function () {
    DB::connection()->getPdo();

    return response()->json([
        'status' => 'ok',
        'app' => config('app.name'),
        'database' => 'ok',
    ]);
})->name('health');
