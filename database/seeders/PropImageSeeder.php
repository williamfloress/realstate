<?php

namespace Database\Seeders;

use App\Models\Prop\Property;
use App\Models\Prop\PropImage;
use Illuminate\Database\Seeder;

class PropImageSeeder extends Seeder
{
    /**
     * Cada imagen principal se mapea a una galería de 3 fotos de su mismo set.
     * La galería comienza con la imagen principal y rota las otras dos del set.
     */
    private array $galleries = [
        'apto_1.png'        => ['apto_1.png', 'apto_1_vista2.png', 'apto_1_vista3.png'],
        'apto_1_vista2.png' => ['apto_1_vista2.png', 'apto_1_vista3.png', 'apto_1.png'],
        'apto_1_vista3.png' => ['apto_1_vista3.png', 'apto_1.png', 'apto_1_vista2.png'],

        'apto_2_vista1.png' => ['apto_2_vista1.png', 'apto_2_vista2.png', 'apto_2_vista3.png'],
        'apto_2_vista2.png' => ['apto_2_vista2.png', 'apto_2_vista3.png', 'apto_2_vista1.png'],
        'apto_2_vista3.png' => ['apto_2_vista3.png', 'apto_2_vista1.png', 'apto_2_vista2.png'],

        'apto_3_vista1.png' => ['apto_3_vista1.png', 'apto_3_vista2.png', 'apto_3_vista3.png'],
        'apto_3_vista2.png' => ['apto_3_vista2.png', 'apto_3_vista3.png', 'apto_3_vista1.png'],
        'apto_3_vista3.png' => ['apto_3_vista3.png', 'apto_3_vista1.png', 'apto_3_vista2.png'],

        'apto_4_vista1.png' => ['apto_4_vista1.png', 'apto_4_vista2.png', 'apto_4_vista3.png'],
        'apto_4_vista2.png' => ['apto_4_vista2.png', 'apto_4_vista3.png', 'apto_4_vista1.png'],
        'apto_4_vista3.png' => ['apto_4_vista3.png', 'apto_4_vista1.png', 'apto_4_vista2.png'],
    ];

    private array $fallbackGallery = [
        'apto_1.png', 'apto_2_vista1.png', 'apto_3_vista1.png',
    ];

    public function run(): void
    {
        $properties = Property::all();

        if ($properties->isEmpty()) {
            $this->command->warn('No hay propiedades. Ejecuta PropertySeeder primero.');
            return;
        }

        $captions = ['Vista principal', 'Vista interior', 'Vista adicional'];
        $created = 0;

        foreach ($properties as $property) {
            $images = $this->galleries[$property->image] ?? $this->fallbackGallery;

            foreach ($images as $order => $path) {
                PropImage::create([
                    'property_id' => $property->id,
                    'path'        => $path,
                    'caption'     => $captions[$order] ?? null,
                    'order'       => $order,
                ]);
                $created++;
            }
        }

        $this->command->info("PropImageSeeder: {$created} imágenes de galería creadas para {$properties->count()} propiedades.");
    }
}
