<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminPermissionMiddleware
{
    private const ROLE_PERMISSIONS = [
        'super_admin' => [
            'admin.manage',
            'landing.manage',
            'landing.settings.manage',
            'landing.jobs.manage',
            'landing.menu.manage',
            'landing.promotions.manage',
            'landing.branches.manage',
            'messages.manage',
            'history.view',
            'history.revert',
            'approval.manage',
        ],
        'content_manager' => [
            'landing.manage',
            'landing.settings.manage',
            'landing.jobs.manage',
            'landing.menu.manage',
            'landing.promotions.manage',
            'landing.branches.manage',
            'messages.manage',
            'history.view',
        ],
        'support_agent' => [
            'messages.manage',
            'history.view',
        ],
    ];

    public function handle(Request $request, Closure $next, string $requiredPermission): Response
    {
        $adminUser = $request->attributes->get('adminUser');

        if (!$adminUser) {
            return response()->json([
                'message' => 'No autorizado.',
            ], 401);
        }

        $role = $adminUser->role ?: 'content_manager';
        $permissions = self::ROLE_PERMISSIONS[$role] ?? [];

        if (!in_array($requiredPermission, $permissions, true)) {
            return response()->json([
                'message' => 'No tienes permisos para esta acción.',
            ], 403);
        }

        return $next($request);
    }

    public static function permissionsForRole(?string $role): array
    {
        $normalizedRole = $role ?: 'content_manager';
        return self::ROLE_PERMISSIONS[$normalizedRole] ?? [];
    }

    public static function getAvailablePermissions(): array
    {
        return [
            'admin.manage' => 'Gestionar administradores',
            'landing.manage' => 'Gestionar todo el landing',
            'landing.settings.manage' => 'Editar configuración del sitio',
            'landing.jobs.manage' => 'Editar vacantes',
            'landing.menu.manage' => 'Editar menú y productos',
            'landing.promotions.manage' => 'Editar promociones',
            'landing.branches.manage' => 'Editar sucursales',
            'messages.manage' => 'Gestionar mensajes de contacto',
            'history.view' => 'Ver historial de cambios',
            'history.revert' => 'Deshacer cambios',
            'approval.manage' => 'Revisar y aprobar cambios',
        ];
    }
}
