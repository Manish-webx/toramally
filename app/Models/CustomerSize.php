<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerSize extends Model
{
    protected $fillable = [
        'customer_id', 'label', 'size_uk', 'last_name', 'foot_cm', 'notes'
    ];

    protected $casts = [
        'foot_cm' => 'decimal:1',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
