<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\MoisResource;
use App\Models\Mois;
use App\Models\SessionScolaire;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MoisController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $sessionId = $request->filled('session_scolaire_id')
            ? $request->session_scolaire_id
            : SessionScolaire::active()->value('id');

        return MoisResource::collection(
            Mois::query()
                ->when($sessionId, fn ($q) => $q->where('session_scolaire_id', $sessionId))
                ->orderBy('ordre')
                ->get(),
        );
    }
}
