<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración: tabla saved_properties (propiedades guardadas / favoritos)
 *
 * Tabla pivot que relaciona usuarios con propiedades que han guardado como favoritas.
 * Solo almacena user_id y property_id; los datos de la propiedad (título, precio, etc.)
 * se obtienen mediante la relación con la tabla properties.
 */
return new class extends Migration
{
    /**
     * Ejecuta la migración: crea la tabla saved_properties.
     */
    public function up(): void
    {
        Schema::create('saved_properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();   // Usuario que guardó la propiedad
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete(); // Propiedad guardada
            $table->timestamps();                                                     // created_at, updated_at
        });

        // Evita que un usuario guarde la misma propiedad más de una vez
        Schema::table('saved_properties', function (Blueprint $table) {
            $table->unique(['user_id', 'property_id']);
        });
    }

    /**
     * Revierte la migración: elimina la tabla saved_properties.
     */
    public function down(): void
    {
        Schema::dropIfExists('saved_properties');
    }
};
