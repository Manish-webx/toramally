<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'order_id', 'gateway_code', 'gateway_ref', 'gateway_payment_id',
        'amount', 'currency', 'status', 'verified', 'raw_json'
    ];

    protected $casts = [
        'amount' => 'integer',
        'verified' => 'boolean',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
