<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CreateLiderUserSeeder extends Seeder
{
    /**
     * Crea el usuario Líder (lider@alistamiento.com) con rol COLABORADOR.
     * Contraseña por defecto: password (cámbiala desde Perfil tras el primer acceso).
     */
    public function run(): void
    {
        $email = 'lider@alistamiento.com';

        if (User::where('email', $email)->exists()) {
            $this->command?->info("El usuario {$email} ya existe. No se crea de nuevo.");
            return;
        }

        $roleColaborador = Role::where('name', 'COLABORADOR')->first();
        if (! $roleColaborador) {
            $this->command?->warn('No existe el rol COLABORADOR. Ejecuta las migraciones.');
            return;
        }

        User::create([
            'name' => 'Líder',
            'email' => $email,
            'password' => Hash::make('password'),
            'role_id' => $roleColaborador->id,
            'colaborador_id' => null,
        ]);

        $this->command?->info("Usuario Líder creado: {$email}");
        $this->command?->info('Contraseña inicial: password (cámbiala desde Perfil tras el primer acceso).');
    }
}
