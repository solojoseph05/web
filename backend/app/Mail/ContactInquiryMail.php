<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerInterface;

/**
 * Mailable corporativo para despachar mensajes del formulario de contacto hacia soporte@pollofelizdgo.com.
 */
class ContactInquiryMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Instancia de logger dedicada para auditar el ciclo de vida del correo.
     */
    protected ?LoggerInterface $mailLogger = null;

    /**
     * Crea una nueva instancia del correo de contacto con sus metadatos contextuales.
     *
     * @param ContactMessage $contactMessage Registro del mensaje de contacto.
     * @param array<string, mixed> $metadata Metadatos adicionales (folio, sucursal, asunto, timestamp).
     * @param LoggerInterface|null $logger Logger PSR-3 opcional para trazabilidad dedicada.
     */
    public function __construct(
        public ContactMessage $contactMessage,
        public array $metadata = [],
        ?LoggerInterface $logger = null
    ) {
        $this->mailLogger = $logger;

        $this->logEvent('info', '[ContactInquiryMail] Instancia creada para despacho corporativo', [
            'folio' => $this->resolveFolio(),
            'contact_message_id' => $this->contactMessage->id ?? null,
            'sender_email' => $this->contactMessage->email ?? null,
            'support_address' => config('mail.support_address', 'soporte@pollofelizdgo.com'),
            'mailer_driver' => config('mail.default', 'log'),
            'smtp_host' => config('mail.mailers.smtp.host'),
            'smtp_port' => config('mail.mailers.smtp.port'),
            'is_test' => (bool) ($this->metadata['is_test'] ?? false),
        ]);
    }

    /**
     * Configura el asunto con folio y establece Reply-To hacia el cliente remitente.
     */
    public function envelope(): Envelope
    {
        $folio = $this->resolveFolio();
        $subjectLine = trim((string) ($this->metadata['subject'] ?? 'Consulta General'));

        $replyTo = [];
        $candidateEmail = trim((string) ($this->contactMessage->email ?? ''));
        if ($candidateEmail !== '' && filter_var($candidateEmail, FILTER_VALIDATE_EMAIL)) {
            try {
                $replyTo[] = new Address(
                    $candidateEmail,
                    $this->contactMessage->name ?: 'Cliente Pollo Feliz'
                );
            } catch (\Throwable $e) {
                $this->logEvent('warning', '[ContactInquiryMail] Correo Reply-To omitido por formato inválido', [
                    'folio' => $folio,
                    'candidate_email' => $candidateEmail,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $fromAddress = (string) config('mail.from.address', 'soporte@pollofelizdgo.com');
        $fromName = (string) config('mail.from.name', config('app.name', 'Pollo Feliz Durango'));

        $this->logEvent('info', '[ContactInquiryMail] Sobre (Envelope) construido', [
            'folio' => $folio,
            'from_address' => $fromAddress,
            'has_reply_to' => count($replyTo) > 0,
            'reply_to' => $replyTo[0]->address ?? null,
            'subject' => $subjectLine,
        ]);

        return new Envelope(
            from: new Address($fromAddress, $fromName),
            replyTo: $replyTo,
            subject: "[{$folio}] Nuevo mensaje de contacto: {$subjectLine} - Pollo Feliz Durango",
        );
    }

    /**
     * Define la plantilla Blade responsiva y sus variables de contexto.
     */
    public function content(): Content
    {
        $templateExists = function_exists('view') ? view()->exists('emails.contact-inquiry') : true;

        $this->logEvent($templateExists ? 'info' : 'error', '[ContactInquiryMail] Renderizando vista emails.contact-inquiry', [
            'folio' => $this->resolveFolio(),
            'contact_message_id' => $this->contactMessage->id ?? null,
            'template_exists' => $templateExists,
        ]);

        return new Content(
            view: 'emails.contact-inquiry',
            with: [
                'contactMessage' => $this->contactMessage,
                'metadata' => $this->metadata,
            ],
        );
    }

    /**
     * Registra la verificación de recursos gráficos incrustados en el correo.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $logoFound = false;
        foreach ([
            public_path('images/logo-pollofeliz.png'),
            public_path('logo.png'),
            public_path('favicon.jpg'),
        ] as $candidate) {
            if ($candidate && file_exists($candidate)) {
                $logoFound = true;
                break;
            }
        }

        $this->logEvent('debug', '[ContactInquiryMail] Verificación de recursos del correo completada', [
            'folio' => $this->resolveFolio(),
            'embedded_logo_available' => $logoFound,
        ]);

        return [];
    }

    /**
     * Captura fallos cuando el Mailable se procesa en cola y registra diagnóstico detallado.
     */
    public function failed(\Throwable $exception): void
    {
        $this->logEvent('error', '[ContactInquiryMail] Falló el envío del correo de contacto', [
            'folio' => $this->resolveFolio(),
            'contact_message_id' => $this->contactMessage->id ?? null,
            'exception' => get_class($exception),
            'error' => $exception->getMessage(),
        ]);
    }

    /**
     * Resuelve el folio del ticket de contacto.
     */
    protected function resolveFolio(): string
    {
        return (string) ($this->metadata['folio'] ?? ('TKT-' . str_pad((string) ($this->contactMessage->id ?? 1), 4, '0', STR_PAD_LEFT)));
    }

    /**
     * Resuelve el canal de logger configurado para correo con respaldo seguro al canal por defecto.
     */
    protected function resolveLogger(): LoggerInterface
    {
        if ($this->mailLogger instanceof LoggerInterface) {
            return $this->mailLogger;
        }

        $channel = config('mail.log_channel');
        if (is_string($channel) && $channel !== '') {
            try {
                return $this->mailLogger = Log::channel($channel);
            } catch (\Throwable) {
                // Respaldo al canal por defecto si el canal específico no existe
            }
        }

        return $this->mailLogger = Log::getFacadeRoot();
    }

    /**
     * Escribe un evento estructurado usando el logger de correo sin interrumpir el despacho.
     *
     * @param 'debug'|'info'|'warning'|'error' $level
     * @param array<string, mixed> $context
     */
    protected function logEvent(string $level, string $message, array $context = []): void
    {
        try {
            $logger = $this->resolveLogger();
            $logger->{$level}($message, $context);
        } catch (\Throwable) {
            // Nunca bloquear el envío SMTP por un fallo en el subsistema de logs
        }
    }
}
