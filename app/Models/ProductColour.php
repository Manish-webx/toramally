<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductColour extends Model
{
    public $timestamps = false;

    protected $fillable = ['product_id', 'name', 'hex', 'price_diff', 'sort'];

    protected $casts = [
        'price_diff' => 'integer',
        'sort' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
