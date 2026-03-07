<?php

namespace Database\Seeders;

use App\Models\Acabado;
use Illuminate\Database\Seeder;

class AcabadoSeeder extends Seeder
{
    public function run(): void
    {
        $acabados = [
            ['tipo' => 'piso', 'nombre' => 'Terracota', 'puntaje' => 1],
            ['tipo' => 'piso', 'nombre' => 'Cerámica', 'puntaje' => 2],
            ['tipo' => 'piso', 'nombre' => 'Machiembrado', 'puntaje' => 3],
            ['tipo' => 'piso', 'nombre' => 'Porcelanato', 'puntaje' => 4],
            ['tipo' => 'piso', 'nombre' => 'Parquet', 'puntaje' => 4],
            ['tipo' => 'piso', 'nombre' => 'Marmol', 'puntaje' => 8],
            ['tipo' => 'cocina', 'nombre' => 'Fórmica', 'puntaje' => 1],
            ['tipo' => 'cocina', 'nombre' => 'Granito', 'puntaje' => 5],
            ['tipo' => 'cocina', 'nombre' => 'Cuarzo', 'puntaje' => 6],
            ['tipo' => 'cocina', 'nombre' => 'Marmol', 'puntaje' => 8],
            ['tipo' => 'bano', 'nombre' => 'Cerámica', 'puntaje' => 2],
            ['tipo' => 'bano', 'nombre' => 'Porcelanato', 'puntaje' => 4],
            ['tipo' => 'bano', 'nombre' => 'Marmol', 'puntaje' => 8],
        ];

        foreach ($acabados as $a) {
            Acabado::updateOrCreate(
                ['tipo' => $a['tipo'], 'nombre' => $a['nombre']],
                ['puntaje' => $a['puntaje']]
            );
        }
    }
}
