<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_profiles', function (Blueprint $table) {
            // Minimal pembelian per SKU, diatur admin (keputusan 5 Okt 2026).
            $table->unsignedInteger('minimum_order_quantity')->default(1)->after('partai_minimum_quantity');
        });
    }

    public function down(): void
    {
        Schema::table('store_profiles', function (Blueprint $table) {
            $table->dropColumn('minimum_order_quantity');
        });
    }
};
