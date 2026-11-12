<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request and attach enterprise security headers.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Previene MIME-sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Previene ataques de Clickjacking manteniendo compatibilidad con el mismo origen
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Protección contra Cross-Site Scripting (XSS)
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Control de fuga de información en cabecera Referer
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Restringe el acceso a APIs de hardware innecesarias
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(self)');

        // En conexiones HTTPS, activa HTTP Strict Transport Security (HSTS)
        if ($request->isSecure() || $request->header('X-Forwarded-Proto') === 'https') {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        // Remueve cabeceras que revelan la versión de PHP/Laravel
        $response->headers->remove('X-Powered-By');

        return $response;
    }
}
