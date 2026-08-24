<?php

namespace App\Http\Requests\V1;

class UpdateProfesseurRequest extends StoreProfesseurRequest
{
    public function rules(): array
    {
        return collect(parent::rules())
            ->map(function (array $rule) {
                // unique reste unique, mais on ignore l'enregistrement courant
                if (($key = array_search('unique:professeurs,email', $rule, true)) !== false) {
                    $rule[$key] = 'unique:professeurs,email,'.$this->route('professeur')->id;
                }

                return array_merge(['sometimes'], $rule);
            })
            ->all();
    }
}
