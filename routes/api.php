<?php

use App\Http\Controllers\Api\PosOrderController;
use App\Http\Middleware\VerifyPosApiToken;
use Illuminate\Support\Facades\Route;

Route::middleware(VerifyPosApiToken::class)->prefix('pos')->group(function () {
    Route::get('/orders/{orderNumber}', [PosOrderController::class, 'show'])->name('api.pos.orders.show');
});
