<?php

namespace App\Notifications;

use App\Models\Ticket;
use App\Models\TicketComment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class TicketRepliedNotification extends Notification implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Ticket $ticket,
        public TicketComment $comment,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Balasan baru pada tiket {$this->ticket->code}")
            ->greeting("Halo {$notifiable->name},")
            ->line("{$this->comment->user->name} membalas tiket \"{$this->ticket->subject}\":")
            ->line(Str::limit($this->comment->body, 200))
            ->action('Buka Tiket', route('tickets.show', $this->ticket));
    }
}