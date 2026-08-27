<?php

namespace App\Models;

use App\Enums\Section;
use App\Models\Concerns\BelongsToInstitution;
use App\Support\CurrentInstitution;
use Database\Factories\EleveFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'matricule', 'nom', 'post_nom', 'prenom', 'date_naissance', 'sexe',
    'lieu_naissance', 'adresse', 'section', 'option_id', 'classe_id',
    'session_scolaire_id', 'nom_pere', 'nom_mere', 'contact_pere',
    'contact_mere', 'statut', 'est_reinscrit', 'photo', 'institution_id',
])]
class Eleve extends Model
{
    /** @use HasFactory<EleveFactory> */
    use BelongsToInstitution, HasFactory, HasUuids;

    protected $attributes = [
        'statut' => 'actif',
    ];

    protected function casts(): array
    {
        return [
            'date_naissance' => 'date',
            'section' => Section::class,
            'est_reinscrit' => 'boolean',
        ];
    }

    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class);
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(Option::class);
    }

    public function sessionScolaire(): BelongsTo
    {
        return $this->belongsTo(SessionScolaire::class);
    }

    public function paiements(): HasMany
    {
        return $this->hasMany(PaiementFrais::class);
    }

    public function absences(): HasMany
    {
        return $this->hasMany(Absence::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'parent_eleve', 'eleve_id', 'parent_id')
            ->withPivot('lien')
            ->withTimestamps();
    }

    public function scopeActifs($query)
    {
        return $query->where('statut', 'actif');
    }

    protected function nomComplet(): Attribute
    {
        return Attribute::get(
            fn () => trim("{$this->nom} {$this->post_nom} {$this->prenom}"),
        );
    }

    protected static function booted(): void
    {
        static::creating(function (Eleve $eleve) {
            if (blank($eleve->matricule)) {
                $eleve->matricule = self::genererMatricule(
                    $eleve->institution ?? Institution::find($eleve->institution_id),
                );
            }
        });
    }

    public static function genererMatricule(?Institution $institution = null): string
    {
        $prefix = $institution?->code
            ?? Institution::find(app(CurrentInstitution::class)->id)?->code
            ?? 'GEN';

        do {
            $matricule = sprintf('%s-%d-%04d', strtoupper($prefix), now()->year, random_int(1, 9999));
        } while (self::where('matricule', $matricule)->exists());

        return $matricule;
    }
}
