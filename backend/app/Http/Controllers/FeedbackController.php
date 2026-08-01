<?php

namespace App\Http\Controllers;

use App\Mail\FeedbackReceivedMail;
use App\Models\ContactMessage;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class FeedbackController extends Controller
{
    // Este endpoint recibe mensajes del landing y los persiste en dos tablas:
    // feedback para auditoría general y contact_messages para seguimiento del contacto.
    public function store(Request $request)
    {
        $payload = $request->isJson() ? $request->json()->all() : $request->all();

        $data = Validator::make($payload, [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'rating' => ['nullable', 'string', 'max:20'],
            'type' => ['nullable', 'string', 'max:50'],
            'message' => ['required', 'string', 'max:2000'],
        ])->validate();

        $data['rating'] = !empty($data['rating']) ? $data['rating'] : '5 estrellas';
        $data['type'] = !empty($data['type']) ? $data['type'] : 'Contacto';

        // Guardamos primero el feedback para conservar la información original del mensaje.
        $feedback = Feedback::create($data);

        // Luego registramos una entrada dedicada a contacto para seguimiento de estado y notas.
        $contactMessage = ContactMessage::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'message' => $data['message'],
            'phone' => $payload['phone'] ?? null,
            'source' => $payload['source'] ?? 'landing_contact',
            'status' => 'pending',
            'notes' => $payload['notes'] ?? null,
        ]);

        $supportEmail = env('FEEDBACK_NOTIFICATION_EMAIL', 'support@pollofelizdgo.com');
        $mailSent = false;

        try {
            Mail::to($supportEmail)->send(new FeedbackReceivedMail($feedback));
            $mailSent = true;
        } catch (\Throwable $exception) {
            report($exception);
        }

        return response()->json([
            'message' => $mailSent
                ? 'Gracias por tu comentario. Lo hemos recibido y lo revisaremos pronto.'
                : 'Tu comentario fue guardado correctamente, pero no pudimos enviar el correo de notificación. El registro quedó almacenado en la base de datos.',
            'feedback' => $feedback,
            'contact_message' => $contactMessage,
            'mail_sent' => $mailSent,
        ], 201);
    }
}
