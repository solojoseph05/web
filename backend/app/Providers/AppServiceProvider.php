<?php

namespace App\Providers;

use App\Models\Feedback;
use App\Models\LandingBranch;
use App\Models\LandingJob;
use App\Models\LandingMenuItem;
use App\Models\LandingPromotion;
use App\Models\LandingSetting;
use App\Models\Location;
use App\Models\Review;
use App\Observers\LandingCacheObserver;
use Illuminate\Support\ServiceProvider;

/**
 * Proveedor principal de servicios de la aplicación Laravel para Pollo Feliz Durango.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Registra servicios en el contenedor de dependencias.
     */
    public function register(): void
    {
        // Registro de servicios adicionales del dominio cuando aplique
    }

    /**
     * Inicializa observadores globales de modelos y configuraciones de arranque.
     */
    public function boot(): void
    {
        // La persistencia opera directamente sobre Eloquent (MySQL / PostgreSQL)
        // Registra el Observer global que invalida la llave maestra `landing_page_data`
        foreach ([
            LandingSetting::class,
            Location::class,
            LandingBranch::class,
            LandingMenuItem::class,
            LandingPromotion::class,
            LandingJob::class,
            Feedback::class,
            Review::class,
        ] as $observedModel) {
            $observedModel::observe(LandingCacheObserver::class);
        }
    }
}
