<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('institution_id')->constrained('institutions')->cascadeOnDelete();
            $table->foreignUuid('eleve_id')->constrained('eleves')->cascadeOnDelete();
            $table->foreignUuid('cours_id')->constrained('cours')->cascadeOnDelete();
            $table->foreignUuid('periode_id')->constrained('periodes')->cascadeOnDelete();
            $table->foreignUuid('session_scolaire_id')->constrained('sessions_scolaires')->cascadeOnDelete();
            $table->decimal('note', 5, 2); // 0-100
            $table->decimal('max', 5, 2)->default(100);
            $table->decimal('coefficient', 4, 2)->default(1);
            $table->text('commentaire')->nullable();
            $table->foreignUuid('professeur_id')->nullable()->constrained('professeurs')->nullOnDelete();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['eleve_id', 'cours_id', 'periode_id'], 'notes_unique_eleve_cours_periode');
            $table->index(['eleve_id', 'periode_id']);
            $table->index(['cours_id', 'periode_id']);
            $table->index(['periode_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
