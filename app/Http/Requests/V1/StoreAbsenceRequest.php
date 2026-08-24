<?php

namespace App\Http\Requests\V1;

use App\Rules\TenantExists;
use Illuminate\Foundation\Http\FormRequest;

class StoreAbsenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'eleve_id' => ['required', 'integer', new TenantExists('eleves')],
            'date_absence' => ['required', 'date'],
            'motif' => ['nullable', 'string'],
            'justifiee' => ['nullable', 'boolean'],
        ];
    }
}
