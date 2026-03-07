<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->foreignId('sector_id')->nullable()->after('home_type_id')->constrained('sectores')->nullOnDelete();
            $table->decimal('area_construccion_m2', 10, 2)->nullable()->after('sqft');
            $table->unsignedTinyInteger('parqueos')->nullable()->after('baths');
            $table->foreignId('acabado_piso_id')->nullable()->after('parqueos')->constrained('acabados')->nullOnDelete();
            $table->foreignId('acabado_cocina_id')->nullable()->after('acabado_piso_id')->constrained('acabados')->nullOnDelete();
            $table->foreignId('acabado_bano_id')->nullable()->after('acabado_cocina_id')->constrained('acabados')->nullOnDelete();
        });

        // Copiar sqft a area_construccion_m2 para registros existentes (asumiendo mismo valor)
        foreach (\DB::table('properties')->whereNotNull('sqft')->get(['id', 'sqft']) as $row) {
            \DB::table('properties')->where('id', $row->id)->update(['area_construccion_m2' => $row->sqft]);
        }

        Schema::table('properties', function (Blueprint $table) {
            $table->index(['sector_id', 'area_construccion_m2']);
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropIndex(['sector_id', 'area_construccion_m2']);
            $table->dropConstrainedForeignId('sector_id');
            $table->dropColumn('area_construccion_m2');
            $table->dropColumn('parqueos');
            $table->dropConstrainedForeignId('acabado_piso_id');
            $table->dropConstrainedForeignId('acabado_cocina_id');
            $table->dropConstrainedForeignId('acabado_bano_id');
        });
    }
};
