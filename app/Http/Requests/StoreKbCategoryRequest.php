<?php

namespace App\Http\Requests;

use App\Models\KbArticle;
use Illuminate\Foundation\Http\FormRequest;

class StoreKbCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', KbArticle::class) ?? false;
    }

    public function rules(): array
    {
        return ['name' => ['required', 'string', 'max:255'], 'parent_id' => ['nullable', 'integer', 'exists:kb_categories,id']];
    }
}
