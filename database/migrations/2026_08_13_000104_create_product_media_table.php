<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->index();
            $table->foreign('product_id', 'fk_product_media_product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->string('media_type', 16);
            $table->string('object_key', 500)->unique();
            $table->string('alt_text', 255)->nullable();
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('file_size');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_media');
    }
};
