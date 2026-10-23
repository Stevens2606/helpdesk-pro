<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketCreatedNotification;
use Illuminate\Support\Facades\Notification;

class TicketService
{
    public function __construct(private AttachmentService $attachments) {}

    public function create(User $user, array $data, array $files = []): Ticket
    {
        $ticket = Ticket::create(array_merge($data, ['user_id' => $user->id]));

        $this->attachments->store($ticket, $files);

        $staff = User::whereIn('role', ['admin', 'staff'])
            ->where('id', '!=', $user->id)
            ->get();

        Notification::send($staff, new TicketCreatedNotification($ticket));

        return $ticket;
    }

    public function update(Ticket $ticket, array $data): Ticket
    {
        $isDone = in_array($data['status'], ['resolved', 'closed']);

        if ($isDone && ! $ticket->resolved_at) {
            $data['resolved_at'] = now();
        } elseif (! $isDone) {
            $data['resolved_at'] = null;
        }

        $ticket->update($data);

        return $ticket;
    }
}