<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

/**
 * Modelo Eloquent para la bolsa de vacantes laborales de Pollo Feliz Durango.
 */
class LandingJob extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'employment_type',
        'location',
        'schedule',
        'image_url',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Invalida la caché pública cuando se publica, edita, elimina o restaura una vacante.
     */
    protected static function booted(): void
    {
        $clearCache = static function (): void {
            Cache::forget('landing_page_data');
            Cache::forget('landing_page_jobs');
        };

        static::saved($clearCache);
        static::deleted($clearCache);
        static::restored($clearCache);
    }
}