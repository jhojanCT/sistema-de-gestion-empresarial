<?php

namespace App\Observers;

use App\Models\Producto;

class ProductoObserver
{
    public function creating(Producto $producto): void
    {
        // Lógica para cuando se crea un producto
    }

    public function updating(Producto $producto): void
    {
        // Lógica para cuando se actualiza un producto
    }

    public function deleted(Producto $producto): void
    {
        // Lógica para cuando se elimina un producto
    }
} 