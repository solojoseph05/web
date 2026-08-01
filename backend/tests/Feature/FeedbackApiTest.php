<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class FeedbackApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_stores_feedback_and_sends_an_email(): void
    {
        Mail::fake();

        $payload = [
            'name' => 'Ana Torres',
            'email' => 'ana@example.com',
            'rating' => '5 estrellas',
            'type' => 'Sugerencia',
            'message' => 'Me gustó mucho el servicio.',
        ];

        $response = $this->postJson('/api/feedback', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('message', 'Gracias por tu comentario. Lo hemos recibido y lo revisaremos pronto.')
            ->assertJsonPath('mail_sent', true);

        $this->assertDatabaseHas('feedbacks', [
            'email' => 'ana@example.com',
            'type' => 'Sugerencia',
        ]);

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'ana@example.com',
            'status' => 'pending',
            'source' => 'landing_contact',
        ]);
    }

    public function test_it_uses_contact_defaults_when_rating_and_type_are_missing(): void
    {
        Mail::fake();

        $payload = [
            'name' => 'Luis Pérez',
            'email' => 'luis@example.com',
            'message' => 'Quiero información de horarios.',
        ];

        $response = $this->postJson('/api/feedback', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('message', 'Gracias por tu comentario. Lo hemos recibido y lo revisaremos pronto.')
            ->assertJsonPath('mail_sent', true);

        $this->assertDatabaseHas('feedbacks', [
            'email' => 'luis@example.com',
            'rating' => '5 estrellas',
            'type' => 'Contacto',
        ]);

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'luis@example.com',
            'status' => 'pending',
            'source' => 'landing_contact',
        ]);
    }
}
