<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parent_eleve', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('parent_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('eleve_id')->constrained('eleves')->cascadeOnDelete();
            $table->string('lien', 20)->nullable(); // pere, mere, tuteur
            $table->timestamps();

            $table->unique(['parent_id', 'eleve_id']);
            $table->index('eleve_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parent_eleve');
    }
};
