<?php

namespace Liberty\Tickets\Tests\Unit;

use Liberty\Tickets\Tests\Support\TestUser;
use Illuminate\Support\Facades\Schema;
use Liberty\Tickets\Models\Ticket;
use Liberty\Tickets\Services\TicketService;
use Liberty\Tickets\Tests\TestCase;

class TicketServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Fake users table
        Schema::create('users', function ($t) {
            $t->id();
            $t->string('name');
            $t->string('email')->nullable();
        });

        // Run package migrations
        $this->artisan('migrate');
    }

    public function test_user_can_create_ticket()
    {
        $user = TestUser::create(['name' => 'Agent']);

        $service = app(TicketService::class);

        $ticket = $service->create([
            'title' => 'Test bug',
            'description' => 'Something broken',
            'type' => 'bug',
            'priority' => 3,
        ], $user);

        $this->assertInstanceOf(Ticket::class, $ticket);
        $this->assertEquals('submitted', $ticket->status);
        $this->assertEquals(3, $ticket->priority);
    }
}
