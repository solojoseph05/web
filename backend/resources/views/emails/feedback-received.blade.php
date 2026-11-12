<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comentario recibido</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #fff7ed; color: #374151; padding: 24px;">
    <div style="max-width: 640px; margin: 0 auto; background: #ffffff; border-radius: 16px; padding: 32px; box-shadow: 0 20px 60px -20px rgba(239, 68, 68, 0.25);">
        <h2 style="color: #dc2626; margin-top: 0;">¡Gracias por tu comentario!</h2>
        <p>Hola <strong>{{ $feedback->name }}</strong>,</p>
        <p>Hemos recibido tu <strong>{{ strtolower($feedback->type) }}</strong> en Pollo Feliz y lo revisaremos con mucho gusto.</p>

        <div style="background: #fef2f2; border: 1px solid #fecaca; padding: 16px; border-radius: 12px; margin: 24px 0;">
            <p style="margin: 0 0 8px;"><strong>Calificación:</strong> {{ $feedback->rating }}</p>
            <p style="margin: 0;"><strong>Mensaje:</strong> {{ $feedback->message }}</p>
        </div>

        <p>Gracias por ayudarnos a mejorar.</p>
        <p style="margin-bottom: 0;">Equipo Pollo Feliz</p>
    </div>
</body>
</html>
