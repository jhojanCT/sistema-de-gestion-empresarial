<?php

namespace App\Observers;

use App\Models\CierreDiario;
use Illuminate\Support\Facades\Auth;

class CierreDiarioObserver
{
    public function creating(CierreDiario $cierre): void
    {
        // Asegurar que el usuario esté asignado
        if (!$cierre->usuario_id) {
            $cierre->usuario_id = Auth::id();
        }

        // Verificar que no exista un cierre para la misma fecha
        if (CierreDiario::whereDate('fecha', $cierre->fecha)->exists()) {
            throw new \Exception('Ya existe un cierre para la fecha seleccionada.');
        }
    }

    public function updating(CierreDiario $cierre): void
    {
        // Si se está cerrando el cierre
        if ($cierre->isDirty('cerrado') && $cierre->cerrado) {
            $cierre->fecha_cierre = now();
        }
    }

    public function deleted(CierreDiario $cierre): void
    {
        // Eliminar los asientos contables relacionados
        $cierre->asientos()->delete();
    }
} 