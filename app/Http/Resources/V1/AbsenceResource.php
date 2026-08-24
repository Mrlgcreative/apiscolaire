<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AbsenceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'date_absence' => $this->date_absence?->toDateString(),
            'motif' => $this->motif,
            'justifiee' => $this->justifiee,
            'eleve' => new EleveResource($this->whenLoaded('eleve')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
