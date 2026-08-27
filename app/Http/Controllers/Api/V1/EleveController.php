<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreEleveRequest;
use App\Http\Requests\V1\UpdateEleveRequest;
use App\Http\Resources\V1\EleveResource;
use App\Models\Eleve;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class EleveController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $eleves = Eleve::query()
            ->with(['classe', 'option', 'sessionScolaire'])
            ->when($request->boolean('actifs'), fn ($q) => $q->actifs())
            ->when($request->filled('reinscrit'), function ($q) use ($request) {
                if ($request->boolean('reinscrit')) {
                    // « Réinscriptions » : dossier recopié dans la nouvelle année
                    // (est_reinscrit) OU ancien dossier déjà transféré (statut
                    // 'reinscrit' côté source).
                    $q->where(fn ($q) => $q->where('est_reinscrit', true)->orWhere('statut', 'reinscrit'));
                } else {
                    // « Nouvelles inscriptions » : ni recopiés, ni les anciens dossiers
                    // déjà transférés (statut 'reinscrit' du côté source). Par défaut,
                    // uniquement ceux de la session active (une année précise affichée
                    // dans la barre de filtrage prime sur la session active).
                    $q->where('est_reinscrit', false)->where('statut', 'actif');
                    if (! $request->filled('session_scolaire_id')) {
                        $q->whereHas('sessionScolaire', fn ($q) => $q->where('est_active', true));
                    }
                }
            })
            ->when($request->filled('section'), fn ($q) => $q->where('section', $request->section))
            ->when($request->filled('classe_id'), fn ($q) => $q->where('classe_id', $request->classe_id))
            ->when($request->filled('option_id'), fn ($q) => $q->where('option_id', $request->option_id))
            ->when($request->filled('session_scolaire_id'), fn ($q) => $q->where('session_scolaire_id', $request->session_scolaire_id))
            ->when($request->filled('sexe'), fn ($q) => $q->where('sexe', $request->sexe))
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = '%'.$request->search.'%';

                return $q->where(fn ($q) => $q
                    ->where('nom', 'like', $term)
                    ->orWhere('post_nom', 'like', $term)
                    ->orWhere('prenom', 'like', $term)
                    ->orWhere('matricule', 'like', $term));
            })
            ->latest()
            ->paginate(min(max($request->integer('per_page', 15), 1), 100))
            ->withQueryString();

        return EleveResource::collection($eleves);
    }

    public function store(StoreEleveRequest $request): JsonResponse
    {
        $eleve = DB::transaction(function () use ($request) {
            $data = $request->validated();

            if ($request->hasFile('photo')) {
                $data['photo'] = $request->file('photo')->store('eleves', 'public');
            }

            return Eleve::create($data);
        });

        return (new EleveResource($eleve->load(['classe', 'option'])))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Eleve $eleve): EleveResource
    {
        return new EleveResource($eleve->load(['classe', 'option', 'sessionScolaire']));
    }

    public function update(UpdateEleveRequest $request, Eleve $eleve): EleveResource
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('eleves', 'public');
        }

        $eleve->update($data);

        return new EleveResource($eleve->load(['classe', 'option']));
    }

    public function destroy(Eleve $eleve): JsonResponse
    {
        $eleve->update(['statut' => 'archive']);

        return response()->json(['message' => 'Élève archivé.']);
    }
}
