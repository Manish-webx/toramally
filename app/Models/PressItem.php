<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PressItem extends Model
{
    protected $fillable = ['name', 'note', 'url', 'logo_path', 'kind', 'sort', 'visible'];

    protected $casts = [
        'sort' => 'integer',
        'visible' => 'boolean',
    ];
}
