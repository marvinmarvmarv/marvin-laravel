<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class TaskDeleteRequest extends FormRequest
{
    public function authorize(): bool
    {
        $task = $this->route('task');

        return $this->user()->can('delete', $task);
    }

    public function rules(): array
    {
        return [];
    }
}
