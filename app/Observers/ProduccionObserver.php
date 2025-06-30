<?php

namespace App\Observers;

use App\Models\Produccion;

class ProduccionObserver
{
    public function creating(Produccion $produccion): void
    {
        // Lógica para cuando se crea una producción
    }

    public function updating(Produccion $produccion): void
    {
        // Lógica para cuando se actualiza una producción
    }

    public function deleted(Produccion $produccion): void
    {
        // Lógica para cuando se elimina una producción
    }
} 