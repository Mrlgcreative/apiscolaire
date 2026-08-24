<?php

namespace App\Http\Requests\V1;

use App\Rules\TenantExists;
use Illuminate\Foundation\Http\FormRequest;

class StorePaiementFraisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'eleve_id' => ['required', 'integer', new TenantExists('eleves')],
            'frais_id' => ['required', 'integer', new TenantExists('frais')],
            'moi_id' => ['required', 'integer', 'exists:mois,id'],
            'amount_paid' => ['required', 'numeric', 'min:0.01'],
            'payment_date' => ['required', 'date'],
            'statut' => ['nullable', 'in:paye,impaye'],
        ];
    }
}
