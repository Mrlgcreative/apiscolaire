<?php

namespace App\Models\Concerns;

use App\Models\Institution;
use App\Support\CurrentInstitution;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

/**
 * Scoping multi-institution : toute requête est automatiquement filtrée par
 * l'institution courante, et l'attribut institution_id est rempli à la création.
 */
trait BelongsToInstitution
{
    public static function bootBelongsToInstitution(): void
    {
        static::addGlobalScope('institution', function (Builder $query) {
            $institution = app(CurrentInstitution::class);

            if ($institution->isSet()) {
                $query->where(
                    $query->getModel()->getTable().'.institution_id',
                    $institution->id,
                );
            }
        });

        static::creating(function (Model $model) {
            $current = app(CurrentInstitution::class)->id;

            // En contexte de requête, l'institution est TOUJOURS dérivée du
            // contexte (user ou header), jamais du payload client.
            if ($current !== null) {
                $model->institution_id = $current;

                return;
            }

            if ($model->institution_id === null) {
                // Un super-admin doit choisir une école (header X-Institution)
                // avant de créer des données scolaires — sinon la ligne serait
                // orpheline, invisible de toutes les écoles.
                if (auth()->check()) {
                    throw ValidationException::withMessages([
                        'institution' => [
                            "Action interdite sans école : fournissez l'en-tête X-Institution.",
                        ],
                    ]);
                }
            }
        });
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }
}
