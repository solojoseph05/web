<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_BOOTSTRAP_EMAIL');
        $password = env('ADMIN_BOOTSTRAP_PASSWORD');
        $name = env('ADMIN_BOOTSTRAP_NAME', 'Administrador');

        if (!$email || !$password) {
            return;
        }

        User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => $password,
                'is_admin' => true,
                'role' => 'super_admin',
            ]
        );
    }
}