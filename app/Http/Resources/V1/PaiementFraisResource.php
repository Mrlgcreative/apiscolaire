<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaiementFraisResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'montant' => $this->amount_paid,
            'payment_date' => $this->payment_date?->toDateString(),
            'statut' => $this->statut,
            'eleve' => new EleveResource($this->whenLoaded('eleve')),
            'frais' => new FraisResource($this->whenLoaded('frais')),
            'mois' => new MoisResource($this->whenLoaded('mois')),
            'classe' => new ClasseResource($this->whenLoaded('classe')),
            'encaisse_par' => new UserResource($this->whenLoaded('user')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
