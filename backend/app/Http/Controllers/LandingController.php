<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\LandingBranch;
use App\Models\LandingMenuItem;
use App\Models\LandingPromotion;
use App\Models\LandingJob;
use App\Models\LandingSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LandingController extends Controller
{
    /**
     * Muestra la Landing Page principal con todas las sucursales, platillos, promociones y vacantes activas.
     */
    public function index()
    {
        // 1. Obtención de Sucursales / Ubicaciones Activas con caché invalidable
        $locations = Cache::remember('landing_page_locations', 3600, function () {
            // Intentar consultar Location
            try {
                $items = Location::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get();
                if ($items->count() > 0) {
                    return $items;
                }
            } catch (\Throwable $e) {
                // Fallback a LandingBranch si la tabla locations no ha sido migrada
            }

            try {
                return LandingBranch::query()
                    ->when(\Schema::hasColumn('landing_branches', 'is_active'), function ($q) {
                        $q->where('is_active', true);
                    }, function ($q) {
                        $q->where('is_open', true);
                    })
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->get();
            } catch (\Throwable $e) {
                return collect([]);
            }
        });

        // 2. Si la base de datos está vacía, proveer las sucursales iniciales oficiales de Pollo Feliz Durango
        if ($locations->isEmpty()) {
            $locations = collect([
                (object)[
                    'id' => 1,
                    'branch_name' => 'Sucursal Centro Histórico',
                    'name' => 'Sucursal Centro Histórico',
                    'address' => 'Av. 5 de Mayo #123, Zona Centro, Durango, Dgo.',
                    'phone' => '618-123-3694',
                    'schedule' => 'Lun - Dom: 10:00 AM - 10:00 PM',
                    'latitude' => 24.027724,
                    'longitude' => -104.653176,
                    'map_zoom' => 16,
                    'google_maps_url' => 'https://maps.google.com/?q=24.027724,-104.653176',
                    'map_link' => 'https://maps.google.com/?q=24.027724,-104.653176',
                    'image_url' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=800&auto=format&fit=crop&q=80',
                    'is_active' => true,
                    'is_open' => true,
                    'is_coming_soon' => false,
                    'is_new' => false,
                ],
                (object)[
                    'id' => 2,
                    'branch_name' => 'Sucursal Jardines de Durango',
                    'name' => 'Sucursal Jardines de Durango',
                    'address' => 'Blvd. Francisco Villa #456, Fracc. Jardines, Durango, Dgo.',
                    'phone' => '618-987-6543',
                    'schedule' => 'Lun - Dom: 10:00 AM - 10:00 PM',
                    'latitude' => 24.038120,
                    'longitude' => -104.649210,
                    'map_zoom' => 16,
                    'google_maps_url' => 'https://maps.google.com/?q=24.038120,-104.649210',
                    'map_link' => 'https://maps.google.com/?q=24.038120,-104.649210',
                    'image_url' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=800&auto=format&fit=crop&q=80',
                    'is_active' => true,
                    'is_open' => true,
                    'is_coming_soon' => false,
                    'is_new' => false,
                ],
                (object)[
                    'id' => 3,
                    'branch_name' => 'Sucursal Domingo Arrieta',
                    'name' => 'Sucursal Domingo Arrieta',
                    'address' => 'Prolongación Pino Suárez #789, Durango, Dgo.',
                    'phone' => '618-456-7890',
                    'schedule' => 'Lun - Dom: 10:00 AM - 9:00 PM',
                    'latitude' => 24.015420,
                    'longitude' => -104.662140,
                    'map_zoom' => 16,
                    'google_maps_url' => 'https://maps.google.com/?q=24.015420,-104.662140',
                    'map_link' => 'https://maps.google.com/?q=24.015420,-104.662140',
                    'image_url' => 'https://images.unsplash.com/photo-1552566626-52f8b828add9?w=800&auto=format&fit=crop&q=80',
                    'is_active' => true,
                    'is_open' => true,
                    'is_coming_soon' => false,
                    'is_new' => false,
                ]
            ]);
        }

        // 3. Obtener Platillos del Menú
        $menuItems = Cache::remember('landing_page_menu', 3600, function () {
            try {
                return LandingMenuItem::query()->whereNull('deleted_at')->where('is_active', true)->orderBy('sort_order')->get();
            } catch (\Throwable $e) {
                return collect([]);
            }
        });

        // 4. Obtener Promociones Vigentes
        $promotions = Cache::remember('landing_page_promos', 3600, function () {
            try {
                return LandingPromotion::query()->where('is_active', true)->orderBy('sort_order')->get();
            } catch (\Throwable $e) {
                return collect([]);
            }
        });

        // 5. Obtener Vacantes de Empleo
        $jobs = Cache::remember('landing_page_jobs', 3600, function () {
            try {
                return LandingJob::query()->whereNull('deleted_at')->where('is_active', true)->orderBy('sort_order')->get();
            } catch (\Throwable $e) {
                return collect([]);
            }
        });

        // 6. Obtener Configuración / Ajustes
        $settings = Cache::remember('landing_page_settings', 3600, function () {
            try {
                return LandingSetting::pluck('value', 'key')->all();
            } catch (\Throwable $e) {
                return [];
            }
        });

        return view('welcome', compact('locations', 'menuItems', 'promotions', 'jobs', 'settings'));
    }
}
