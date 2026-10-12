<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Rellena la base de datos con datos de prueba.
     */
    public function run(): void
    {
        // Esto ya venía en el archivo: crea un usuario de prueba
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Esto es lo nuevo: ejecuta el seeder que crea las mesas
        $this->call(MesaSeeder::class);
    }
}
