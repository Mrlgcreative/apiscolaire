<?php

namespace App\Models;

use App\Models\Concerns\BelongsToInstitution;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Professeur extends Model
{
    use BelongsToInstitution;
    use HasUuids;

    protected $fillable = [
        'nom', 'prenom', 'contact', 'email', 'adresse', 'date_embauche',
    ];

    protected function casts(): array
    {
        return [
            'date_embauche' => 'date',
        ];
    }

    public function cours(): HasMany
    {
        return $this->hasMany(Cours::class);
    }

    public function classesTitulaires(): HasMany
    {
        return $this->hasMany(Classe::class);
    }
}
