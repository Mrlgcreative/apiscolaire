<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NoteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'note' => (float) $this->note,
            'max' => (float) $this->max,
            'coefficient' => (float) $this->coefficient,
            'commentaire' => $this->commentaire,
            'pourcentage' => $this->max > 0 ? round((float) $this->note / (float) $this->max * 100, 2) : 0,
            'eleve' => new EleveResource($this->whenLoaded('eleve')),
            'cours' => new CoursResource($this->whenLoaded('cours')),
            'periode' => new PeriodeResource($this->whenLoaded('periode')),
            'professeur' => new ProfesseurResource($this->whenLoaded('professeur')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
