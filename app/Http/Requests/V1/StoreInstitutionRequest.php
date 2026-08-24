<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreInstitutionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:150'],
            'code' => ['required', 'string', 'max:10', 'alpha_dash', 'unique:institutions,code'],
            'type' => ['nullable', 'string', 'max:50'],
            'adresse' => ['nullable', 'string'],
            'telephone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'est_active' => ['nullable', 'boolean'],
        ];
    }
}
