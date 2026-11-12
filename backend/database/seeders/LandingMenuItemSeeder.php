<?php

namespace Database\Seeders;

use App\Models\LandingMenuItem;
use Illuminate\Database\Seeder;

class LandingMenuItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['name' => 'Pollo Entero Clásico', 'description' => 'Pollo asado con especias tradicionales, acompañado de salsa de la casa.', 'price' => 180, 'badge' => 'Más vendido', 'category' => 'Pollo Entero', 'sort_order' => 1],
            ['name' => 'Pollo Entero Especial', 'description' => 'Con salsa especial de la casa y especias secretas.', 'price' => 220, 'badge' => 'Premium', 'category' => 'Pollo Entero', 'sort_order' => 2],
            ['name' => 'Paquete Familiar', 'description' => 'Pollo entero + 2 complementos + refresco familiar.', 'price' => 280, 'badge' => 'Oferta', 'category' => 'Paquetes', 'sort_order' => 3],
            ['name' => 'Medio Pollo Clásico', 'description' => 'Perfecto para una persona, con guarniciones incluidas.', 'price' => 95, 'badge' => 'Popular', 'category' => 'Medio Pollo', 'sort_order' => 4],
            ['name' => 'Papas a la Francesa', 'description' => 'Crujientes y doradas, ideales para acompañar cualquier pedido.', 'price' => 55, 'badge' => 'Complemento', 'category' => 'Complementos', 'sort_order' => 5],
            ['name' => 'Papas Gajo', 'description' => 'Sabrosas papas gajo con toque de especias.', 'price' => 65, 'badge' => 'Nuevo', 'category' => 'Complementos', 'sort_order' => 6],
            ['name' => 'Papas Asadas', 'description' => 'Papas asadas con mantequilla y especias.', 'price' => 60, 'badge' => 'Recomendado', 'category' => 'Complementos', 'sort_order' => 7],
            ['name' => 'Ensalada Fresca', 'description' => 'Ensalada fresca con lechuga, tomate y aderezo ligero.', 'price' => 70, 'badge' => 'Saludable', 'category' => 'Ensaladas', 'sort_order' => 8],
            ['name' => 'Ensalada Mixta', 'description' => 'Ensalada con pollo, aguacate y vegetales frescos.', 'price' => 95, 'badge' => 'Popular', 'category' => 'Ensaladas', 'sort_order' => 9],
            ['name' => 'Ensalada de la Casa', 'description' => 'Ensalada tropical con pollo y aderezo especial.', 'price' => 100, 'badge' => 'Favorita', 'category' => 'Ensaladas', 'sort_order' => 10],
            ['name' => 'Quesadilla Especial', 'description' => 'Quesadilla rellena de queso, pollo y salsa especial.', 'price' => 85, 'badge' => 'Oferta', 'category' => 'Tacos', 'sort_order' => 11],
            ['name' => 'Tacos de Pollo', 'description' => 'Tacos servidos con cebolla, cilantro y salsa.', 'price' => 75, 'badge' => 'Clásico', 'category' => 'Tacos', 'sort_order' => 12],
            ['name' => 'Taco Carreta', 'description' => 'Tacos estilo carreta con mucho sabor.', 'price' => 80, 'badge' => 'Especial', 'category' => 'Tacos', 'sort_order' => 13],
            ['name' => 'Salsas Variadas', 'description' => 'Cuatro salsas caseras para acompañar tu pedido.', 'price' => 30, 'badge' => 'Complemento', 'category' => 'Complementos', 'sort_order' => 14],
            ['name' => 'Spaguetti con Pollo', 'description' => 'Spaguetti con pollo desmenuzado y salsa cremosa.', 'price' => 110, 'badge' => 'Nuevo', 'category' => 'Paquetes', 'sort_order' => 15],
            ['name' => 'Tortillas Calientes', 'description' => 'Tortillas recién hechas para tus tacos o complementar tu orden.', 'price' => 20, 'badge' => 'Complemento', 'category' => 'Complementos', 'sort_order' => 16],
            ['name' => 'Nuggets Dorados', 'description' => 'Nuggets crujientes perfectos para compartir.', 'price' => 95, 'badge' => 'Popular', 'category' => 'Complementos', 'sort_order' => 17],
            ['name' => 'Hamburguesa de Pollo', 'description' => 'Hamburguesa jugosa con pollo, lechuga y aderezo especial.', 'price' => 120, 'badge' => 'Recomendado', 'category' => 'Paquetes', 'sort_order' => 18],
            ['name' => 'Pollo Grande con Guarnición', 'description' => 'Pollo entero con papas y ensalada.', 'price' => 240, 'badge' => 'Promoción', 'category' => 'Paquetes', 'sort_order' => 19],
            ['name' => 'Pollo a la Brasa', 'description' => 'Porción de pollo a la brasa con salsa especial.', 'price' => 160, 'badge' => 'Clásico', 'category' => 'Pollo Entero', 'sort_order' => 20],
        ];

        foreach ($items as $item) {
            $payload = [
                ...$item,
                'is_featured' => true,
                'is_active' => true,
            ];

            $existingItem = LandingMenuItem::withTrashed()
                ->where('name', $item['name'])
                ->first();

            if ($existingItem) {
                if ($existingItem->trashed()) {
                    $existingItem->restore();
                }

                $existingItem->fill($payload);
                $existingItem->save();
                continue;
            }

            LandingMenuItem::create([
                ...$payload,
                'image_url' => null,
            ]);
        }
    }
}
