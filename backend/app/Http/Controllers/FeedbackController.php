<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactFeedbackRequest;
use App\Mail\ContactInquiryMail;
use App\Models\ContactMessage;
use App\Models\Feedback;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Controlador encargado de recibir mensajes del formulario de contacto y retroalimentación pública.
 */
class FeedbackController extends Controller
{
    /**
     * Valida mediante ContactFeedbackRequest, persiste transaccionalmente y despacha por correo
     * las consultas hacia soporte@pollofelizdgo.com.
     */
    public function store(ContactFeedbackRequest $request)
    {
        $payload = $request->isJson() ? $request->json()->all() : $request->all();

        Log::info('[FeedbackController] 1. Petición recibida en formulario de contacto', [
            'source' => $payload['source'] ?? 'landing_contact',
            'ip' => $request->ip(),
            'has_email' => !empty($payload['email']),
            'subject' => $payload['subject'] ?? null,
            'branch' => $payload['branch'] ?? null,
        ]);

        // Protección antispam Honeypot: si bots automatizados llenan campos trampa ocultos, responder sin persistir ni enviar correo
        if (!empty($payload['website']) || !empty($payload['_gotcha'])) {
            Log::info('[FeedbackController] Solicitud filtrada silenciosamente por trampa Honeypot', [
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'message' => '¡Gracias por comunicarte con Pollo Feliz! Tu consulta ha sido procesada.',
                'folio' => 'TKT-0000',
                'mail_sent' => false,
            ], 201);
        }

        $data = $request->validated();

        $rating = !empty($data['rating']) ? $data['rating'] : '5 estrellas';
        $type = !empty($data['type']) ? $data['type'] : ($data['subject'] ?? 'Contacto');
        $cleanMessage = trim($data['message']);
        $branch = trim((string) ($data['branch'] ?? 'Cualquier sucursal'));
        $subject = trim((string) ($data['subject'] ?? 'Consulta general'));

        try {
            [$feedback, $contactMessage] = DB::transaction(function () use ($data, $payload, $rating, $type, $cleanMessage, $branch, $subject) {
                $feedback = Feedback::create([
                    'name' => trim($data['name']),
                    'email' => strtolower(trim($data['email'])),
                    'rating' => $rating,
                    'type' => $type,
                    'message' => $cleanMessage,
                ]);

                $notes = trim(implode(' · ', array_filter([
                    $subject !== '' ? "Asunto: {$subject}" : null,
                    $branch !== '' ? "Sucursal: {$branch}" : null,
                    $payload['notes'] ?? null,
                ])));

                $contactMessage = ContactMessage::create([
                    'name' => trim($data['name']),
                    'email' => strtolower(trim($data['email'])),
                    'message' => $cleanMessage,
                    'phone' => !empty($data['phone']) ? trim((string) $data['phone']) : null,
                    'source' => $payload['source'] ?? 'landing_contact',
                    'status' => 'pending',
                    'notes' => $notes !== '' ? $notes : null,
                ]);

                return [$feedback, $contactMessage];
            });
        } catch (\Throwable $dbException) {
            Log::error('[FeedbackController] Error persistiendo mensaje de contacto en base de datos', [
                'error' => $dbException->getMessage(),
                'email' => $data['email'] ?? null,
            ]);

            return response()->json([
                'message' => 'No pudimos guardar tu mensaje temporalmente. Por favor intenta nuevamente o contáctanos por WhatsApp.',
            ], 500);
        }

        $folio = 'TKT-' . str_pad((string) $contactMessage->id, 4, '0', STR_PAD_LEFT);
        Log::info('[FeedbackController] 2. Registro guardado en BD correctamente', [
            'folio' => $folio,
            'contact_message_id' => $contactMessage->id,
            'feedback_id' => $feedback->id,
        ]);

        // Verificación de configuración de correo corporativo desde config('mail.support_address')
        $rawSupportAddress = trim((string) config('mail.support_address'));
        $supportEmail = (
            $rawSupportAddress !== '' &&
            strtolower($rawSupportAddress) !== 'null' &&
            !str_contains(strtolower($rawSupportAddress), 'example.com') &&
            filter_var($rawSupportAddress, FILTER_VALIDATE_EMAIL)
        )
            ? $rawSupportAddress
            : 'soporte@pollofelizdgo.com';

        $activeMailer = (string) config('mail.default', 'log');
        $smtpHost = (string) config('mail.mailers.smtp.host', '');
        $smtpPort = config('mail.mailers.smtp.port', 587);
        $hasSmtpAuth = !empty(config('mail.mailers.smtp.username')) && !empty(config('mail.mailers.smtp.password'));

        Log::info('[FeedbackController] 3. Configuración de correo resuelta antes de enviar', [
            'folio' => $folio,
            'config_mail_support_address' => $rawSupportAddress !== '' ? $rawSupportAddress : '(vacío)',
            'resolved_support_email' => $supportEmail,
            'default_mailer' => $activeMailer,
            'smtp_host' => $smtpHost,
            'smtp_port' => $smtpPort,
            'has_smtp_credentials' => $hasSmtpAuth,
            'from_address' => config('mail.from.address'),
        ]);

        if ($activeMailer === 'log' || $activeMailer === 'array') {
            Log::warning('[FeedbackController] El driver MAIL_MAILER actual no transmite correos externos', [
                'folio' => $folio,
                'default_mailer' => $activeMailer,
            ]);
        }

        $mailSent = false;
        $mailError = null;

        try {
            Log::info('[FeedbackController] 4. Invocando Mail::to()->send(ContactInquiryMail)', [
                'folio' => $folio,
                'recipient' => $supportEmail,
            ]);

            Mail::to($supportEmail)->send(new ContactInquiryMail($contactMessage, [
                'folio' => $folio,
                'subject' => $subject,
                'branch' => $branch,
                'timestamp' => now()->format('d/m/Y H:i:s'),
            ]));

            $mailSent = !in_array($activeMailer, ['log', 'array'], true);

            Log::info('[FeedbackController] 5. Flujo Mail::send completado sin excepción', [
                'folio' => $folio,
                'recipient' => $supportEmail,
                'mailer' => $activeMailer,
                'transmitted_externally' => $mailSent,
            ]);
        } catch (\Throwable $mailException) {
            $mailError = $mailException->getMessage();
            Log::error('[FeedbackController] Fallo en despacho SMTP de correo de contacto hacia soporte@pollofelizdgo.com', [
                'folio' => $folio,
                'recipient' => $supportEmail,
                'mailer' => $activeMailer,
                'smtp_host' => $smtpHost,
                'exception' => get_class($mailException),
                'error' => $mailError,
            ]);
        }

        return response()->json([
            'message' => $mailSent
                ? "¡Gracias por comunicarte con Pollo Feliz! Tu consulta ha sido enviada exitosamente a {$supportEmail} con el folio {$folio}."
                : "Tu mensaje quedó registrado con el folio {$folio}, aunque el servidor de correo presentó un retraso temporal. Nuestro equipo lo atenderá desde el panel.",
            'folio' => $folio,
            'recipient_email' => $supportEmail,
            'mailer_driver' => $activeMailer,
            'feedback' => $feedback,
            'contact_message' => $contactMessage,
            'mail_sent' => $mailSent,
            'mail_error' => $mailError,
        ], 201);
    }
}
