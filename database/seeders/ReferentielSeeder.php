<?php

namespace Database\Seeders;

use App\Models\Mois;
use App\Models\Option;
use App\Models\SessionScolaire;
use Illuminate\Database\Seeder;

class ReferentielSeeder extends Seeder
{
    public function run(): void
    {
        $options = [
            'scientifique', 'commerciale', 'pedagogie genrale', 'electronique',
            'electricite', 'mecanique auto', 'mecanique generale', 'EB', 'literaire',
        ];

        foreach ($options as $nom) {
            Option::create(['nom' => $nom]);
        }

        SessionScolaire::create([
            'annee_debut' => 2024,
            'annee_fin' => 2025,
            'libelle' => '2024-2025',
            'est_active' => false,
        ]);

        SessionScolaire::create([
            'annee_debut' => 2025,
            'annee_fin' => 2026,
            'libelle' => '2025-2026',
            'est_active' => true,
        ]);

        $mois = [
            ['janvier', 1], ['fevrier', 2], ['mars', 3], ['avril', 4],
            ['mai', 5], ['juin', 6], ['juillet', 7], ['aout', 8],
            ['septembre', 9], ['octobre', 10], ['novembre', 11], ['decembre', 12],
        ];

        foreach ($mois as [$nom, $ordre]) {
            Mois::create(['nom' => $nom, 'ordre' => $ordre]);
        }
    }
}
