<?php

namespace App\Observers;

use App\Models\Cliente;

class ClienteObserver
{
    public function creating(Cliente $cliente): void
    {
        // Lógica para cuando se crea un cliente
    }

    public function updating(Cliente $cliente): void
    {
        // Lógica para cuando se actualiza un cliente
    }

    public function deleted(Cliente $cliente): void
    {
        // Lógica para cuando se elimina un cliente
    }
} 