<?php

namespace Tests\Feature;

use App\Models\Mois;
use App\Models\SessionScolaire;
use App\Models\User;
use Database\Seeders\ReferentielSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SessionsScolairesTest extends TestCase
{
    use RefreshDatabase;

    private function sessionActiveAvecMois(): SessionScolaire
    {
        $session = SessionScolaire::active()->firstOrFail();
        $mois = ['septembre', 'octobre', 'novembre', 'decembre', 'janvier', 'fevrier', 'mars', 'avril', 'mai', 'juin'];

        foreach ($mois as $i => $nom) {
            Mois::create(['nom' => $nom, 'ordre' => $i + 1, 'session_scolaire_id' => $session->id]);
        }

        return $session;
    }

    public function test_une_nouvelle_session_archive_la_precedente_et_clone_ses_mois(): void
    {
        $this->seed(ReferentielSeeder::class);
        $active = $this->sessionActiveAvecMois();

        $admin = User::factory()->create(['username' => 'superadmin', 'role' => 'admin', 'institution_id' => null]);
        Sanctum::actingAs($admin);

        $this->postJson('/api/v1/sessions-scolaires', [
            'annee_debut' => 2026,
            'annee_fin' => 2027,
        ])->assertStatus(201);

        $nouvelle = SessionScolaire::where('annee_debut', 2026)->first();
        $this->assertNotNull($nouvelle);
        $this->assertTrue($nouvelle->est_active);
        $this->assertFalse($active->refresh()->est_active);
        $this->assertCount(10, $nouvelle->mois);
        $this->assertSame(
            [1, 5, 10],
            $nouvelle->mois()->whereIn('nom', ['septembre', 'janvier', 'juin'])->pluck('ordre')->sort()->values()->all(),
        );
    }

    public function test_seul_le_super_admin_peut_creer_une_session(): void
    {
        $this->seed(ReferentielSeeder::class);

        $prof = User::factory()->create(['username' => 'prof', 'role' => 'professeur', 'institution_id' => null]);
        Sanctum::actingAs($prof);

        $this->postJson('/api/v1/sessions-scolaires', ['annee_debut' => 2026, 'annee_fin' => 2027])
            ->assertStatus(403);
    }
}