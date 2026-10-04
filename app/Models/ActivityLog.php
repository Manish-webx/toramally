<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    public $timestamps = true;
    const UPDATED_AT = null;
    protected $table = 'activity_log';

    protected $fillable = [
        'admin_id', 'action', 'entity', 'entity_id', 'details', 'ip'
    ];
}
