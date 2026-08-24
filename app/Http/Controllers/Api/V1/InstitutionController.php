<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreInstitutionRequest;
use App\Http\Requests\V1\UpdateInstitutionRequest;
use App\Http\Resources\V1\InstitutionResource;
use App\Models\Institution;
use App\Support\CurrentInstitution;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class InstitutionController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        // Super-admin : toutes les écoles. Admin d'école : la sienne.
        if ($request->user()?->isSchoolAdmin()) {
            return InstitutionResource::collection(
                Institution::where('id', app(CurrentInstitution::class)->id)->get(),
            );
        }

        abort_unless($request->user()?->isGroupAdmin(), 403, 'Accès réservé aux administrateurs.');

        return InstitutionResource::collection(
            Institution::orderBy('nom')->get(),
        );
    }

    public function store(StoreInstitutionRequest $request): JsonResponse
    {
        abort_unless($request->user()?->isGroupAdmin(), 403, 'Seul le super-admin peut créer une école.');

        return (new InstitutionResource(Institution::create($request->validated())))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, Institution $institution): InstitutionResource
    {
        $this->authorizeSchool($request, $institution);

        return new InstitutionResource($institution);
    }

    public function update(UpdateInstitutionRequest $request, Institution $institution): InstitutionResource
    {
        $this->authorizeSchool($request, $institution);

        $institution->update($request->validated());

        return new InstitutionResource($institution);
    }

    public function destroy(Request $request, Institution $institution): JsonResponse
    {
        abort_unless($request->user()?->isGroupAdmin(), 403, 'Seul le super-admin peut supprimer une école.');

        if ($institution->eleves()->exists()) {
            return response()->json([
                'message' => 'Impossible de supprimer une institution contenant des élèves.',
            ], 409);
        }

        $institution->delete();

        return response()->json(['message' => 'Institution supprimée.']);
    }

    /**
     * Super-admin : accès total. Admin d'école : uniquement sa propre école (404 sinon).
     */
    private function authorizeSchool(Request $request, Institution $institution): void
    {
        if ($request->user()?->isGroupAdmin()) {
            return;
        }

        abort_unless(
            $request->user()?->isSchoolAdmin()
                && $institution->id === app(CurrentInstitution::class)->id,
            404,
            'Ressource introuvable.',
        );
    }
}
