<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración: tabla requests (solicitudes de información sobre propiedades)
 *
 * Crea la tabla donde se guardan las solicitudes que los visitantes envían
 * al interesarse por una propiedad. Incluye datos de contacto, mensaje,
 * referencia a la propiedad y estado de seguimiento.
 */
return new class extends Migration
{
    /**
     * Ejecuta la migración: crea la tabla requests.
     */
    public function up(): void
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->string('name');                                    // Nombre del solicitante
            $table->string('email');                                    // Email de contacto
            $table->string('phone')->nullable();                       // Teléfono (opcional)
            $table->text('message')->nullable();                       // Mensaje o consulta
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();  // Propiedad de interés (si se borra la propiedad, se borran sus requests)
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // Usuario logueado (opcional)
            $table->string('status', 20)->default('pending');          // Estado: pending, contacted, closed
            $table->timestamps();                                      // created_at, updated_at
        });

        // Índice en status para filtrar requests por estado (ej: solo pendientes)
        Schema::table('requests', function (Blueprint $table) {
            $table->index('status');
        });
    }

    /**
     * Revierte la migración: elimina la tabla requests.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
