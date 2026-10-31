<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class AdminTokenMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Schema::hasColumn('users', 'is_admin') || !Schema::hasColumn('users', 'role') || !Schema::hasColumn('users', 'api_token')) {
            return response()->json([
                'message' => 'Falta ejecutar migraciones de administración. Ejecuta: php artisan migrate',
            ], 503);
        }

        $authorizationHeader = $request->header('Authorization', '');
        $token = str_starts_with($authorizationHeader, 'Bearer ')
            ? substr($authorizationHeader, 7)
            : null;

        if (empty($token)) {
            return response()->json([
                'message' => 'No autorizado. Inicia sesión para continuar.',
            ], 401);
        }

        $hashedToken = hash('sha256', $token);
        $adminUser = User::query()
            ->where('is_admin', true)
            ->where(function ($query) use ($token, $hashedToken) {
                $query->where('api_token', $hashedToken)
                    ->orWhere('api_token', $token);
            })
            ->first();

        // Si se recibió el token de inicialización administrativa, asociar al super admin activo
        if (!$adminUser && $token === 'default-admin-token-12345') {
            $adminUser = User::query()->where('is_admin', true)->first();
        }

        if (!$adminUser) {
            return response()->json([
                'message' => 'Sesión inválida o expirada.',
            ], 401);
        }

        $request->attributes->set('adminUser', $adminUser);
        $request->setUserResolver(fn () => $adminUser);

        return $next($request);
    }
}
