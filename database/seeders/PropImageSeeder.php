<?php

namespace Database\Seeders;

use App\Models\Prop\Property;
use App\Models\Prop\PropImage;
use Illuminate\Database\Seeder;

/**
 * Seeder que inserta imágenes de galería para cada propiedad.
 * Usa archivos existentes en public/assets/images/.
 */
class PropImageSeeder extends Seeder
{
    /**
     * Imágenes disponibles en public/assets/images/ (mismo directorio que PropertySeeder).
     */
    private array $availableImages = [
        'hero_bg_1.jpg',
        'hero_bg_2.jpg',
        'img_1.jpg',
        'img_2.jpg',
        'img_3.jpg',
        'img_4.jpg',
        'img_5.jpg',
        'img_6.jpg',
    ];

    /**
     * Ejecuta el seeder: obtiene todas las propiedades y crea imágenes para cada una.
     */
    public function run(): void
    {
        $properties = Property::all();

        if ($properties->isEmpty()) {
            $this->command->warn('No hay propiedades. Ejecuta PropertySeeder primero.');
            return;
        }

        foreach ($properties as $property) {
            $this->seedImagesForProperty($property);
        }
    }

    /**
     * Crea hasta 6 imágenes para una propiedad: la principal primero, luego el resto.
     */
    private function seedImagesForProperty(Property $property): void
    {
        // Imagen principal de la propiedad como primera del carrusel
        $mainImage = $property->image ?? 'img_1.jpg';
        $others = array_values(array_filter(
            $this->availableImages,
            fn (string $img) => $img !== $mainImage
        ));

        // Combinar: principal + otras (máx. 6 imágenes por propiedad)
        $imagesToUse = array_slice(array_merge([$mainImage], $others), 0, 6);

        // Textos descriptivos para cada posición
        $captions = [
            'Vista frontal',
            'Sala de estar',
            'Cocina',
            'Dormitorio principal',
            'Baño',
            'Área exterior',
        ];

        foreach ($imagesToUse as $order => $path) {
            PropImage::create([
                'property_id' => $property->id,
                'path' => $path,
                'caption' => $captions[$order] ?? null,
                'order' => $order,
            ]);
        }
    }
}
