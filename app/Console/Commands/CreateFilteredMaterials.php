<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MateriaPrimaSinFiltrar;
use App\Models\MateriaPrimaFiltrada;
use Illuminate\Support\Facades\DB;

class CreateFilteredMaterials extends Command
{
    protected $signature = 'materials:create-filtered';
    protected $description = 'Create filtered materials for existing unfiltered materials';

    public function handle()
    {
        $this->info('Iniciando creación de materias primas filtradas...');

        $materiasPrimas = MateriaPrimaSinFiltrar::all();
        $count = 0;

        foreach ($materiasPrimas as $materia) {
            // Verificar si ya existe la versión filtrada
            $materiaFiltrada = MateriaPrimaFiltrada::where('nombre', $materia->nombre . ' filtrada')->first();

            if (!$materiaFiltrada) {
                DB::transaction(function () use ($materia) {
                    MateriaPrimaFiltrada::create([
                        'nombre' => $materia->nombre . ' filtrada',
                        'unidad_medida' => $materia->unidad_medida,
                        'stock' => 0
                    ]);
                });
                $count++;
                $this->info("Creada materia prima filtrada para: {$materia->nombre}");
            }
        }

        $this->info("Proceso completado. Se crearon {$count} materias primas filtradas.");
    }
} 