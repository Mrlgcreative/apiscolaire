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
            ['septembre', 1], ['octobre', 2], ['novembre', 3], ['decembre', 4],
            ['janvier', 5], ['fevrier', 6], ['mars', 7], ['avril', 8],
            ['mai', 9], ['juin', 10],
        ];

        foreach ($mois as [$nom, $ordre]) {
            Mois::create(['nom' => $nom, 'ordre' => $ordre]);
        }
    }
}
