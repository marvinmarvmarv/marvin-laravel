<?php

namespace App\Http\Requests\Project;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;

class ProjectStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Project::class);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
        ];
    }
}
