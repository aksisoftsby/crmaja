<?php

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        $project = $this->route('project');

        return $project instanceof Project && ($this->user()?->can('update', $project) ?? false);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'client_id' => ['nullable', 'integer', 'exists:clients,id'],
            'description' => ['nullable', 'string', 'max:10000'],
            'start_date' => ['nullable', 'date'],
            'deadline' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['required', Rule::in(['not_started', 'in_progress', 'on_hold', 'completed', 'cancelled'])],
            'billing_type' => ['required', Rule::in(['fixed', 'hourly', 'not_billed'])],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'progress' => ['nullable', 'integer', 'min:0', 'max:100'],
            'member_ids' => ['nullable', 'array'],
            'member_ids.*' => ['integer', 'exists:users,id'],
        ];
    }
}
