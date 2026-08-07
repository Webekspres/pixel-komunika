<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_proofs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id');
            $table->foreign('order_id', 'fk_proofs_order_id')->references('id')->on('orders')->cascadeOnDelete();
            $table->foreignId('user_id');
            $table->foreign('user_id', 'fk_proofs_user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->string('bank_name');
            $table->string('account_name');
            $table->decimal('amount', 12, 2);
            $table->string('proof_path');
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('rejection_reason')->nullable();
            $table->foreignId('reviewed_by')->nullable();
            $table->foreign('reviewed_by', 'fk_proofs_reviewed_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('order_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id');
            $table->foreign('order_id', 'fk_returns_order_id')->references('id')->on('orders')->cascadeOnDelete();
            $table->foreignId('user_id');
            $table->foreign('user_id', 'fk_returns_user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->text('reason');
            $table->string('status')->default('requested'); // requested, approved, rejected, refunded
            $table->decimal('refund_amount', 12, 2)->nullable();
            $table->text('admin_notes')->nullable();
            $table->foreignId('processed_by')->nullable();
            $table->foreign('processed_by', 'fk_returns_processed_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_returns');
        Schema::dropIfExists('payment_proofs');
    }
};
