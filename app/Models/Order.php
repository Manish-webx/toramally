<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'order_no', 'customer_id', 'email', 'phone', 'ship_name', 'ship_line1',
        'ship_line2', 'ship_city', 'ship_state', 'ship_postcode', 'ship_country',
        'bill_same', 'bill_json', 'gstin_buyer', 'currency', 'fx_rate',
        'subtotal_inr', 'patron_benefit_inr', 'code_benefit_inr', 'shipping_inr',
        'total_inr', 'total_charged', 'gift_note', 'status', 'payment_status',
        'gateway_code', 'is_international', 'shipping_rate_id', 'admin_notes',
        'access_token'
    ];

    protected $casts = [
        'bill_same' => 'boolean',
        'is_international' => 'boolean',
        'fx_rate' => 'decimal:4',
        'subtotal_inr' => 'integer',
        'patron_benefit_inr' => 'integer',
        'code_benefit_inr' => 'integer',
        'shipping_inr' => 'integer',
        'total_inr' => 'integer',
        'total_charged' => 'integer',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function shipment(): HasOne
    {
        return $this->hasOne(Shipment::class);
    }
}
