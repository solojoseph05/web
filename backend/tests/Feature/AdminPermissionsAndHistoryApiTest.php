<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPermissionsAndHistoryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_permissions_are_enforced_by_role(): void
    {
        User::create([
            'name' => 'Manager',
            'email' => 'manager@example.com',
            'password' => 'password123',
            'is_admin' => true,
            'role' => 'content_manager',
            'api_token' => hash('sha256', 'token-manager'),
        ]);

        User::create([
            'name' => 'Support',
            'email' => 'support@example.com',
            'password' => 'password123',
            'is_admin' => true,
            'role' => 'support_agent',
            'api_token' => hash('sha256', 'token-support'),
        ]);

        $managerResponse = $this->postJson('/api/admin/jobs', [
            'title' => 'Cajero',
            'description' => 'Atención en sucursal.',
            'employment_type' => 'Tiempo completo',
            'location' => 'Centro',
            'schedule' => 'Matutino',
            'is_active' => true,
        ], [
            'Authorization' => 'Bearer token-manager',
        ]);

        $managerResponse->assertStatus(201);

        $supportResponse = $this->postJson('/api/admin/menu-items', [
            'name' => 'Combo Prueba',
            'description' => 'Contenido',
            'price' => 100,
            'category' => 'Paquetes',
            'is_featured' => true,
            'is_active' => true,
        ], [
            'Authorization' => 'Bearer token-support',
        ]);

        $supportResponse->assertStatus(403);
    }

    public function test_change_logs_endpoint_returns_recorded_actions(): void
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'owner@example.com',
            'password' => 'password123',
            'is_admin' => true,
            'role' => 'super_admin',
            'api_token' => hash('sha256', 'token-super'),
        ]);

        $this->putJson('/api/admin/settings', [
            'hero_title' => 'Título Historial',
            'hero_description' => 'Descripción Historial',
            'hero_image_url' => 'https://example.com/hero.webp',
            'company_address' => 'Dirección Historial',
        ], [
            'Authorization' => 'Bearer token-super',
        ])->assertStatus(200);

        $logsResponse = $this->getJson('/api/admin/change-logs?per_page=10', [
            'Authorization' => 'Bearer token-super',
        ]);

        $logsResponse->assertStatus(200)
            ->assertJsonPath('data.0.action', 'landing.settings.update');
    }

    public function test_granular_landing_permissions_are_applied_per_resource(): void
    {
        User::create([
            'name' => 'Manager',
            'email' => 'manager2@example.com',
            'password' => 'password123',
            'is_admin' => true,
            'role' => 'content_manager',
            'api_token' => hash('sha256', 'token-manager-granular'),
        ]);

        User::create([
            'name' => 'Support',
            'email' => 'support2@example.com',
            'password' => 'password123',
            'is_admin' => true,
            'role' => 'support_agent',
            'api_token' => hash('sha256', 'token-support-granular'),
        ]);

        $managerHeaders = [
            'Authorization' => 'Bearer token-manager-granular',
        ];
        $supportHeaders = [
            'Authorization' => 'Bearer token-support-granular',
        ];

        $this->putJson('/api/admin/settings', [
            'hero_title' => 'Granular title',
            'hero_description' => 'Granular description',
            'hero_image_url' => 'https://example.com/granular.webp',
            'company_address' => 'Granular address',
        ], $managerHeaders)->assertStatus(200);

        $this->postJson('/api/admin/promotions', [
            'title' => 'Promo granular',
            'description' => 'Detalle',
            'code' => 'PROMO10',
            'is_active' => true,
        ], $managerHeaders)->assertStatus(201);

        $this->putJson('/api/admin/settings', [
            'hero_title' => 'Forbidden title',
            'hero_description' => 'Forbidden description',
            'hero_image_url' => 'https://example.com/forbidden.webp',
            'company_address' => 'Forbidden address',
        ], $supportHeaders)->assertStatus(403);

        $this->postJson('/api/admin/jobs', [
            'title' => 'No autorizado',
            'description' => 'No autorizado',
            'employment_type' => 'Tiempo completo',
            'location' => 'Centro',
            'schedule' => 'Matutino',
            'is_active' => true,
        ], $supportHeaders)->assertStatus(403);
    }
}
