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
            || !Schema::hasColumn('users', 'two_factor_secret')
            || !Schema::hasColumn('users', 'two_factor_enabled')
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
            'two_factor_code' => ['nullable', 'string', 'size:6'],
        ]);

        $user = User::query()->where('email', $data['email'])->first();

        if (!$user || !$user->is_admin || !Hash::check($data['password'], $user->password)) {
            return response()->json([
                'message' => 'Credenciales inválidas para acceso administrativo.',
            ], 422);
        }

        if ($user->two_factor_enabled) {
            $code = $data['two_factor_code'] ?? '';
            if (!$this->verifyTotpCode($user->two_factor_secret, $code)) {
                return response()->json([
                    'message' => 'Se requiere un código 2FA válido para iniciar sesión.',
                    'requires_two_factor' => true,
                ], 428);
            }
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
                'two_factor_enabled' => (bool) $user->two_factor_enabled,
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
                'two_factor_enabled' => (bool) $user->two_factor_enabled,
                'permissions' => AdminPermissionMiddleware::permissionsForRole($user->role),
            ],
        ]);
    }

    public function setupTwoFactor(Request $request)
    {
        if ($migrationError = $this->ensureAdminColumnsExist()) {
            return $migrationError;
        }

        $adminUser = $request->attributes->get('adminUser');
        $secret = $this->generateBase32Secret();
        $adminUser->two_factor_secret = $secret;
        $adminUser->two_factor_enabled = false;
        $adminUser->two_factor_enabled_at = null;
        $adminUser->save();

        $issuer = rawurlencode('PolloFeliz Admin');
        $account = rawurlencode($adminUser->email);
        $otpauthUrl = "otpauth://totp/{$issuer}:{$account}?secret={$secret}&issuer={$issuer}&algorithm=SHA1&digits=6&period=30";

        return response()->json([
            'message' => 'Se generó un secreto 2FA. Verifícalo con un código para activarlo.',
            'secret' => $secret,
            'otpauth_url' => $otpauthUrl,
        ]);
    }

    public function enableTwoFactor(Request $request)
    {
        if ($migrationError = $this->ensureAdminColumnsExist()) {
            return $migrationError;
        }

        $data = $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $adminUser = $request->attributes->get('adminUser');
        if (!$adminUser->two_factor_secret) {
            return response()->json([
                'message' => 'Primero genera un secreto 2FA.',
            ], 422);
        }

        if (!$this->verifyTotpCode($adminUser->two_factor_secret, $data['code'])) {
            return response()->json([
                'message' => 'El código 2FA no es válido.',
            ], 422);
        }

        $adminUser->two_factor_enabled = true;
        $adminUser->two_factor_enabled_at = now();
        $adminUser->save();

        return response()->json([
            'message' => '2FA activado correctamente.',
        ]);
    }

    public function disableTwoFactor(Request $request)
    {
        if ($migrationError = $this->ensureAdminColumnsExist()) {
            return $migrationError;
        }

        $data = $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $adminUser = $request->attributes->get('adminUser');
        if (!$adminUser->two_factor_enabled || !$adminUser->two_factor_secret) {
            return response()->json([
                'message' => '2FA ya está desactivado.',
            ], 422);
        }

        if (!$this->verifyTotpCode($adminUser->two_factor_secret, $data['code'])) {
            return response()->json([
                'message' => 'El código 2FA no es válido.',
            ], 422);
        }

        $adminUser->two_factor_enabled = false;
        $adminUser->two_factor_secret = null;
        $adminUser->two_factor_enabled_at = null;
        $adminUser->save();

        return response()->json([
            'message' => '2FA desactivado correctamente.',
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
                'two_factor_enabled' => (bool) $user->two_factor_enabled,
                'permissions' => AdminPermissionMiddleware::permissionsForRole($user->role),
            ],
        ], 201);
    }

    private function generateBase32Secret(int $length = 32): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';
        for ($i = 0; $i < $length; $i += 1) {
            $secret .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }
        return $secret;
    }

    private function verifyTotpCode(?string $secret, ?string $code): bool
    {
        if (!$secret || !$code || !preg_match('/^\d{6}$/', $code)) {
            return false;
        }

        $timeSlice = (int) floor(time() / 30);
        for ($offset = -1; $offset <= 1; $offset += 1) {
            if (hash_equals($this->totpAtTime($secret, $timeSlice + $offset), $code)) {
                return true;
            }
        }

        return false;
    }

    private function totpAtTime(string $secret, int $timeSlice): string
    {
        $binarySecret = $this->base32Decode($secret);
        $time = pack('N*', 0, $timeSlice);
        $hash = hash_hmac('sha1', $time, $binarySecret, true);
        $offset = ord(substr($hash, -1)) & 0x0F;
        $segment = substr($hash, $offset, 4);
        $value = unpack('N', $segment)[1] & 0x7FFFFFFF;
        $modulo = $value % 1000000;
        return str_pad((string) $modulo, 6, '0', STR_PAD_LEFT);
    }

    private function base32Decode(string $input): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $normalized = strtoupper(preg_replace('/[^A-Z2-7]/', '', $input));
        $bits = '';

        foreach (str_split($normalized) as $char) {
            $value = strpos($alphabet, $char);
            if ($value === false) {
                continue;
            }
            $bits .= str_pad(decbin($value), 5, '0', STR_PAD_LEFT);
        }

        $binary = '';
        foreach (str_split($bits, 8) as $chunk) {
            if (strlen($chunk) === 8) {
                $binary .= chr(bindec($chunk));
            }
        }

        return $binary;
    }
}