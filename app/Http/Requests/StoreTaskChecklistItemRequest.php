<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;

class StoreTaskChecklistItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        $task = $this->route('task');

        return $task instanceof Task && ($this->user()?->can('update', $task) ?? false);
    }

    public function rules(): array
    {
        return ['description' => ['required', 'string', 'max:1000']];
    }
}
