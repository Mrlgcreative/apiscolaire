<?php

namespace App\Http\Requests\V1;

use App\Rules\TenantExists;
use Illuminate\Foundation\Http\FormRequest;

class StoreCoursRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titre' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'section' => ['required', 'in:maternelle,primaire,secondaire'],
            'coefficient' => ['nullable', 'integer', 'min:1', 'max:10'],
            'heures_semaine' => ['nullable', 'integer', 'min:1', 'max:40'],
            'professeur_id' => ['nullable', 'uuid', new TenantExists('professeurs')],
            'classe_id' => ['required', 'uuid', new TenantExists('classes')],
            'option_id' => ['nullable', 'uuid', 'exists:options,id'],
        ];
    }
}
