<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingPartner extends Model
{
    protected $fillable = [
        'name', 'driver', 'scope', 'account_no',
        'credentials_enc', 'tracking_url_format', 'enabled', 'sort'
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'sort' => 'integer',
    ];
}
