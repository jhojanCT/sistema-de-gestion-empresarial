<?php

namespace Database\Seeders;

use App\Models\CentroCosto;
use Illuminate\Database\Seeder;

class CentroCostoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Área de Producción
        $produccion = CentroCosto::create([
            'codigo' => 'PROD-001',
            'nombre' => 'Área de Producción',
            'descripcion' => 'Gestión de todos los procesos productivos del café',
            'tipo' => 'PRODUCCION',
            'es_auxiliar' => false,
            'presupuesto_mensual' => 15000.00,
            'activo' => true
        ]);

        // Subcentros de Producción
        CentroCosto::create([
            'codigo' => 'PROD-001-01',
            'nombre' => 'Tostado de Café',
            'descripcion' => 'Proceso de tostado de granos de café',
            'tipo' => 'PRODUCCION',
            'es_auxiliar' => true,
            'centro_costo_padre_id' => $produccion->id,
            'presupuesto_mensual' => 5000.00,
            'activo' => true
        ]);

        CentroCosto::create([
            'codigo' => 'PROD-001-02',
            'nombre' => 'Molienda',
            'descripcion' => 'Proceso de molienda de café tostado',
            'tipo' => 'PRODUCCION',
            'es_auxiliar' => true,
            'centro_costo_padre_id' => $produccion->id,
            'presupuesto_mensual' => 3000.00,
            'activo' => true
        ]);

        CentroCosto::create([
            'codigo' => 'PROD-001-03',
            'nombre' => 'Empaquetado',
            'descripcion' => 'Proceso de empaquetado del café molido',
            'tipo' => 'PRODUCCION',
            'es_auxiliar' => true,
            'centro_costo_padre_id' => $produccion->id,
            'presupuesto_mensual' => 2000.00,
            'activo' => true
        ]);

        // 2. Área de Filtrado
        $filtrado = CentroCosto::create([
            'codigo' => 'FILT-001',
            'nombre' => 'Área de Filtrado',
            'descripcion' => 'Gestión de procesos de filtrado y control de calidad',
            'tipo' => 'PRODUCCION',
            'es_auxiliar' => false,
            'presupuesto_mensual' => 8000.00,
            'activo' => true
        ]);

        // Subcentros de Filtrado
        CentroCosto::create([
            'codigo' => 'FILT-001-01',
            'nombre' => 'Proceso de Filtrado',
            'descripcion' => 'Operaciones de filtrado de café',
            'tipo' => 'PRODUCCION',
            'es_auxiliar' => true,
            'centro_costo_padre_id' => $filtrado->id,
            'presupuesto_mensual' => 5000.00,
            'activo' => true
        ]);

        CentroCosto::create([
            'codigo' => 'FILT-001-02',
            'nombre' => 'Control de Calidad',
            'descripcion' => 'Control de calidad del proceso de filtrado',
            'tipo' => 'PRODUCCION',
            'es_auxiliar' => true,
            'centro_costo_padre_id' => $filtrado->id,
            'presupuesto_mensual' => 3000.00,
            'activo' => true
        ]);

        // 3. Almacén
        $almacen = CentroCosto::create([
            'codigo' => 'ALM-001',
            'nombre' => 'Almacén',
            'descripcion' => 'Gestión de inventarios y almacenamiento',
            'tipo' => 'SERVICIO',
            'es_auxiliar' => false,
            'presupuesto_mensual' => 5000.00,
            'activo' => true
        ]);

        // Subcentros de Almacén
        CentroCosto::create([
            'codigo' => 'ALM-001-01',
            'nombre' => 'Almacén de Materia Prima',
            'descripcion' => 'Gestión de inventario de materia prima',
            'tipo' => 'SERVICIO',
            'es_auxiliar' => true,
            'centro_costo_padre_id' => $almacen->id,
            'presupuesto_mensual' => 2500.00,
            'activo' => true
        ]);

        CentroCosto::create([
            'codigo' => 'ALM-001-02',
            'nombre' => 'Almacén de Productos Terminados',
            'descripcion' => 'Gestión de inventario de productos terminados',
            'tipo' => 'SERVICIO',
            'es_auxiliar' => true,
            'centro_costo_padre_id' => $almacen->id,
            'presupuesto_mensual' => 2500.00,
            'activo' => true
        ]);

        // 4. Administración
        CentroCosto::create([
            'codigo' => 'ADM-001',
            'nombre' => 'Administración',
            'descripcion' => 'Gestión administrativa y financiera',
            'tipo' => 'ADMINISTRATIVO',
            'es_auxiliar' => false,
            'presupuesto_mensual' => 10000.00,
            'activo' => true
        ]);

        // 5. Ventas y Comercialización
        CentroCosto::create([
            'codigo' => 'VEN-001',
            'nombre' => 'Ventas y Comercialización',
            'descripcion' => 'Gestión comercial y ventas',
            'tipo' => 'ADMINISTRATIVO',
            'es_auxiliar' => false,
            'presupuesto_mensual' => 8000.00,
            'activo' => true
        ]);
    }
} 