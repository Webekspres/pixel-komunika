<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_courier_rates', function (Blueprint $table) {
            $table->id();
            $table->string('area_code', 32)->nullable()->index();
            $table->string('area_name', 191)->index();
            $table->decimal('rate_amount', 19, 2);
            $table->string('eta_text', 100)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_courier_rates');
    }
};
