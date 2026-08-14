<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MediaLibrary extends Model
{
    public const IMAGE = 'IMAGE';

    public const VIDEO = 'VIDEO';

    protected $table = 'media_library';

    protected $fillable = [
        'object_key',
        'original_name',
        'media_type',
        'mime_type',
        'file_size',
        'width',
        'height',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
        ];
    }

    public function productUsages(): HasMany
    {
        return $this->hasMany(ProductMedia::class, 'media_id');
    }

    /**
     * URL root-relative agar mengikuti origin request (host/port/protokol),
     * tidak terkunci pada APP_URL (lihat bug media tidak termuat saat port dev berbeda).
     */
    public function url(): string
    {
        return '/storage/'.ltrim($this->object_key, '/');
    }
}
