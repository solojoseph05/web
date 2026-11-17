<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Observer global de modelos para invalidar automáticamente la clave maestra de caché
 * `landing_page_data` (y sus llaves auxiliares) ante cualquier creación, edición,
 * eliminación o restauración en los módulos públicos de Pollo Feliz Durango.
 */
class LandingCacheObserver
{
    /**
     * Limpia todas las llaves de caché vinculadas a la Landing Page pública.
     */
    protected function invalidateCache(string $event, Model $model): void
    {
        $cacheKeys = [
            'landing_page_data',
            'landing_page_locations',
            'locations_active',
            'landing_page_menu',
            'landing_page_promos',
            'landing_page_jobs',
            'landing_page_settings',
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }

        Log::debug('[LandingCacheObserver] Caché landing_page_data invalidada automáticamente', [
            'event' => $event,
            'model' => $model::class,
            'model_id' => $model->getKey(),
        ]);
    }

    /**
     * Se ejecuta automáticamente al crear o actualizar cualquier registro observado.
     */
    public function saved(Model $model): void
    {
        $this->invalidateCache('saved', $model);
    }

    /**
     * Se ejecuta automáticamente al eliminar (soft o hard delete) un registro observado.
     */
    public function deleted(Model $model): void
    {
        $this->invalidateCache('deleted', $model);
    }

    /**
     * Se ejecuta automáticamente al restaurar un registro desde papelera (SoftDeletes).
     */
    public function restored(Model $model): void
    {
        $this->invalidateCache('restored', $model);
    }
}
