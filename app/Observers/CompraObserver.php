<?php

namespace App\Observers;

use App\Models\Compra;

class CompraObserver
{
    public function creating(Compra $compra): void
    {
        // Lógica para cuando se crea una compra
    }

    public function updating(Compra $compra): void
    {
        // Lógica para cuando se actualiza una compra
    }

    public function deleted(Compra $compra): void
    {
        // Lógica para cuando se elimina una compra
    }
} 