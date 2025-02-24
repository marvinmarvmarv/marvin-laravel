<?php

namespace App\Http\Requests\project;

use Illuminate\Foundation\Http\FormRequest;

class ProjectUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $project = $this->route('project');

        return $this->user()->can('update', $project);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
        ];
    }
}
