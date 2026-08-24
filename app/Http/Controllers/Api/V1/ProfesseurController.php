<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreProfesseurRequest;
use App\Http\Requests\V1\UpdateProfesseurRequest;
use App\Http\Resources\V1\ProfesseurResource;
use App\Models\Professeur;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProfesseurController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $professeurs = Professeur::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = '%'.$request->search.'%';

                return $q->where(fn ($q) => $q
                    ->where('nom', 'like', $term)
                    ->orWhere('prenom', 'like', $term));
            })
            ->orderBy('nom')
            ->paginate(min(max($request->integer('per_page', 15), 1), 100))
            ->withQueryString();

        return ProfesseurResource::collection($professeurs);
    }

    public function store(StoreProfesseurRequest $request): JsonResponse
    {
        return (new ProfesseurResource(Professeur::create($request->validated())))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Professeur $professeur): ProfesseurResource
    {
        return new ProfesseurResource($professeur->load('cours'));
    }

    public function update(UpdateProfesseurRequest $request, Professeur $professeur): ProfesseurResource
    {
        $professeur->update($request->validated());

        return new ProfesseurResource($professeur);
    }

    public function destroy(Professeur $professeur): JsonResponse
    {
        $professeur->delete();

        return response()->json(['message' => 'Professeur supprimé.']);
    }
}
