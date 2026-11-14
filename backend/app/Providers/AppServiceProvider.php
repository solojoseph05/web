<?php

namespace App\Providers;

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
     * Inicializa configuraciones globales de arranque de la aplicación.
     */
    public function boot(): void
    {
        // La persistencia opera directamente sobre Eloquent (MySQL / PostgreSQL)
    }
}
