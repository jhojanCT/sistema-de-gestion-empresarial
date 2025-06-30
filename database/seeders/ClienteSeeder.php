<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = [
            [
                'nombre' => 'Restaurante El Buen Sabor',
                'telefono' => '555-1001',
                'direccion' => 'Av. Principal 123, Centro'
            ],
            [
                'nombre' => 'Cafetería Dulce Tentación',
                'telefono' => '555-1002',
                'direccion' => 'Calle Comercial 456, Zona Norte'
            ],
            [
                'nombre' => 'Panadería La Especial',
                'telefono' => '555-1003',
                'direccion' => 'Av. Industrial 789, Zona Industrial'
            ],
            [
                'nombre' => 'Restaurante Mar y Tierra',
                'telefono' => '555-1004',
                'direccion' => 'Calle Costera 101, Zona Sur'
            ],
            [
                'nombre' => 'Cafetería Aromas del Café',
                'telefono' => '555-1005',
                'direccion' => 'Av. Comercial 202, Zona Este'
            ],
            [
                'nombre' => 'Panadería El Trigal',
                'telefono' => '555-1006',
                'direccion' => 'Calle Principal 303, Centro'
            ],
            [
                'nombre' => 'Restaurante La Parrilla',
                'telefono' => '555-1007',
                'direccion' => 'Av. Norte 404, Zona Norte'
            ],
            [
                'nombre' => 'Cafetería Delicias',
                'telefono' => '555-1008',
                'direccion' => 'Calle Sur 505, Zona Sur'
            ],
            [
                'nombre' => 'Panadería El Horno',
                'telefono' => '555-1009',
                'direccion' => 'Av. Este 606, Zona Este'
            ],
            [
                'nombre' => 'Restaurante Sabores del Mundo',
                'telefono' => '555-1010',
                'direccion' => 'Calle Oeste 707, Zona Oeste'
            ]
        ];

        foreach ($clientes as $cliente) {
            Cliente::create($cliente);
        }
    }
} 