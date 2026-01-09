<?php

return [
    // Map your app roles to ticket "levels"
    'role_levels' => [
        // Level 1
        'agent' => 1,
        'negotiator' => 1,

        // Level 2
        'sales_manager' => 2,
        'team_leader' => 2,
        'negotiator_team_leader' => 2,
        'negotiator_admin' => 2,

        // Level 3
        'admin' => 3,
        'super_admin' => 3,
    ],

    /*
   |--------------------------------------------------------------------------
    | Ticket Level Resolver
    |--------------------------------------------------------------------------
    | Return an integer level for the current user.
    | Default: tries user->ticketLevel(), otherwise returns 1.
    */

    'level_resolver' => fn($user): int => method_exists($user, 'ticketLevel')
        ? (int) $user->ticketLevel()
        : 1,
    // Priority caps per level
    'priority_caps' => [
        1 => 5,
        2 => 7,
        3 => 10,
    ],

    // Visibility rule:
    // user can see tickets created at levels <= their level
    'visibility' => [
        'max_created_level_visible' => true,
    ],

    'notifications' => [
        'enabled' => env('TICKETS_NOTIFICATIONS_ENABLED', false),
    ],
];
