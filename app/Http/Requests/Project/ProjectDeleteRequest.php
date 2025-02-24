<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class ProjectDeleteRequest extends FormRequest
{
    public function authorize(): bool
    {
        $project = $this->route('project');

        return $this->user()->can('delete', $project);
    }

    public function rules(): array
    {
        return [];
    }
}
