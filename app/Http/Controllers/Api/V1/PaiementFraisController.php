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
            ->with(['eleve.classe', 'frais', 'mois'])
            ->when($request->filled('eleve_id'), fn ($q) => $q->where('eleve_id', $request->eleve_id))
            ->when($request->filled('moi_id'), fn ($q) => $q->where('moi_id', $request->moi_id))
            ->when($request->filled('classe_id'), fn ($q) => $q->where('classe_id', $request->classe_id))
            ->when($request->filled('statut'), fn ($q) => $q->where('statut', $request->statut))
            ->latest('payment_date')
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        return PaiementFraisResource::collection($paiements);
    }

    public function store(StorePaiementFraisRequest $request): JsonResponse
    {
        $paiement = DB::transaction(function () use ($request) {
            $data = $request->validated();
            $eleve = Eleve::findOrFail($data['eleve_id']);

            return PaiementFrais::create([
                ...$data,
                'classe_id' => $eleve->classe_id,
                'session_scolaire_id' => $eleve->session_scolaire_id,
                'user_id' => $request->user()?->id,
                'statut' => $data['statut'] ?? 'paye',
            ]);
        });

        return (new PaiementFraisResource($paiement->load(['eleve', 'frais', 'mois'])))
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
