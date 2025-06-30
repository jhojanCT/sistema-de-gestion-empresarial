<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    public function creating(User $user): void
    {
        // Lógica para cuando se crea un usuario
    }

    public function updating(User $user): void
    {
        // Lógica para cuando se actualiza un usuario
    }

    public function deleted(User $user): void
    {
        // Lógica para cuando se elimina un usuario
    }
} 