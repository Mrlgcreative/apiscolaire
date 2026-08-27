<?php

use App\Models\Mois;
use App\Models\SessionScolaire;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mois', function (Blueprint $table) {
            $table->foreignUuid('session_scolaire_id')
                ->nullable()
                ->after('ordre')
                ->constrained('sessions_scolaires')
                ->nullOnDelete();
        });

        Schema::table('mois', function (Blueprint $table) {
            $table->dropUnique('mois_nom_unique');
            $table->index(['session_scolaire_id', 'nom']);
        });

        // Rattachage : les mois "orphelins" (jamais liés) reviennent à la session active.
        $active = SessionScolaire::active()->first();

        if ($active !== null) {
            Mois::whereNull('session_scolaire_id')->update(['session_scolaire_id' => $active->id]);

            // Une année scolaire = 10 mois (septembre → juin) propres à la session.
            foreach (SessionScolaire::all() as $session) {
                if ($session->id === $active->id || $session->mois()->exists()) {
                    continue;
                }

                foreach ($active->mois()->orderBy('ordre')->get() as $m) {
                    Mois::create([
                        'nom' => $m->nom,
                        'ordre' => $m->ordre,
                        'session_scolaire_id' => $session->id,
                    ]);
                }
            }
        }

        // Une session sans session active et sans aucun mois ne peut pas exister ici.
        foreach (SessionScolaire::all() as $session) {
            if ($session->mois()->count() === 0) {
                foreach (['septembre', 'octobre', 'novembre', 'decembre', 'janvier', 'fevrier', 'mars', 'avril', 'mai', 'juin'] as $i => $nom) {
                    Mois::create(['nom' => $nom, 'ordre' => $i + 1, 'session_scolaire_id' => $session->id]);
                }
            }
        }
    }

    public function down(): void
    {
        $active = SessionScolaire::active()->first();

        if ($active !== null) {
            Mois::where('session_scolaire_id', $active->id)->update(['session_scolaire_id' => null]);
        }

        Mois::whereNotNull('session_scolaire_id')->delete();

        Schema::table('mois', function (Blueprint $table) {
            $table->dropIndex('mois_session_scolaire_id_nom_index');
            $table->unique('nom');
            $table->dropConstrainedForeignId('session_scolaire_id');
        });
    }
};