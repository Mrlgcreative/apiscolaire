<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('periodes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('institution_id')->constrained('institutions')->cascadeOnDelete();
            $table->foreignUuid('session_scolaire_id')->constrained('sessions_scolaires')->cascadeOnDelete();
            $table->foreignUuid('parent_id')->nullable()->constrained('periodes')->nullOnDelete();
            $table->string('type', 20); // semestre, trimestre, periode
            $table->string('section', 20)->nullable()->index(); // maternelle, primaire, secondaire (null = toutes)
            $table->string('code', 20); // S1, S2, 1P, 2P, 3P, 4P, 5P, 6P, T1, T2, T3
            $table->string('libelle', 100);
            $table->unsignedTinyInteger('ordre');
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->boolean('est_cloturee')->default(false);
            $table->timestamps();

            $table->unique(['institution_id', 'session_scolaire_id', 'section', 'code']);
            $table->index(['institution_id', 'session_scolaire_id', 'ordre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('periodes');
    }
};
