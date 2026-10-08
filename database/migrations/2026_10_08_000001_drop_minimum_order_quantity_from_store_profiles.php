<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Keputusan klien 7 Okt: tidak ada minimal per SKU; checkout cukup satu SKU mencapai minimum partai.
        Schema::table('store_profiles', function (Blueprint $table) {
            $table->dropColumn('minimum_order_quantity');
        });
    }

    public function down(): void
    {
        Schema::table('store_profiles', function (Blueprint $table) {
            $table->unsignedInteger('minimum_order_quantity')->default(1)->after('partai_minimum_quantity');
        });
    }
};
