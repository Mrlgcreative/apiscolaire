<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfesseurResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'contact' => $this->contact,
            'email' => $this->email,
            'adresse' => $this->adresse,
            'date_embauche' => $this->date_embauche?->toDateString(),
        ];
    }
}
