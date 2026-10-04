<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    protected $fillable = ['customer_id', 'session_key', 'data_json'];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
