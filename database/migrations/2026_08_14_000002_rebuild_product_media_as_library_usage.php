<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_media', function (Blueprint $table) {
            $table->unsignedBigInteger('media_id')->nullable()->after('product_id');
            $table->foreign('media_id')->references('id')->on('media_library')->cascadeOnDelete();
        });

        // Backfill: metadata file dipindah ke media_library, lalu usage ditautkan.
        DB::table('product_media')
            ->whereNull('media_id')
            ->orderBy('id')
            ->select('id', 'object_key', 'media_type', 'mime_type', 'file_size')
            ->chunkById(200, function ($rows): void {
                foreach ($rows as $row) {
                    $mediaId = DB::table('media_library')->insertGetId([
                        'object_key' => $row->object_key,
                        'original_name' => basename($row->object_key),
                        'media_type' => $row->media_type,
                        'mime_type' => $row->mime_type,
                        'file_size' => $row->file_size,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    DB::table('product_media')->where('id', $row->id)->update(['media_id' => $mediaId]);
                }
            });

        Schema::table('product_media', function (Blueprint $table) {
            $table->unsignedBigInteger('media_id')->nullable(false)->change();
            // SQLite menolak DROP COLUMN pada kolom yang masih direferensikan index (object_key unique).
            $table->dropUnique(['object_key']);
            $table->dropColumn(['object_key', 'media_type', 'mime_type', 'file_size']);
            $table->unique(['product_id', 'media_id']);
        });
    }

    public function down(): void
    {
        Schema::table('product_media', function (Blueprint $table) {
            $table->dropUnique(['product_id', 'media_id']);
            $table->string('object_key', 500)->nullable()->after('product_id');
            $table->string('media_type', 16)->nullable()->after('object_key');
            $table->string('mime_type', 100)->nullable()->after('media_type');
            $table->unsignedBigInteger('file_size')->nullable()->after('mime_type');
        });

        DB::table('product_media')
            ->orderBy('id')
            ->select('id', 'media_id')
            ->chunkById(200, function ($rows): void {
                foreach ($rows as $row) {
                    $media = DB::table('media_library')->where('id', $row->media_id)
                        ->first(['object_key', 'media_type', 'mime_type', 'file_size']);

                    if ($media) {
                        DB::table('product_media')->where('id', $row->id)->update([
                            'object_key' => $media->object_key,
                            'media_type' => $media->media_type,
                            'mime_type' => $media->mime_type,
                            'file_size' => $media->file_size,
                        ]);
                    }
                }
            });

        Schema::table('product_media', function (Blueprint $table) {
            $table->string('object_key', 500)->nullable(false)->change();
            $table->string('media_type', 16)->nullable(false)->change();
            $table->string('mime_type', 100)->nullable(false)->change();
            $table->unsignedBigInteger('file_size')->nullable(false)->change();
            $table->dropForeign(['media_id']);
            $table->dropColumn('media_id');
        });
    }
};
