<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('category_tax_rules', function (Blueprint $table) {
            $table->string('calculation_basis', 48)->default('TRIGGERED_SUBTOTAL_DIV_1_11')->after('rate_percent');
            $table->timestamp('effective_from')->nullable()->after('is_active');
            $table->timestamp('effective_until')->nullable()->after('effective_from');
            $table->foreignId('updated_by')->nullable()->after('effective_until');
            $table->foreign('updated_by', 'fk_tax_rules_updated_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('category_tax_rules', function (Blueprint $table) {
            $table->dropForeign('fk_tax_rules_updated_by');
            $table->dropColumn(['calculation_basis', 'effective_from', 'effective_until', 'updated_by']);
        });
    }
};
