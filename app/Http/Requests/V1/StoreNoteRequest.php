<?php

namespace App\Http\Requests\V1;

use App\Rules\TenantExists;
use Illuminate\Foundation\Http\FormRequest;

class StoreNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (! $user) {
            return false;
        }
        // Parent et comptable ne saisissent pas de notes
        if ($user->isParent()) {
            return false;
        }

        return true;
    }

    public function rules(): array
    {
        return [
            'eleve_id' => ['required', 'uuid', new TenantExists('eleves')],
            'cours_id' => ['required', 'uuid', new TenantExists('cours')],
            'periode_id' => ['required', 'uuid', new TenantExists('periodes')],
            'session_scolaire_id' => ['required', 'uuid', 'exists:sessions_scolaires,id'],
            'note' => ['required', 'numeric', 'min:0', 'max:100'],
            'max' => ['sometimes', 'numeric', 'min:1', 'max:100'],
            'coefficient' => ['sometimes', 'numeric', 'min:0.5', 'max:10'],
            'commentaire' => ['nullable', 'string', 'max:500'],
        ];
    }
}
