<?php

namespace App\Models;

use App\Enums\Section;
use App\Models\Concerns\BelongsToInstitution;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classe extends Model
{
    use BelongsToInstitution;
    use HasUuids;

    protected $fillable = ['nom', 'section', 'niveau', 'titulaire', 'professeur_id', 'institution_id'];

    protected function casts(): array
    {
        return [
            'section' => Section::class,
        ];
    }

    public function professeur(): BelongsTo
    {
        return $this->belongsTo(Professeur::class);
    }

    public function eleves(): HasMany
    {
        return $this->hasMany(Eleve::class);
    }

    public function cours(): HasMany
    {
        return $this->hasMany(Cours::class);
    }
}
