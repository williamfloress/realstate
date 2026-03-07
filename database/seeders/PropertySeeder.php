<?php

namespace Database\Seeders;

use App\Models\Acabado;
use App\Models\Prop\HomeType;
use App\Models\Prop\Property;
use App\Models\Sector;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PropertySeeder extends Seeder
{
    private array $images = [
        'apto_1.png', 'apto_2_vista1.png', 'apto_3_vista1.png', 'apto_4_vista1.png',
        'apto_1_vista2.png', 'apto_2_vista2.png', 'apto_3_vista2.png', 'apto_4_vista2.png',
        'apto_1_vista3.png', 'apto_2_vista3.png', 'apto_3_vista3.png', 'apto_4_vista3.png',
    ];

    private array $typeNames = [
        'Apartamento', 'Residencia', 'Estudio', 'PH',
        'Loft', 'Apartamento', 'Residencia', 'Apartamento',
    ];

    public function run(): void
    {
        $agents = User::where('role', User::ROLE_AGENT)->get();
        $homeTypeId = HomeType::where('home_type', 'apartment')->value('id');
        $sectors = Sector::all()->keyBy('nombre');
        $acabados = $this->mapAcabados();

        $agentIds = array_filter([
            $agents->firstWhere('email', 'maria.rodriguez@umbral.com')?->id,
            $agents->firstWhere('email', 'carlos.mendoza@umbral.com')?->id,
            $agents->firstWhere('email', 'valentina.torres@umbral.com')?->id,
        ]);

        if (empty($agentIds)) {
            $this->command->warn('No se encontraron agentes. Ejecuta DatabaseSeeder primero.');
            return;
        }

        $agentIds = array_values($agentIds);
        $sectorData = $this->buildSectorData();

        $imageIdx = 0;
        $agentIdx = 0;
        $totalCreated = 0;

        foreach ($sectorData as $sectorName => $properties) {
            $sector = $sectors[$sectorName] ?? null;
            if (!$sector) {
                $this->command->warn("Sector {$sectorName} no encontrado, saltando.");
                continue;
            }

            foreach ($properties as $i => $row) {
                [$price, $area, $beds, $baths, $parking, $year, $offer, $piso, $cocina, $bano] = $row;

                $typeName = $this->typeNames[$i % count($this->typeNames)];
                $num = $i + 1;
                $title = "{$typeName} en {$sectorName} #{$num}";
                $slug = Str::slug($title);

                if (Property::where('slug', $slug)->exists()) {
                    continue;
                }

                $pricePerSqft = $offer === 'sale' && $area > 0 ? round($price / $area, 2) : null;

                Property::create([
                    'title'                => $title,
                    'slug'                 => $slug,
                    'description'          => "Propiedad ubicada en {$sectorName}, Caracas. {$beds} hab., {$baths} baños, {$parking} est. Área de {$area} m².",
                    'price'                => $price,
                    'address'              => "Urb. {$sectorName}, Caracas",
                    'city'                 => 'Caracas',
                    'state'                => 'Miranda',
                    'country'              => 'VE',
                    'image'                => $this->images[$imageIdx % count($this->images)],
                    'status'               => Property::STATUS_ACTIVE,
                    'offer_type'           => $offer,
                    'beds'                 => $beds,
                    'baths'                => $baths,
                    'sqft'                 => (int) round($area),
                    'area_construccion_m2' => $area,
                    'parqueos'             => $parking,
                    'year_built'           => $year,
                    'price_per_sqft'       => $pricePerSqft,
                    'home_type_id'         => $homeTypeId,
                    'sector_id'            => $sector->id,
                    'agent_id'             => $agentIds[$agentIdx % count($agentIds)],
                    'featured'             => $i < 2,
                    'acabado_piso_id'      => $acabados['piso'][$piso] ?? null,
                    'acabado_cocina_id'    => $acabados['cocina'][$cocina] ?? null,
                    'acabado_bano_id'      => $acabados['bano'][$bano] ?? null,
                ]);

                $imageIdx++;
                $agentIdx++;
                $totalCreated++;
            }
        }

        $this->command->info("PropertySeeder: {$totalCreated} propiedades creadas en " . count($sectorData) . ' sectores.');
    }

    /**
     * Datos por sector: [precio, area_m2, hab, baños, parqueos, año, oferta, piso, cocina, baño].
     * Las áreas están agrupadas para que el AMC (±15%) encuentre suficientes comparables.
     */
    private function buildSectorData(): array
    {
        return [
            'CHACAO' => [
                [250000, 105, 2, 2, 1, 2020, 'sale', 'Porcelanato', 'Granito', 'Porcelanato'],
                [290000, 120, 3, 2, 2, 2022, 'sale', 'Porcelanato', 'Cuarzo', 'Porcelanato'],
                [215000,  90, 2, 1, 1, 2018, 'sale', 'Cerámica', 'Granito', 'Cerámica'],
                [390000, 160, 4, 3, 2, 2023, 'sale', 'Marmol', 'Marmol', 'Marmol'],
                [275000, 115, 3, 2, 1, 2019, 'sale', 'Cerámica', 'Granito', 'Cerámica'],
                [310000, 128, 3, 2, 2, 2021, 'sale', 'Parquet', 'Cuarzo', 'Porcelanato'],
                [1500,    72, 2, 1, 1, 2017, 'rent', 'Cerámica', 'Fórmica', 'Cerámica'],
                [260000, 110, 2, 2, 1, 2020, 'sale', 'Porcelanato', 'Granito', 'Cerámica'],
            ],

            'ALTAMIRA' => [
                [300000, 110, 2, 2, 1, 2021, 'sale', 'Porcelanato', 'Cuarzo', 'Porcelanato'],
                [340000, 125, 3, 2, 2, 2022, 'sale', 'Porcelanato', 'Granito', 'Porcelanato'],
                [245000,  92, 2, 1, 1, 2019, 'sale', 'Cerámica', 'Granito', 'Cerámica'],
                [520000, 190, 5, 4, 3, 2024, 'sale', 'Marmol', 'Marmol', 'Marmol'],
                [310000, 115, 3, 2, 1, 2020, 'sale', 'Porcelanato', 'Granito', 'Cerámica'],
                [360000, 132, 3, 3, 2, 2023, 'sale', 'Parquet', 'Cuarzo', 'Porcelanato'],
                [2200,    80, 2, 1, 1, 2020, 'rent', 'Cerámica', 'Fórmica', 'Cerámica'],
                [280000, 105, 2, 2, 1, 2021, 'sale', 'Cerámica', 'Granito', 'Cerámica'],
            ],

            'LAS MERCEDES' => [
                [230000, 105, 2, 2, 1, 2020, 'sale', 'Porcelanato', 'Granito', 'Porcelanato'],
                [270000, 122, 3, 2, 2, 2022, 'sale', 'Porcelanato', 'Cuarzo', 'Porcelanato'],
                [195000,  88, 2, 1, 1, 2018, 'sale', 'Cerámica', 'Fórmica', 'Cerámica'],
                [385000, 168, 4, 3, 2, 2023, 'sale', 'Marmol', 'Cuarzo', 'Marmol'],
                [245000, 112, 3, 2, 1, 2019, 'sale', 'Cerámica', 'Granito', 'Cerámica'],
                [290000, 130, 3, 2, 2, 2021, 'sale', 'Parquet', 'Granito', 'Porcelanato'],
                [1200,    60, 1, 1, 1, 2020, 'rent', 'Cerámica', 'Fórmica', 'Cerámica'],
                [240000, 110, 2, 2, 1, 2020, 'sale', 'Porcelanato', 'Granito', 'Cerámica'],
            ],

            'LOS PALOS GRANDES' => [
                [240000, 102, 2, 2, 1, 2020, 'sale', 'Porcelanato', 'Granito', 'Porcelanato'],
                [280000, 118, 3, 2, 2, 2021, 'sale', 'Porcelanato', 'Cuarzo', 'Porcelanato'],
                [200000,  86, 2, 1, 1, 2018, 'sale', 'Cerámica', 'Granito', 'Cerámica'],
                [400000, 170, 4, 3, 2, 2023, 'sale', 'Marmol', 'Cuarzo', 'Marmol'],
                [260000, 113, 3, 2, 1, 2019, 'sale', 'Cerámica', 'Granito', 'Cerámica'],
                [300000, 128, 3, 3, 2, 2022, 'sale', 'Parquet', 'Cuarzo', 'Porcelanato'],
                [1400,    68, 2, 1, 1, 2019, 'rent', 'Cerámica', 'Fórmica', 'Cerámica'],
                [245000, 108, 2, 2, 1, 2020, 'sale', 'Porcelanato', 'Granito', 'Cerámica'],
            ],

            'VALLE ARRIBA' => [
                [420000, 150, 3, 3, 2, 2022, 'sale', 'Marmol', 'Cuarzo', 'Porcelanato'],
                [365000, 130, 3, 2, 2, 2021, 'sale', 'Porcelanato', 'Granito', 'Porcelanato'],
                [490000, 172, 4, 3, 3, 2023, 'sale', 'Marmol', 'Marmol', 'Marmol'],
                [305000, 108, 2, 2, 1, 2020, 'sale', 'Porcelanato', 'Granito', 'Cerámica'],
                [345000, 122, 3, 2, 2, 2021, 'sale', 'Porcelanato', 'Cuarzo', 'Porcelanato'],
                [400000, 142, 3, 3, 2, 2022, 'sale', 'Parquet', 'Cuarzo', 'Porcelanato'],
                [3000,    95, 2, 2, 1, 2021, 'rent', 'Porcelanato', 'Granito', 'Porcelanato'],
                [470000, 165, 4, 3, 2, 2023, 'sale', 'Porcelanato', 'Marmol', 'Marmol'],
            ],

            'LA TAHONA' => [
                [140000, 108, 3, 2, 2, 2016, 'sale', 'Cerámica', 'Granito', 'Cerámica'],
                [125000,  95, 2, 2, 1, 2013, 'sale', 'Terracota', 'Fórmica', 'Cerámica'],
                [170000, 130, 3, 3, 2, 2018, 'sale', 'Porcelanato', 'Granito', 'Porcelanato'],
                [100000,  78, 2, 1, 1, 2010, 'sale', 'Terracota', 'Fórmica', 'Cerámica'],
                [155000, 118, 3, 2, 1, 2017, 'sale', 'Cerámica', 'Granito', 'Cerámica'],
                [180000, 138, 3, 2, 2, 2019, 'sale', 'Porcelanato', 'Cuarzo', 'Porcelanato'],
                [800,     82, 2, 1, 1, 2014, 'rent', 'Cerámica', 'Fórmica', 'Cerámica'],
                [150000, 115, 3, 2, 1, 2016, 'sale', 'Machiembrado', 'Granito', 'Cerámica'],
            ],

            'EL HATILLO' => [
                [120000, 108, 3, 2, 2, 2015, 'sale', 'Cerámica', 'Granito', 'Cerámica'],
                [100000,  92, 2, 1, 1, 2011, 'sale', 'Terracota', 'Fórmica', 'Cerámica'],
                [155000, 135, 3, 3, 2, 2018, 'sale', 'Porcelanato', 'Granito', 'Porcelanato'],
                [80000,   72, 2, 1, 1, 2009, 'sale', 'Terracota', 'Fórmica', 'Cerámica'],
                [135000, 120, 3, 2, 1, 2016, 'sale', 'Cerámica', 'Granito', 'Cerámica'],
                [145000, 128, 3, 2, 2, 2017, 'sale', 'Porcelanato', 'Cuarzo', 'Porcelanato'],
                [700,     78, 2, 1, 1, 2013, 'rent', 'Cerámica', 'Fórmica', 'Cerámica'],
                [130000, 115, 3, 2, 1, 2015, 'sale', 'Machiembrado', 'Granito', 'Cerámica'],
            ],

            'BARUTA' => [
                [130000, 110, 3, 2, 2, 2017, 'sale', 'Cerámica', 'Granito', 'Cerámica'],
                [105000,  88, 2, 1, 1, 2013, 'sale', 'Terracota', 'Fórmica', 'Cerámica'],
                [165000, 135, 3, 3, 2, 2019, 'sale', 'Porcelanato', 'Granito', 'Porcelanato'],
                [85000,   70, 1, 1, 1, 2011, 'sale', 'Terracota', 'Fórmica', 'Cerámica'],
                [145000, 118, 3, 2, 1, 2018, 'sale', 'Cerámica', 'Granito', 'Cerámica'],
                [175000, 142, 3, 2, 2, 2020, 'sale', 'Porcelanato', 'Cuarzo', 'Porcelanato'],
                [750,     75, 2, 1, 1, 2015, 'rent', 'Cerámica', 'Fórmica', 'Cerámica'],
                [155000, 125, 3, 2, 2, 2018, 'sale', 'Machiembrado', 'Granito', 'Porcelanato'],
            ],
        ];
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
