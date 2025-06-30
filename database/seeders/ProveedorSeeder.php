<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Proveedor;

class ProveedorSeeder extends Seeder
{
    public function run(): void
    {
        $proveedores = [
            [
                'nombre' => 'Distribuidora de Materias Primas S.A.',
                'telefono' => '555-0101',
                'direccion' => 'Av. Industrial 123, Zona Industrial',
                'email' => 'contacto@distribuidora.com'
            ],
            [
                'nombre' => 'Proveedores Unidos S.R.L.',
                'telefono' => '555-0202',
                'direccion' => 'Calle Comercial 456, Centro',
                'email' => 'info@proveedoresunidos.com'
            ],
            [
                'nombre' => 'Suministros Industriales del Norte',
                'telefono' => '555-0303',
                'direccion' => 'Av. Norte 789, Zona Norte',
                'email' => 'ventas@suministrosnorte.com'
            ],
            [
                'nombre' => 'Materias Primas Express',
                'telefono' => '555-0404',
                'direccion' => 'Calle Principal 101, Zona Sur',
                'email' => 'contacto@mpexpress.com'
            ],
            [
                'nombre' => 'Proveedores Nacionales S.A.',
                'telefono' => '555-0505',
                'direccion' => 'Av. Nacional 202, Zona Este',
                'email' => 'info@provnacionales.com'
            ]
        ];

        foreach ($proveedores as $proveedor) {
            Proveedor::create($proveedor);
        }
    }
} 