<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete()->index();
            $table->string('session_id', 191)->nullable()->index();
            $table->timestamps();
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();

            $table->unique(['cart_id', 'product_id']);
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 64)->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->index();
            $table->foreignId('address_id')->nullable()->constrained('addresses')->nullOnDelete();
            $table->string('status', 32)->default('unpaid')->index();
            $table->string('recipient_name', 150);
            $table->string('recipient_phone', 32);
            $table->text('shipping_address_line');
            $table->string('shipping_province', 100);
            $table->string('shipping_city', 100);
            $table->string('shipping_district', 100);
            $table->string('shipping_postal_code', 16)->nullable();
            $table->string('courier_code', 32);
            $table->string('courier_service', 64);
            $table->decimal('shipping_cost', 19, 2)->default(0);
            $table->decimal('subtotal', 19, 2)->default(0);
            $table->decimal('tax_pph22', 19, 2)->default(0);
            $table->decimal('grand_total', 19, 2)->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();
            $table->string('product_name', 255);
            $table->string('sku', 191);
            $table->decimal('unit_price', 19, 2);
            $table->unsignedInteger('quantity');
            $table->decimal('subtotal', 19, 2);
            $table->decimal('pph22_amount', 19, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 64)->unique();
            $table->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
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
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
    }
};
