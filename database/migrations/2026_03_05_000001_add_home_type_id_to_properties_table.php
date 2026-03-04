<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migración: reemplaza home_type (string) por home_type_id (FK a home_types).
 *
 * Migra datos existentes: propiedades con home_type='condo' → home_type_id del HomeType 'condo'.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->foreignId('home_type_id')->nullable()->after('sqft')->constrained('home_types')->nullOnDelete();
        });

        // Migrar datos: home_type (string) → home_type_id
        $types = \DB::table('home_types')->pluck('id', 'home_type');
        \DB::table('properties')->whereNotNull('home_type')->get()->each(function ($prop) use ($types) {
            $id = $types[$prop->home_type] ?? null;
            if ($id) {
                \DB::table('properties')->where('id', $prop->id)->update(['home_type_id' => $id]);
            }
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn('home_type');
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->string('home_type', 50)->nullable()->after('sqft');
        });

        $types = \DB::table('home_types')->pluck('home_type', 'id');
        \DB::table('properties')->whereNotNull('home_type_id')->get()->each(function ($prop) use ($types) {
            $slug = $types[$prop->home_type_id] ?? null;
            if ($slug) {
                \DB::table('properties')->where('id', $prop->id)->update(['home_type' => $slug]);
            }
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->dropConstrainedForeignId('home_type_id');
        });
    }
};
