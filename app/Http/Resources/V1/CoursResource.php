<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CoursResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'titre' => $this->titre,
            'description' => $this->description,
            'section' => $this->section,
            'coefficient' => $this->coefficient,
            'heures_semaine' => $this->heures_semaine,
            'professeur' => new ProfesseurResource($this->whenLoaded('professeur')),
            'classe' => new ClasseResource($this->whenLoaded('classe')),
            'option' => new OptionResource($this->whenLoaded('option')),
        ];
    }
}
