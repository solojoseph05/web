<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class LandingBranch extends Model
{
    protected $table = 'landing_branches';

    protected $fillable = [
        'name',
        'branch_name',
        'address',
        'phone',
        'schedule',
        'latitude',
        'longitude',
        'map_zoom',
        'google_maps_url',
        'image_url',
        'map_link',
        'is_active',
        'is_open',
        'is_coming_soon',
        'is_new',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'map_zoom' => 'integer',
            'is_active' => 'boolean',
            'is_open' => 'boolean',
            'is_coming_soon' => 'boolean',
            'is_new' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function getBranchNameAttribute($value): string
    {
        return $value ?: ($this->attributes['name'] ?? '');
    }

    public function getNameAttribute($value): string
    {
        return $value ?: ($this->attributes['branch_name'] ?? '');
    }

    public function getGoogleMapsUrlAttribute($value): string
    {
        return $value ?: ($this->attributes['map_link'] ?? '');
    }

    public function getMapLinkAttribute($value): string
    {
        return $value ?: ($this->attributes['google_maps_url'] ?? '');
    }

    /**
     * Model Observer / Booted Events para invalidación automática de caché de la Landing Page
     */
    protected static function booted(): void
    {
        static::saved(function ($branch) {
            Cache::forget('landing_page_data');
            Cache::forget('landing_page_locations');
            Cache::forget('locations_active');
        });

        static::deleted(function ($branch) {
            Cache::forget('landing_page_data');
            Cache::forget('landing_page_locations');
            Cache::forget('locations_active');
        });
    }
}
