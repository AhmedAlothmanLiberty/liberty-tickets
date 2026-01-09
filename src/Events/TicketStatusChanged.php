<?php

namespace Liberty\Tickets\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Liberty\Tickets\Models\Ticket;

class TicketStatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Ticket $ticket,
        public string $oldStatus,
        public string $newStatus,
        public int $actorId,
    ) {}
}
