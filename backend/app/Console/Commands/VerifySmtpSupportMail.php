<?php

namespace App\Console\Commands;

use App\Http\Controllers\MailDiagnosticsController;
use Illuminate\Console\Command;

/**
 * Comando Artisan para verificar la conectividad SMTP configurada en .env hacia soporte@pollofelizdgo.com.
 */
class VerifySmtpSupportMail extends Command
{
    /**
     * Nombre y firma del comando en consola.
     *
     * @var string
     */
    protected $signature = 'mail:test-support
                            {to=soporte@pollofelizdgo.com : Correo destino de prueba}
                            {--dry-run : Verifica configuración .env y handshake TCP/SMTP sin enviar correo}';

    /**
     * Descripción mostrada en php artisan list.
     *
     * @var string
     */
    protected $description = 'Verifica la conectividad SMTP del archivo .env y despacha un correo de prueba hacia soporte@pollofelizdgo.com';

    /**
     * Ejecuta la verificación diagnóstica paso a paso.
     */
    public function handle(): int
    {
        $recipient = (string) $this->argument('to');
        $dryRun = (bool) $this->option('dry-run');

        $this->info("=== Diagnóstico SMTP Pollo Feliz Durango ===");
        $this->line("Destino objetivo: <comment>{$recipient}</comment>");
        $this->line("Modo: <comment>" . ($dryRun ? 'Solo verificación de conectividad (--dry-run)' : 'Prueba completa + envío de ContactInquiryMail') . "</comment>");

        $result = MailDiagnosticsController::runSmtpVerification($recipient, $dryRun);
        $cfg = $result['smtp_config'];
        $conn = $result['connectivity'];

        $this->table(
            ['Parámetro (.env)', 'Valor detectado'],
            [
                ['MAIL_MAILER', $cfg['mailer_driver']],
                ['MAIL_HOST', $cfg['host']],
                ['MAIL_PORT', (string) $cfg['port']],
                ['MAIL_ENCRYPTION', $cfg['encryption']],
                ['Credenciales SMTP', $cfg['has_credentials'] ? 'Configuradas (' . ($cfg['username_masked'] ?? '***') . ')' : 'Faltantes / null'],
                ['MAIL_FROM_ADDRESS', $cfg['from_address']],
                ['MAIL_SUPPORT_ADDRESS', $cfg['support_address']],
            ]
        );

        if ($conn['reachable']) {
            $this->info("✓ Socket SMTP accesible ({$conn['endpoint']}, {$conn['latency_ms']} ms)");
            if (!empty($conn['banner'])) {
                $this->line("  Banner: <comment>{$conn['banner']}</comment>");
            }
        } else {
            $this->error("✕ No fue posible abrir socket SMTP ({$conn['endpoint']}): " . ($conn['error'] ?? 'Error desconocido'));
        }

        if ($dryRun) {
            if ($result['ok']) {
                $this->info('✓ Configuración SMTP y conectividad verificadas correctamente.');
                return self::SUCCESS;
            }
            $this->warn('⚠ Conectividad o credenciales incompletas en .env (MAIL_MAILER=smtp, MAIL_USERNAME, MAIL_PASSWORD).');
            return self::FAILURE;
        }

        if ($result['mail_sent']) {
            $this->info("✓ Correo de prueba enviado correctamente a {$result['target_email']} (Folio: {$result['test_folio']}).");
            return self::SUCCESS;
        }

        $this->error('✕ No se pudo enviar el correo real: ' . ($result['mail_error'] ?? 'Revisa storage/logs/laravel.log'));
        return self::FAILURE;
    }
}
