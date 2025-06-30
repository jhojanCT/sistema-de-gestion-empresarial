<?php

namespace App\Observers;

use App\Models\Permission;

class PermissionObserver
{
    public function creating(Permission $permission): void
    {
        // Lógica para cuando se crea un permiso
    }

    public function updating(Permission $permission): void
    {
        // Lógica para cuando se actualiza un permiso
    }

    public function deleted(Permission $permission): void
    {
        // Lógica para cuando se elimina un permiso
    }
} 