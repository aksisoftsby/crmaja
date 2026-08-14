<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('tickets.view_all') || $user->can('tickets.view_own');
    }

    public function view(User $user, Ticket $ticket): bool
    {
        return $user->can('tickets.view_all') || ($user->can('tickets.view_own') && ($ticket->created_by === $user->id || $ticket->assigned_to === $user->id));
    }

    public function create(User $user): bool
    {
        return $user->can('tickets.create');
    }

    public function update(User $user, Ticket $ticket): bool
    {
        return $user->can('tickets.edit') && ($user->can('tickets.view_all') || $ticket->created_by === $user->id || $ticket->assigned_to === $user->id);
    }

    public function delete(User $user, Ticket $ticket): bool
    {
        return $user->can('tickets.delete');
    }

    public function restore(User $user, Ticket $ticket): bool
    {
        return $this->delete($user, $ticket);
    }

    public function forceDelete(User $user, Ticket $ticket): bool
    {
        return false;
    }
}
