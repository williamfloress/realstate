<?php

namespace Database\Seeders;

use App\Models\Sector;
use Illuminate\Database\Seeder;

class SectorSeeder extends Seeder
{
    public function run(): void
    {
        $sectores = [
            ['nombre' => 'LOS NARANJOS',     'latitud' => 10.4200, 'longitud' => -66.8800],
            ['nombre' => 'CHACAO',            'latitud' => 10.4961, 'longitud' => -66.8553],
            ['nombre' => 'LOS PALOS GRANDES', 'latitud' => 10.4933, 'longitud' => -66.8467],
            ['nombre' => 'VALLE ARRIBA',      'latitud' => 10.4600, 'longitud' => -66.8900],
            ['nombre' => 'LA TAHONA',         'latitud' => 10.4350, 'longitud' => -66.8700],
            ['nombre' => 'ALTAMIRA',          'latitud' => 10.4961, 'longitud' => -66.8533],
            ['nombre' => 'LAS MERCEDES',      'latitud' => 10.4922, 'longitud' => -66.8583],
            ['nombre' => 'EL HATILLO',        'latitud' => 10.4250, 'longitud' => -66.8250],
            ['nombre' => 'BARUTA',            'latitud' => 10.4400, 'longitud' => -66.8800],
        ];

        foreach ($sectores as $s) {
            Sector::updateOrCreate(
                ['nombre' => $s['nombre']],
                [
                    'latitud' => $s['latitud'] ?? null,
                    'longitud' => $s['longitud'] ?? null,
                ]
            );
        }
    }
}
