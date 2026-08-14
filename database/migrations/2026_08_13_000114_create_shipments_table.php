<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique();
            $table->foreign('order_id', 'fk_shipments_order_id')->references('id')->on('orders')->cascadeOnDelete();
            $table->foreignId('store_courier_rate_id')->nullable();
            $table->foreign('store_courier_rate_id', 'fk_shipments_store_rate')->references('id')->on('store_courier_rates')->nullOnDelete();
            $table->string('rate_provider', 32)->index();
            $table->string('courier_code', 100)->nullable();
            $table->string('courier_name_snapshot', 191)->nullable();
            $table->string('service_code', 100)->nullable();
            $table->string('service_name_snapshot', 191);
            $table->string('eta_snapshot', 100)->nullable();
            $table->string('origin_biteship_area_id_snapshot', 191)->nullable();
            $table->string('destination_biteship_area_id_snapshot', 191)->nullable();
            $table->string('recipient_name_snapshot', 150);
            $table->string('recipient_phone_snapshot', 32);
            $table->text('address_snapshot');
            $table->string('province_snapshot', 100);
            $table->string('city_snapshot', 100);
            $table->string('district_snapshot', 100)->index();
            $table->string('postal_code_snapshot', 16)->nullable();
            $table->char('currency', 3)->default('IDR');
            $table->decimal('shipping_amount', 19, 2);
            $table->string('rate_request_hash', 128)->nullable()->index();
            $table->timestamp('quoted_at')->nullable();
            $table->string('tracking_number', 191)->nullable()->index();
            $table->string('shipment_group_code', 64)->nullable()->index();
            $table->string('status', 32)->default('PROCESSING')->index();
            $table->string('issue_status', 24)->default('NONE')->index();
            $table->text('issue_reason')->nullable();
            $table->timestamp('issue_reported_at')->nullable();
            $table->timestamp('issue_resolved_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
