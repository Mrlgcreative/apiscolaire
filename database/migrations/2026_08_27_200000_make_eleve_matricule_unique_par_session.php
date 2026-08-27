<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Le matricule d'un élève ne change jamais d'une année à l'autre :
     * il n'est donc plus unique globalement, mais unique par école et par
     * session scolaire (un même matricule peut réapparaître à la nouvelle
     * année lors de la réinscription, jamais deux fois dans la même session).
     */
    public function up(): void
    {
        Schema::table('eleves', function (Blueprint $table) {
            $table->dropUnique('eleves_matricule_unique');
            $table->unique(['institution_id', 'session_scolaire_id', 'matricule'], 'eleves_matricule_par_session_unique');
        });
    }

    public function down(): void
    {
        Schema::table('eleves', function (Blueprint $table) {
            $table->dropUnique('eleves_matricule_par_session_unique');
            $table->unique('matricule');
        });
    }
};