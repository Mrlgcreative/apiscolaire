<?php

namespace App\Rules;

use App\Support\CurrentInstitution;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

/**
 * exists: classique mais restreint à l'institution courante.
 * Sans contexte (super-admin sans header), retombe sur un exists global.
 */
class TenantExists implements ValidationRule
{
    public function __construct(
        private readonly string $table,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $query = DB::table($this->table)->where('id', $value);

        if ($institution = app(CurrentInstitution::class)->id) {
            $query->where('institution_id', $institution);
        }

        if (! $query->exists()) {
            $fail("La valeur sélectionnée pour :attribute est invalide ou n'appartient pas à votre école.");
        }
    }
}
