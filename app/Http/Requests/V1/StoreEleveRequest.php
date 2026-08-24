<?php

namespace App\Http\Requests\V1;

use App\Rules\TenantExists;
use Illuminate\Foundation\Http\FormRequest;

class StoreEleveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:100'],
            'post_nom' => ['required', 'string', 'max:100'],
            'prenom' => ['required', 'string', 'max:100'],
            'date_naissance' => ['required', 'date', 'before_or_equal:today'],
            'sexe' => ['required', 'in:M,F'],
            'lieu_naissance' => ['required', 'string', 'max:150'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'section' => ['required', 'in:maternelle,primaire,secondaire'],
            'option_id' => ['nullable', 'integer', 'exists:options,id'],
            'classe_id' => ['nullable', 'integer', new TenantExists('classes')],
            'session_scolaire_id' => ['nullable', 'integer', 'exists:sessions_scolaires,id'],
            'nom_pere' => ['nullable', 'string', 'max:100'],
            'nom_mere' => ['nullable', 'string', 'max:100'],
            'contact_pere' => ['nullable', 'string', 'max:20'],
            'contact_mere' => ['nullable', 'string', 'max:20'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ];
    }
}
