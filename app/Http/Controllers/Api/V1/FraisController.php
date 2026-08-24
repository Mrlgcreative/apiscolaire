<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreFraisRequest;
use App\Http\Resources\V1\FraisResource;
use App\Models\Frais;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FraisController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $frais = Frais::query()
            ->when($request->filled('section'), fn ($q) => $q->where('section', $request->section))
            ->orderBy('description')
            ->get();

        return FraisResource::collection($frais);
    }

    public function store(StoreFraisRequest $request): JsonResponse
    {
        return (new FraisResource(Frais::create($request->validated())))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Frais $frais): FraisResource
    {
        return new FraisResource($frais);
    }

    public function update(StoreFraisRequest $request, Frais $frais): FraisResource
    {
        $frais->update($request->validated());

        return new FraisResource($frais);
    }

    public function destroy(Frais $frais): JsonResponse
    {
        $frais->delete();

        return response()->json(['message' => 'Frais supprimé.']);
    }
}
