<?php

namespace App\Http\Requests\V1;

use App\Rules\TenantExists;
use Illuminate\Foundation\Http\FormRequest;

class StorePeriodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->isAdmin();
    }

    public function rules(): array
    {
        return [
            'session_scolaire_id' => ['required', 'uuid', 'exists:sessions_scolaires,id'],
            'parent_id' => ['nullable', 'uuid', new TenantExists('periodes')],
            'type' => ['required', 'in:semestre,trimestre,periode'],
            'section' => ['nullable', 'in:maternelle,primaire,secondaire'],
            'code' => ['required', 'string', 'max:20'],
            'libelle' => ['required', 'string', 'max:100'],
            'ordre' => ['required', 'integer', 'min:1', 'max:20'],
            'date_debut' => ['nullable', 'date'],
            'date_fin' => ['nullable', 'date', 'after_or_equal:date_debut'],
            'est_cloturee' => ['sometimes', 'boolean'],
        ];
    }
}
