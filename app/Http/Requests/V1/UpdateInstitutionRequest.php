<?php

namespace App\Http\Requests\V1;

use Illuminate\Validation\Rule;

class UpdateInstitutionRequest extends StoreInstitutionRequest
{
    public function rules(): array
    {
        return collect(parent::rules())
            ->map(function (array $rule) {
                if (($key = array_search('unique:institutions,code', $rule, true)) !== false) {
                    $rule[$key] = Rule::unique('institutions', 'code')
                        ->ignore($this->route('institution'));
                }

                return array_merge(['sometimes'], $rule);
            })
            ->all();
    }
}
