<?php

namespace App\Policies;

use App\Models\Lead;
use App\Models\User;

class LeadPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(['leads.view_all', 'leads.view_own']);
    }

    public function view(User $user, Lead $lead): bool
    {
        return $user->can('leads.view_all')
            || ($user->can('leads.view_own') && $lead->assigned_to === $user->id);
    }

    public function create(User $user): bool
    {
        return $user->can('leads.create');
    }

    public function update(User $user, Lead $lead): bool
    {
        return $user->can('leads.edit')
            && ($user->can('leads.view_all') || $lead->assigned_to === $user->id);
    }

    public function delete(User $user, Lead $lead): bool
    {
        return $user->can('leads.delete')
            && ($user->can('leads.view_all') || $lead->assigned_to === $user->id);
    }

    public function restore(User $user, Lead $lead): bool
    {
        return $this->delete($user, $lead);
    }

    public function forceDelete(User $user, Lead $lead): bool
    {
        return false;
    }
}
