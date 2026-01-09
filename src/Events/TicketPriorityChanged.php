<?php

namespace Liberty\Tickets\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Liberty\Tickets\Models\Ticket;

class TicketPriorityChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Ticket $ticket,
        public int $oldPriority,
        public int $newPriority,
        public int $actorId,
    ) {}
}
