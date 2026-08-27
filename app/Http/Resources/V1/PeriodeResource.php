<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PeriodeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'institution' => new InstitutionResource($this->whenLoaded('institution')),
            'session_scolaire' => new SessionScolaireResource($this->whenLoaded('sessionScolaire')),
            'parent' => new self($this->whenLoaded('parent')),
            'type' => $this->type,
            'section' => $this->section,
            'code' => $this->code,
            'libelle' => $this->libelle,
            'ordre' => $this->ordre,
            'date_debut' => $this->date_debut?->toDateString(),
            'date_fin' => $this->date_fin?->toDateString(),
            'est_cloturee' => $this->est_cloturee,
            'enfants' => self::collection($this->whenLoaded('enfants')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
