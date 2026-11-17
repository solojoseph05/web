<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}