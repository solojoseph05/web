<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminContactMessagesApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_contact_messages_and_updates_their_status(): void
    {
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@example.com',
            'password' => 'password123',
            'is_admin' => true,
            'role' => 'support_agent',
            'api_token' => hash('sha256', 'token-admin'),
        ]);

        $headers = [
            'Authorization' => 'Bearer token-admin',
        ];

        $message = ContactMessage::create([
            'name' => 'María López',
            'email' => 'maria@example.com',
            'message' => 'Quiero hacer un pedido para mañana.',
            'phone' => '6181233694',
            'source' => 'landing_contact',
            'status' => 'pending',
        ]);

        $listResponse = $this->getJson('/api/admin/contact-messages', $headers);

        $listResponse->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonPath('0.id', $message->id)
            ->assertJsonPath('0.status', 'pending');

        $updateResponse = $this->patchJson("/api/admin/contact-messages/{$message->id}", [
            'status' => 'attended',
            'notes' => 'Se contactó por WhatsApp.',
        ], $headers);

        $updateResponse->assertStatus(200)
            ->assertJsonPath('contact_message.status', 'attended')
            ->assertJsonPath('contact_message.notes', 'Se contactó por WhatsApp.');

        $this->assertDatabaseHas('contact_messages', [
            'id' => $message->id,
            'status' => 'attended',
            'notes' => 'Se contactó por WhatsApp.',
        ]);
    }
}
