<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    public $timestamps = true;
    const UPDATED_AT = null;

    protected $fillable = ['scope', 'identifier', 'ip', 'success'];

    protected $casts = [
        'success' => 'boolean',
    ];
}
