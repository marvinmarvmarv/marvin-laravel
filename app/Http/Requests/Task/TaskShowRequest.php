<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class TaskShowRequest extends FormRequest
{
    public function authorize(): bool
    {
        $task = $this->route('task');

        return $this->user()->can('view', $task);
    }

    public function rules(): array
    {
        return [];
    }
}
