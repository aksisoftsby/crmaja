<?php

namespace App\Policies;

use App\Models\Proposal;
use App\Models\User;

class ProposalPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('proposals.view');
    }

    public function view(User $user, Proposal $proposal): bool
    {
        return $user->can('proposals.view');
    }

    public function create(User $user): bool
    {
        return $user->can('proposals.create');
    }

    public function update(User $user, Proposal $proposal): bool
    {
        return $user->can('proposals.edit');
    }

    public function delete(User $user, Proposal $proposal): bool
    {
        return $user->can('proposals.delete');
    }

    public function restore(User $user, Proposal $proposal): bool
    {
        return $this->delete($user, $proposal);
    }

    public function forceDelete(User $user, Proposal $proposal): bool
    {
        return false;
    }
}
