<?php

namespace Database\Seeders;

use App\Models\Absence;
use App\Models\Eleve;
use App\Models\Frais;
use App\Models\Institution;
use App\Models\Mois;
use App\Models\PaiementFrais;
use App\Models\SessionScolaire;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $csk = Institution::where('code', 'CSK')->first();

        $fraisMat = Frais::where('section', 'maternelle')->where('institution_id', $csk->id)->first();
        $fraisPrim = Frais::where('section', 'secondaire')->where('institution_id', $csk->id)->first();
        $maternelle = Mois::where('nom', 'mars')->first();
        $avril = Mois::where('nom', 'avril')->first();

        $eleve1 = Eleve::where('nom', 'Kasong')->first();
        $eleve2 = Eleve::where('prenom', 'Fabrice')->first();

        $session = SessionScolaire::where('est_active', true)->first();

        PaiementFrais::create([
            'eleve_id' => $eleve1->id, 'frais_id' => $fraisMat->id, 'moi_id' => $maternelle->id,
            'amount_paid' => $fraisMat->montant, 'payment_date' => now(),
            'classe_id' => $eleve1->classe_id, 'session_scolaire_id' => $session->id,
        ]);

        PaiementFrais::create([
            'eleve_id' => $eleve2->id, 'frais_id' => $fraisPrim->id, 'moi_id' => $avril->id,
            'amount_paid' => $fraisPrim->montant, 'payment_date' => now(),
            'classe_id' => $eleve2->classe_id, 'session_scolaire_id' => $session->id,
        ]);

        Absence::create([
            'eleve_id' => $eleve2->id, 'date_absence' => now()->subDays(3),
            'motif' => 'Non spécifié', 'justifiee' => false,
        ]);

        // Liaison parent → enfants (N)
        $parent = User::where('username', 'parent-kasong')->first();
        if ($parent && $eleve1 && $eleve2) {
            $now = now();
            foreach ([$eleve1->id => 'pere', $eleve2->id => 'pere'] as $eid => $lien) {
                DB::table('parent_eleve')->updateOrInsert(
                    ['parent_id' => $parent->id, 'eleve_id' => $eid],
                    ['id' => (string) Str::uuid(), 'lien' => $lien, 'created_at' => $now, 'updated_at' => $now],
                );
            }
        }
    }
}
