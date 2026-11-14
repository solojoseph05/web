<?php

namespace App\Http\Controllers;

use App\Models\LandingBranch;
use App\Models\LandingJob;
use App\Models\LandingMenuItem;
use App\Models\LandingPromotion;
use App\Models\LandingSetting;
use App\Models\Location;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Controlador de la Landing Page pública de Pollo Feliz Durango.
 */
class LandingController extends Controller
{
    /**
     * Renderiza la vista principal cargando dinámicamente sucursales, menú, promociones,
     * vacantes y ajustes globales mediante la clave unificada de caché `landing_page_data`.
     */
    public function index()
    {
        $landingData = Cache::remember('landing_page_data', 3600, function (): array {
            // 1. Carga dinámica de sucursales activas desde Location (con respaldo a LandingBranch solo si falta la tabla)
            $locations = collect([]);
            try {
                if (Schema::hasTable('locations')) {
                    $locations = Location::query()
                        ->where('is_active', true)
                        ->orderBy('sort_order')
                        ->orderBy('id')
                        ->get();
                } elseif (Schema::hasTable('landing_branches')) {
                    $locations = $this->loadActiveBranchesFallback();
                }
            } catch (\Throwable $e) {
                Log::warning('No se pudo consultar tabla locations en LandingController: ' . $e->getMessage());
                $locations = $this->loadActiveBranchesFallback();
            }

            // 2. Platillos activos del menú
            $menuItems = collect([]);
            try {
                if (Schema::hasTable('landing_menu_items')) {
                    $menuItems = LandingMenuItem::query()
                        ->whereNull('deleted_at')
                        ->where('is_active', true)
                        ->orderBy('sort_order')
                        ->orderBy('id')
                        ->get();
                }
            } catch (\Throwable $e) {
                Log::warning('Error consultando menú en LandingController: ' . $e->getMessage());
            }

            // 3. Promociones activas dentro de su vigencia programada
            $promotions = collect([]);
            try {
                if (Schema::hasTable('landing_promotions')) {
                    $promotions = LandingPromotion::query()
                        ->currentlyValid()
                        ->orderBy('sort_order')
                        ->orderBy('id')
                        ->get();
                }
            } catch (\Throwable $e) {
                Log::warning('Error consultando promociones en LandingController: ' . $e->getMessage());
            }

            // 4. Vacantes laborales activas
            $jobs = collect([]);
            try {
                if (Schema::hasTable('landing_jobs')) {
                    $jobs = LandingJob::query()
                        ->whereNull('deleted_at')
                        ->where('is_active', true)
                        ->orderBy('sort_order')
                        ->orderBy('id')
                        ->get();
                }
            } catch (\Throwable $e) {
                Log::warning('Error consultando vacantes en LandingController: ' . $e->getMessage());
            }

            // 5. Ajustes generales de marca y textos editables
            $settings = [];
            try {
                if (Schema::hasTable('landing_settings')) {
                    $settings = LandingSetting::toKeyValueMap();
                }
            } catch (\Throwable $e) {
                Log::warning('Error consultando ajustes en LandingController: ' . $e->getMessage());
            }

            return [
                'locations' => $locations,
                'menuItems' => $menuItems,
                'promotions' => $promotions,
                'jobs' => $jobs,
                'settings' => $settings,
            ];
        });

        return view('welcome', [
            'locations' => $landingData['locations'] ?? collect([]),
            'menuItems' => $landingData['menuItems'] ?? collect([]),
            'promotions' => $landingData['promotions'] ?? collect([]),
            'jobs' => $landingData['jobs'] ?? collect([]),
            'settings' => $landingData['settings'] ?? [],
        ]);
    }

    /**
     * Consulta de respaldo sobre `landing_branches` cuando la tabla `locations` aún no ha sido migrada.
     */
    private function loadActiveBranchesFallback()
    {
        try {
            return LandingBranch::query()
                ->when(Schema::hasColumn('landing_branches', 'is_active'), function ($q) {
                    $q->where('is_active', true);
                }, function ($q) {
                    $q->where('is_open', true);
                })
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();
        } catch (\Throwable $e) {
            Log::warning('Fallback de landing_branches falló en LandingController: ' . $e->getMessage());
            return collect([]);
        }
    }
}
