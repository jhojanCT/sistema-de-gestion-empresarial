<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Obtener o crear roles
        $admin = Role::firstOrCreate(
            ['name' => 'admin'],
            ['description' => 'Administrador del sistema', 'guard_name' => 'web']
        );
        
        // Asignar todos los permisos al rol Administrador
        $admin->syncPermissions(Permission::all());

        // Obtener o crear rol de inventario
        $inventario = Role::firstOrCreate(
            ['name' => 'inventario'],
            ['description' => 'Usuario de inventario', 'guard_name' => 'web']
        );

        // Asignar permisos de inventario
        $inventario->syncPermissions([
            'ver-materias-primas', 'crear-materias-primas', 'editar-materias-primas', 'eliminar-materias-primas',
            'ver-productos', 'crear-productos', 'editar-productos', 'eliminar-productos',
            'ver-inventario', 'crear-inventario', 'editar-inventario', 'eliminar-inventario',
            'ver-reportes'
        ]);

        // Obtener o crear rol de ventas
        $ventas = Role::firstOrCreate(
            ['name' => 'ventas'],
            ['description' => 'Usuario de ventas', 'guard_name' => 'web']
        );

        // Asignar permisos de ventas
        $ventas->syncPermissions([
            'ver-productos',
            'ver-ventas', 'crear-ventas', 'editar-ventas', 'eliminar-ventas',
            'ver-clientes', 'crear-clientes', 'editar-clientes', 'eliminar-clientes',
            'ver-reportes',
            'ver-cuentas-bancarias'
        ]);

        // Obtener o crear rol de producción
        $produccion = Role::firstOrCreate(
            ['name' => 'produccion'],
            ['description' => 'Usuario de producción', 'guard_name' => 'web']
        );

        // Asignar permisos de producción
        $produccion->syncPermissions([
            'ver-materias-primas',
            'ver-productos', 'crear-productos', 'editar-productos',
            'ver-inventario',
            'ver-produccion', 'crear-produccion', 'editar-produccion',
            'ver-reportes',
            'ver-cuentas-bancarias'
        ]);

        $admin->givePermissionTo([
            'ver-reportes',
            'generar-reportes',
            'ver-reportes-contables',
            'generar-reportes-contables',
            'ver-contabilidad',
            'crear-contabilidad',
            'editar-contabilidad',
            'eliminar-contabilidad',
            'ver-centros-costo',
            'crear-centros-costo',
            'editar-centros-costo',
            'eliminar-centros-costo',
        ]);
    }
} 