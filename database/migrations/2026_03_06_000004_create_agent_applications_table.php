<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('phone', 50)->nullable();
            $table->string('license_number', 100)->nullable();
            $table->text('bio')->nullable();
            $table->string('real_estate_certificate')->nullable();
            $table->string('id_document')->nullable();
            $table->json('other_documents')->nullable();
            $table->text('message')->nullable();
            $table->string('status', 20)->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_applications');
    }
};
