<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EleveResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'matricule' => $this->matricule,
            'nom' => $this->nom,
            'post_nom' => $this->post_nom,
            'prenom' => $this->prenom,
            'nom_complet' => $this->nom_complet,
            'date_naissance' => $this->date_naissance?->toDateString(),
            'sexe' => $this->sexe,
            'lieu_naissance' => $this->lieu_naissance,
            'adresse' => $this->adresse,
            'section' => $this->section,
            'statut' => $this->statut,
            'est_reinscrit' => $this->est_reinscrit,
            'photo' => $this->photo,
            'institution' => new InstitutionResource($this->whenLoaded('institution')),
            'option' => new OptionResource($this->whenLoaded('option')),
            'classe' => new ClasseResource($this->whenLoaded('classe')),
            'session_scolaire' => new SessionScolaireResource($this->whenLoaded('sessionScolaire')),
            'parents' => [
                'pere' => $this->nom_pere,
                'mere' => $this->nom_mere,
                'contact_pere' => $this->contact_pere,
                'contact_mere' => $this->contact_mere,
            ],
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
