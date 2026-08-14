<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 64)->unique();
            $table->foreignId('order_id')->unique();
            $table->foreign('order_id', 'fk_invoices_order_id')->references('id')->on('orders')->cascadeOnDelete();
            $table->foreignId('user_id');
            $table->foreign('user_id', 'fk_invoices_user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->decimal('subtotal', 19, 2);
            $table->decimal('tax_pph22', 19, 2)->default(0);
            $table->decimal('shipping_cost', 19, 2)->default(0);
            $table->decimal('amount', 19, 2);
            $table->string('status', 32)->default('unpaid')->index();
            $table->timestamp('due_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
