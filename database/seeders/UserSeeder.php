<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuarios de prueba para cada rol
        $this->createUserWithRole(
            'Administrador',
            'admin@fimi.com',
            'admin123',
            'admin',
            'Administrador del sistema'
        );

        $this->createUserWithRole(
            'Usuario Inventario',
            'inventario@fimi.com',
            'inventario123',
            'inventario',
            'Usuario de inventario'
        );

        $this->createUserWithRole(
            'Usuario Ventas',
            'ventas@fimi.com',
            'ventas123',
            'ventas',
            'Usuario de ventas'
        );

        $this->createUserWithRole(
            'Usuario Producción',
            'produccion@fimi.com',
            'produccion123',
            'produccion',
            'Usuario de producción'
        );
    }

    /**
     * Crea un usuario y le asigna un rol específico
     */
    private function createUserWithRole(string $name, string $email, string $password, string $roleName, string $description): void
    {
        // Verificar si el rol existe
        $role = Role::where('name', $roleName)->first();
        
        if (!$role) {
            $this->command->error("El rol {$roleName} no existe. Asegúrate de ejecutar el RoleSeeder primero.");
            return;
        }

        // Verificar si el usuario ya existe
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]);
            
            $user->assignRole($role);
            $this->command->info("Usuario {$name} creado y asignado al rol {$roleName}");
        } else {
            $this->command->info("El usuario {$name} ya existe");
        }
    }
}
