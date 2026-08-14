<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id_snapshot')->nullable()->index()->after('product_id');
            $table->string('category_name_snapshot', 191)->nullable()->after('category_id_snapshot');
            $table->string('price_type', 16)->nullable()->after('sku');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['category_id_snapshot', 'category_name_snapshot', 'price_type']);
        });
    }
};
