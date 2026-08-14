<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_ledger', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('source', 32)->index();
            $table->integer('quantity_before');
            $table->integer('quantity_after');
            $table->integer('quantity_delta');
            $table->string('external_reference', 191)->nullable()->index();
            $table->timestamp('occurred_at')->index();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_ledger');
    }
};
