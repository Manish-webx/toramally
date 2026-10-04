<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = ['title', 'url', 'poster_path', 'caption', 'sort', 'visible'];

    protected $casts = [
        'sort' => 'integer',
        'visible' => 'boolean',
    ];
}
