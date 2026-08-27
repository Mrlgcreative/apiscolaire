<?php

namespace App\Models;

use App\Models\Concerns\BelongsToInstitution;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Note extends Model
{
    use BelongsToInstitution;
    use HasUuids;

    protected $fillable = [
        'institution_id', 'eleve_id', 'cours_id', 'periode_id', 'session_scolaire_id',
        'note', 'max', 'coefficient', 'commentaire', 'professeur_id', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'note' => 'decimal:2',
            'max' => 'decimal:2',
            'coefficient' => 'decimal:2',
        ];
    }

    public function eleve(): BelongsTo
    {
        return $this->belongsTo(Eleve::class);
    }

    public function cours(): BelongsTo
    {
        return $this->belongsTo(Cours::class);
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class);
    }

    public function sessionScolaire(): BelongsTo
    {
        return $this->belongsTo(SessionScolaire::class);
    }

    public function professeur(): BelongsTo
    {
        return $this->belongsTo(Professeur::class);
    }
}
