<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Task::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'priority' => ['required', Rule::in(['low', 'medium', 'high', 'urgent'])],
            'status' => ['required', Rule::in(['todo', 'in_progress', 'testing', 'completed', 'cancelled'])],
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'milestone_id' => ['nullable', 'integer', 'exists:milestones,id'],
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'related_type' => ['nullable', Rule::in(['project', 'lead', 'client'])],
            'related_id' => ['nullable', 'integer', 'required_with:related_type'],
            'is_recurring' => ['nullable', 'boolean'],
            'assignee_ids' => ['nullable', 'array'],
            'assignee_ids.*' => ['integer', 'exists:users,id'],
        ];
    }
}
