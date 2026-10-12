<?php

namespace App\Observers;

use App\Models\Ticket;

class TicketObserver
{
    public function created(Ticket $ticket): void
    {
        $ticket->logs()->create([
            'user_id'   => auth()->id(),
            'action'    => 'created',
            'new_value' => $ticket->code,
        ]);
    }

    public function updated(Ticket $ticket): void
    {
        $tracked = ['status', 'priority', 'assigned_to'];

        foreach ($tracked as $field) {
            if (! $ticket->wasChanged($field)) {
                continue;
            }

            $ticket->logs()->create([
                'user_id'   => auth()->id(),
                'action'    => $field . '_changed',
                'old_value' => $this->format($ticket->getOriginal($field)),
                'new_value' => $this->format($ticket->{$field}),
            ]);
        }
    }

    private function format(mixed $value): ?string
    {
        if ($value instanceof \BackedEnum) {
            return $value->value;
        }

        return $value === null ? null : (string) $value;
    }
}