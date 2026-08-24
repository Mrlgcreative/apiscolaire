<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Institution extends Model
{
    use HasUuids;

    protected $fillable = [
        'nom', 'code', 'type', 'adresse', 'telephone', 'email', 'logo', 'est_active',
    ];

    protected function casts(): array
    {
        return [
            'est_active' => 'boolean',
        ];
    }

    public function eleves(): HasMany
    {
        return $this->hasMany(Eleve::class);
    }

    public function classes(): HasMany
    {
        return $this->hasMany(Classe::class);
    }

    public function professeurs(): HasMany
    {
        return $this->hasMany(Professeur::class);
    }

    public function frais(): HasMany
    {
        return $this->hasMany(Frais::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
