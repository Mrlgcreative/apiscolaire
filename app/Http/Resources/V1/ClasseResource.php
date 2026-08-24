<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClasseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'section' => $this->section,
            'niveau' => $this->niveau,
            'titulaire' => $this->titulaire,
            'professeur' => new ProfesseurResource($this->whenLoaded('professeur')),
            'effectif' => $this->whenCounted('eleves'),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
