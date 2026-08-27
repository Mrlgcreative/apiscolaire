<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parametres', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('institution_id')->nullable()->constrained('institutions')->cascadeOnDelete();
            $table->string('cle', 100);
            $table->json('valeur')->nullable();
            $table->timestamps();

            $table->unique(['institution_id', 'cle'], 'parametres_unique_scope_cle');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parametres');
    }
};
