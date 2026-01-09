<?php

namespace Liberty\Tickets\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Liberty\Tickets\Events\TicketAssigned;
use Liberty\Tickets\Events\TicketPriorityChanged;
use Liberty\Tickets\Events\TicketStatusChanged;
use Liberty\Tickets\Listeners\SendTicketAssignedEmail;
use Liberty\Tickets\Listeners\SendTicketPriorityChangedEmail;
use Liberty\Tickets\Listeners\SendTicketStatusChangedEmail;
use Liberty\Tickets\Models\Ticket;
use Liberty\Tickets\Policies\TicketPolicy;

class TicketsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/tickets.php', 'tickets');
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'tickets');

        Gate::policy(Ticket::class, TicketPolicy::class);

        Event::listen(TicketStatusChanged::class, SendTicketStatusChangedEmail::class);
        Event::listen(TicketPriorityChanged::class, SendTicketPriorityChangedEmail::class);
        Event::listen(TicketAssigned::class, SendTicketAssignedEmail::class);

        $this->publishes([
            __DIR__ . '/../../config/tickets.php' => config_path('tickets.php'),
        ], 'tickets-config');
    }
}
