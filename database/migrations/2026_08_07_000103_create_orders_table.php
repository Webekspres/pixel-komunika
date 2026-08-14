<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 64)->unique();
            $table->foreignId('user_id')->index();
            $table->foreign('user_id', 'fk_orders_user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreignId('address_id')->nullable();
            $table->foreign('address_id', 'fk_orders_address_id')->references('id')->on('addresses')->nullOnDelete();
            $table->string('status', 32)->default('unpaid')->index();
            $table->string('recipient_name', 150);
            $table->string('recipient_phone', 32);
            $table->text('shipping_address_line');
            $table->string('shipping_province', 100);
            $table->string('shipping_city', 100);
            $table->string('shipping_district', 100);
            $table->string('shipping_postal_code', 16)->nullable();
            $table->string('courier_code', 32);
            $table->string('courier_service', 64);
            $table->decimal('shipping_cost', 19, 2)->default(0);
            $table->decimal('subtotal', 19, 2)->default(0);
            $table->decimal('tax_pph22', 19, 2)->default(0);
            $table->decimal('grand_total', 19, 2)->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
