<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreClasseRequest;
use App\Http\Requests\V1\UpdateClasseRequest;
use App\Http\Resources\V1\ClasseResource;
use App\Models\Classe;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ClasseController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $classes = Classe::query()
            ->with('professeur')
            ->withCount('eleves')
            ->when($request->filled('section'), fn ($q) => $q->where('section', $request->section))
            ->orderBy('section')
            ->orderBy('nom')
            ->get();

        return ClasseResource::collection($classes);
    }

    public function store(StoreClasseRequest $request): JsonResponse
    {
        $classe = Classe::create($request->validated());

        return (new ClasseResource($classe->load('professeur')))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Classe $classe): ClasseResource
    {
        return new ClasseResource($classe->load(['professeur', 'eleves']));
    }

    public function update(UpdateClasseRequest $request, Classe $classe): ClasseResource
    {
        $classe->update($request->validated());

        return new ClasseResource($classe->load('professeur'));
    }

    public function destroy(Classe $classe): JsonResponse
    {
        $classe->delete();

        return response()->json(['message' => 'Classe supprimée.']);
    }
}
