<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Collection extends Model
{
    protected $fillable = [
        'slug', 'type', 'name', 'scale_word', 'tagline', 'description',
        'how_1', 'how_2', 'how_3', 'from_price', 'lead_weeks', 'buy_mode_note',
        'on_ladder', 'sort', 'active', 'seo_title', 'seo_desc'
    ];

    protected $casts = [
        'on_ladder' => 'boolean',
        'active' => 'boolean',
        'sort' => 'integer',
        'from_price' => 'integer',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_collection');
    }

    public function craftProducts(): HasMany
    {
        return $this->hasMany(Product::class, 'craft_id');
    }

    public static function crafts()
    {
        return static::where('type', 'craft')
            ->where('active', true)
            ->orderBy('sort')
            ->orderBy('id')
            ->get();
    }

    public static function editorial()
    {
        return static::where('type', 'collection')
            ->where('active', true)
            ->orderBy('sort')
            ->orderBy('id')
            ->get();
    }
}
