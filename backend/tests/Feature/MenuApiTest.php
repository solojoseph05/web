<?php

namespace Tests\Feature;

use Tests\TestCase;

class MenuApiTest extends TestCase
{
    /**
     * Verifica que el endpoint del menú responda correctamente en JSON.
     */
    public function test_menu_api_returns_menu_payload(): void
    {
        $response = $this->get('/api/menu');

        $response->assertOk()
            ->assertJsonPath('message', 'Menú disponible')
            ->assertJsonStructure([
                'message',
                'items' => [
                    '*' => ['id', 'name', 'category', 'price'],
                ],
            ]);
    }
}
