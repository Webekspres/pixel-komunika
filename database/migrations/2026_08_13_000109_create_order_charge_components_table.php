<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_charge_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->index();
            $table->foreign('order_id', 'fk_charge_components_order_id')->references('id')->on('orders')->cascadeOnDelete();
            $table->foreignId('category_tax_rule_id')->nullable()->index();
            $table->foreign('category_tax_rule_id', 'fk_charge_components_tax_rule')->references('id')->on('category_tax_rules')->nullOnDelete();
            $table->string('component_code', 32)->index();
            $table->string('label_snapshot', 100);
            $table->decimal('basis_amount', 19, 2);
            $table->decimal('divisor', 8, 4)->default(1.1100);
            $table->decimal('rate_percent', 8, 4);
            $table->decimal('amount', 19, 2);
            $table->json('config_snapshot');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_charge_components');
    }
};
