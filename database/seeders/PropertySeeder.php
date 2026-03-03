<?php

namespace Database\Seeders;

use App\Models\Prop\Property;
use App\Models\User;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    /**
     * Seed properties usando imágenes de public/assets/images/
     */
    public function run(): void
    {
        $agentId = User::first()?->id;

        $properties = [
            [
                'title' => '871 Crenshaw Blvd',
                'slug' => '871-crenshaw-blvd',
                'description' => 'Amplia casa familiar con jardín y garaje para 2 autos. Zona residencial tranquila.',
                'price' => 2250500,
                'address' => '871 Crenshaw Blvd',
                'city' => 'Los Angeles',
                'state' => 'CA',
                'zip' => '90005',
                'country' => 'US',
                'image' => 'hero_bg_1.jpg',
                'status' => 'active',
                'offer_type' => 'rent',
                'beds' => 4,
                'baths' => 3,
                'sqft' => 2800,
                'home_type' => 'house',
                'year_built' => 2015,
                'price_per_sqft' => 803.75,
                'featured' => true,
            ],
            [
                'title' => '625 S. Berendo St Unit 607',
                'slug' => '625-berendo-st-unit-607',
                'description' => 'Apartamento moderno en edificio céntrico. Vista panorámica y amenities incluidos.',
                'price' => 1000500,
                'address' => '625 S. Berendo St Unit 607',
                'city' => 'Los Angeles',
                'state' => 'CA',
                'zip' => '90005',
                'country' => 'US',
                'image' => 'hero_bg_2.jpg',
                'status' => 'active',
                'offer_type' => 'sale',
                'beds' => 2,
                'baths' => 2,
                'sqft' => 1430,
                'home_type' => 'apartment',
                'year_built' => 2018,
                'price_per_sqft' => 699.65,
                'featured' => true,
            ],
            [
                'title' => 'Condo con vista al mar',
                'slug' => 'condo-vista-mar',
                'description' => 'Condominio de lujo a pasos de la playa. Piscina, gimnasio y seguridad 24h.',
                'price' => 850000,
                'address' => '1500 Ocean Ave',
                'city' => 'Miami Beach',
                'state' => 'FL',
                'zip' => '33139',
                'country' => 'US',
                'image' => 'img_1.jpg',
                'status' => 'active',
                'offer_type' => 'sale',
                'beds' => 3,
                'baths' => 2,
                'sqft' => 1850,
                'home_type' => 'condo',
                'year_built' => 2020,
                'price_per_sqft' => 459.46,
                'featured' => false,
            ],
            [
                'title' => 'Casa familiar en Brooklyn',
                'slug' => 'casa-familiar-brooklyn',
                'description' => 'Encantadora casa de dos plantas con patio trasero. Ideal para familias.',
                'price' => 1250000,
                'address' => '245 Park Slope Ave',
                'city' => 'Brooklyn',
                'state' => 'NY',
                'zip' => '11215',
                'country' => 'US',
                'image' => 'img_2.jpg',
                'status' => 'active',
                'offer_type' => 'sale',
                'beds' => 5,
                'baths' => 4,
                'sqft' => 3200,
                'home_type' => 'house',
                'year_built' => 1995,
                'price_per_sqft' => 390.63,
                'featured' => false,
            ],
            [
                'title' => 'Apartamento en Manhattan',
                'slug' => 'apartamento-manhattan',
                'description' => 'Loft de diseño en zona premium. Techos altos y grandes ventanales.',
                'price' => 3200,
                'address' => '88 5th Ave',
                'city' => 'New York',
                'state' => 'NY',
                'zip' => '10011',
                'country' => 'US',
                'image' => 'img_3.jpg',
                'status' => 'active',
                'offer_type' => 'rent',
                'beds' => 2,
                'baths' => 1,
                'sqft' => 1100,
                'home_type' => 'apartment',
                'year_built' => 2010,
                'price_per_sqft' => null,
                'featured' => false,
            ],
            [
                'title' => 'Terreno comercial en zona industrial',
                'slug' => 'terreno-comercial-industrial',
                'description' => 'Lote amplio con acceso a carretera principal. Ideal para bodega o negocio.',
                'price' => 450000,
                'address' => '1200 Industrial Blvd',
                'city' => 'Houston',
                'state' => 'TX',
                'zip' => '77001',
                'country' => 'US',
                'image' => 'img_4.jpg',
                'status' => 'active',
                'offer_type' => 'sale',
                'beds' => null,
                'baths' => null,
                'sqft' => 15000,
                'home_type' => 'commercial',
                'year_built' => null,
                'price_per_sqft' => 30,
                'featured' => false,
            ],
        ];

        foreach ($properties as $data) {
            Property::create(array_merge($data, ['agent_id' => $agentId]));
        }
    }
}
