<?php

namespace Liberty\Tickets\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    public const TYPE_BUG = 'bug';
    public const TYPE_FEATURE = 'feature';

    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_VERIFIED = 'verified';
    public const STATUS_PENDING = 'pending';
    public const STATUS_ASSIGNED = 'assigned';
    public const STATUS_RESOLVED = 'resolved';

    protected $table = 'tickets';

    protected $fillable = [
        'title',
        'description',
        'type',
        'status',
        'priority',
        'created_level',
        'created_by',
        'assigned_to',
        'escalation_requested',
        'verified_at',
        'verified_by',
        'resolved_at',
        'resolved_by',
    ];

    protected $casts = [
        'escalation_requested' => 'boolean',
        'verified_at' => 'datetime',
        'resolved_at' => 'datetime',
        'priority' => 'integer',
        'created_level' => 'integer',
    ];

    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class, 'ticket_id');
    }
}
