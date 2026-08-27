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
            $table->string('code', 20); // S1, S2, 1P, 2P, T1...
            $table->string('libelle', 100);
            $table->unsignedTinyInteger('ordre');
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->boolean('est_cloturee')->default(false);
            $table->timestamps();

            $table->unique(['institution_id', 'session_scolaire_id', 'code']);
            $table->index(['institution_id', 'session_scolaire_id', 'ordre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('periodes');
    }
};
