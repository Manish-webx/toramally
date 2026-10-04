<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $primaryKey = 'k';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['k', 'v'];

    public static function get(string $key, $default = '')
    {
        static $cached = null;
        if ($cached === null) {
            $cached = static::pluck('v', 'k')->all();
        }
        return (isset($cached[$key]) && $cached[$key] !== '') ? $cached[$key] : $default;
    }
}
