<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('professeurs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nom', 100);
            $table->string('prenom', 100);
            $table->string('contact', 20)->nullable();
            $table->string('email')->nullable()->unique();
            $table->text('adresse')->nullable();
            $table->date('date_embauche')->nullable();
            $table->timestamps();
        });

        Schema::create('classes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nom', 100);
            $table->string('section');
            $table->string('niveau', 50)->nullable();
            $table->string('titulaire', 150)->nullable();
            $table->foreignUuid('professeur_id')->nullable()->constrained('professeurs')->nullOnDelete();
            $table->timestamps();

            $table->index(['nom', 'section']);
        });

        Schema::create('cours', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('titre', 150);
            $table->text('description')->nullable();
            $table->string('section');
            $table->unsignedTinyInteger('coefficient')->default(1);
            $table->unsignedTinyInteger('heures_semaine')->default(2);
            $table->foreignUuid('professeur_id')->nullable()->constrained('professeurs')->nullOnDelete();
            $table->foreignUuid('classe_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignUuid('option_id')->nullable()->constrained('options')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cours');
        Schema::dropIfExists('classes');
        Schema::dropIfExists('professeurs');
    }
};
