<?php

namespace Database\Seeders;

use App\Models\LandingBranch;
use App\Models\LandingJob;
use App\Models\LandingPromotion;
use App\Models\LandingSetting;
use App\Models\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

/**
 * Seeder maestro idempotente con los datos iniciales oficiales de Pollo Feliz Durango.
 */
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Ejecuta todos los seeders de catálogo, usuarios administrativos, sucursales,
     * promociones, vacantes y configuración de marca sin duplicar registros.
     */
    public function run(): void
    {
        // 1. Usuarios administrativos y catálogo base del menú
        $this->call([
            AdminUserSeeder::class,
            LandingMenuItemSeeder::class,
        ]);

        // 2. Configuración general de Landing Page y marca
        $defaultSettings = [
            'hero_title' => 'El mejor Pollo Asado de Durango',
            'hero_description' => 'Descubre el auténtico sabor que reúne a las familias. Pollo marinado con receta secreta de especias naturales y asado al carbón de mezquite.',
            'hero_image_url' => 'https://images.unsplash.com/photo-1598103442097-8b74394b95c6?w=1000',
            'company_address' => 'Av. 5 de Mayo #123, Zona Centro, Durango, Dgo.',
            'company_phone' => '618-123-4567',
            'company_email' => 'soporte@pollofelizdgo.com',
        ];

        if (Schema::hasTable('landing_settings')) {
            foreach ($defaultSettings as $key => $value) {
                LandingSetting::query()->updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }
        }

        // 3. Sucursales / Ubicaciones oficiales en Durango
        $branches = [
            [
                'name' => 'Sucursal Centro Histórico',
                'branch_name' => 'Sucursal Centro Histórico',
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
                'sort_order' => 1,
            ],
            [
                'name' => 'Sucursal Jardines de Durango',
                'branch_name' => 'Sucursal Jardines de Durango',
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
                'sort_order' => 2,
            ],
            [
                'name' => 'Sucursal Blvd. Domingo Arrieta',
                'branch_name' => 'Sucursal Blvd. Domingo Arrieta',
                'address' => 'Blvd. Domingo Arrieta #1204, Col. Juan Lira, Durango, Dgo.',
                'phone' => '618-456-7890',
                'schedule' => 'Lun - Dom: 10:00 AM - 10:00 PM',
                'latitude' => 24.015240,
                'longitude' => -104.662190,
                'map_zoom' => 16,
                'google_maps_url' => 'https://maps.google.com/?q=24.015240,-104.662190',
                'map_link' => 'https://maps.google.com/?q=24.015240,-104.662190',
                'image_url' => 'https://images.unsplash.com/photo-1552566626-52f8b828add9?w=800&auto=format&fit=crop&q=80',
                'is_active' => true,
                'is_open' => true,
                'is_coming_soon' => false,
                'is_new' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($branches as $branchData) {
            if (Schema::hasTable('landing_branches')) {
                LandingBranch::query()->updateOrCreate(
                    ['name' => $branchData['name']],
                    $branchData
                );
            }
            if (Schema::hasTable('locations')) {
                Location::query()->updateOrCreate(
                    ['branch_name' => $branchData['branch_name']],
                    $branchData
                );
            }
        }

        // 4. Promociones vigentes
        if (Schema::hasTable('landing_promotions')) {
            $promotions = [
                [
                    'title' => '2x1 en Pollo Entero',
                    'description' => 'Lleva 2 pollos enteros asados al carbón por el precio de 1. Acompañado de salsas y tortillas calientes.',
                    'image_url' => 'https://images.unsplash.com/photo-1598103442097-8b74394b95c6?w=800',
                    'valid_until' => '2026-12-31',
                    'code' => 'POLLO2X1',
                    'badge' => 'Oferta Especial',
                    'is_active' => true,
                    'sort_order' => 1,
                ],
                [
                    'title' => 'Paquete Familiar Especial',
                    'description' => 'Incluye 1 pollo entero y medio + 4 complementos grandes + refresco familiar de 2L.',
                    'image_url' => 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?w=800',
                    'valid_until' => '2026-12-31',
                    'code' => 'FAMILIA30',
                    'badge' => 'Más Vendido',
                    'is_active' => true,
                    'sort_order' => 2,
                ],
            ];

            foreach ($promotions as $promo) {
                LandingPromotion::query()->updateOrCreate(
                    ['title' => $promo['title']],
                    $promo
                );
            }
        }

        // 5. Vacantes laborales
        if (Schema::hasTable('landing_jobs')) {
            $jobs = [
                [
                    'title' => 'Parrillero de Pollo Asado al Carbón',
                    'description' => 'Manejo de parrilla al carbón de mezquite, rotación de piezas y control de tiempos de cocción.',
                    'employment_type' => 'Tiempo completo',
                    'location' => 'Sucursal Centro Histórico',
                    'schedule' => 'Lunes a Sábado: 8:00 AM - 4:30 PM',
                    'image_url' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=800&auto=format&fit=crop&q=80',
                    'is_active' => true,
                    'sort_order' => 1,
                ],
                [
                    'title' => 'Atención al Cliente & Mostrador / Caja',
                    'description' => 'Recepción de comensales, toma de pedidos en mostrador, cobro en punto de venta y entrega.',
                    'employment_type' => 'Medio tiempo',
                    'location' => 'Sucursal Jardines de Durango',
                    'schedule' => 'Viernes a Domingo: 11:00 AM - 7:00 PM',
                    'image_url' => 'https://images.unsplash.com/photo-1556740738-b6a63e27c4df?w=800&auto=format&fit=crop&q=80',
                    'is_active' => true,
                    'sort_order' => 2,
                ],
                [
                    'title' => 'Repartidor Motociclista con Moto Propia',
                    'description' => 'Entrega ágil de pedidos a domicilio en Durango con sueldo base, bono de gasolina y propinas.',
                    'employment_type' => 'Tiempo completo',
                    'location' => 'Sucursal Blvd. Domingo Arrieta',
                    'schedule' => 'Lunes a Domingo (descanso entre semana)',
                    'image_url' => 'https://images.unsplash.com/photo-1617347454431-f49d7ff5c3b1?w=800&auto=format&fit=crop&q=80',
                    'is_active' => true,
                    'sort_order' => 3,
                ],
            ];

            foreach ($jobs as $job) {
                $existing = LandingJob::withTrashed()->where('title', $job['title'])->first();
                if ($existing) {
                    if ($existing->trashed()) {
                        $existing->restore();
                    }
                    $existing->fill($job);
                    $existing->save();
                } else {
                    LandingJob::create($job);
                }
            }
        }

        Cache::forget('landing_page_data');
    }
}
