<?php

use App\Http\Controllers\Account\AccountController;
use App\Http\Controllers\Account\AddressController;
use App\Http\Controllers\Admin\CatalogController;
use App\Http\Controllers\Admin\CategoryTaxRuleController;
use App\Http\Controllers\Admin\CustomerReviewController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PaymentReviewController;
use App\Http\Controllers\Admin\ProductEnrichmentController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Api\AreaSearchController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\OrderInvoiceController;
use App\Http\Controllers\ReceiptConfirmationController;
use App\Livewire\Admin\AdminAuditLog;
use App\Livewire\Admin\AdminOrders;
use App\Livewire\Admin\AdminPayments;
use App\Livewire\Admin\CustomerReviewDetail;
use App\Livewire\Admin\MediaLibrary;
use App\Livewire\Customer\CustomerOrders;
use App\Livewire\Customer\OrderDetail;
use App\Livewire\Pages\Home;
use App\Livewire\Storefront\CartIndex;
use App\Livewire\Storefront\Checkout;
use App\Livewire\Storefront\ProductIndex;
use App\Livewire\Storefront\ProductShow;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', Home::class)->name('home');
Route::get('/produk', ProductIndex::class)->name('products.index');
Route::get('/produk/{product}', ProductShow::class)->name('products.show');
Route::get('/cart', CartIndex::class)->name('cart.index');

Route::get('/konfirmasi-penerimaan/{order}', ReceiptConfirmationController::class)
    ->middleware('throttle:10,1')
    ->name('orders.confirm-receipt');

Route::middleware('guest')->group(function () {
    Route::get('/daftar', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/daftar', [RegisteredUserController::class, 'store'])->name('register.store')->middleware('throttle:5,1');
    Route::get('/masuk', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/masuk', [AuthenticatedSessionController::class, 'store'])->name('login.store')->middleware('throttle:5,1');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/akun', [AccountController::class, 'show'])->name('account.dashboard');
    Route::get('/akun/profil', [AccountController::class, 'profile'])->name('account.profile');
    Route::get('/profil', [AccountController::class, 'profile']);
    Route::patch('/akun/profil', [AccountController::class, 'update'])->name('account.update');
    Route::get('/akun/alamat', [AccountController::class, 'addresses'])->name('account.addresses.index');
    Route::get('/alamat', [AccountController::class, 'addresses']);
    Route::post('/akun/alamat', [AddressController::class, 'store'])->name('account.addresses.store');
    Route::patch('/akun/alamat/{address}', [AddressController::class, 'update'])->name('account.addresses.update');
    Route::patch('/akun/alamat/{address}/utama', [AddressController::class, 'setDefault'])->name('account.addresses.default');
    Route::delete('/akun/alamat/{address}', [AddressController::class, 'destroy'])->name('account.addresses.destroy');
    Route::get('/akun/pesanan/{order}', OrderDetail::class)->name('orders.show');
    Route::get('/orders/{order}', OrderDetail::class);
    Route::get('/pesanan/{order}', OrderDetail::class);
    Route::get('/akun/pesanan/{order}/invoice', [OrderInvoiceController::class, 'show'])->name('orders.invoice');
    Route::get('/akun/pesanan/{order}/invoice/download', [OrderInvoiceController::class, 'download'])->name('orders.invoice.download');
    Route::get('/orders/{order}/invoice', [OrderInvoiceController::class, 'show']);
    Route::get('/pesanan/{order}/invoice', [OrderInvoiceController::class, 'show']);
    Route::get('/orders/{order}/invoice/download', [OrderInvoiceController::class, 'download']);
    Route::get('/pesanan/{order}/invoice/download', [OrderInvoiceController::class, 'download']);
    Route::get('/api/areas/search', [AreaSearchController::class, 'search'])->name('api.areas.search');
    Route::get('/api/areas/districts', [AreaSearchController::class, 'districts'])->name('api.areas.districts');
});

Route::middleware(['auth', 'active.customer'])->group(function () {
    Route::get('/checkout', Checkout::class)->name('checkout.index');
    Route::get('/akun/pesanan', CustomerOrders::class)->name('orders.index');
    Route::get('/orders', CustomerOrders::class);
    Route::get('/pesanan', CustomerOrders::class);
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::get('/customers', [CustomerReviewController::class, 'index'])->name('customers.index');
    Route::get('/customers/{customerProfile}', CustomerReviewDetail::class)->name('customers.show');
    Route::get('/orders', AdminOrders::class)->name('orders.index');
    Route::get('/orders/{order}', function (Order $order) {
        return view('admin.orders.show', [
            'order' => $order->load(['user', 'items', 'invoice', 'latestPaymentProof', 'paymentProofs.reviewer']),
        ]);
    })->name('orders.show');
    Route::get('/tax-rules', [CategoryTaxRuleController::class, 'index'])->name('tax-rules.index');
    Route::get('/tax-rules/{category}/edit', [CategoryTaxRuleController::class, 'edit'])->name('tax-rules.edit');
    Route::patch('/tax-rules/{category}', [CategoryTaxRuleController::class, 'update'])->name('tax-rules.update');

    Route::get('/payments', AdminPayments::class)->name('payments.index');
    Route::get('/payments/{paymentProof}', [PaymentReviewController::class, 'show'])
        ->name('payments.show')
        ->middleware('signed');

    Route::get('/products', [ProductEnrichmentController::class, 'index'])->name('products.index');
    Route::get('/products/{product}', [ProductEnrichmentController::class, 'edit'])->name('products.edit');
    Route::patch('/products/{product}', [ProductEnrichmentController::class, 'update'])->name('products.update');

    Route::get('/media', MediaLibrary::class)->name('media.index');

    Route::get('/categories', [CatalogController::class, 'categories'])->name('categories.index');
    Route::patch('/categories/{category}', [CatalogController::class, 'toggleCategory'])->name('categories.toggle');
    Route::get('/brands', [CatalogController::class, 'brands'])->name('brands.index');
    Route::patch('/brands/{brand}', [CatalogController::class, 'toggleBrand'])->name('brands.toggle');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/audit-logs', AdminAuditLog::class)->name('audit-logs.index');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::patch('/settings/store-profile', [SettingsController::class, 'updateStoreProfile'])->name('settings.store-profile.update');
    Route::post('/settings/bank-accounts', [SettingsController::class, 'storeBankAccount'])->name('settings.bank-accounts.store');
    Route::patch('/settings/bank-accounts/{bankAccount}', [SettingsController::class, 'updateBankAccount'])->name('settings.bank-accounts.update');
    Route::delete('/settings/bank-accounts/{bankAccount}', [SettingsController::class, 'destroyBankAccount'])->name('settings.bank-accounts.destroy');
    Route::post('/settings/courier-rates', [SettingsController::class, 'storeCourierRate'])->name('settings.courier-rates.store');
    Route::patch('/settings/courier-rates/{courierRate}', [SettingsController::class, 'updateCourierRate'])->name('settings.courier-rates.update');
    Route::delete('/settings/courier-rates/{courierRate}', [SettingsController::class, 'destroyCourierRate'])->name('settings.courier-rates.destroy');
});

Route::get('/health', function () {
    DB::connection()->getPdo();

    return response()->json([
        'status' => 'ok',
        'app' => config('app.name'),
        'database' => 'ok',
    ]);
})->name('health');
