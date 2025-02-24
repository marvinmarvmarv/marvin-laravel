<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserShowRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->route('user');

        return $this->user()->can('view', $user);
    }

    public function rules(): array
    {
        return [];
    }
}
