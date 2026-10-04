<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'sort',
        'active',
    ];

    protected $casts = [
        'sort' => 'integer',
        'active' => 'boolean',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category', 'name');
    }

    public static function activeList()
    {
        return static::where('active', true)->orderBy('sort', 'asc')->orderBy('name', 'asc')->get();
    }
}
