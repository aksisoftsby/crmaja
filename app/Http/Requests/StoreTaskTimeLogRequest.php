<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;

class StoreTaskTimeLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        $task = $this->route('task');

        return $task instanceof Task && ($this->user()?->can('view', $task) ?? false);
    }

    public function rules(): array
    {
        return [
            'start_time' => ['required', 'date'],
            'end_time' => ['nullable', 'date', 'after:start_time'],
            'note' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
