<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_enrichments', function (Blueprint $table) {
            $table->string('display_name', 255)->nullable()->index()->after('product_id');
            $table->string('seo_title', 191)->nullable()->after('description');
            $table->string('seo_description', 320)->nullable()->after('seo_title');
        });
    }

    public function down(): void
    {
        Schema::table('product_enrichments', function (Blueprint $table) {
            $table->dropColumn(['display_name', 'seo_title', 'seo_description']);
        });
    }
};
