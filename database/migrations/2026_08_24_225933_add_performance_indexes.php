<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Liste admin : filtre par rôle dans le périmètre d'une école.
            $table->index(['institution_id', 'role']);
        });

        Schema::table('absences', function (Blueprint $table) {
            // Filtres date_debut / date_fin + tri chronologique.
            $table->index('date_absence');
        });

        Schema::table('paiements_frais', function (Blueprint $table) {
            // Historique des encaissements par date.
            $table->index('payment_date');
        });
    }

    public function down(): void
    {
        Schema::table('users', fn (Blueprint $table) => $table->dropIndex(['institution_id', 'role']));
        Schema::table('absences', fn (Blueprint $table) => $table->dropIndex(['date_absence']));
        Schema::table('paiements_frais', fn (Blueprint $table) => $table->dropIndex(['payment_date']));
    }
};
