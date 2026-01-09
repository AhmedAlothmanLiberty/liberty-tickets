<?php

namespace Liberty\Tickets\Tests\Feature;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Schema;
use Liberty\Tickets\Tests\Support\TestUser;
use Liberty\Tickets\Tests\TestCase;

class CreateTicketTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function ($t) {
            $t->id();
            $t->string('name');
        });

        $this->artisan('migrate');
    }

    public function test_authenticated_user_can_access_create_page()
    {
        $user = TestUser::create(['name' => 'Agent']);

        $this->actingAs($user)
            ->get('/tickets/create')
            ->assertStatus(200);
    }
}
