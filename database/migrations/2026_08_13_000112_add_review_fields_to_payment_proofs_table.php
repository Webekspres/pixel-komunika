<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_proofs', function (Blueprint $table) {
            $table->foreignId('payment_id')->nullable()->index()->after('id');
            $table->foreign('payment_id', 'fk_proofs_payment_id')->references('id')->on('payments')->nullOnDelete();
            $table->boolean('is_active')->default(true)->after('status');
            $table->string('checksum', 128)->nullable()->index()->after('proof_path');
            $table->timestamp('retain_until')->nullable()->index()->after('checksum');
        });
    }

    public function down(): void
    {
        Schema::table('payment_proofs', function (Blueprint $table) {
            $table->dropForeign('fk_proofs_payment_id');
            $table->dropColumn(['payment_id', 'is_active', 'checksum', 'retain_until']);
        });
    }
};
