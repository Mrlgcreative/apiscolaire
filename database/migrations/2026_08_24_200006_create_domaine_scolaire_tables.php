<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eleves', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('matricule', 30)->unique();
            $table->string('nom', 100);
            $table->string('post_nom', 100);
            $table->string('prenom', 100);
            $table->date('date_naissance');
            $table->string('sexe', 1);
            $table->string('lieu_naissance', 150);
            $table->string('adresse', 255)->nullable();
            $table->string('section');
            $table->foreignUuid('option_id')->nullable()->constrained('options')->nullOnDelete();
            $table->foreignUuid('classe_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->foreignUuid('session_scolaire_id')->nullable()->constrained('sessions_scolaires')->nullOnDelete();
            $table->string('nom_pere', 100)->nullable();
            $table->string('nom_mere', 100)->nullable();
            $table->string('contact_pere', 20)->nullable();
            $table->string('contact_mere', 20)->nullable();
            $table->string('statut', 20)->default('actif');
            $table->string('photo')->nullable();
            $table->timestamps();

            $table->index(['section', 'statut']);
            $table->index('classe_id');
        });

        Schema::create('frais', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->decimal('montant', 10, 2);
            $table->string('description', 255);
            $table->string('section')->nullable();
            $table->timestamps();
        });

        Schema::create('mois', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nom', 50)->unique();
            $table->unsignedTinyInteger('ordre');
            $table->timestamps();
        });

        Schema::create('paiements_frais', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('eleve_id')->constrained('eleves')->cascadeOnDelete();
            $table->foreignUuid('frais_id')->constrained('frais')->restrictOnDelete();
            $table->foreignUuid('moi_id')->constrained('mois')->restrictOnDelete();
            $table->foreignUuid('classe_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->decimal('amount_paid', 10, 2);
            $table->date('payment_date');
            $table->string('statut', 20)->default('paye');
            $table->foreignUuid('session_scolaire_id')->nullable()->constrained('sessions_scolaires')->nullOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['eleve_id', 'moi_id']);
        });

        Schema::create('absences', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('eleve_id')->constrained('eleves')->cascadeOnDelete();
            $table->date('date_absence');
            $table->text('motif')->nullable();
            $table->boolean('justifiee')->default(false);
            $table->timestamps();

            $table->unique(['eleve_id', 'date_absence']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absences');
        Schema::dropIfExists('paiements_frais');
        Schema::dropIfExists('mois');
        Schema::dropIfExists('frais');
        Schema::dropIfExists('eleves');
    }
};
