<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\SessionScolaireResource;
use App\Models\Mois;
use App\Models\SessionScolaire;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class SessionScolaireController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return SessionScolaireResource::collection(
            SessionScolaire::orderByDesc('annee_debut')->get(),
        );
    }

    public function active(): SessionScolaireResource
    {
        return new SessionScolaireResource(
            SessionScolaire::active()->firstOrFail(),
        );
    }

    public function store(Request $request): JsonResponse
    {
        if (! $request->user()?->isGroupAdmin()) {
            return response()->json(['message' => 'Réservé au super-admin.'], 403);
        }

        $data = $request->validate([
            'annee_debut' => ['required', 'integer', 'min:2000', 'max:2200'],
            'annee_fin' => ['required', 'integer', 'gt:annee_debut', 'max:2200'],
            'libelle' => ['nullable', 'string', 'max:50'],
        ]);

        $session = DB::transaction(function () use ($data) {
            $precedente = SessionScolaire::active()->first();

            $session = SessionScolaire::create([
                'annee_debut' => $data['annee_debut'],
                'annee_fin' => $data['annee_fin'],
                'libelle' => $data['libelle'] ?? "{$data['annee_debut']}-{$data['annee_fin']}",
                'est_active' => true,
            ]);

            // La précédente devient archivée : ses données (élèves, paiements,
            // bulletins, mois…) restent rattachées à elle, mais seule la
            // nouvelle session est active.
            if ($precedente !== null) {
                $precedente->update(['est_active' => false]);

                // Nouvelle année scolaire = nouveaux mois (10 : septembre → juin).
                foreach ($precedente->mois()->orderBy('ordre')->get() as $m) {
                    Mois::create([
                        'nom' => $m->nom,
                        'ordre' => $m->ordre,
                        'session_scolaire_id' => $session->id,
                    ]);
                }
            } else {
                foreach (['septembre', 'octobre', 'novembre', 'decembre', 'janvier', 'fevrier', 'mars', 'avril', 'mai', 'juin'] as $i => $nom) {
                    Mois::create(['nom' => $nom, 'ordre' => $i + 1, 'session_scolaire_id' => $session->id]);
                }
            }

            return $session;
        });

        return (new SessionScolaireResource($session))
            ->response()
            ->setStatusCode(201);
    }
}
