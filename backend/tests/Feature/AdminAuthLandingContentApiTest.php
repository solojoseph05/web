<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthLandingContentApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_bootstrap_login_and_update_landing_content(): void
    {
        $bootstrapResponse = $this->postJson('/api/admin/auth/bootstrap', [
            'name' => 'Admin Principal',
            'email' => 'owner@example.com',
            'password' => 'password123',
        ]);

        $bootstrapResponse->assertStatus(201)
            ->assertJsonPath('user.email', 'owner@example.com')
            ->assertJsonPath('user.role', 'super_admin');

        $loginResponse = $this->postJson('/api/admin/auth/login', [
            'email' => 'owner@example.com',
            'password' => 'password123',
        ]);

        $token = $loginResponse->json('token');
        $this->assertNotEmpty($token);

        $headers = [
            'Authorization' => "Bearer {$token}",
        ];

        $settingsResponse = $this->putJson('/api/admin/settings', [
            'hero_title' => 'Nuevo titulo principal',
            'hero_description' => 'Descripcion principal actualizada para la landing.',
            'hero_image_url' => 'https://example.com/hero.webp',
            'company_address' => 'Blvd. Nuevo #456, Durango',
        ], $headers);

        $settingsResponse->assertStatus(200)
            ->assertJsonPath('settings.hero_title', 'Nuevo titulo principal')
            ->assertJsonPath('settings.hero_image_url', 'https://example.com/hero.webp')
            ->assertJsonPath('settings.company_address', 'Blvd. Nuevo #456, Durango');

        $jobResponse = $this->postJson('/api/admin/jobs', [
            'title' => 'Ayudante general',
            'description' => 'Apoyo en preparacion y atencion.',
            'employment_type' => 'Tiempo completo',
            'location' => 'Sucursal Centro',
            'schedule' => 'Turno matutino',
            'image_url' => 'https://example.com/jobs/ayudante.webp',
            'is_active' => true,
            'sort_order' => 1,
        ], $headers);

        $jobResponse->assertStatus(201)
            ->assertJsonPath('job.title', 'Ayudante general');

        $itemResponse = $this->postJson('/api/admin/menu-items', [
            'name' => 'Combo Especial',
            'description' => 'Pollo con papas y refresco.',
            'price' => 199.90,
            'image_url' => 'https://example.com/combo.webp',
            'badge' => 'Nuevo',
            'category' => 'Paquetes',
            'is_featured' => true,
            'is_active' => true,
            'sort_order' => 1,
        ], $headers);

        $itemResponse->assertStatus(201)
            ->assertJsonPath('item.name', 'Combo Especial')
            ->assertJsonPath('item.price', '199.90');

        $publicContentResponse = $this->getJson('/api/landing/content');

        $publicContentResponse->assertOk()
            ->assertJsonPath('settings.hero_title', 'Nuevo titulo principal')
            ->assertJsonPath('settings.hero_image_url', 'https://example.com/hero.webp')
            ->assertJsonPath('jobs.0.title', 'Ayudante general')
            ->assertJsonPath('menu_items.0.name', 'Combo Especial')
            ->assertJsonStructure([
                'summary' => [
                    'trends' => [
                        'active_jobs' => ['recent', 'previous', 'delta'],
                        'active_menu_items' => ['recent', 'previous', 'delta'],
                        'contact_messages' => ['recent', 'previous', 'delta'],
                        'admin_users' => ['recent', 'previous', 'delta'],
                        'change_logs' => ['recent', 'previous', 'delta'],
                    ],
                ],
            ]);
    }

    public function test_super_admin_can_create_additional_admin_user(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'password123',
            'is_admin' => true,
            'role' => 'super_admin',
            'api_token' => hash('sha256', 'token-principal'),
        ]);

        $response = $this->postJson('/api/admin/users', [
            'name' => 'Admin Secundario',
            'email' => 'admin2@example.com',
            'password' => 'password123',
            'role' => 'content_manager',
        ], [
            'Authorization' => 'Bearer token-principal',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('user.email', 'admin2@example.com')
            ->assertJsonPath('user.role', 'content_manager');

        $this->assertDatabaseHas('users', [
            'email' => 'admin2@example.com',
            'is_admin' => true,
            'role' => 'content_manager',
        ]);
    }

    public function test_non_super_admin_cannot_create_admin_users(): void
    {
        User::create([
            'name' => 'Editor',
            'email' => 'editor@example.com',
            'password' => 'password123',
            'is_admin' => true,
            'role' => 'content_manager',
            'api_token' => hash('sha256', 'token-editor'),
        ]);

        $response = $this->postJson('/api/admin/users', [
            'name' => 'Otro Admin',
            'email' => 'otro-admin@example.com',
            'password' => 'password123',
            'role' => 'support_agent',
        ], [
            'Authorization' => 'Bearer token-editor',
        ]);

        $response->assertStatus(403);
    }
}
