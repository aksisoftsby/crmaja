<?php

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;

class StoreMilestoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        $project = $this->route('project');

        return $project instanceof Project && ($this->user()?->can('update', $project) ?? false);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'due_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
