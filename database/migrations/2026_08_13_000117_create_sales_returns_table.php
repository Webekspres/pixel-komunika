<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique();
            $table->foreign('order_id', 'fk_sales_returns_order')->references('id')->on('orders')->cascadeOnDelete();
            $table->string('return_number', 64)->unique();
            $table->text('reason');
            $table->string('reporting_status', 24)->index();
            $table->string('pos_ack_reference', 191)->nullable()->index();
            $table->timestamp('returned_at')->index();
            $table->timestamp('reported_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_returns');
    }
};
