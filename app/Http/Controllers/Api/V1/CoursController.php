<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreCoursRequest;
use App\Http\Requests\V1\UpdateCoursRequest;
use App\Http\Resources\V1\CoursResource;
use App\Models\Cours;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CoursController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $cours = Cours::query()
            ->with(['classe', 'professeur', 'option'])
            ->when($request->filled('section'), fn ($q) => $q->where('section', $request->section))
            ->when($request->filled('classe_id'), fn ($q) => $q->where('classe_id', $request->classe_id))
            ->when($request->filled('professeur_id'), fn ($q) => $q->where('professeur_id', $request->professeur_id))
            ->orderBy('titre')
            ->get();

        return CoursResource::collection($cours);
    }

    public function store(StoreCoursRequest $request): JsonResponse
    {
        return (new CoursResource(Cours::create($request->validated())->load(['classe', 'professeur'])))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Cours $cours): CoursResource
    {
        return new CoursResource($cours->load(['classe', 'professeur', 'option']));
    }

    public function update(UpdateCoursRequest $request, Cours $cours): CoursResource
    {
        $cours->update($request->validated());

        return new CoursResource($cours->load(['classe', 'professeur']));
    }

    public function destroy(Cours $cours): JsonResponse
    {
        $cours->delete();

        return response()->json(['message' => 'Cours supprimé.']);
    }
}
