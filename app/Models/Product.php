<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'slug', 'name', 'poetic', 'story', 'category', 'line', 'silhouette',
        'last_name', 'craft_id', 'personalisation_level', 'construction',
        'material', 'base_price', 'availability', 'lead_min', 'lead_max',
        'buy_mode', 'occasions', 'size_type', 'patron_eligible', 'featured',
        'drawing_json', 'hsn_code', 'weight_g', 'customs_desc', 'status',
        'seo_title', 'seo_desc', 'sort'
    ];

    protected $casts = [
        'base_price' => 'integer',
        'lead_min' => 'integer',
        'lead_max' => 'integer',
        'patron_eligible' => 'boolean',
        'featured' => 'boolean',
        'sort' => 'integer',
        'weight_g' => 'integer',
    ];

    public function craft(): BelongsTo
    {
        return $this->belongsTo(Collection::class, 'craft_id');
    }

    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(Collection::class, 'product_collection');
    }

    public function colours(): HasMany
    {
        return $this->hasMany(ProductColour::class)->orderBy('sort')->orderBy('id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort')->orderBy('id');
    }

    public function stock(): HasMany
    {
        return $this->hasMany(ProductStock::class);
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(ProductStock::class);
    }

    public function getDrawingAttribute(): array
    {
        return json_decode($this->drawing_json ?: '{}', true) ?: [];
    }

    public function getPriceAttribute(): int
    {
        return (int) ($this->base_price ?? 0);
    }

    public function getOccasionListAttribute(): array
    {
        return array_values(array_filter(array_map('trim', explode(',', (string) $this->occasions))));
    }

    public function getCraftSlugAttribute(): string
    {
        return $this->craft ? $this->craft->slug : 'patina';
    }

    public function getCraftNameAttribute(): string
    {
        return $this->craft ? $this->craft->name : '';
    }

    public function getReadySizesAttribute(): array
    {
        return $this->stock()->where('qty', '>', 0)->pluck('size')->unique()->values()->all();
    }

    public function getUrlAttribute(): string
    {
        if ($this->category === 'Service') {
            return url($this->slug);
        }
        $cat = strtolower($this->category);
        $silSlug = Str::slug($this->silhouette) . 's';
        return url("shop/{$cat}/{$silSlug}/{$this->slug}");
    }

    public function getAvailabilityLabelAttribute(): string
    {
        return $this->availability === 'Made to Order'
            ? "Made to Order, {$this->lead_min} to {$this->lead_max} weeks"
            : $this->availability;
    }

    public function getSizeOptionsAttribute(): array
    {
        if ($this->size_type === 'belt') {
            return ['80 cm', '85 cm', '90 cm', '95 cm', '100 cm', '105 cm', '110 cm'];
        }
        if ($this->size_type === 'none') {
            return [];
        }
        $women = $this->size_type === 'shoe_women';
        $out = [];
        for ($n = $women ? 3 : 5; $n <= ($women ? 9 : 12); $n += 0.5) {
            $out[] = 'UK ' . rtrim(rtrim(number_format($n, 1), '0'), '.');
        }
        return $out;
    }

    public function scopePublished($query)
    {
        return $query->whereIn('status', ['Published', 'Sold']);
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }
}
