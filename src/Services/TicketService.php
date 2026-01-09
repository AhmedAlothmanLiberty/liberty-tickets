<?php

namespace Liberty\Tickets\Services;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Liberty\Tickets\Models\Ticket;
use Liberty\Tickets\Models\TicketComment;

class TicketService
{
    /**
     * Create a new ticket
     */
    public function create(array $data, $user): Ticket
    {
        $level = $user->ticketLevel();
        $priorityCap = $user->ticketPriorityCap();

        if ($data['priority'] > $priorityCap) {
            throw new AuthorizationException('Priority exceeds your allowed level.');
        }

        return DB::transaction(function () use ($data, $user, $level) {

            $ticket = Ticket::create([
                'title'         => $data['title'],
                'description'   => $data['description'],
                'type'          => $data['type'], // bug | feature
                'status'        => Ticket::STATUS_SUBMITTED,
                'priority'      => $data['priority'],
                'created_level' => $level,
                'created_by'    => $user->id,
            ]);

            $this->systemComment(
                $ticket,
                "Ticket created by {$user->name} (Level {$level})"
            );

            return $ticket;
        });
    }

    /**
     * Update ticket content (Level 2+)
     */
    public function update(Ticket $ticket, array $data, $user): Ticket
    {
        Gate::authorize('update', $ticket);

        return DB::transaction(function () use ($ticket, $data, $user) {

            $ticket->update([
                'title'       => $data['title'] ?? $ticket->title,
                'description' => $data['description'] ?? $ticket->description,
            ]);

            $this->systemComment(
                $ticket,
                "Ticket updated by {$user->name}"
            );

            return $ticket;
        });
    }

    /**
     * Change priority
     */
    public function changePriority(Ticket $ticket, int $priority, $user): Ticket
    {
        Gate::authorize('changePriority', [$ticket, $priority]);

        return DB::transaction(function () use ($ticket, $priority, $user) {

            $old = $ticket->priority;
            $ticket->update(['priority' => $priority]);
            event(new \Liberty\Tickets\Events\TicketPriorityChanged(
                ticket: $ticket,
                oldPriority: $old,
                newPriority: $priority,
                actorId: $user->id
            ));

            $this->systemComment(
                $ticket,
                "Priority changed from {$old} to {$priority} by {$user->name}"
            );

            return $ticket;
        });
    }

    /**
     * Request escalation (Level 1)
     */
    public function requestEscalation(Ticket $ticket, $user): Ticket
    {
        Gate::authorize('escalate', $ticket);

        return DB::transaction(function () use ($ticket, $user) {

            $ticket->update(['escalation_requested' => true]);

            $this->systemComment(
                $ticket,
                "Escalation requested by {$user->name}"
            );

            return $ticket;
        });
    }

    /**
     * Verify bug (Level 2+)
     */
    public function verifyBug(Ticket $ticket, $user): Ticket
    {
        Gate::authorize('verifyBug', $ticket);

        return DB::transaction(function () use ($ticket, $user) {
            $oldStatus = $ticket->status;

            $ticket->update([
                'status'      => Ticket::STATUS_VERIFIED,
                'verified_at' => now(),
                'verified_by' => $user->id,
            ]);
            event(new \Liberty\Tickets\Events\TicketStatusChanged(
                ticket: $ticket,
                oldStatus: $oldStatus,
                newStatus: $ticket->status,
                actorId: $user->id
            ));
            $this->systemComment(
                $ticket,
                "Bug verified by {$user->name}"
            );

            return $ticket;
        });
    }

    /**
     * Assign ticket
     */
    public function assign(Ticket $ticket, int $assigneeId, $user): Ticket
    {
        Gate::authorize('assign', $ticket);

        return DB::transaction(function () use ($ticket, $assigneeId, $user) {
            $old = $ticket->assigned_to;

            $ticket->update([
                'assigned_to' => $assigneeId,
                'status'      => Ticket::STATUS_ASSIGNED,
            ]);
            event(new \Liberty\Tickets\Events\TicketAssigned(
                ticket: $ticket,
                oldAssigneeId: $old,
                newAssigneeId: $assigneeId,
                actorId: $user->id
            ));
            $this->systemComment(
                $ticket,
                "Ticket assigned to user #{$assigneeId} by {$user->name}"
            );

            return $ticket;
        });
    }

    /**
     * Resolve ticket
     */
    public function resolve(Ticket $ticket, $user): Ticket
    {
        Gate::authorize('resolve', $ticket);

        return DB::transaction(function () use ($ticket, $user) {
            $oldStatus = $ticket->status;

            $ticket->update([
                'status'      => Ticket::STATUS_RESOLVED,
                'resolved_at' => now(),
                'resolved_by' => $user->id,
            ]);
            event(new \Liberty\Tickets\Events\TicketStatusChanged(
                ticket: $ticket,
                oldStatus: $oldStatus,
                newStatus: $ticket->status,
                actorId: $user->id
            ));
            $this->systemComment(
                $ticket,
                "Ticket resolved by {$user->name}"
            );

            return $ticket;
        });
    }

    /**
     * Add system comment (audit trail)
     */
    protected function systemComment(Ticket $ticket, string $message): void
    {
        TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id'   => null,
            'body'      => $message,
            'is_system' => true,
        ]);
    }

    public function addComment(Ticket $ticket, string $body, $user): TicketComment
    {
        if (!$user->can('view', $ticket)) {
            throw new AuthorizationException('Not allowed to comment on this ticket.');
        }

        return DB::transaction(function () use ($ticket, $body, $user) {
            $comment = TicketComment::create([
                'ticket_id' => $ticket->id,
                'user_id'   => $user->id,
                'body'      => trim($body),
                'is_system' => false,
            ]);

            // optional audit system comment (I recommend it)
            $this->systemComment($ticket, "Comment added by {$user->name}");

            return $comment;
        });
    }
}
