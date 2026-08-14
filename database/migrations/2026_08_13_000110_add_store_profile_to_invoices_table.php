<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('store_profile_id')->nullable()->after('order_id');
            $table->foreign('store_profile_id', 'fk_invoices_store_profile')->references('id')->on('store_profiles')->nullOnDelete();
            $table->string('company_name_snapshot', 191)->nullable()->after('store_npwp');
            $table->string('reseller_account_number_snapshot', 64)->nullable()->after('company_name_snapshot');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign('fk_invoices_store_profile');
            $table->dropColumn(['store_profile_id', 'company_name_snapshot', 'reseller_account_number_snapshot']);
        });
    }
};
