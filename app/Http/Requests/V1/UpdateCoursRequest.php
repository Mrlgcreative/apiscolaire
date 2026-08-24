<?php

namespace App\Http\Requests\V1;

class UpdateCoursRequest extends StoreCoursRequest
{
    public function rules(): array
    {
        return collect(parent::rules())
            ->map(fn (array $rule) => array_merge(['sometimes'], $rule))
            ->all();
    }
}
