<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sync_runs', function (Blueprint $table) {
            $table->id();
            $table->string('sync_type', 32)->index();
            $table->string('source', 16);
            $table->string('status', 24)->index();
            $table->timestamp('started_at')->index();
            $table->timestamp('finished_at')->nullable();
            $table->unsignedInteger('success_count')->default(0);
            $table->unsignedInteger('failed_count')->default(0);
            $table->string('correlation_id', 191)->nullable()->unique();
            $table->json('summary')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_runs');
    }
};
