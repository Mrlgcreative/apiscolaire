<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SessionScolaireResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'annee_debut' => $this->annee_debut,
            'annee_fin' => $this->annee_fin,
            'libelle' => $this->libelle,
            'est_active' => $this->est_active,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
