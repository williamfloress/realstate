<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración: tabla home_types (tipos de inmueble)
 *
 * Catálogo de tipos: condo, land, commercial, house, apartment.
 * Permite al admin gestionar tipos dinámicamente en el futuro.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_types', function (Blueprint $table) {
            $table->id();
            $table->string('home_type', 50)->unique();  // slug para URL: condo, land, commercial
            $table->string('name', 100);               // nombre para mostrar: Condo, Property Land
            $table->unsignedTinyInteger('order')->default(0);
            $table->timestamps();
        });

        // Tipos por defecto
        $defaults = [
            ['home_type' => 'condo', 'name' => 'Condo', 'order' => 1],
            ['home_type' => 'land', 'name' => 'Property Land', 'order' => 2],
            ['home_type' => 'commercial', 'name' => 'Commercial Building', 'order' => 3],
            ['home_type' => 'house', 'name' => 'House', 'order' => 4],
            ['home_type' => 'apartment', 'name' => 'Apartment', 'order' => 5],
        ];

        foreach ($defaults as $row) {
            \DB::table('home_types')->insert(array_merge($row, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('home_types');
    }
};
