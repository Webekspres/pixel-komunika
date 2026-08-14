<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index();
            $table->foreign('user_id', 'fk_notifications_user')->references('id')->on('users')->cascadeOnDelete();
            $table->foreignId('order_id')->index();
            $table->foreign('order_id', 'fk_notifications_order')->references('id')->on('orders')->cascadeOnDelete();
            $table->string('channel', 16)->index();
            $table->string('type', 64)->index();
            $table->json('data');
            $table->string('status', 24)->index();
            $table->string('external_message_id', 191)->nullable()->index();
            $table->timestamp('read_at')->nullable()->index();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
