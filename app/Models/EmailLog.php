<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    public $timestamps = true;
    const UPDATED_AT = null;
    protected $table = 'email_log';

    protected $fillable = [
        'to_email', 'subject', 'template', 'status', 'error'
    ];
}
