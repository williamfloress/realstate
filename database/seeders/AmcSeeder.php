<?php

namespace Database\Seeders;

use App\Models\Acabado;
use App\Models\Prop\HomeType;
use App\Models\Prop\Property;
use App\Models\Sector;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Propiedades de referencia para verificar el cálculo del AMC.
 * Datos extraídos de AMC.csv - Oferta Inmobiliaria Apto en Venta Urb. Los Naranjos.
 *
 * Inmueble en estudio esperado: 128 m², Parquet/Cerámica/Granito
 * Valor de Mercado esperado: ~117.743,55 | Valor con acabados: ~116.793,75
 */
class AmcSeeder extends Seeder
{
    private array $images = [
        'apto_1.png', 'apto_2_vista1.png', 'apto_3_vista1.png', 'apto_4_vista1.png',
        'apto_1_vista2.png', 'apto_2_vista2.png', 'apto_3_vista2.png', 'apto_4_vista2.png',
        'apto_1_vista3.png', 'apto_2_vista3.png', 'apto_3_vista3.png', 'apto_4_vista3.png',
    ];

    private array $typeNames = [
        'Apartamento', 'Residencia', 'Estudio', 'PH', 'Loft',
        'Apartamento', 'Residencia', 'Apartamento', 'Estudio', 'PH',
        'Residencia', 'Apartamento', 'Loft', 'Residencia', 'Apartamento',
    ];

    public function run(): void
    {
        $sector = Sector::where('nombre', 'LOS NARANJOS')->first();
        if (!$sector) {
            $this->command->warn('Sector LOS NARANJOS no encontrado. Ejecuta SectorSeeder primero.');
            return;
        }

        $agents = User::where('role', User::ROLE_AGENT)->get();
        $agentIds = array_filter([
            $agents->firstWhere('email', 'maria.rodriguez@umbral.com')?->id,
            $agents->firstWhere('email', 'carlos.mendoza@umbral.com')?->id,
            $agents->firstWhere('email', 'valentina.torres@umbral.com')?->id,
        ]);
        $agentIds = array_values($agentIds);

        $homeTypeId = HomeType::where('home_type', 'apartment')->value('id');
        $acabados = $this->mapAcabados();

        $comparables = [
            [75000, 126, 3, 2, 2, 'Terracota', 'Fórmica', 'Cerámica'],
            [80000, 120, 4, 2, 2, 'Machiembrado', 'Granito', 'Cerámica'],
            [97000, 126, 3, 3, 2, 'Porcelanato', 'Granito', 'Porcelanato'],
            [97000, 127, 3, 3, 2, 'Cerámica', 'Granito', 'Cerámica'],
            [97500, 121, 3, 3, 1, 'Porcelanato', 'Granito', 'Cerámica'],
            [99000, 127, 3, 3, 2, 'Porcelanato', 'Granito', 'Porcelanato'],
            [100000, 121, 3, 3, 1, 'Porcelanato', 'Granito', 'Cerámica'],
            [110000, 124, 4, 3, 2, 'Marmol', 'Cuarzo', 'Cerámica'],
            [115000, 130, 3, 2, 2, 'Porcelanato', 'Granito', 'Porcelanato'],
            [115000, 130, 3, 2, 2, 'Porcelanato', 'Granito', 'Cerámica'],
            [125000, 130.65, 3, 3, 1, 'Porcelanato', 'Granito', 'Porcelanato'],
            [130000, 130, 3, 3, 2, 'Porcelanato', 'Granito', 'Porcelanato'],
            [160000, 130.65, 3, 3, 2, 'Porcelanato', 'Cuarzo', 'Porcelanato'],
            [165000, 124, 3, 3, 2, 'Marmol', 'Marmol', 'Marmol'],
            [165000, 116, 3, 3, 2, 'Porcelanato', 'Granito', 'Porcelanato'],
        ];

        $imageOffset = 5;

        foreach ($comparables as $i => $row) {
            [$precio, $area, $beds, $baths, $parqueos, $piso, $cocina, $bano] = $row;

            $typeName = $this->typeNames[$i % count($this->typeNames)];
            $num = $i + 1;
            $title = "{$typeName} en LOS NARANJOS #{$num}";
            $slug = Str::slug($title);

            if (Property::where('slug', $slug)->exists()) {
                continue;
            }

            Property::create([
                'title'                => $title,
                'slug'                 => $slug,
                'description'          => "Propiedad ubicada en Los Naranjos, Caracas. {$beds} hab., {$baths} baños, {$parqueos} est. Área de {$area} m².",
                'price'                => $precio,
                'address'              => 'Urb. Los Naranjos',
                'city'                 => 'Caracas',
                'state'                => 'Miranda',
                'country'              => 'VE',
                'image'                => $this->images[($i + $imageOffset) % count($this->images)],
                'status'               => Property::STATUS_ACTIVE,
                'offer_type'           => 'sale',
                'beds'                 => $beds,
                'baths'                => $baths,
                'parqueos'             => $parqueos,
                'sqft'                 => (int) round($area),
                'area_construccion_m2' => $area,
                'year_built'           => 2010,
                'home_type_id'         => $homeTypeId,
                'sector_id'            => $sector->id,
                'agent_id'             => $agentIds[$i % count($agentIds)],
                'acabado_piso_id'      => $acabados['piso'][$piso] ?? null,
                'acabado_cocina_id'    => $acabados['cocina'][$cocina] ?? null,
                'acabado_bano_id'      => $acabados['bano'][$bano] ?? null,
            ]);
        }

        $this->command->info('AMC: ' . count($comparables) . ' propiedades comparables creadas en LOS NARANJOS.');
    }

    private function mapAcabados(): array
    {
        $map = ['piso' => [], 'cocina' => [], 'bano' => []];
        foreach (Acabado::all() as $a) {
            $map[$a->tipo][$a->nombre] = $a->id;
        }
        return $map;
    }
}
