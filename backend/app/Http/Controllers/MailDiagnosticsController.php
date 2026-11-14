<?php

namespace App\Http\Controllers;

use App\Mail\ContactInquiryMail;
use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Controlador de diagnóstico para verificar la conectividad SMTP configurada en .env
 * y probar el envío hacia soporte@pollofelizdgo.com usando ContactInquiryMail.
 */
class MailDiagnosticsController extends Controller
{
    /**
     * Ejecuta la prueba de conectividad SMTP y opcionalmente envía un correo de verificación.
     *
     * @return array<string, mixed>
     */
    public static function runSmtpVerification(?string $recipientOverride = null, bool $dryRun = false): array
    {
        $configuredSupport = trim((string) config('mail.support_address', 'soporte@pollofelizdgo.com'));
        $targetEmail = $recipientOverride && filter_var(trim($recipientOverride), FILTER_VALIDATE_EMAIL)
            ? trim($recipientOverride)
            : ($configuredSupport !== '' && filter_var($configuredSupport, FILTER_VALIDATE_EMAIL)
                ? $configuredSupport
                : 'soporte@pollofelizdgo.com');

        $mailerDriver = (string) config('mail.default', 'log');
        $smtpHost = (string) config('mail.mailers.smtp.host', '127.0.0.1');
        $smtpPort = (int) config('mail.mailers.smtp.port', 587);
        $smtpEncryption = (string) (config('mail.mailers.smtp.encryption') ?: config('mail.mailers.smtp.scheme') ?: 'tls');
        $smtpUsername = (string) config('mail.mailers.smtp.username', '');
        $smtpPassword = (string) config('mail.mailers.smtp.password', '');
        $fromAddress = (string) config('mail.from.address', 'soporte@pollofelizdgo.com');

        $hasCredentials = $smtpUsername !== '' && strtolower($smtpUsername) !== 'null'
            && $smtpPassword !== '' && strtolower($smtpPassword) !== 'null';

        Log::info('[MailDiagnostics] Iniciando prueba de conectividad SMTP', [
            'target_email' => $targetEmail,
            'mailer_driver' => $mailerDriver,
            'smtp_host' => $smtpHost,
            'smtp_port' => $smtpPort,
            'smtp_encryption' => $smtpEncryption,
            'has_credentials' => $hasCredentials,
            'from_address' => $fromAddress,
            'dry_run' => $dryRun,
        ]);

        // 1. Prueba de socket TCP/SSL al servidor SMTP definido en .env
        $socketReachable = false;
        $socketBanner = null;
        $socketError = null;
        $latencyMs = null;

        $startedAt = microtime(true);
        $protocol = ($smtpPort === 465 || strtolower($smtpEncryption) === 'ssl') ? 'ssl://' : 'tcp://';
        $endpoint = $protocol . $smtpHost . ':' . $smtpPort;

        try {
            $errno = 0;
            $errstr = '';
            $stream = @stream_socket_client($endpoint, $errno, $errstr, 6);
            $latencyMs = (int) round((microtime(true) - $startedAt) * 1000);

            if ($stream !== false) {
                stream_set_timeout($stream, 4);
                $banner = fgets($stream, 512);
                $socketReachable = true;
                $socketBanner = $banner !== false ? trim($banner) : 'Conexión TCP establecida';
                @fwrite($stream, "QUIT\r\n");
                @fclose($stream);
            } else {
                $socketError = $errstr !== '' ? "{$errstr} (código {$errno})" : "No se pudo conectar a {$endpoint}";
            }
        } catch (\Throwable $e) {
            $latencyMs = (int) round((microtime(true) - $startedAt) * 1000);
            $socketError = $e->getMessage();
        }

        // 2. Despacho de prueba mediante ContactInquiryMail si no es --dry-run
        $mailSent = false;
        $mailError = null;
        $testFolio = 'TEST-' . now()->format('His');

        if (!$dryRun) {
            try {
                // Instancia en memoria (no se guarda en base de datos) para validar plantilla y transporte real
                $testMessage = new ContactMessage([
                    'name' => 'Diagnóstico SMTP Pollo Feliz',
                    'email' => $fromAddress,
                    'phone' => '618-123-4567',
                    'message' => "Correo automático de verificación SMTP enviado hacia {$targetEmail}.\nDriver: {$mailerDriver} | Host: {$smtpHost}:{$smtpPort} ({$smtpEncryption}).",
                    'source' => 'smtp_diagnostics',
                    'status' => 'resolved',
                ]);
                $testMessage->id = 0;

                Mail::to($targetEmail)->send(new ContactInquiryMail($testMessage, [
                    'folio' => $testFolio,
                    'subject' => 'Prueba de Conectividad SMTP (.env)',
                    'branch' => 'Diagnóstico del Servidor',
                    'timestamp' => now()->format('d/m/Y H:i:s'),
                    'is_test' => true,
                ], Log::getFacadeRoot()));

                $mailSent = !in_array($mailerDriver, ['log', 'array'], true);
                if (!$mailSent) {
                    $mailError = "MAIL_MAILER está configurado como '{$mailerDriver}'; cambia MAIL_MAILER=smtp en .env para enviar correos reales.";
                }
            } catch (\Throwable $e) {
                $mailError = $e->getMessage();
                Log::error('[MailDiagnostics] Error al enviar correo de prueba SMTP', [
                    'target_email' => $targetEmail,
                    'smtp_host' => $smtpHost,
                    'smtp_port' => $smtpPort,
                    'error' => $mailError,
                ]);
            }
        }

        $ok = $dryRun
            ? ($socketReachable && $mailerDriver === 'smtp' && $hasCredentials)
            : $mailSent;

        Log::info('[MailDiagnostics] Resultado de verificación SMTP', [
            'ok' => $ok,
            'target_email' => $targetEmail,
            'socket_reachable' => $socketReachable,
            'latency_ms' => $latencyMs,
            'mail_sent' => $mailSent,
            'mail_error' => $mailError,
        ]);

        return [
            'ok' => $ok,
            'target_email' => $targetEmail,
            'dry_run' => $dryRun,
            'test_folio' => $testFolio,
            'smtp_config' => [
                'mailer_driver' => $mailerDriver,
                'host' => $smtpHost,
                'port' => $smtpPort,
                'encryption' => $smtpEncryption,
                'has_credentials' => $hasCredentials,
                'username_masked' => $smtpUsername !== '' && strtolower($smtpUsername) !== 'null'
                    ? substr($smtpUsername, 0, 3) . '***'
                    : null,
                'from_address' => $fromAddress,
                'support_address' => $configuredSupport,
            ],
            'connectivity' => [
                'endpoint' => $endpoint,
                'reachable' => $socketReachable,
                'latency_ms' => $latencyMs,
                'banner' => $socketBanner,
                'error' => $socketError,
            ],
            'mail_sent' => $mailSent,
            'mail_error' => $mailError,
            'checked_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Endpoint administrativo para verificar SMTP y enviar correo de prueba a soporte@pollofelizdgo.com.
     */
    public function testSupport(Request $request): JsonResponse
    {
        $recipient = $request->input('to', $request->query('to'));
        $dryRun = $request->boolean('dry_run', false);

        $result = self::runSmtpVerification(
            is_string($recipient) ? $recipient : null,
            $dryRun
        );

        $statusCode = $result['ok'] ? 200 : ($result['dry_run'] ? 200 : 502);

        return response()->json($result, $statusCode);
    }
}
