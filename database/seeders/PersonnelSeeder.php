<?php

namespace Database\Seeders;

use App\Models\Classe;
use App\Models\Cours;
use App\Models\Eleve;
use App\Models\Frais;
use App\Models\Institution;
use App\Models\Option;
use App\Models\Professeur;
use App\Models\SessionScolaire;
use App\Models\User;
use Illuminate\Database\Seeder;

class PersonnelSeeder extends Seeder
{
    public function run(): void
    {
        $csk = Institution::where('code', 'CSK')->first();
        $glg = Institution::where('code', 'GLG')->first();

        // ---- École CSK -------------------------------------------------
        $profsCsk = collect([
            ['nom' => 'sky', 'prenom' => 'elle', 'contact' => '0979099031', 'email' => 'skyelle@gmail.com', 'adresse' => 'ikuku'],
            ['nom' => 'Mujing', 'prenom' => 'asnat', 'contact' => '+243 999099031', 'email' => 'ciella@mail.com', 'adresse' => 'AV. Mbembe, Q.KAMANYOLA No. 48'],
            ['nom' => 'KAPEND', 'prenom' => 'FABRICE', 'contact' => '+243 999099031', 'email' => 'moilui@gmail.com', 'adresse' => 'Kolwezi.manika, Moïse Tshombe, mbembe,48'],
        ])->map(fn (array $p) => Professeur::create([...$p, 'institution_id' => $csk->id]));

        // Lier le premier prof à son compte user professeur
        $profUser = User::where('username', 'prof-sky')->first();
        if ($profUser) {
            $profsCsk[0]->update(['user_id' => $profUser->id]);
        }

        $classesCsk = collect([
            ['nom' => '1er', 'section' => 'maternelle', 'niveau' => '1ère', 'titulaire' => 'KAPEND FABRICE'],
            ['nom' => '2eme', 'section' => 'maternelle', 'niveau' => '2eme', 'titulaire' => 'Mujing asnat'],
            ['nom' => '1er', 'section' => 'primaire', 'niveau' => '1ère', 'titulaire' => 'Mujing asnat'],
            ['nom' => '6eme', 'section' => 'primaire', 'niveau' => '6ème', 'titulaire' => 'Mujing asnat'],
            ['nom' => 'Sec 7eme', 'section' => 'secondaire', 'niveau' => '7eme', 'titulaire' => 'Mujing asnat'],
            ['nom' => 'Sec 1ère', 'section' => 'secondaire', 'niveau' => '1ère', 'titulaire' => 'Mujing asnat'],
        ])->map(fn (array $c) => Classe::create([...$c, 'institution_id' => $csk->id]));

        Cours::create(['titre' => 'Francais', 'description' => 'Langue française', 'section' => 'secondaire', 'professeur_id' => $profsCsk[0]->id, 'classe_id' => $classesCsk[5]->id, 'institution_id' => $csk->id]);
        Cours::create(['titre' => 'Maths', 'description' => 'Mathématiques', 'section' => 'maternelle', 'professeur_id' => $profsCsk[2]->id, 'classe_id' => $classesCsk[0]->id, 'institution_id' => $csk->id]);

        Frais::create(['montant' => 65, 'description' => 'Frais mensuel_maternelle', 'section' => 'maternelle', 'institution_id' => $csk->id]);
        Frais::create(['montant' => 100, 'description' => 'Mensuels', 'section' => 'primaire', 'institution_id' => $csk->id]);
        Frais::create(['montant' => 50, 'description' => 'FIP', 'section' => 'secondaire', 'institution_id' => $csk->id]);

        $session = SessionScolaire::where('est_active', true)->first();

        Eleve::create([
            'nom' => 'Kasong', 'post_nom' => 'Tshisola', 'prenom' => 'Malika',
            'date_naissance' => '2020-04-19', 'sexe' => 'F', 'lieu_naissance' => 'Kolwezi',
            'adresse' => 'Kolwezi.manika, Moïse Tshombe, mbembe,48', 'section' => 'maternelle',
            'classe_id' => $classesCsk[1]->id, 'session_scolaire_id' => $session->id,
            'nom_pere' => 'chris', 'nom_mere' => 'Kaj',
            'contact_pere' => '+243 97 90 99 01', 'contact_mere' => '+243 89 07 91 92',
            'institution_id' => $csk->id,
        ]);

        Eleve::create([
            'nom' => 'kapend', 'post_nom' => 'Mwinkeu', 'prenom' => 'Fabrice',
            'date_naissance' => '2009-04-19', 'sexe' => 'M', 'lieu_naissance' => 'kolwezi',
            'adresse' => 'Kolwezi.manika, Moïse Tshombe, mbembe,48', 'section' => 'secondaire',
            'option_id' => Option::where('nom', 'EB')->first()->id,
            'classe_id' => $classesCsk[5]->id, 'session_scolaire_id' => $session->id,
            'nom_pere' => 'fabrice', 'nom_mere' => 'kapend',
            'contact_pere' => '0970998700', 'contact_mere' => '0988743400',
            'institution_id' => $csk->id,
        ]);

        // ---- École GLG --------------------------------------------------
        $profGlg = Professeur::create([
            'nom' => 'Ilunga', 'prenom' => 'Grace', 'contact' => '+243 99 88 77 66',
            'email' => 'grace@glg.test', 'adresse' => 'Avenue de la Paix 12, Kolwezi',
            'institution_id' => $glg->id,
        ]);

        $classeGlg = Classe::create([
            'nom' => '1er', 'section' => 'primaire', 'niveau' => '1ère',
            'titulaire' => 'Ilunga Grace', 'institution_id' => $glg->id,
        ]);

        Frais::create(['montant' => 80, 'description' => 'Frais mensuels primaire', 'section' => 'primaire', 'institution_id' => $glg->id]);

        Eleve::create([
            'nom' => 'Divina', 'post_nom' => 'Tshisola', 'prenom' => 'Malika',
            'date_naissance' => '2014-04-11', 'sexe' => 'F', 'lieu_naissance' => 'Kolwezi',
            'adresse' => 'AV. Mbembe, Q.KAMANYOLA No. 48', 'section' => 'primaire',
            'classe_id' => $classeGlg->id, 'session_scolaire_id' => $session->id,
            'nom_pere' => 'moi', 'nom_mere' => 'lui',
            'contact_pere' => '+243 97 90 99 01', 'contact_mere' => '+243 89 07 91 92',
            'institution_id' => $glg->id,
        ]);
    }
}
