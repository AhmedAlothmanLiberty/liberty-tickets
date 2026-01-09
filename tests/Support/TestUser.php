<?php

namespace Liberty\Tickets\Tests\Support;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Liberty\Tickets\Support\ResolvesTicketLevel;


class TestUser extends Authenticatable
{
    use ResolvesTicketLevel;

    protected $table = 'users';

    protected $guarded = []; // allow mass assignment in tests
    public $timestamps = false;
    
    public function ticketLevel(): int
    {
        return 1;
    }
}
