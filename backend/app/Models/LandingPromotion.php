<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

/**
 * Modelo Eloquent para las promociones vigentes de Pollo Feliz Durango.
 */
class LandingPromotion extends Model
{
    /**
     * Atributos asignables en masa para promociones y vigencia programada.
     */
    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'image_url',
        'valid_from',
        'valid_until',
        'code',
        'terms_conditions',
        'badge',
        'discount_percentage',
        'promo_price',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'valid_from' => 'date',
            'valid_until' => 'date',
            'discount_percentage' => 'decimal:2',
            'promo_price' => 'decimal:2',
        ];
    }

    /**
     * Scope local para filtrar únicamente promociones activas dentro de su rango de vigencia.
     */
    public function scopeCurrentlyValid(Builder $query): Builder
    {
        $today = now()->toDateString();

        $query->where('is_active', true);

        if (Schema::hasColumn($this->getTable(), 'valid_from')) {
            $query->where(function (Builder $sub) use ($today): void {
                $sub->whereNull('valid_from')->orWhereDate('valid_from', '<=', $today);
            });
        }

        if (Schema::hasColumn($this->getTable(), 'valid_until')) {
            $query->where(function (Builder $sub) use ($today): void {
                $sub->whereNull('valid_until')->orWhereDate('valid_until', '>=', $today);
            });
        }

        return $query;
    }

    /**
     * Invalida la caché de la Landing Page ante cualquier cambio en promociones.
     */
    protected static function booted(): void
    {
        $clearCache = static function (): void {
            Cache::forget('landing_page_data');
            Cache::forget('landing_page_promos');
        };

        static::saved($clearCache);
        static::deleted($clearCache);
    }
}
