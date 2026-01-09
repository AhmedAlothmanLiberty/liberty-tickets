<?php

namespace Liberty\Tickets\Tests\Unit;

use Illuminate\Foundation\Auth\User;
use Liberty\Tickets\Models\Ticket;
use Liberty\Tickets\Policies\TicketPolicy;
use Liberty\Tickets\Tests\TestCase;

class TicketPolicyTest extends TestCase
{
    public function test_level_1_cannot_edit_ticket()
    {
        $user = new class extends User {
            public function ticketLevel() { return 1; }
        };

        $ticket = new Ticket(['created_level' => 1]);

        $policy = new TicketPolicy();

        $this->assertFalse($policy->update($user, $ticket));
    }

    public function test_level_2_can_edit_level_1_ticket()
    {
        $user = new class extends User {
            public function ticketLevel() { return 2; }
        };

        $ticket = new Ticket(['created_level' => 1]);

        $policy = new TicketPolicy();

        $this->assertTrue($policy->update($user, $ticket));
    }
}
