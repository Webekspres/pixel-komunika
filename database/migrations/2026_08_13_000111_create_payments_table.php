<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique();
            $table->foreign('order_id', 'fk_payments_order_id')->references('id')->on('orders')->cascadeOnDelete();
            $table->foreignId('bank_account_id');
            $table->foreign('bank_account_id', 'fk_payments_bank_account')->references('id')->on('bank_accounts');
            $table->string('status', 24)->default('NOT_SUBMITTED')->index();
            $table->decimal('amount', 19, 2)->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('verified_by')->nullable();
            $table->foreign('verified_by', 'fk_payments_verified_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
