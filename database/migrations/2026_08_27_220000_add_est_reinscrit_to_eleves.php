<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Distingue, au sein d'une session, les élèves réinscrits (recopiés depuis
     * une année précédente par la fonction Réinscription) des nouvelles
     * inscriptions réalisées manuellement.
     */
    public function up(): void
    {
        Schema::table('eleves', function (Blueprint $table) {
            $table->boolean('est_reinscrit')->default(false)->after('statut');
        });
    }

    public function down(): void
    {
        Schema::table('eleves', function (Blueprint $table) {
            $table->dropColumn('est_reinscrit');
        });
    }
};