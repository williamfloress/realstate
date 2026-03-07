<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Usuario regular de prueba
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Agentes venezolanos
        User::factory()->create([
            'name'  => 'María Alejandra Rodríguez',
            'email' => 'maria.rodriguez@umbral.com',
            'role'  => User::ROLE_AGENT,
            'phone' => '+58 412-5551234',
        ]);

        User::factory()->create([
            'name'  => 'Carlos Eduardo Mendoza',
            'email' => 'carlos.mendoza@umbral.com',
            'role'  => User::ROLE_AGENT,
            'phone' => '+58 414-5559876',
        ]);

        User::factory()->create([
            'name'  => 'Valentina Torres García',
            'email' => 'valentina.torres@umbral.com',
            'role'  => User::ROLE_AGENT,
            'phone' => '+58 424-5554321',
        ]);

        $this->call(AcabadoSeeder::class);
        $this->call(PonderacionAcabadoSeeder::class);
        $this->call(SectorSeeder::class);
        $this->call(PropertySeeder::class);
        $this->call(AmcSeeder::class);
        $this->call(PropImageSeeder::class);
    }
}
