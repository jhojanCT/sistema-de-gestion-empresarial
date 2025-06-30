<?php

namespace App\Observers;

use App\Models\Venta;

class VentaObserver
{
    public function creating(Venta $venta): void
    {
        // Lógica para cuando se crea una venta
    }

    public function updating(Venta $venta): void
    {
        // Lógica para cuando se actualiza una venta
    }

    public function deleted(Venta $venta): void
    {
        // Lógica para cuando se elimina una venta
    }
} 