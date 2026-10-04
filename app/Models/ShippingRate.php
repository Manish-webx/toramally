<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShippingRate extends Model
{
    protected $fillable = [
        'scope', 'name', 'countries', 'rate_inr', 'free_above_inr',
        'partner_id', 'eta_text', 'enabled', 'sort'
    ];

    protected $casts = [
        'rate_inr' => 'integer',
        'free_above_inr' => 'integer',
        'enabled' => 'boolean',
        'sort' => 'integer',
    ];

    public function partner(): BelongsTo
    {
        return $this->belongsTo(ShippingPartner::class, 'partner_id');
    }
}
