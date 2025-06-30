<?php

namespace App\Observers;

use App\Models\Role;

class RoleObserver
{
    public function creating(Role $role): void
    {
        // Lógica para cuando se crea un rol
    }

    public function updating(Role $role): void
    {
        // Lógica para cuando se actualiza un rol
    }

    public function deleted(Role $role): void
    {
        // Lógica para cuando se elimina un rol
    }
} 