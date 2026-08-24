<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreFraisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'montant' => ['required', 'numeric', 'min:0'],
            'description' => ['required', 'string', 'max:255'],
            'section' => ['nullable', 'in:maternelle,primaire,secondaire'],
        ];
    }
}
