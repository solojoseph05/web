<?php

use Illuminate\Support\Facades\Route;

// Ruta inicial de la aplicación Laravel.
Route::get('/', function () {
    return view('welcome');
});

// Endpoint simple para exponer un catálogo del menú en formato JSON.
// Sirve como base para futuras integraciones con el frontend o aplicaciones móviles.
Route::get('/api/menu', function () {
    return response()->json([
        'message' => 'Menú disponible',
        'items' => [
            ['id' => 1, 'name' => 'Pollo Entero Clásico', 'category' => 'Pollo Entero', 'price' => 180],
            ['id' => 2, 'name' => 'Paquete Familiar', 'category' => 'Paquetes', 'price' => 280],
            ['id' => 3, 'name' => 'Papas a la Francesa', 'category' => 'Complementos', 'price' => 55],
            ['id' => 4, 'name' => 'Ensalada Fresca', 'category' => 'Ensaladas', 'price' => 70],
        ],
    ]);
});

