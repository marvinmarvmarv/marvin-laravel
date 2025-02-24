<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserDeleteRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->route('user');

        return $this->user()->can('delete', $user);
    }

    public function rules(): array
    {
        return [];
    }
}
