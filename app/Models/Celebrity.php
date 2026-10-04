<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Celebrity extends Model
{
    protected $fillable = [
        'name', 'occasion', 'pair_worn', 'product_id',
        'photo_path', 'consent_on_file', 'sort', 'visible'
    ];

    protected $casts = [
        'consent_on_file' => 'boolean',
        'sort' => 'integer',
        'visible' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
