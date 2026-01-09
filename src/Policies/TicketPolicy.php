<?php

namespace Liberty\Tickets\Policies;

use Liberty\Tickets\Models\Ticket;
use Liberty\Tickets\Support\TicketUser;

class TicketPolicy
{
    public function viewAny($user): bool
    {
        return (int) $user->ticketLevel() >= 1;
    }

    public function view($user, Ticket $ticket): bool
    {
        $userLevel = (int) $user->ticketLevel();

        // Level-based visibility: user sees tickets created at <= their level
        if ($ticket->created_level > $userLevel) {
            return false;
        }

        return true;
    }

    public function create($user): bool
    {
        return (int) $user->ticketLevel() >= 1;
    }

    public function update($user, Ticket $ticket): bool
    {
        $userLevel = (int) $user->ticketLevel();

        // Level 1 cannot edit tickets (except comments - handled separately)
        if ($userLevel === 1) return false;

        // Level 2 can edit tickets created at level 1 or 2
        if ($userLevel === 2) return $ticket->created_level <= 2;

        // Level 3 can edit all
        return $userLevel >= 3;
    }

    public function changePriority($user, Ticket $ticket, int $newPriority): bool
    {
        $cap = TicketUser::priorityCap($user);
        if ($newPriority < 1 || $newPriority > $cap) return false;

        // Only Level 2/3 can change priority
        return (int) $user->ticketLevel() >= 2 && $this->view($user, $ticket);
    }

    public function escalate($user, Ticket $ticket): bool
    {
        // Only creator can request escalation (or Level 1 generally)
        return (int) $user->ticketLevel() === 1
            && (int) $ticket->created_by === (int) $user->id;
    }

    public function verifyBug($user, Ticket $ticket): bool
    {
        // Only Level 2+ can verify bugs
        return (int) $user->ticketLevel() >= 2
            && $ticket->type === 'bug'
            && $this->view($user, $ticket);
    }

    public function assign($user, Ticket $ticket): bool
    {
        // Level 2 can assign within tickets they can see; Level 3 all
        return (int) $user->ticketLevel() >= 2 && $this->view($user, $ticket);
    }

    public function resolve($user, Ticket $ticket): bool
    {
        // Level 2+ can resolve tickets they manage/see
        return (int) $user->ticketLevel() >= 2 && $this->view($user, $ticket);
    }
}
