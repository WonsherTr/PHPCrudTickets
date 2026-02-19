<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    /**
     * Admin can do anything.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return true; // filtered in controller
    }

    public function view(User $user, Ticket $ticket): bool
    {
        return $user->id === $ticket->created_by;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Ticket $ticket): bool
    {
        return $user->id === $ticket->created_by;
    }

    public function delete(User $user, Ticket $ticket): bool
    {
        return $user->id === $ticket->created_by;
    }

    public function comment(User $user, Ticket $ticket): bool
    {
        return $user->id === $ticket->created_by;
    }

    public function attach(User $user, Ticket $ticket): bool
    {
        return $user->id === $ticket->created_by;
    }

    public function changeStatus(User $user, Ticket $ticket): bool
    {
        // Only admin — handled by before()
        return false;
    }

    public function changePriority(User $user, Ticket $ticket): bool
    {
        return false;
    }
}
