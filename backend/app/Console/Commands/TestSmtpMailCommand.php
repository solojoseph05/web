<?php

namespace App\Console\Commands;

use App\Mail\ContactInquiryMail;
use App\Models\ContactMessage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Psr\Log\LoggerInterface;

/**
 * Comando Artisan `php artisan mail:test` para verificar la conectividad SMTP
 * configurada en `.env`, intentar el envío de un correo de prueba a
 * `soporte@pollofelizdgo.com` y registrar todo el proceso en `storage/logs/laravel.log`.
 */
class TestSmtpMailCommand extends Command
{
    /**
     * Nombre y firma del comando en consola.
     *
     * @var string
     */
    protected $signature = 'mail:test
                            {to=soporte@pollofelizdgo.com : Correo destino de prueba}
                            {--dry-run : Verifica configuración .env y conexión TCP/SMTP sin enviar correo}';

    /**
     * Descripción mostrada en `php artisan list`.
     *
     * @var string
     */
    protected $description = 'Envía un correo de prueba a soporte@pollofelizdgo.com usando SMTP de .env, registra el proceso en storage/logs/laravel.log y devuelve éxito o fallo';

    /**
     * Ejecuta la prueba integral de conectividad y envío SMTP.
     */
    public function handle(): int
    {
        $logger = $this->resolveLaravelFileLogger();
        $runId = 'SMTP-' . now()->format('Ymd-His');
        $rawRecipient = trim((string) $this->argument('to'));
        $dryRun = (bool) $this->option('dry-run');

        $configuredSupport = trim((string) config('mail.support_address', 'soporte@pollofelizdgo.com'));
        $targetEmail = $rawRecipient !== '' ? $rawRecipient : ($configuredSupport !== '' ? $configuredSupport : 'soporte@pollofelizdgo.com');

        if (!filter_var($targetEmail, FILTER_VALIDATE_EMAIL)) {
            $logger->error('[mail:test] Dirección de destino inválida', [
                'run_id' => $runId,
                'target_email' => $targetEmail,
            ]);
            $this->error("✕ Dirección de correo destino inválida: {$targetEmail}");
            return self::FAILURE;
        }

        $mailerDriver = (string) config('mail.default', 'log');
        $smtpHost = trim((string) config('mail.mailers.smtp.host', '127.0.0.1'));
        $smtpPort = (int) config('mail.mailers.smtp.port', 587);
        $smtpEncryption = (string) (config('mail.mailers.smtp.encryption') ?: config('mail.mailers.smtp.scheme') ?: 'tls');
        $smtpUsername = trim((string) config('mail.mailers.smtp.username', ''));
        $smtpPassword = trim((string) config('mail.mailers.smtp.password', ''));
        $fromAddress = trim((string) config('mail.from.address', 'soporte@pollofelizdgo.com'));
        $fromName = trim((string) config('mail.from.name', config('app.name', 'Pollo Feliz Durango')));

        $hasCredentials = $smtpUsername !== ''
            && strtolower($smtpUsername) !== 'null'
            && $smtpPassword !== ''
            && strtolower($smtpPassword) !== 'null';

        $maskedUsername = ($smtpUsername !== '' && strtolower($smtpUsername) !== 'null')
            ? substr($smtpUsername, 0, 3) . '***'
            : null;

        $this->info("=== Prueba SMTP Pollo Feliz Durango ({$runId}) ===");
        $this->line("Destino: <comment>{$targetEmail}</comment>");
        $this->line("Modo: <comment>" . ($dryRun ? 'Solo verificación de conectividad (--dry-run)' : 'Envío real de correo de prueba') . "</comment>");

        $logger->info('[mail:test] 1. Iniciando prueba de conectividad y envío SMTP', [
            'run_id' => $runId,
            'target_email' => $targetEmail,
            'dry_run' => $dryRun,
            'mailer_driver' => $mailerDriver,
            'smtp_host' => $smtpHost,
            'smtp_port' => $smtpPort,
            'smtp_encryption' => $smtpEncryption,
            'smtp_username_masked' => $maskedUsername,
            'has_credentials' => $hasCredentials,
            'from_address' => $fromAddress,
            'from_name' => $fromName,
            'support_address' => $configuredSupport,
        ]);

        $this->table(
            ['Variable (.env)', 'Valor detectado'],
            [
                ['MAIL_MAILER', $mailerDriver],
                ['MAIL_HOST', $smtpHost !== '' ? $smtpHost : '(vacío)'],
                ['MAIL_PORT', (string) $smtpPort],
                ['MAIL_ENCRYPTION', $smtpEncryption],
                ['MAIL_USERNAME', $maskedUsername ?? '(sin configurar)'],
                ['MAIL_PASSWORD', ($smtpPassword !== '' && strtolower($smtpPassword) !== 'null') ? 'Configurada (***)' : '(sin configurar)'],
                ['MAIL_FROM_ADDRESS', $fromAddress],
                ['MAIL_SUPPORT_ADDRESS', $configuredSupport],
            ]
        );

        // Paso 2: Verificación de socket TCP/SSL al servidor SMTP configurado
        $protocol = ($smtpPort === 465 || strtolower($smtpEncryption) === 'ssl') ? 'ssl://' : 'tcp://';
        $endpoint = $protocol . $smtpHost . ':' . $smtpPort;
        $socketReachable = false;
        $socketBanner = null;
        $socketError = null;
        $startedAt = microtime(true);

        $logger->info('[mail:test] 2. Probando apertura de socket SMTP', [
            'run_id' => $runId,
            'endpoint' => $endpoint,
        ]);

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

                $logger->info('[mail:test] 2. Socket SMTP accesible y banner recibido', [
                    'run_id' => $runId,
                    'endpoint' => $endpoint,
                    'latency_ms' => $latencyMs,
                    'banner' => $socketBanner,
                ]);
                $this->info("✓ Conectividad TCP/SMTP verificada ({$endpoint}, {$latencyMs} ms)");
                $this->line("  Banner: <comment>{$socketBanner}</comment>");
            } else {
                $socketError = $errstr !== '' ? "{$errstr} (código {$errno})" : "No se pudo conectar a {$endpoint}";
                $logger->error('[mail:test] 2. Falló la conexión de socket SMTP', [
                    'run_id' => $runId,
                    'endpoint' => $endpoint,
                    'latency_ms' => $latencyMs,
                    'error' => $socketError,
                ]);
                $this->error("✕ No fue posible conectar al socket SMTP ({$endpoint}): {$socketError}");
            }
        } catch (\Throwable $socketException) {
            $latencyMs = (int) round((microtime(true) - $startedAt) * 1000);
            $socketError = $socketException->getMessage();
            $logger->error('[mail:test] 2. Excepción probando socket SMTP', [
                'run_id' => $runId,
                'endpoint' => $endpoint,
                'latency_ms' => $latencyMs,
                'exception' => get_class($socketException),
                'error' => $socketError,
            ]);
            $this->error("✕ Excepción en socket SMTP ({$endpoint}): {$socketError}");
        }

        if ($dryRun) {
            $dryRunOk = $socketReachable && $mailerDriver === 'smtp' && $hasCredentials;
            $logger->info('[mail:test] 3. Resultado final (--dry-run)', [
                'run_id' => $runId,
                'ok' => $dryRunOk,
                'socket_reachable' => $socketReachable,
                'has_credentials' => $hasCredentials,
                'log_file' => storage_path('logs/laravel.log'),
            ]);

            if ($dryRunOk) {
                $this->info('✓ Verificación --dry-run exitosa. Registro guardado en storage/logs/laravel.log.');
                return self::SUCCESS;
            }

            $this->error('✕ Verificación --dry-run fallida: revisa host/puerto SMTP y credenciales en .env (detalle en storage/logs/laravel.log).');
            return self::FAILURE;
        }

        // Paso 3: Intentar envío del correo de prueba usando ContactInquiryMail
        if (in_array($mailerDriver, ['log', 'array'], true)) {
            $logger->warning('[mail:test] 3. Driver de correo actual no transmite externamente', [
                'run_id' => $runId,
                'mailer_driver' => $mailerDriver,
            ]);
            $this->error("✕ MAIL_MAILER está configurado como '{$mailerDriver}'. Configura MAIL_MAILER=smtp en .env para probar envío real.");
            return self::FAILURE;
        }

        if ($mailerDriver === 'smtp' && !$hasCredentials) {
            $logger->warning('[mail:test] 3. Credenciales SMTP incompletas en .env (MAIL_USERNAME / MAIL_PASSWORD)', [
                'run_id' => $runId,
                'smtp_host' => $smtpHost,
                'smtp_port' => $smtpPort,
            ]);
        }

        $testFolio = 'TEST-' . now()->format('His');
        $testMessage = new ContactMessage([
            'name' => 'Prueba SMTP Pollo Feliz Durango',
            'email' => $fromAddress !== '' ? $fromAddress : 'soporte@pollofelizdgo.com',
            'phone' => '618-123-4567',
            'message' => "Correo de prueba ejecutado mediante `php artisan mail:test` ({$runId}).\nDestino: {$targetEmail}\nDriver: {$mailerDriver} | Host: {$smtpHost}:{$smtpPort} ({$smtpEncryption}).",
            'source' => 'artisan_mail_test',
            'status' => 'resolved',
        ]);
        $testMessage->id = 0;

        $logger->info('[mail:test] 3. Despachando ContactInquiryMail por SMTP', [
            'run_id' => $runId,
            'folio' => $testFolio,
            'target_email' => $targetEmail,
            'from_address' => $fromAddress,
            'smtp_host' => $smtpHost,
            'smtp_port' => $smtpPort,
        ]);

        try {
            Mail::to($targetEmail)->send(new ContactInquiryMail(
                $testMessage,
                [
                    'folio' => $testFolio,
                    'subject' => 'Verificación SMTP (php artisan mail:test)',
                    'branch' => 'Consola Artisan / Servidor',
                    'timestamp' => now()->format('d/m/Y H:i:s'),
                    'is_test' => true,
                ],
                $logger
            ));

            $logger->info('[mail:test] 4. Envío SMTP completado exitosamente', [
                'run_id' => $runId,
                'folio' => $testFolio,
                'target_email' => $targetEmail,
                'mailer_driver' => $mailerDriver,
                'log_file' => storage_path('logs/laravel.log'),
            ]);

            $this->info("✓ Correo de prueba enviado exitosamente a {$targetEmail} (Folio: {$testFolio}).");
            $this->line('  Proceso completo registrado en <comment>storage/logs/laravel.log</comment>.');
            return self::SUCCESS;
        } catch (\Throwable $mailException) {
            $logger->error('[mail:test] 4. Falló el envío del correo de prueba SMTP', [
                'run_id' => $runId,
                'folio' => $testFolio,
                'target_email' => $targetEmail,
                'smtp_host' => $smtpHost,
                'smtp_port' => $smtpPort,
                'exception' => get_class($mailException),
                'code' => $mailException->getCode(),
                'error' => $mailException->getMessage(),
                'trace_preview' => mb_substr($mailException->getTraceAsString(), 0, 1200),
            ]);

            $this->error('✕ Falló el envío SMTP: ' . $mailException->getMessage());
            $this->line('  Detalle completo del error registrado en <comment>storage/logs/laravel.log</comment>.');
            return self::FAILURE;
        }
    }

    /**
     * Garantiza el uso del canal `single` (`storage/logs/laravel.log`) para auditar el proceso completo.
     */
    protected function resolveLaravelFileLogger(): LoggerInterface
    {
        $logsDir = storage_path('logs');
        if (!is_dir($logsDir)) {
            @mkdir($logsDir, 0775, true);
        }

        try {
            return Log::channel('single');
        } catch (\Throwable) {
            return Log::getFacadeRoot();
        }
    }
}
