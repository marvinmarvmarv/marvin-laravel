<?php

namespace App\Http\Requests\Task;

use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class TaskUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $task = $this->route('task');

        return $this->user()->can('update', $task);
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|integer',
            'project_id' => 'nullable|integer',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'status' => ['required', new Enum(TaskStatus::class)],
            'deadline' => ['sometimes', 'date_format:Y-m-d', 'after_or_equal:today'],
        ];
    }
}
