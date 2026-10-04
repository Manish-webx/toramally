<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Commission extends Model
{
    protected $fillable = [
        'ref', 'customer_id', 'name', 'contact', 'build_json',
        'estimate_inr', 'quote_inr', 'status', 'admin_notes'
    ];

    protected $casts = [
        'estimate_inr' => 'integer',
        'quote_inr' => 'integer',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function uploads(): MorphMany
    {
        return $this->morphMany(Upload::class, 'owner', 'owner_type', 'owner_id');
    }
}
