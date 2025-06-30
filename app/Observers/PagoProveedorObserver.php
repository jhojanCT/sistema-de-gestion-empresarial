<?php

namespace App\Observers;

use App\Models\PagoProveedor;

class PagoProveedorObserver
{
    public function creating(PagoProveedor $pagoProveedor): void
    {
        // Lógica para cuando se crea un pago a proveedor
    }

    public function updating(PagoProveedor $pagoProveedor): void
    {
        // Lógica para cuando se actualiza un pago a proveedor
    }

    public function deleted(PagoProveedor $pagoProveedor): void
    {
        // Lógica para cuando se elimina un pago a proveedor
    }
} 