<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductMedia extends Model
{
    public const IMAGE = 'IMAGE';

    public const VIDEO = 'VIDEO';

    protected $table = 'product_media';

    protected $fillable = [
        'product_id',
        'media_type',
        'object_key',
        'alt_text',
        'mime_type',
        'file_size',
        'sort_order',
        'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'file_size' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
