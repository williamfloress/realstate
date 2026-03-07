<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ponderacion_acabados', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 20)->unique(); // piso, cocina, bano
            $table->unsignedTinyInteger('ponderacion');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ponderacion_acabados');
    }
};
