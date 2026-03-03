<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Crea la tabla prop_images para almacenar las imágenes de cada propiedad.
 */
return new class extends Migration
{
    /**
     * Crea la tabla prop_images con FK a properties.
     */
    public function up(): void
    {
        Schema::create('prop_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete(); // Si se borra la propiedad, se borran sus imágenes
            $table->string('path');                    // Nombre del archivo (ej: img_1.jpg)
            $table->string('caption')->nullable();     // Descripción opcional
            $table->unsignedSmallInteger('order')->default(0);  // Orden en el carrusel
            $table->timestamps();
        });

        // Índice para consultas rápidas por property_id
        Schema::table('prop_images', function (Blueprint $table) {
            $table->index('property_id');
        });
    }

    /**
     * Elimina la tabla al hacer rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('prop_images');
    }
};
