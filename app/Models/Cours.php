<?php

namespace App\Models;

use App\Enums\Domaine;
use App\Enums\Section;
use App\Models\Concerns\BelongsToInstitution;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cours extends Model
{
    use BelongsToInstitution;
    use HasUuids;

    protected $fillable = [
        'titre', 'description', 'section', 'domaine', 'coefficient',
        'heures_semaine', 'professeur_id', 'classe_id', 'option_id',
        'institution_id',
    ];

    protected function casts(): array
    {
        return [
            'section' => Section::class,
            'domaine' => Domaine::class,
        ];
    }

    public function professeur(): BelongsTo
    {
        return $this->belongsTo(Professeur::class);
    }

    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class);
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(Option::class);
    }
}
