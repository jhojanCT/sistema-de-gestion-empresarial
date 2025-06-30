<?php

namespace App\Observers;

use App\Models\Filtrado;

class FiltradoObserver
{
    public function creating(Filtrado $filtrado): void
    {
        // Lógica para cuando se crea un filtrado
    }

    public function updating(Filtrado $filtrado): void
    {
        // Lógica para cuando se actualiza un filtrado
    }

    public function deleted(Filtrado $filtrado): void
    {
        // Lógica para cuando se elimina un filtrado
    }
} 