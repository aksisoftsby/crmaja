<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;

class StoreTaskCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $task = $this->route('task');

        return $task instanceof Task && ($this->user()?->can('view', $task) ?? false);
    }

    public function rules(): array
    {
        return ['comment' => ['required', 'string', 'max:10000']];
    }
}
