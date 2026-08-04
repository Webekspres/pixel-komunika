<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->index();
            $table->string('label', 100)->nullable();
            $table->string('recipient_name', 150);
            $table->string('recipient_phone', 32);
            $table->text('address_line');
            $table->string('province_code', 32)->nullable()->index();
            $table->string('province_name', 100);
            $table->string('city_code', 32)->nullable()->index();
            $table->string('city_name', 100);
            $table->string('district_code', 32)->nullable()->index();
            $table->string('district_name', 100)->index();
            $table->string('postal_code', 16)->nullable();
            $table->string('biteship_area_id', 191)->nullable()->index();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
