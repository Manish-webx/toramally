<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Upload extends Model
{
    protected $fillable = [
        'owner_type', 'owner_id', 'path', 'original_name',
        'mime', 'size_bytes', 'kind'
    ];

    protected $casts = [
        'size_bytes' => 'integer',
    ];

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }
}
