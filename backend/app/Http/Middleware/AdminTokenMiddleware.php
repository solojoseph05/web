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
            ->where('api_token', $hashedToken)
            ->where('is_admin', true)
            ->first();

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
