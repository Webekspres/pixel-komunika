<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // CR-024: penanda POS `pph` (1 = kena PPh 22); ambang & tarif tetap dari aturan website.
            $table->boolean('pph22_applicable')->default(true)->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('pph22_applicable');
        });
    }
};
