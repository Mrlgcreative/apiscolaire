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
            // Secondaire (7e CTEB) : 2 semestres → 4 périodes
            // S1 contient 1P + 2P, S2 contient 3P + 4P
            foreach ([
                ['code' => 'S1', 'libelle' => 'Semestre 1', 'ordre' => 1],
                ['code' => 'S2', 'libelle' => 'Semestre 2', 'ordre' => 2],
            ] as $sem) {
                $parent = Periode::create([
                    'institution_id' => $inst->id,
                    'session_scolaire_id' => $session->id,
                    'type' => 'semestre',
                    'section' => 'secondaire',
                    'code' => $sem['code'],
                    'libelle' => $sem['libelle'],
                    'ordre' => $sem['ordre'],
                ]);

                $prefix = $sem['code'] === 'S1' ? 0 : 2;
                foreach ([1, 2] as $i) {
                    Periode::create([
                        'institution_id' => $inst->id,
                        'session_scolaire_id' => $session->id,
                        'parent_id' => $parent->id,
                        'type' => 'periode',
                        'section' => 'secondaire',
                        'code' => $prefix + $i.'P',
                        'libelle' => ($prefix + $i).'ème Période',
                        'ordre' => $i,
                    ]);
                }
            }

            // Primaire + Maternelle : 3 trimestres → 2 périodes chacun
            // T1 → 1P + 2P, T2 → 3P + 4P, T3 → 5P + 6P
            foreach (['primaire', 'maternelle'] as $section) {
                foreach ([
                    ['code' => 'T1', 'libelle' => 'Trimestre 1', 'ordre' => 1],
                    ['code' => 'T2', 'libelle' => 'Trimestre 2', 'ordre' => 2],
                    ['code' => 'T3', 'libelle' => 'Trimestre 3', 'ordre' => 3],
                ] as $idx => $trim) {
                    $parent = Periode::create([
                        'institution_id' => $inst->id,
                        'session_scolaire_id' => $session->id,
                        'type' => 'trimestre',
                        'section' => $section,
                        'code' => $trim['code'],
                        'libelle' => $trim['libelle'],
                        'ordre' => $trim['ordre'],
                    ]);

                    foreach ([1, 2] as $i) {
                        $num = $idx * 2 + $i;
                        Periode::create([
                            'institution_id' => $inst->id,
                            'session_scolaire_id' => $session->id,
                            'parent_id' => $parent->id,
                            'type' => 'periode',
                            'section' => $section,
                            'code' => $num.'P',
                            'libelle' => $num.'ème Période',
                            'ordre' => $i,
                        ]);
                    }
                }
            }
        }
    }
}
