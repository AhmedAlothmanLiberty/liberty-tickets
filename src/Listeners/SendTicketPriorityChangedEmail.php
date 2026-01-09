<?php

namespace Liberty\Tickets\Listeners;

use Illuminate\Support\Facades\Mail;
use Liberty\Tickets\Events\TicketPriorityChanged;
use Liberty\Tickets\Mail\TicketNotificationMail;

class SendTicketPriorityChangedEmail
{
    public function handle(TicketPriorityChanged $event): void
    {
        if (!config('tickets.notifications.enabled')) {
            return;
        }

        $to = $this->resolveUserEmail($event->ticket->created_by);
        if (!$to) {
            return;
        }

        Mail::to($to)->send(new TicketNotificationMail(
            ticket: $event->ticket,
            subjectLine: "Ticket #{$event->ticket->id} priority updated",
            messageLine: "Priority changed from {$event->oldPriority} to {$event->newPriority}.",
            meta: ['Actor ID' => $event->actorId],
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
