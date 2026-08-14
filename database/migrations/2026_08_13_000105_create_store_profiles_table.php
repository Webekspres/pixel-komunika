<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('store_name', 191);
            $table->text('address');
            $table->string('contact_number', 32);
            $table->string('company_name', 191)->nullable();
            $table->string('company_npwp', 32);
            $table->unsignedInteger('partai_minimum_quantity')->default(5);
            $table->string('origin_biteship_area_id', 191)->nullable()->index();
            $table->string('origin_postal_code', 16)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_profiles');
    }
};
