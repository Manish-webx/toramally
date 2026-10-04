<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    protected $fillable = [
        'code', 'name', 'enabled', 'mode', 'region',
        'credentials_enc', 'display_label', 'sort'
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'sort' => 'integer',
    ];
}
