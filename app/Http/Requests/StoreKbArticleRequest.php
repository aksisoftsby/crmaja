<?php

namespace App\Http\Requests;

use App\Models\KbArticle;
use Illuminate\Foundation\Http\FormRequest;

class StoreKbArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', KbArticle::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['nullable', 'integer', 'exists:kb_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:50000'],
            'is_published' => ['nullable', 'boolean'],
            'is_client_only' => ['nullable', 'boolean'],
        ];
    }
}
