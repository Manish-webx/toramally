<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalPost extends Model
{
    protected $fillable = [
        'slug', 'category', 'title', 'dek', 'body_html', 'craft_id',
        'cover_path', 'status', 'published_at', 'seo_title', 'seo_desc'
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function craft(): BelongsTo
    {
        return $this->belongsTo(Collection::class, 'craft_id');
    }
}
