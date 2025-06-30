<?php

namespace App\Observers;

use App\Models\PagoCliente;

class PagoClienteObserver
{
    public function creating(PagoCliente $pagoCliente): void
    {
        // Lógica para cuando se crea un pago de cliente
    }

    public function updating(PagoCliente $pagoCliente): void
    {
        // Lógica para cuando se actualiza un pago de cliente
    }

    public function deleted(PagoCliente $pagoCliente): void
    {
        // Lógica para cuando se elimina un pago de cliente
    }
} 