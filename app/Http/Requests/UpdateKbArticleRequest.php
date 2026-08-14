<?php

namespace App\Http\Requests;

use App\Models\KbArticle;

class UpdateKbArticleRequest extends StoreKbArticleRequest
{
    public function authorize(): bool
    {
        $article = $this->route('kb_article');

        return $article instanceof KbArticle && ($this->user()?->can('update', $article) ?? false);
    }
}
