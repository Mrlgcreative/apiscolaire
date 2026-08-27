<?php

namespace App\Models;

use App\Models\Concerns\BelongsToInstitution;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Periode extends Model
{
    use BelongsToInstitution;
    use HasUuids;

    protected $fillable = [
        'institution_id', 'session_scolaire_id', 'parent_id', 'type', 'section', 'code', 'libelle', 'ordre', 'date_debut', 'date_fin', 'est_cloturee',
    ];

    protected function casts(): array
    {
        return [
            'ordre' => 'integer',
            'date_debut' => 'date',
            'date_fin' => 'date',
            'est_cloturee' => 'boolean',
        ];
    }

    public function sessionScolaire(): BelongsTo
    {
        return $this->belongsTo(SessionScolaire::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function enfants(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }
}
