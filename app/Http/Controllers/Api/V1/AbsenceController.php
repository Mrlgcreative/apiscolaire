<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreAbsenceRequest;
use App\Http\Resources\V1\AbsenceResource;
use App\Models\Absence;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AbsenceController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $absences = Absence::query()
            ->with('eleve')
            ->when($request->filled('eleve_id'), fn ($q) => $q->where('eleve_id', $request->eleve_id))
            ->when($request->filled('justifiee'), fn ($q) => $q->where('justifiee', $request->boolean('justifiee')))
            ->when($request->filled('date_debut'), fn ($q) => $q->where('date_absence', '>=', $request->date_debut))
            ->when($request->filled('date_fin'), fn ($q) => $q->where('date_absence', '<=', $request->date_fin))
            ->latest('date_absence')
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        return AbsenceResource::collection($absences);
    }

    public function store(StoreAbsenceRequest $request): JsonResponse
    {
        $dejaPresente = Absence::where('eleve_id', $request->eleve_id)
            ->whereDate('date_absence', $request->date_absence)
            ->exists();

        if ($dejaPresente) {
            return response()->json([
                'message' => 'Une absence est déjà enregistrée pour cet élève à cette date.',
            ], 409);
        }

        $absence = Absence::create([
            ...$request->validated(),
            'justifiee' => $request->boolean('justifiee'),
        ]);

        return (new AbsenceResource($absence->load('eleve')))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Absence $absence): AbsenceResource
    {
        return new AbsenceResource($absence->load('eleve'));
    }

    public function update(StoreAbsenceRequest $request, Absence $absence): AbsenceResource
    {
        $absence->update($request->validated());

        return new AbsenceResource($absence->load('eleve'));
    }

    public function destroy(Absence $absence): JsonResponse
    {
        $absence->delete();

        return response()->json(['message' => 'Absence supprimée.']);
    }
}
