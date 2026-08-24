<?php

namespace App\Http\Requests\V1;

use Illuminate\Validation\Rule;

class UpdateUserRequest extends StoreUserRequest
{
    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        return collect(parent::rules())
            ->map(fn (array $rule) => array_merge(['sometimes'], $rule))
            ->put('username', ['sometimes', 'string', 'max:50', Rule::unique('users', 'username')->ignore($userId)])
            ->put('email', ['sometimes', 'email', 'max:150', Rule::unique('users', 'email')->ignore($userId)])
            ->put('password', ['sometimes', 'string', 'min:6'])
            ->all();
    }
}
