<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->route('user');

        return $this->user()->can('update', $user);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email,'.$this->route('user')->id,
            ],
            'password' => ['nullable', 'string', 'min:8'],
        ];
    }
}
