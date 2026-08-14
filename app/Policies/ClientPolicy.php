<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;

class ClientPolicy
{
    /**
     * Determine whether the user can view any customers.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(['clients.view_all', 'clients.view_own']);
    }

    /**
     * Determine whether the user can view the customer.
     */
    public function view(User $user, Client $client): bool
    {
        return $user->can('clients.view_all')
            || ($user->can('clients.view_own') && $client->assigned_staff_id === $user->id);
    }

    /**
     * Determine whether the user can create customers.
     */
    public function create(User $user): bool
    {
        return $user->can('clients.create');
    }

    /**
     * Determine whether the user can update the customer.
     */
    public function update(User $user, Client $client): bool
    {
        return $user->can('clients.edit')
            && ($user->can('clients.view_all') || $client->assigned_staff_id === $user->id);
    }

    /**
     * Determine whether the user can delete the customer.
     */
    public function delete(User $user, Client $client): bool
    {
        return $user->can('clients.delete')
            && ($user->can('clients.view_all') || $client->assigned_staff_id === $user->id);
    }

    /**
     * Determine whether the user can restore the customer.
     */
    public function restore(User $user, Client $client): bool
    {
        return $this->delete($user, $client);
    }

    /**
     * Determine whether the user can permanently delete the customer.
     */
    public function forceDelete(User $user, Client $client): bool
    {
        return false;
    }
}
