<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_ledger', function (Blueprint $table) {
            $table->foreignId('sync_run_id')->nullable()->index()->after('product_id');
            $table->foreign('sync_run_id', 'fk_ledger_sync_run')->references('id')->on('sync_runs')->nullOnDelete();
            $table->foreignId('sales_return_id')->nullable()->index()->after('sync_run_id');
            $table->foreign('sales_return_id', 'fk_ledger_sales_return')->references('id')->on('sales_returns')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('inventory_ledger', function (Blueprint $table) {
            $table->dropForeign('fk_ledger_sales_return');
            $table->dropForeign('fk_ledger_sync_run');
            $table->dropColumn(['sync_run_id', 'sales_return_id']);
        });
    }
};
