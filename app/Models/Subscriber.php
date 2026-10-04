<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    public $timestamps = true;
    const UPDATED_AT = null;

    protected $fillable = ['email', 'source', 'status'];
}
