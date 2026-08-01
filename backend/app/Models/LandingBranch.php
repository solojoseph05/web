<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingBranch extends Model
{
    protected $fillable = [
        'name',
        'address',
        'phone',
        'schedule',
        'image_url',
        'map_link',
        'is_open',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_open' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
