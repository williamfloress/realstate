<?php

namespace Database\Seeders;

use App\Models\PonderacionAcabado;
use Illuminate\Database\Seeder;

class PonderacionAcabadoSeeder extends Seeder
{
    public function run(): void
    {
        $ponderaciones = [
            ['tipo' => 'piso', 'ponderacion' => 7],
            ['tipo' => 'cocina', 'ponderacion' => 3],
            ['tipo' => 'bano', 'ponderacion' => 4],
        ];

        foreach ($ponderaciones as $p) {
            PonderacionAcabado::updateOrCreate(
                ['tipo' => $p['tipo']],
                ['ponderacion' => $p['ponderacion']]
            );
        }
    }
}
