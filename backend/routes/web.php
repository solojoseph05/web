<?php

use Illuminate\Support\Facades\Route;

// Ruta inicial de la aplicación Laravel.
Route::get('/', function () {
    return view('welcome');
});

// Endpoint simple para exponer un catálogo del menú en formato JSON.
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

// Enrutador de respaldo para Single Page Application (SPA /admin, /menu, etc.)
Route::get('/{any}', function () {
    if (file_exists(public_path('index.html'))) {
        return file_get_contents(public_path('index.html'));
    }
    return view('welcome');
})->where('any', '^(?!api/).*');

