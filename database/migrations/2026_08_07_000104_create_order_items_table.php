<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id');
            $table->foreign('order_id', 'fk_order_items_order_id')->references('id')->on('orders')->cascadeOnDelete();
            $table->foreignId('product_id');
            $table->foreign('product_id', 'fk_order_items_product_id')->references('id')->on('products');
            $table->string('product_name', 255);
            $table->string('sku', 191);
            $table->decimal('unit_price', 19, 2);
            $table->unsignedInteger('quantity');
            $table->decimal('subtotal', 19, 2);
            $table->decimal('pph22_amount', 19, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
