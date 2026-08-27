<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StorePaiementFraisRequest;
use App\Http\Resources\V1\PaiementFraisResource;
use App\Models\Eleve;
use App\Models\PaiementFrais;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class PaiementFraisController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $paiements = PaiementFrais::query()
            ->with(['eleve.classe', 'frais', 'mois', 'user'])
            ->when($request->filled('eleve_id'), fn ($q) => $q->where('eleve_id', $request->eleve_id))
            ->when($request->filled('moi_id'), fn ($q) => $q->where('moi_id', $request->moi_id))
            ->when($request->filled('classe_id'), fn ($q) => $q->where('classe_id', $request->classe_id))
            ->when($request->filled('session_scolaire_id'), fn ($q) => $q->where('session_scolaire_id', $request->session_scolaire_id))
            ->when($request->filled('statut'), fn ($q) => $q->where('statut', $request->statut))
            ->latest('payment_date')
            ->paginate(min(max($request->integer('per_page', 15), 1), 100))
            ->withQueryString();

        return PaiementFraisResource::collection($paiements);
    }

    public function store(StorePaiementFraisRequest $request): JsonResponse
    {
        $data = $request->validated();

        $duplicate = PaiementFrais::query()
            ->where('eleve_id', $data['eleve_id'])
            ->where('frais_id', $data['frais_id'])
            ->where('moi_id', $data['moi_id'])
            ->exists();

        if ($duplicate) {
            return response()->json([
                'message' => 'Ce mois est déjà enregistré pour cet élève et ce frais.',
            ], 409);
        }

        $paiement = DB::transaction(function () use ($request, $data) {
            $eleve = Eleve::findOrFail($data['eleve_id']);

            return PaiementFrais::create([
                ...$data,
                'classe_id' => $eleve->classe_id,
                'session_scolaire_id' => $eleve->session_scolaire_id,
                'user_id' => $request->user()?->id,
                'statut' => $data['statut'] ?? 'paye',
            ]);
        });

        return (new PaiementFraisResource($paiement->load(['eleve', 'frais', 'mois', 'user'])))
            ->response()
            ->setStatusCode(201);
    }

    public function show(PaiementFrais $paiement): PaiementFraisResource
    {
        return new PaiementFraisResource($paiement->load(['eleve', 'frais', 'mois', 'user']));
    }

    public function destroy(PaiementFrais $paiement): JsonResponse
    {
        $paiement->delete();

        return response()->json(['message' => 'Paiement supprimé.']);
    }
}
