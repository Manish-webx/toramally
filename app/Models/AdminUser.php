<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminUser extends Model
{
    protected $fillable = [
        'name', 'email', 'password_hash', 'role', 'active', 'last_login_at'
    ];

    protected $casts = [
        'active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    protected $hidden = [
        'password_hash',
    ];
}
