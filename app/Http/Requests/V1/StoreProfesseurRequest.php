<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreProfesseurRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:100'],
            'prenom' => ['required', 'string', 'max:100'],
            'contact' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'unique:professeurs,email'],
            'adresse' => ['nullable', 'string'],
            'date_embauche' => ['nullable', 'date'],
        ];
    }
}
