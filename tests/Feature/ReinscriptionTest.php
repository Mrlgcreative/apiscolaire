<?php

namespace Tests\Feature;

use App\Models\Classe;
use App\Models\Cours;
use App\Models\Eleve;
use App\Models\Institution;
use App\Models\Note;
use App\Models\Periode;
use App\Models\SessionScolaire;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ReinscriptionTest extends TestCase
{
    use RefreshDatabase;

    private function creerSession(int $annee, bool $active): SessionScolaire
    {
        return SessionScolaire::create([
            'annee_debut' => $annee,
            'annee_fin' => $annee + 1,
            'libelle' => $annee.'-'.($annee + 1),
            'est_active' => $active,
        ]);
    }

    private function inscrireEleve(
        Institution $inst,
        SessionScolaire $session,
        Classe $classe,
        string $nom,
        string $matricule,
    ): Eleve {
        return Eleve::create([
            'matricule' => $matricule,
            'nom' => $nom,
            'post_nom' => 'Test',
            'prenom' => 'Env.',
            'date_naissance' => '2010-01-01',
            'sexe' => 'F',
            'lieu_naissance' => 'Kolwezi',
            'adresse' => 'Test',
            'section' => 'primaire',
            'classe_id' => $classe->id,
            'session_scolaire_id' => $session->id,
            'institution_id' => $inst->id,
        ]);
    }

    /** @return array{0: Periode, 1: Cours} */
    private function creerPeriodeEtCours(Institution $inst, SessionScolaire $session, Classe $classe): array
    {
        $periode = Periode::create([
            'institution_id' => $inst->id,
            'session_scolaire_id' => $session->id,
            'type' => 'periode',
            'section' => 'primaire',
            'code' => '1P',
            'libelle' => 'Periode 1',
            'ordre' => 1,
        ]);

        $cours = Cours::create([
            'titre' => 'Mathematiques',
            'section' => 'primaire',
            'classe_id' => $classe->id,
            'institution_id' => $inst->id,
        ]);

        return [$periode, $cours];
    }

    private function ajouterNote(
        Institution $inst,
        Eleve $eleve,
        SessionScolaire $session,
        Cours $cours,
        Periode $periode,
        float $note,
    ): void {
        Note::create([
            'institution_id' => $inst->id,
            'eleve_id' => $eleve->id,
            'cours_id' => $cours->id,
            'periode_id' => $periode->id,
            'session_scolaire_id' => $session->id,
            'note' => $note,
            'max' => 100,
        ]);
    }

    public function test_reinscription_conserve_le_matricule_et_promeut_en_classe_superieure(): void
    {
        $inst = Institution::create(['nom' => 'College Test', 'code' => 'TST', 'type' => 'primaire']);
        $admin = User::factory()->create(['username' => 'admin-tst', 'role' => 'admin', 'institution_id' => $inst->id]);
        Sanctum::actingAs($admin);

        // L'ancienne année est archivée, la nouvelle est active (comme après
        // l'appui sur « Nouvelle année » dans Paramètres).
        $ancienne = $this->creerSession(2025, false);
        $nouvelle = $this->creerSession(2026, true);

        $prim6 = Classe::create(['nom' => '6eme', 'section' => 'primaire', 'niveau' => '6eme', 'institution_id' => $inst->id]);
        $sec7 = Classe::create(['nom' => 'Sec 7eme', 'section' => 'secondaire', 'niveau' => '7eme', 'institution_id' => $inst->id]);

        $eleve = $this->inscrireEleve($inst, $ancienne, $prim6, 'Mukendi', 'CSK-2025-0001');
        [$periode, $cours] = $this->creerPeriodeEtCours($inst, $ancienne, $prim6);
        $this->ajouterNote($inst, $eleve, $ancienne, $cours, $periode, 72);

        $response = $this->postJson("/api/v1/eleves/{$eleve->id}/reinscrire");

        $response->assertStatus(201)
            ->assertJsonPath('data.matricule', 'CSK-2025-0001')
            ->assertJsonPath('reinscription.promu', true)
            ->assertJsonPath('reinscription.pourcentage', 72)
            ->assertJsonPath('reinscription.classe', 'Sec 7eme');

        $nouveau = Eleve::where('matricule', 'CSK-2025-0001')->where('session_scolaire_id', $nouvelle->id)->first();
        $this->assertNotNull($nouveau);
        $this->assertSame($sec7->id, $nouveau->classe_id);
        $this->assertTrue($nouveau->est_reinscrit);
        $this->assertSame('reinscrit', $eleve->refresh()->statut);

        // Une nouvelle inscription « manuelle », mais dans l'ancienne année (archivée) :
        // elle ne doit apparaître ni comme nouvelle inscription de la session active,
        $this->inscrireEleve($inst, $ancienne, $prim6, 'Kasongo', 'CSK-2025-0002');

        // « Nouvelles inscriptions » : uniquement la session active, ni réinscrits,
        // ni anciens dossiers sources, ni les actifs d'une année archivée.
        $this->getJson('/api/v1/eleves?reinscrit=false')
            ->assertJsonPath('meta.total', 0);
        // « Réinscriptions » : dossier recopié + ancien dossier source (statut 'reinscrit')
        $this->getJson('/api/v1/eleves?reinscrit=true')
            ->assertJsonPath('meta.total', 2)
            ->assertJsonFragment(['matricule' => 'CSK-2025-0001'])
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['statut' => 'reinscrit'])
            ->assertJsonFragment(['est_reinscrit' => true]);

        // Une seconde tentative est refusée
        $this->postJson("/api/v1/eleves/{$eleve->id}/reinscrire")->assertStatus(422);
    }

    public function test_reinscription_en_masse_promeut_ou_fait_redoubler_selon_la_moyenne(): void
    {
        $inst = Institution::create(['nom' => 'College Test', 'code' => 'TST', 'type' => 'primaire']);
        $admin = User::factory()->create(['username' => 'admin-tst', 'role' => 'admin', 'institution_id' => $inst->id]);
        Sanctum::actingAs($admin);

        $ancienne = $this->creerSession(2025, false);
        $nouvelle = $this->creerSession(2026, true);

        $prim4 = Classe::create(['nom' => '4eme', 'section' => 'primaire', 'niveau' => '4eme', 'institution_id' => $inst->id]);
        $prim5 = Classe::create(['nom' => '5eme', 'section' => 'primaire', 'niveau' => '5eme', 'institution_id' => $inst->id]);

        $brillante = $this->inscrireEleve($inst, $ancienne, $prim4, 'Mwamba', 'CSK-2025-0002');
        $faible = $this->inscrireEleve($inst, $ancienne, $prim4, 'Ilunga', 'CSK-2025-0003');

        [$periode, $cours] = $this->creerPeriodeEtCours($inst, $ancienne, $prim4);
        $this->ajouterNote($inst, $brillante, $ancienne, $cours, $periode, 80);
        $this->ajouterNote($inst, $faible, $ancienne, $cours, $periode, 30);

        $response = $this->postJson('/api/v1/eleves/reinscrire-en-masse', [
            'eleve_ids' => [$brillante->id, $faible->id],
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.resume.reinscrits', 2)
            ->assertJsonPath('data.resume.promus', 1)
            ->assertJsonPath('data.resume.redoublements', 1);

        $nouveauBrillante = Eleve::where('matricule', 'CSK-2025-0002')->where('session_scolaire_id', $nouvelle->id)->first();
        $nouveauFaible = Eleve::where('matricule', 'CSK-2025-0003')->where('session_scolaire_id', $nouvelle->id)->first();

        $this->assertSame($prim5->id, $nouveauBrillante->classe_id);
        $this->assertSame($prim4->id, $nouveauFaible->classe_id);
        $this->assertTrue($nouveauBrillante->est_reinscrit);
        $this->assertTrue($nouveauFaible->est_reinscrit);
        $this->assertSame('reinscrit', $brillante->refresh()->statut);
        $this->assertSame('reinscrit', $faible->refresh()->statut);
    }
}