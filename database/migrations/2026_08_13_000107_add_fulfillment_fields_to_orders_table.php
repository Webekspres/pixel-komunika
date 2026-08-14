<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('idempotency_key', 191)->nullable()->unique()->after('order_number');
            $table->date('order_date_local')->nullable()->index()->after('status');
            $table->string('cancellation_source', 16)->nullable()->index()->after('expires_at');
            $table->foreignId('cancelled_by_user_id')->nullable()->after('cancellation_source');
            $table->foreign('cancelled_by_user_id', 'fk_orders_cancelled_by')->references('id')->on('users')->nullOnDelete();
            $table->text('cancellation_reason')->nullable()->after('cancelled_by_user_id');
            $table->timestamp('cancelled_at')->nullable()->after('cancellation_reason');
            $table->string('completion_source', 24)->nullable()->index()->after('cancelled_at');
            $table->timestamp('receipt_confirmed_at')->nullable()->after('completion_source');
            $table->string('receipt_token_hash', 255)->nullable()->unique()->after('receipt_confirmed_at');
            $table->timestamp('receipt_token_expires_at')->nullable()->index()->after('receipt_token_hash');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign('fk_orders_cancelled_by');
            $table->dropColumn([
                'idempotency_key',
                'order_date_local',
                'cancellation_source',
                'cancelled_by_user_id',
                'cancellation_reason',
                'cancelled_at',
                'completion_source',
                'receipt_confirmed_at',
                'receipt_token_hash',
                'receipt_token_expires_at',
            ]);
        });
    }
};
