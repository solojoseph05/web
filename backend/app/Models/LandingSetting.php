<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Modelo clave-valor para la configuración editable de la Landing Page.
 */
class LandingSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Devuelve los ajustes en formato arreglo asociativo clave => valor.
     */
    public static function toKeyValueMap(): array
    {
        return static::query()
            ->get(['key', 'value'])
            ->pluck('value', 'key')
            ->toArray();
    }

    /**
     * Invalida la caché de la Landing Page cuando cambia algún ajuste global.
     */
    protected static function booted(): void
    {
        $clearCache = static function (): void {
            Cache::forget('landing_page_data');
            Cache::forget('landing_page_settings');
        };

        static::saved($clearCache);
        static::deleted($clearCache);
    }
}