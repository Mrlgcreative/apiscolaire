<?php

namespace App\Http\Requests\V1;

use App\Rules\TenantExists;
use Illuminate\Foundation\Http\FormRequest;

class StoreClasseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:100'],
            'section' => ['required', 'in:maternelle,primaire,secondaire'],
            'niveau' => ['nullable', 'string', 'max:50'],
            'titulaire' => ['nullable', 'string', 'max:150'],
            'professeur_id' => ['nullable', 'uuid', new TenantExists('professeurs')],
        ];
    }
}
