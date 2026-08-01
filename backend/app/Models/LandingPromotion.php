<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPromotion extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image_url',
        'valid_until',
        'code',
        'badge',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'valid_until' => 'date',
        ];
    }
}
