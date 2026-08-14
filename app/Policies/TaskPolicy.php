<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('tasks.view_all') || $user->can('tasks.view_own');
    }

    public function view(User $user, Task $task): bool
    {
        return $user->can('tasks.view_all') || ($user->can('tasks.view_own') && ($task->created_by === $user->id || $task->assignees()->whereKey($user->id)->exists()));
    }

    public function create(User $user): bool
    {
        return $user->can('tasks.create');
    }

    public function update(User $user, Task $task): bool
    {
        return $user->can('tasks.edit') && ($user->can('tasks.view_all') || $task->created_by === $user->id || $task->assignees()->whereKey($user->id)->exists());
    }

    public function delete(User $user, Task $task): bool
    {
        return $user->can('tasks.delete');
    }

    public function restore(User $user, Task $task): bool
    {
        return $this->delete($user, $task);
    }

    public function forceDelete(User $user, Task $task): bool
    {
        return false;
    }
}
