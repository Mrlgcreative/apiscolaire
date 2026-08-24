<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'role' => $this->role,
            'institution' => new InstitutionResource($this->whenLoaded('institution')),
            'image' => $this->image,
            'telephone' => $this->telephone,
            'adresse' => $this->adresse,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
