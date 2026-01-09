<?php

namespace Liberty\Tickets\Support;

trait ResolvesTicketLevel
{
    public function ticketLevel(): int
    {
        $map = config('tickets.role_levels', []);
        $roles = $this->getRoleNamesSafe();

        $levels = [];
        foreach ($roles as $role) {
            if (isset($map[$role])) {
                $levels[] = (int) $map[$role];
            }
        }

        return $levels ? max($levels) : 1; // default lowest
    }

    public function ticketPriorityCap(): int
    {
        $caps = config('tickets.priority_caps', [1 => 5, 2 => 7, 3 => 10]);
        $level = $this->ticketLevel();

        return $caps[$level] ?? 5;
    }

    private function getRoleNamesSafe(): array
    {
        // Supports Spatie Permission if exists
        if (method_exists($this, 'getRoleNames')) {
            return $this->getRoleNames()->toArray();
        }

        // Fallback: if app has "role" column (optional)
        if (property_exists($this, 'role') && $this->role) {
            return [(string) $this->role];
        }

        return [];
    }
}
