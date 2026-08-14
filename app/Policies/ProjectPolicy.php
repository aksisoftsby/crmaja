<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('projects.view_all') || $user->can('projects.view_own');
    }

    public function view(User $user, Project $project): bool
    {
        return $user->can('projects.view_all') || ($user->can('projects.view_own') && ($project->created_by === $user->id || $project->members()->whereKey($user->id)->exists()));
    }

    public function create(User $user): bool
    {
        return $user->can('projects.create');
    }

    public function update(User $user, Project $project): bool
    {
        return $user->can('projects.edit') && ($user->can('projects.view_all') || $project->created_by === $user->id || $project->members()->whereKey($user->id)->exists());
    }

    public function delete(User $user, Project $project): bool
    {
        return $user->can('projects.delete');
    }

    public function restore(User $user, Project $project): bool
    {
        return $this->delete($user, $project);
    }

    public function forceDelete(User $user, Project $project): bool
    {
        return false;
    }
}
