<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'product_id', 'name', 'colour', 'size', 'qty',
        'unit_price_inr', 'hsn_code', 'personalisation_json', 'is_custom', 'made_to_order'
    ];

    protected $casts = [
        'qty' => 'integer',
        'unit_price_inr' => 'integer',
        'is_custom' => 'boolean',
        'made_to_order' => 'boolean',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
