<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_integration_operations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->index();
            $table->foreign('order_id', 'fk_pos_ops_order')->references('id')->on('orders')->nullOnDelete();
            $table->foreignId('sync_run_id')->nullable()->index();
            $table->foreign('sync_run_id', 'fk_pos_ops_sync_run')->references('id')->on('sync_runs')->nullOnDelete();
            $table->string('operation', 64)->index();
            $table->string('external_reference', 191)->unique();
            $table->string('request_hash', 128)->index();
            $table->string('status', 32)->index();
            $table->unsignedInteger('attempt_count')->default(0);
            $table->string('correlation_id', 191)->nullable()->index();
            $table->string('response_reference', 191)->nullable()->index();
            $table->json('request_payload_redacted')->nullable();
            $table->json('response_payload_redacted')->nullable();
            $table->string('error_code', 100)->nullable()->index();
            $table->text('error_message')->nullable();
            $table->timestamp('last_attempt_at')->nullable();
            $table->timestamp('reconciled_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_integration_operations');
    }
};
