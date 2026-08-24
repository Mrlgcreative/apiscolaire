<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FraisResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'montant' => $this->montant,
            'description' => $this->description,
            'section' => $this->section,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
