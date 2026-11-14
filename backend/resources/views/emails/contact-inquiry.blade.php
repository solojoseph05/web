{{-- Plantilla de correo corporativo para notificaciones a soporte@pollofelizdgo.com --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Consulta - Pollo Feliz Durango</title>
</head>
<body style="margin:0; padding:24px; background-color:#0b1120; font-family:-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color:#e2e8f0;">
    @php
        $logoCandidates = [
            public_path('images/logo-pollofeliz.png'),
            public_path('logo.png'),
            public_path('favicon.jpg'),
        ];
        $logoPath = null;
        foreach ($logoCandidates as $candidate) {
            if ($candidate && is_string($candidate) && file_exists($candidate)) {
                $logoPath = $candidate;
                break;
            }
        }
        $embeddedLogo = null;
        try {
            if ($logoPath && isset($message) && is_object($message) && method_exists($message, 'embed')) {
                $embeddedLogo = $message->embed($logoPath);
            }
        } catch (\Throwable $e) {
            $embeddedLogo = null;
        }
        $folio = $metadata['folio'] ?? ('TKT-' . str_pad((string) ($contactMessage->id ?? 1), 4, '0', STR_PAD_LEFT));
        $branch = $metadata['branch'] ?? 'Cualquier sucursal';
        $subjectText = $metadata['subject'] ?? 'Consulta general';
        $sentAt = $metadata['timestamp'] ?? now()->format('d/m/Y H:i:s');
    @endphp

    <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="max-width:640px; margin:0 auto; background-color:#0f172a; border:1px solid #1e293b; border-radius:16px; overflow:hidden;">
        {{-- Encabezado corporativo con logotipo incrustado de forma segura --}}
        <tr>
            <td style="background:linear-gradient(135deg, #b91c1c 0%, #dc2626 60%, #d97706 100%); padding:24px 28px; text-align:left;">
                <table role="presentation" cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                        <td style="vertical-align:middle;">
                            @if($embeddedLogo)
                                <img src="{{ $embeddedLogo }}" alt="Pollo Feliz Durango" style="height:44px; width:auto; border-radius:8px; vertical-align:middle; margin-right:12px;">
                            @endif
                            <span style="display:inline-block; vertical-align:middle; color:#ffffff; font-size:20px; font-weight:800; letter-spacing:0.5px;">
                                POLLO FELIZ DURANGO
                            </span>
                        </td>
                        <td style="text-align:right; vertical-align:middle;">
                            <span style="display:inline-block; background-color:rgba(15, 23, 42, 0.35); color:#fef3c7; border:1px solid rgba(254, 243, 199, 0.35); font-size:12px; font-weight:700; padding:6px 12px; border-radius:999px;">
                                Folio {{ $folio }}
                            </span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        {{-- Cuerpo principal con detalles del contacto --}}
        <tr>
            <td style="padding:28px;">
                <h1 style="margin:0 0 8px 0; font-size:19px; font-weight:800; color:#f8fafc;">
                    Nuevo mensaje recibido desde la Landing Page
                </h1>
                <p style="margin:0 0 22px 0; font-size:14px; line-height:1.5; color:#94a3b8;">
                    Puedes responder directamente a este correo gracias a la cabecera <strong style="color:#f8fafc;">Reply-To</strong> configurada con el correo del cliente.
                </p>

                <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background-color:#161e2e; border:1px solid #334155; border-radius:12px; margin-bottom:20px;">
                    <tr>
                        <td style="padding:14px 18px; border-bottom:1px solid #263248; font-size:13px;">
                            <span style="color:#94a3b8; display:inline-block; width:145px;">Nombre completo:</span>
                            <strong style="color:#f8fafc;">{{ $contactMessage->name }}</strong>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:14px 18px; border-bottom:1px solid #263248; font-size:13px;">
                            <span style="color:#94a3b8; display:inline-block; width:145px;">Correo electrónico:</span>
                            <a href="mailto:{{ $contactMessage->email }}" style="color:#f87171; font-weight:700; text-decoration:none;">{{ $contactMessage->email }}</a>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:14px 18px; border-bottom:1px solid #263248; font-size:13px;">
                            <span style="color:#94a3b8; display:inline-block; width:145px;">Teléfono / WhatsApp:</span>
                            <strong style="color:#f8fafc;">{{ $contactMessage->phone ?: 'No proporcionado' }}</strong>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:14px 18px; border-bottom:1px solid #263248; font-size:13px;">
                            <span style="color:#94a3b8; display:inline-block; width:145px;">Sucursal de interés:</span>
                            <strong style="color:#fbbf24;">{{ $branch }}</strong>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:14px 18px; border-bottom:1px solid #263248; font-size:13px;">
                            <span style="color:#94a3b8; display:inline-block; width:145px;">Asunto / Motivo:</span>
                            <strong style="color:#f8fafc;">{{ $subjectText }}</strong>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:14px 18px; font-size:13px;">
                            <span style="color:#94a3b8; display:inline-block; width:145px;">Fecha y hora exacta:</span>
                            <span style="color:#cbd5e1;">{{ $sentAt }}</span>
                        </td>
                    </tr>
                </table>

                {{-- Caja del mensaje --}}
                <div style="background-color:#161e2e; border-left:4px solid #dc2626; border-radius:8px; padding:16px 18px;">
                    <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.6px; color:#f87171; margin-bottom:8px;">
                        Contenido del Mensaje
                    </div>
                    <div style="font-size:14px; line-height:1.65; color:#f1f5f9; white-space:pre-line;">{{ $contactMessage->message }}</div>
                </div>
            </td>
        </tr>

        {{-- Pie de página --}}
        <tr>
            <td style="background-color:#0b1120; padding:16px 28px; border-top:1px solid #1e293b; text-align:center; font-size:12px; color:#64748b;">
                Sistema Automatizado de Atención al Cliente · Pollo Feliz Durango &copy; {{ date('Y') }}
            </td>
        </tr>
    </table>
</body>
</html>
