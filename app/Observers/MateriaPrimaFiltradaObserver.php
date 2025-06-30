<?php

namespace App\Observers;

use App\Models\MateriaPrimaFiltrada;

class MateriaPrimaFiltradaObserver
{
    public function creating(MateriaPrimaFiltrada $materiaPrimaFiltrada): void
    {
        // Lógica para cuando se crea una materia prima filtrada
    }

    public function updating(MateriaPrimaFiltrada $materiaPrimaFiltrada): void
    {
        // Lógica para cuando se actualiza una materia prima filtrada
    }

    public function deleted(MateriaPrimaFiltrada $materiaPrimaFiltrada): void
    {
        // Lógica para cuando se elimina una materia prima filtrada
    }
} 