<?php

namespace Liberty\Tickets\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Liberty\Tickets\Models\Ticket;

class TicketAssigned
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Ticket $ticket,
        public ?int $oldAssigneeId,
        public int $newAssigneeId,
        public int $actorId,
    ) {}
}
