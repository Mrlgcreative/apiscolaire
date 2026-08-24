<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sessions_scolaires', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedSmallInteger('annee_debut');
            $table->unsignedSmallInteger('annee_fin');
            $table->string('libelle', 100);
            $table->boolean('est_active')->default(false);
            $table->timestamps();

            $table->unique(['annee_debut', 'annee_fin']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions_scolaires');
    }
};
