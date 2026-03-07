<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('acabados', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 20); // piso, cocina, bano
            $table->string('nombre', 100);
            $table->unsignedTinyInteger('puntaje'); // 1-8
            $table->timestamps();
        });

        Schema::table('acabados', function (Blueprint $table) {
            $table->index('tipo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acabados');
    }
};
