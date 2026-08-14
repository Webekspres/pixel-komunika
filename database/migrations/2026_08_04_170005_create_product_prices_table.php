<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('price_type', 16);
            $table->decimal('amount', 19, 2);
            $table->unsignedInteger('minimum_quantity')->nullable();
            $table->string('pos_price_id', 191)->nullable()->unique();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->unique(['product_id', 'price_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_prices');
    }
};
