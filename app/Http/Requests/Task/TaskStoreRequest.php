<?php

namespace App\Http\Requests\Task;

use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class TaskStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => ['required', new Enum(TaskStatus::class)],
            'deadline' => ['required', 'date', 'after_or_equal:today'],
            'user_id' => 'required|integer',
            'project_id' => 'nullable|integer',
        ];
    }
}
