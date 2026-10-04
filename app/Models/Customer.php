<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Hash;

class Customer extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'email', 'password', 'password_hash', 'first_name', 'last_name', 'phone', 'country',
        'is_patron', 'patron_since', 'marketing_opt_in', 'notes', 'status', 'last_login_at'
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    protected $casts = [
        'is_patron' => 'boolean',
        'marketing_opt_in' => 'boolean',
        'patron_since' => 'date',
        'last_login_at' => 'datetime',
    ];

    public function getAuthPasswordName(): string
    {
        return 'password_hash';
    }

    public function getAuthPassword(): string
    {
        return $this->password_hash ?? '';
    }

    public function setPasswordAttribute($value): void
    {
        $this->attributes['password_hash'] = Hash::needsRehash($value) ? Hash::make($value) : $value;
    }

    public function getFullNameAttribute(): string
    {
        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? '')) ?: ($this->email ?? 'Guest');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(CustomerAddress::class);
    }

    public function defaultAddress(): HasOne
    {
        return $this->hasOne(CustomerAddress::class)->where('is_default', true);
    }

    public function sizes(): HasMany
    {
        return $this->hasMany(CustomerSize::class);
    }

    public function ownedPairs(): HasMany
    {
        return $this->hasMany(OwnedPair::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class)->orderBy('created_at', 'desc');
    }
}
