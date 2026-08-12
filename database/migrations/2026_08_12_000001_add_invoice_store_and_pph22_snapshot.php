<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->json('tax_pph22_snapshot')->nullable()->after('tax_pph22');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->string('store_name', 191)->nullable()->after('user_id');
            $table->text('store_address')->nullable()->after('store_name');
            $table->string('store_phone', 32)->nullable()->after('store_address');
            $table->string('store_npwp', 32)->nullable()->after('store_phone');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('tax_pph22_snapshot');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['store_name', 'store_address', 'store_phone', 'store_npwp']);
        });
    }
};
