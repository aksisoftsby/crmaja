<?php

namespace App\Policies;

use App\Models\KbArticle;
use App\Models\User;

class KbArticlePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('knowledge_base.view') || $user->can('knowledge_base.manage');
    }

    public function view(User $user, KbArticle $article): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->can('knowledge_base.manage');
    }

    public function update(User $user, KbArticle $article): bool
    {
        return $user->can('knowledge_base.manage');
    }

    public function delete(User $user, KbArticle $article): bool
    {
        return $user->can('knowledge_base.manage');
    }

    public function restore(User $user, KbArticle $article): bool
    {
        return $this->delete($user, $article);
    }

    public function forceDelete(User $user, KbArticle $article): bool
    {
        return false;
    }
}
