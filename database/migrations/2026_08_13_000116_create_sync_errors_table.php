<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sync_errors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sync_run_id')->index();
            $table->foreign('sync_run_id', 'fk_sync_errors_run')->references('id')->on('sync_runs')->cascadeOnDelete();
            $table->string('entity_type', 64)->index();
            $table->string('external_id', 191)->nullable()->index();
            $table->string('error_code', 100)->index();
            $table->text('error_message');
            $table->json('payload_excerpt_redacted')->nullable();
            $table->boolean('retryable')->default(false);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_errors');
    }
};
