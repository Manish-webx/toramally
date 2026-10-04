<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentBlock extends Model
{
    protected $fillable = ['page', 'block_key', 'data_json', 'sort', 'visible'];

    protected $casts = [
        'sort' => 'integer',
        'visible' => 'boolean',
    ];

    public static function forPage(string $page = 'home'): array
    {
        $blocks = static::where('page', $page)
            ->where('visible', true)
            ->orderBy('sort')
            ->orderBy('id')
            ->get();

        $out = [];
        foreach ($blocks as $b) {
            $out[$b->block_key] = json_decode($b->data_json ?: '{}', true) ?: [];
        }
        return $out;
    }
}
