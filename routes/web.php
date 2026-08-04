<?php

use App\Livewire\Pages\Home;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', Home::class)->name('home');

Route::get('/health', function () {
    DB::connection()->getPdo();

    return response()->json([
        'status' => 'ok',
        'app' => config('app.name'),
        'database' => 'ok',
    ]);
})->name('health');
