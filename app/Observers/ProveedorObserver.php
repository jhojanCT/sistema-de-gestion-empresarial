<?php

namespace App\Observers;

use App\Models\Proveedor;

class ProveedorObserver
{
    public function creating(Proveedor $proveedor): void
    {
        // Lógica para cuando se crea un proveedor
    }

    public function updating(Proveedor $proveedor): void
    {
        // Lógica para cuando se actualiza un proveedor
    }

    public function deleted(Proveedor $proveedor): void
    {
        // Lógica para cuando se elimina un proveedor
    }
} 