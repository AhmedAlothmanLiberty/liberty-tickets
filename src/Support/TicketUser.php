<?php

namespace Liberty\Tickets\Support;

class TicketUser
{
    public static function level($user): int
    {
        $resolver = config('tickets.level_resolver');

        try {
            return max(1, (int) $resolver($user));
        } catch (\Throwable) {
            return 1;
        }
    }

    public static function priorityCap($user): int
    {
        $level = self::level($user);
        $caps  = (array) config('tickets.priority_caps', []);

        return (int) ($caps[$level] ?? 5);
    }
}
