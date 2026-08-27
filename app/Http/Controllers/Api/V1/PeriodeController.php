<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StorePeriodeRequest;
use App\Http\Resources\V1\PeriodeResource;
use App\Models\Periode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PeriodeController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $periodes = Periode::query()
            ->with(['parent', 'enfants'])
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->type))
            ->when($request->filled('section'), fn ($q) => $q->where('section', $request->section))
            ->when($request->filled('session_scolaire_id'), fn ($q) => $q->where('session_scolaire_id', $request->session_scolaire_id))
            ->orderBy('ordre')
            ->get();

        return PeriodeResource::collection($periodes);
    }

    public function store(StorePeriodeRequest $request): JsonResponse
    {
        $periode = Periode::create($request->validated());

        return (new PeriodeResource($periode->load(['parent'])))->response()->setStatusCode(201);
    }

    public function show(Periode $periode): PeriodeResource
    {
        return new PeriodeResource($periode->load(['parent', 'enfants', 'sessionScolaire']));
    }

    public function update(StorePeriodeRequest $request, Periode $periode): PeriodeResource
    {
        $periode->update($request->validated());

        return new PeriodeResource($periode->load(['parent']));
    }

    public function destroy(Periode $periode): JsonResponse
    {
        if ($periode->enfants()->exists() || $periode->notes()->exists()) {
            return response()->json(['message' => 'Impossible de supprimer une période utilisée.'], 409);
        }
        $periode->delete();

        return response()->json(['message' => 'Période supprimée.']);
    }
}
