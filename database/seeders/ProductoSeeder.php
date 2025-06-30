<?php

namespace Database\Seeders;

use App\Models\Producto;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productos = [
            // Productos producidos
            [
                'nombre' => 'Café Molido Premium',
                'stock' => 50.00,
                'precio_venta' => 120.00,
                'tipo' => 'producido',
                'unidad_medida' => 'kg'
            ],
            [
                'nombre' => 'Café en Grano',
                'stock' => 30.00,
                'precio_venta' => 150.00,
                'tipo' => 'producido',
                'unidad_medida' => 'kg'
            ],
            [
                'nombre' => 'Café Instantáneo',
                'stock' => 25.00,
                'precio_venta' => 180.00,
                'tipo' => 'producido',
                'unidad_medida' => 'kg'
            ],
            [
                'nombre' => 'Café Tostado',
                'stock' => 40.00,
                'precio_venta' => 140.00,
                'tipo' => 'producido',
                'unidad_medida' => 'kg'
            ],
            [
                'nombre' => 'Café Descafeinado',
                'stock' => 20.00,
                'precio_venta' => 200.00,
                'tipo' => 'producido',
                'unidad_medida' => 'kg'
            ],

            // Productos comprados
            [
                'nombre' => 'Filtros de Café',
                'stock' => 100.00,
                'precio_venta' => 25.00,
                'tipo' => 'comprado',
                'unidad_medida' => 'unidades'
            ],
            [
                'nombre' => 'Tazas de Café',
                'stock' => 50.00,
                'precio_venta' => 35.00,
                'tipo' => 'comprado',
                'unidad_medida' => 'unidades'
            ],
            [
                'nombre' => 'Cucharas de Café',
                'stock' => 75.00,
                'precio_venta' => 15.00,
                'tipo' => 'comprado',
                'unidad_medida' => 'unidades'
            ],
            [
                'nombre' => 'Azúcar en Sobres',
                'stock' => 200.00,
                'precio_venta' => 10.00,
                'tipo' => 'comprado',
                'unidad_medida' => 'unidades'
            ],
            [
                'nombre' => 'Leche en Polvo',
                'stock' => 30.00,
                'precio_venta' => 45.00,
                'tipo' => 'comprado',
                'unidad_medida' => 'kg'
            ]
        ];

        foreach ($productos as $producto) {
            Producto::create($producto);
        }
    }
} 