<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    public static function toKeyValueMap(): array
    {
        return static::query()
            ->get(['key', 'value'])
            ->pluck('value', 'key')
            ->toArray();
    }
}