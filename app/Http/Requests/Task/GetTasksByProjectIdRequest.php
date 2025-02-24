<?php

namespace App\Http\Requests\Task;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;

class GetTasksByProjectIdRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('viewAny', Task::class) ?? false;
    }

    public function rules(): array
    {
        return [];
    }
}
