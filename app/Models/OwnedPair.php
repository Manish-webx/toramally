<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OwnedPair extends Model
{
    protected $fillable = [
        'customer_id', 'pair_name', 'purchased_from',
        'purchase_year', 'size_uk', 'notes'
    ];

    protected $casts = [
        'purchase_year' => 'integer',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
