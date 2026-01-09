<?php

namespace Liberty\Tickets\Listeners;

use Illuminate\Support\Facades\Mail;
use Liberty\Tickets\Events\TicketAssigned;
use Liberty\Tickets\Mail\TicketNotificationMail;

class SendTicketAssignedEmail
{
    public function handle(TicketAssigned $event): void
    {
        if (!config('tickets.notifications.enabled')) {
            return;
        }

        // Email assignee
        $to = $this->resolveUserEmail($event->newAssigneeId);
        if (!$to) {
            return;
        }

        Mail::to($to)->send(new TicketNotificationMail(
            ticket: $event->ticket,
            subjectLine: "You were assigned Ticket #{$event->ticket->id}",
            messageLine: "You have been assigned this ticket.",
            meta: ['Assigned By (Actor ID)' => $event->actorId],
        ));
    }

    private function resolveUserEmail(int $userId): ?string
    {
        $model = config('auth.providers.users.model');
        if (!$model || !class_exists($model)) {
            return null;
        }

        $user = $model::query()->find($userId);

        return $user?->email;
    }
}
