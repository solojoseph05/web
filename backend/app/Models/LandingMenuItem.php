<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

/**
 * Modelo Eloquent para los platillos del menú público de Pollo Feliz Durango.
 */
class LandingMenuItem extends Model
{
    use SoftDeletes;

    /**
     * Atributos asignables en masa para los platillos del menú digital.
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'image_url',
        'badge',
        'category',
        'is_featured',
        'is_sold_out',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_featured' => 'boolean',
            'is_sold_out' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Invalida automáticamente la caché de la Landing Page cuando cambia un platillo.
     */
    protected static function booted(): void
    {
        $clearCache = static function (): void {
            Cache::forget('landing_page_data');
            Cache::forget('landing_page_menu');
        };

        static::saved($clearCache);
        static::deleted($clearCache);
        static::restored($clearCache);
    }
}