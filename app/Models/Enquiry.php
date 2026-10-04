<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Enquiry extends Model
{
    protected $fillable = [
        'kind', 'name', 'email', 'phone', 'payload_json', 'status', 'ip'
    ];

    public function uploads(): MorphMany
    {
        return $this->morphMany(Upload::class, 'owner', 'owner_type', 'owner_id');
    }
}
