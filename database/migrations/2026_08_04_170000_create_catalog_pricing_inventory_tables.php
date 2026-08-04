<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('pos_category_id', 191)->unique();
            $table->string('name', 191)->index();
            $table->boolean('is_active')->default(true);
            $table->timestamp('source_updated_at')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
        });

        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('pos_brand_id', 191)->nullable()->unique();
            $table->string('name', 191)->index();
            $table->boolean('is_active')->default(true);
            $table->timestamp('source_updated_at')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $table->string('pos_product_id', 191)->unique();
            $table->string('sku', 191)->unique();
            $table->string('name', 255)->index();
            $table->unsignedInteger('weight_grams')->nullable();
            $table->decimal('length_cm', 10, 2)->nullable();
            $table->decimal('width_cm', 10, 2)->nullable();
            $table->decimal('height_cm', 10, 2)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
        });

        Schema::create('product_enrichments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('slug', 191)->unique();
            $table->string('short_description', 500)->nullable();
            $table->text('description')->nullable();
            $table->string('label', 100)->nullable();
            $table->integer('display_order')->default(0);
            $table->boolean('is_visible')->default(true)->index();
            $table->timestamps();
        });

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

        Schema::create('category_tax_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->unique()->constrained()->cascadeOnDelete();
            $table->decimal('threshold_amount', 19, 2)->default(0);
            $table->decimal('rate_percent', 8, 4)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('inventory_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity_available')->default(0);
            $table->unsignedInteger('low_stock_threshold')->default(0);
            $table->string('stock_status', 16)->index();
            $table->timestamp('source_updated_at')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
        });

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
        Schema::dropIfExists('inventory_snapshots');
        Schema::dropIfExists('category_tax_rules');
        Schema::dropIfExists('product_prices');
        Schema::dropIfExists('product_enrichments');
        Schema::dropIfExists('products');
        Schema::dropIfExists('brands');
        Schema::dropIfExists('categories');
    }
};
