<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // No eliminar los permisos existentes, solo crear los que no existen
        $this->createPermissionIfNotExists('ver-usuarios', 'Ver lista de usuarios', 'usuarios');
        $this->createPermissionIfNotExists('crear-usuarios', 'Crear nuevos usuarios', 'usuarios');
        $this->createPermissionIfNotExists('editar-usuarios', 'Editar usuarios existentes', 'usuarios');
        $this->createPermissionIfNotExists('eliminar-usuarios', 'Eliminar usuarios', 'usuarios');

        $this->createPermissionIfNotExists('ver-roles', 'Ver lista de roles', 'roles');
        $this->createPermissionIfNotExists('crear-roles', 'Crear nuevos roles', 'roles');
        $this->createPermissionIfNotExists('editar-roles', 'Editar roles existentes', 'roles');
        $this->createPermissionIfNotExists('eliminar-roles', 'Eliminar roles', 'roles');

        $this->createPermissionIfNotExists('ver-permisos', 'Ver lista de permisos', 'permisos');
        $this->createPermissionIfNotExists('crear-permisos', 'Crear nuevos permisos', 'permisos');
        $this->createPermissionIfNotExists('editar-permisos', 'Editar permisos existentes', 'permisos');
        $this->createPermissionIfNotExists('eliminar-permisos', 'Eliminar permisos', 'permisos');

        $this->createPermissionIfNotExists('ver-cuentas-bancarias', 'Ver lista de cuentas bancarias', 'cuentas-bancarias');
        $this->createPermissionIfNotExists('crear-cuentas-bancarias', 'Crear nuevas cuentas bancarias', 'cuentas-bancarias');
        $this->createPermissionIfNotExists('editar-cuentas-bancarias', 'Editar cuentas bancarias existentes', 'cuentas-bancarias');
        $this->createPermissionIfNotExists('eliminar-cuentas-bancarias', 'Eliminar cuentas bancarias', 'cuentas-bancarias');

        $this->createPermissionIfNotExists('ver-movimientos-bancarios', 'Ver lista de movimientos bancarios', 'movimientos-bancarios');
        $this->createPermissionIfNotExists('crear-movimientos-bancarios', 'Crear nuevos movimientos bancarios', 'movimientos-bancarios');
        $this->createPermissionIfNotExists('editar-movimientos-bancarios', 'Editar movimientos bancarios existentes', 'movimientos-bancarios');
        $this->createPermissionIfNotExists('eliminar-movimientos-bancarios', 'Eliminar movimientos bancarios', 'movimientos-bancarios');

        $this->createPermissionIfNotExists('ver-proveedores', 'Ver lista de proveedores', 'proveedores');
        $this->createPermissionIfNotExists('crear-proveedores', 'Crear nuevos proveedores', 'proveedores');
        $this->createPermissionIfNotExists('editar-proveedores', 'Editar proveedores existentes', 'proveedores');
        $this->createPermissionIfNotExists('eliminar-proveedores', 'Eliminar proveedores', 'proveedores');

        $this->createPermissionIfNotExists('ver-materias-primas', 'Ver lista de materias primas', 'materias-primas');
        $this->createPermissionIfNotExists('crear-materias-primas', 'Crear nuevas materias primas', 'materias-primas');
        $this->createPermissionIfNotExists('editar-materias-primas', 'Editar materias primas existentes', 'materias-primas');
        $this->createPermissionIfNotExists('eliminar-materias-primas', 'Eliminar materias primas', 'materias-primas');

        $this->createPermissionIfNotExists('ver-productos', 'Ver lista de productos', 'productos');
        $this->createPermissionIfNotExists('crear-productos', 'Crear nuevos productos', 'productos');
        $this->createPermissionIfNotExists('editar-productos', 'Editar productos existentes', 'productos');
        $this->createPermissionIfNotExists('eliminar-productos', 'Eliminar productos', 'productos');

        $this->createPermissionIfNotExists('ver-inventario', 'Ver inventario', 'inventario');
        $this->createPermissionIfNotExists('crear-inventario', 'Crear registros de inventario', 'inventario');
        $this->createPermissionIfNotExists('editar-inventario', 'Editar registros de inventario', 'inventario');
        $this->createPermissionIfNotExists('eliminar-inventario', 'Eliminar registros de inventario', 'inventario');

        $this->createPermissionIfNotExists('ver-compras', 'Ver lista de compras', 'compras');
        $this->createPermissionIfNotExists('crear-compras', 'Crear nuevas compras', 'compras');
        $this->createPermissionIfNotExists('editar-compras', 'Editar compras existentes', 'compras');
        $this->createPermissionIfNotExists('eliminar-compras', 'Eliminar compras', 'compras');

        $this->createPermissionIfNotExists('ver-ventas', 'Ver lista de ventas', 'ventas');
        $this->createPermissionIfNotExists('crear-ventas', 'Crear nuevas ventas', 'ventas');
        $this->createPermissionIfNotExists('editar-ventas', 'Editar ventas existentes', 'ventas');
        $this->createPermissionIfNotExists('eliminar-ventas', 'Eliminar ventas', 'ventas');

        $this->createPermissionIfNotExists('ver-reportes', 'Ver reportes', 'reportes');
        $this->createPermissionIfNotExists('generar-reportes', 'Generar reportes', 'reportes');
        $this->createPermissionIfNotExists('ver-reportes-contables', 'Ver reportes contables', 'reportes');
        $this->createPermissionIfNotExists('generar-reportes-contables', 'Generar reportes contables', 'reportes');

        $this->createPermissionIfNotExists('ver-clientes', 'Ver lista de clientes', 'clientes');
        $this->createPermissionIfNotExists('crear-clientes', 'Crear nuevos clientes', 'clientes');
        $this->createPermissionIfNotExists('editar-clientes', 'Editar clientes existentes', 'clientes');
        $this->createPermissionIfNotExists('eliminar-clientes', 'Eliminar clientes', 'clientes');

        $this->createPermissionIfNotExists('ver-filtrado', 'Ver lista de filtrados', 'filtrado');
        $this->createPermissionIfNotExists('crear-filtrado', 'Crear nuevos filtrados', 'filtrado');
        $this->createPermissionIfNotExists('editar-filtrado', 'Editar filtrados existentes', 'filtrado');
        $this->createPermissionIfNotExists('eliminar-filtrado', 'Eliminar filtrados', 'filtrado');

        $this->createPermissionIfNotExists('ver-pagos', 'Ver lista de pagos', 'pagos');
        $this->createPermissionIfNotExists('crear-pagos', 'Crear nuevos pagos', 'pagos');
        $this->createPermissionIfNotExists('editar-pagos', 'Editar pagos existentes', 'pagos');
        $this->createPermissionIfNotExists('eliminar-pagos', 'Eliminar pagos', 'pagos');

        $this->createPermissionIfNotExists('ver-produccion', 'Ver lista de producción', 'produccion');
        $this->createPermissionIfNotExists('crear-produccion', 'Crear nueva producción', 'produccion');
        $this->createPermissionIfNotExists('editar-produccion', 'Editar producción existente', 'produccion');
        $this->createPermissionIfNotExists('eliminar-produccion', 'Eliminar producción', 'produccion');

        $this->createPermissionIfNotExists('ver-contabilidad', 'Ver módulo de contabilidad', 'contabilidad');
        $this->createPermissionIfNotExists('crear-contabilidad', 'Crear registros contables', 'contabilidad');
        $this->createPermissionIfNotExists('editar-contabilidad', 'Editar registros contables', 'contabilidad');
        $this->createPermissionIfNotExists('eliminar-contabilidad', 'Eliminar registros contables', 'contabilidad');
        $this->createPermissionIfNotExists('ver-reportes-contables', 'Ver reportes contables', 'contabilidad');
        $this->createPermissionIfNotExists('generar-reportes-contables', 'Generar reportes contables', 'contabilidad');

        // Permisos específicos para asientos contables
        $this->createPermissionIfNotExists('ver-asientos', 'Ver lista de asientos contables', 'contabilidad');
        $this->createPermissionIfNotExists('crear-asientos', 'Crear nuevos asientos contables', 'contabilidad');
        $this->createPermissionIfNotExists('editar-asientos', 'Editar asientos contables existentes', 'contabilidad');
        $this->createPermissionIfNotExists('eliminar-asientos', 'Eliminar asientos contables', 'contabilidad');

        $this->createPermissionIfNotExists('ver-centros-costo', 'Ver centros de costo', 'centros-costo');
        $this->createPermissionIfNotExists('crear-centros-costo', 'Crear centros de costo', 'centros-costo');
        $this->createPermissionIfNotExists('editar-centros-costo', 'Editar centros de costo', 'centros-costo');
        $this->createPermissionIfNotExists('eliminar-centros-costo', 'Eliminar centros de costo', 'centros-costo');

        // Permisos para Empleados
        $this->createPermissionIfNotExists('ver-empleados', 'Ver lista de empleados', 'empleados');
        $this->createPermissionIfNotExists('crear-empleados', 'Crear nuevos empleados', 'empleados');
        $this->createPermissionIfNotExists('editar-empleados', 'Editar empleados existentes', 'empleados');
        $this->createPermissionIfNotExists('eliminar-empleados', 'Eliminar empleados', 'empleados');

        // Permisos para Pagos de Salarios
        $this->createPermissionIfNotExists('ver-pagos-salarios', 'Ver lista de pagos de salarios', 'pagos-salarios');
        $this->createPermissionIfNotExists('crear-pagos-salarios', 'Crear nuevos pagos de salarios', 'pagos-salarios');
        $this->createPermissionIfNotExists('editar-pagos-salarios', 'Editar pagos de salarios existentes', 'pagos-salarios');
        $this->createPermissionIfNotExists('eliminar-pagos-salarios', 'Eliminar pagos de salarios', 'pagos-salarios');
        $this->createPermissionIfNotExists('generar-asiento-pagos-salarios', 'Generar asiento contable de pagos de salarios', 'pagos-salarios');

        $this->createPermissionIfNotExists('ver-molido', 'Ver procesos e inventario de molido', 'molido');

        $this->createPermissionIfNotExists('ver-produccion-especial', 'Ver producciones especiales', 'produccion-especial');
    }

    private function createPermissionIfNotExists($name, $description, $group)
    {
        Permission::firstOrCreate(
            ['name' => $name, 'guard_name' => 'web'],
            [
                'description' => $description,
                'group' => $group,
                'guard_name' => 'web'
            ]
        );
    }
} 