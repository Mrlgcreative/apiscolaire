<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\SessionScolaireResource;
use App\Models\SessionScolaire;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SessionScolaireController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return SessionScolaireResource::collection(
            SessionScolaire::orderByDesc('annee_debut')->get(),
        );
    }

    public function active(): SessionScolaireResource
    {
        return new SessionScolaireResource(
            SessionScolaire::active()->firstOrFail(),
        );
    }
}
