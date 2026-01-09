<?php

namespace Liberty\Tickets\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Liberty\Tickets\Models\Ticket;

class TicketNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Ticket $ticket,
        public string $subjectLine,
        public string $messageLine,
        public array $meta = []
    ) {}

    public function build()
    {
        return $this
            ->subject($this->subjectLine)
            ->view('tickets::emails.ticket_notification');
    }
}
