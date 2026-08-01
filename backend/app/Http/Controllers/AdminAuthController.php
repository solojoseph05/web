<?php

namespace App\Http\Controllers;

use App\Http\Middleware\AdminPermissionMiddleware;
use App\Models\AdminChangeLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AdminAuthController extends Controller
{
    private function ensureAdminColumnsExist()
    {
        if (
            !Schema::hasColumn('users', 'is_admin')
            || !Schema::hasColumn('users', 'role')
            || !Schema::hasColumn('users', 'api_token')
        ) {
            return response()->json([
                'message' => 'Falta ejecutar migraciones de administración. Ejecuta: php artisan migrate',
            ], 503);
        }

        return null;
    }

    public function bootstrap(Request $request)
    {
        if ($migrationError = $this->ensureAdminColumnsExist()) {
            return $migrationError;
        }

        if (User::query()->where('is_admin', true)->exists()) {
            return response()->json([
                'message' => 'Ya existe una cuenta administradora.',
            ], 409);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'max:100'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'is_admin' => true,
            'role' => 'super_admin',
        ]);

        AdminChangeLog::create([
            'admin_user_id' => $user->id,
            'action' => 'admin.bootstrap',
            'target_type' => 'User',
            'target_id' => (string) $user->id,
            'description' => 'Se creó el administrador inicial del sistema.',
            'changes' => [
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);

        return response()->json([
            'message' => 'Administrador inicial creado correctamente.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'permissions' => AdminPermissionMiddleware::permissionsForRole($user->role),
            ],
        ], 201);
    }

    public function login(Request $request)
    {
        if ($migrationError = $this->ensureAdminColumnsExist()) {
            return $migrationError;
        }

        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()->where('email', $data['email'])->first();

        if (!$user || !$user->is_admin || !Hash::check($data['password'], $user->password)) {
            return response()->json([
                'message' => 'Credenciales inválidas para acceso administrativo.',
            ], 422);
        }

        $plainToken = Str::random(80);
        $user->api_token = hash('sha256', $plainToken);
        $user->save();

        return response()->json([
            'message' => 'Inicio de sesión exitoso.',
            'token' => $plainToken,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'permissions' => AdminPermissionMiddleware::permissionsForRole($user->role),
            ],
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->attributes->get('adminUser');

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'permissions' => AdminPermissionMiddleware::permissionsForRole($user->role),
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->attributes->get('adminUser');
        $user->api_token = null;
        $user->save();

        return response()->json([
            'message' => 'Sesión cerrada correctamente.',
        ]);
    }

    public function createAdminUser(Request $request)
    {
        if ($migrationError = $this->ensureAdminColumnsExist()) {
            return $migrationError;
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'max:100'],
            'role' => ['nullable', 'string', 'in:super_admin,content_manager,support_agent'],
        ]);

        $adminUser = $request->attributes->get('adminUser');
        if (!$adminUser || $adminUser->role !== 'super_admin') {
            return response()->json([
                'message' => 'Solo un super administrador puede crear usuarios administrativos.',
            ], 403);
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'is_admin' => true,
            'role' => $data['role'] ?? 'content_manager',
        ]);

        AdminChangeLog::create([
            'admin_user_id' => $adminUser->id,
            'action' => 'admin.user.create',
            'target_type' => 'User',
            'target_id' => (string) $user->id,
            'description' => 'Se creó un nuevo usuario administrador.',
            'changes' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);

        return response()->json([
            'message' => 'Usuario administrador creado correctamente.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'permissions' => AdminPermissionMiddleware::permissionsForRole($user->role),
            ],
        ], 201);
    }
}