<?php

namespace App\Models\Concerns;

use App\Models\Institution;
use App\Support\CurrentInstitution;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

            if ($model->institution_id === null) {
                $model->institution_id = $current;
            }
        });
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }
}
