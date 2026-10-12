<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class TicketCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Ticket $ticket) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Tiket baru {$this->ticket->code}")
            ->greeting("Halo {$notifiable->name},")
            ->line("Tiket baru dibuat: {$this->ticket->subject}")
            ->line("Prioritas: {$this->ticket->priority}")
            ->action('Lihat Tiket', route('tickets.show', $this->ticket));
    }
}