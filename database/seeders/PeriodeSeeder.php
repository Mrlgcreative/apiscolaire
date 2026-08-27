<?php

namespace Database\Seeders;

use App\Models\Institution;
use App\Models\Periode;
use App\Models\SessionScolaire;
use Illuminate\Database\Seeder;

class PeriodeSeeder extends Seeder
{
    public function run(): void
    {
        $session = SessionScolaire::where('est_active', true)->first() ?? SessionScolaire::first();
        if (! $session) {
            return;
        }

        foreach (Institution::all() as $inst) {
            // 7e CTEB : 2 semestres → 4 périodes (les deux systèmes : semestres + périodes)
            // S1 contient 1P + 2P, S2 contient 3P + 4P
            $s1 = Periode::create([
                'institution_id' => $inst->id,
                'session_scolaire_id' => $session->id,
                'type' => 'semestre',
                'code' => 'S1',
                'libelle' => 'Semestre 1',
                'ordre' => 1,
            ]);

            $s2 = Periode::create([
                'institution_id' => $inst->id,
                'session_scolaire_id' => $session->id,
                'type' => 'semestre',
                'code' => 'S2',
                'libelle' => 'Semestre 2',
                'ordre' => 2,
            ]);

            // Périodes rattachées aux semestres
            Periode::create([
                'institution_id' => $inst->id,
                'session_scolaire_id' => $session->id,
                'parent_id' => $s1->id,
                'type' => 'periode',
                'code' => '1P',
                'libelle' => '1ère Période',
                'ordre' => 1,
            ]);
            Periode::create([
                'institution_id' => $inst->id,
                'session_scolaire_id' => $session->id,
                'parent_id' => $s1->id,
                'type' => 'periode',
                'code' => '2P',
                'libelle' => '2ème Période',
                'ordre' => 2,
            ]);
            Periode::create([
                'institution_id' => $inst->id,
                'session_scolaire_id' => $session->id,
                'parent_id' => $s2->id,
                'type' => 'periode',
                'code' => '3P',
                'libelle' => '3ème Période',
                'ordre' => 3,
            ]);
            Periode::create([
                'institution_id' => $inst->id,
                'session_scolaire_id' => $session->id,
                'parent_id' => $s2->id,
                'type' => 'periode',
                'code' => '4P',
                'libelle' => '4ème Période',
                'ordre' => 4,
            ]);

            // Trimestres (les deux systèmes) — T1 = 1P+2P, T2 = 3P, T3 = 4P (flexible)
            Periode::create([
                'institution_id' => $inst->id,
                'session_scolaire_id' => $session->id,
                'type' => 'trimestre',
                'code' => 'T1',
                'libelle' => 'Trimestre 1',
                'ordre' => 10,
            ]);
            Periode::create([
                'institution_id' => $inst->id,
                'session_scolaire_id' => $session->id,
                'type' => 'trimestre',
                'code' => 'T2',
                'libelle' => 'Trimestre 2',
                'ordre' => 11,
            ]);
            Periode::create([
                'institution_id' => $inst->id,
                'session_scolaire_id' => $session->id,
                'type' => 'trimestre',
                'code' => 'T3',
                'libelle' => 'Trimestre 3',
                'ordre' => 12,
            ]);
        }
    }
}
