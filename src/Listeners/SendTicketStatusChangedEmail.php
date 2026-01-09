<?php

namespace Liberty\Tickets\Listeners;

use Illuminate\Support\Facades\Mail;
use Liberty\Tickets\Events\TicketStatusChanged;
use Liberty\Tickets\Mail\TicketNotificationMail;

class SendTicketStatusChangedEmail
{
    public function handle(TicketStatusChanged $event): void
    {
        if (!config('tickets.notifications.enabled')) {
            return;
        }

        // Send to ticket creator (simple)
        $to = $this->resolveUserEmail($event->ticket->created_by);
        if (!$to) {
            return;
        }

        Mail::to($to)->send(new TicketNotificationMail(
            ticket: $event->ticket,
            subjectLine: "Ticket #{$event->ticket->id} status updated",
            messageLine: "Status changed from {$event->oldStatus} to {$event->newStatus}.",
            meta: ['Actor ID' => $event->actorId],
        ));
    }

    private function resolveUserEmail(int $userId): ?string
    {
        // Package-safe: assumes host app user model is default auth provider
        $provider = config('auth.providers.users.model');
        if (!$provider || !class_exists($provider)) {
            return null;
        }

        $user = $provider::query()->find($userId);

        return $user?->email;
    }
}
