<?php

namespace Liberty\Tickets\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketComment extends Model
{
    protected $table = 'ticket_comments';

    protected $fillable = [
        'ticket_id',
        'user_id',
        'body',
        'is_system',
    ];

    protected $casts = [
        'is_system' => 'boolean',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }
}
