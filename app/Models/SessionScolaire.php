<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SessionScolaire extends Model
{
    protected $table = 'sessions_scolaires';

    protected $fillable = ['annee_debut', 'annee_fin', 'libelle', 'est_active'];

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

    public function scopeActive($query)
    {
        return $query->where('est_active', true);
    }
}
