<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admins = [
            [
                'name' => env('ADMIN_BOOTSTRAP_NAME', 'Super Administrador'),
                'email' => env('ADMIN_BOOTSTRAP_EMAIL', 'admin@pollofeliz.com'),
                'password' => env('ADMIN_BOOTSTRAP_PASSWORD', 'admin123456'),
                'role' => 'super_admin',
            ],
            [
                'name' => 'Joseph Admin',
                'email' => 'josep_lityo@hotmail.com',
                'password' => 'admin123456',
                'role' => 'super_admin',
            ],
            [
                'name' => 'Lic. Roberto Garza',
                'email' => 'gerente@pollofeliz.com',
                'password' => 'gerente123',
                'role' => 'gerente',
            ],
            [
                'name' => 'Chef Manuel Parra',
                'email' => 'cocina@pollofeliz.com',
                'password' => 'cocina123',
                'role' => 'cocina',
            ],
            [
                'name' => 'Valeria Soto',
                'email' => 'soporte@pollofeliz.com',
                'password' => 'soporte123',
                'role' => 'atencion',
            ],
            [
                'name' => 'Andrea Morales',
                'email' => 'cajero@pollofeliz.com',
                'password' => 'cajero123',
                'role' => 'cajero',
            ],
        ];

        foreach ($admins as $item) {
            User::query()->updateOrCreate(
                ['email' => strtolower($item['email'])],
                [
                    'name' => $item['name'],
                    'password' => $item['password'],
                    'is_admin' => true,
                    'role' => $item['role'],
                ]
            );
        }
    }
}