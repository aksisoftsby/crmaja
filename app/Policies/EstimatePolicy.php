<?php

namespace App\Policies;

use App\Models\Estimate;
use App\Models\User;

class EstimatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('estimates.view');
    }

    public function view(User $user, Estimate $estimate): bool
    {
        return $user->can('estimates.view');
    }

    public function create(User $user): bool
    {
        return $user->can('estimates.create');
    }

    public function update(User $user, Estimate $estimate): bool
    {
        return $user->can('estimates.edit');
    }

    public function delete(User $user, Estimate $estimate): bool
    {
        return $user->can('estimates.delete');
    }

    public function restore(User $user, Estimate $estimate): bool
    {
        return $this->delete($user, $estimate);
    }

    public function forceDelete(User $user, Estimate $estimate): bool
    {
        return false;
    }
}
