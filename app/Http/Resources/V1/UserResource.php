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
            'professeur' => $this->whenLoaded('professeur', fn () => [
                'id' => $this->professeur->id,
                'nom' => $this->professeur->nom,
                'prenom' => $this->professeur->prenom,
            ]),
            'enfants' => $this->whenLoaded('enfants', fn () => $this->enfants
                ->filter(fn ($e) => $e->statut === 'actif')
                ->map(fn ($e) => [
                    'id' => $e->id,
                    'matricule' => $e->matricule,
                    'nom_complet' => $e->nom_complet,
                    'section' => $e->section?->value,
                    'statut' => $e->statut,
                    'classe' => $e->relationLoaded('classe') && $e->classe
                        ? ['id' => $e->classe->id, 'nom' => $e->classe->nom]
                        : null,
                ])
                ->values()),
            'image' => $this->image,
            'telephone' => $this->telephone,
            'adresse' => $this->adresse,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
