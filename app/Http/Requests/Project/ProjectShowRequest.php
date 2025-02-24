<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class ProjectShowRequest extends FormRequest
{
    public function authorize(): bool
    {
        $project = $this->route('project');

        return $this->user()->can('view', $project);
    }

    public function rules(): array
    {
        return [];
    }
}
