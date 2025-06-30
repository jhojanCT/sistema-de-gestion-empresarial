<?php

namespace App\Observers;

use App\Models\MateriaPrimaSinFiltrar;

class MateriaPrimaSinFiltrarObserver
{
    public function creating(MateriaPrimaSinFiltrar $materiaPrimaSinFiltrar): void
    {
        // Lógica para cuando se crea una materia prima sin filtrar
    }

    public function updating(MateriaPrimaSinFiltrar $materiaPrimaSinFiltrar): void
    {
        // Lógica para cuando se actualiza una materia prima sin filtrar
    }

    public function deleted(MateriaPrimaSinFiltrar $materiaPrimaSinFiltrar): void
    {
        // Lógica para cuando se elimina una materia prima sin filtrar
    }
} 