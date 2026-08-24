<?php

namespace App\Http\Requests\V1;

class UpdateClasseRequest extends StoreClasseRequest
{
    public function rules(): array
    {
        return collect(parent::rules())
            ->map(fn (array $rule) => array_merge(['sometimes'], $rule))
            ->all();
    }
}
