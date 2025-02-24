<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class GetAllTasksByUserIdRequest extends FormRequest
{
    public function authorize(): bool
    {
        $targetUser = $this->route('user');

        return $this->user()->can('view', $targetUser) ?? false;
    }

    public function rules(): array
    {
        return [];
    }
}
