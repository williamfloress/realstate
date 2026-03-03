<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->nullable()->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 14, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state', 100)->nullable();
            $table->string('zip', 20)->nullable();
            $table->string('country', 2)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('image')->nullable();
            $table->string('status', 20)->default('draft');
            $table->string('offer_type', 20)->nullable(); // sale, rent, lease
            $table->unsignedTinyInteger('beds')->nullable();
            $table->unsignedTinyInteger('baths')->nullable();
            $table->unsignedInteger('sqft')->nullable();
            $table->string('home_type', 50)->nullable(); // condo, commercial, land, house, apartment
            $table->unsignedSmallInteger('year_built')->nullable();
            $table->decimal('price_per_sqft', 12, 2)->nullable();
            $table->boolean('featured')->default(false);
            $table->foreignId('agent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->index(['status', 'offer_type']);
            $table->index('city');
            $table->index(['price']);
            $table->index('featured');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
