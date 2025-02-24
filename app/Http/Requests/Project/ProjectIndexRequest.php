<?php

namespace App\Http\Requests\Project;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;

class ProjectIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', Project::class);
    }

    public function rules(): array
    {
        return [];
    }
}
