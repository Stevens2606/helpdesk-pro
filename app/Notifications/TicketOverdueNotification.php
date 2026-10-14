<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;

class TicketOverdueNotification extends Notification implements ShouldQueue
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
            ->subject("SLA terlewati: {$this->ticket->code}")
            ->greeting("Halo {$notifiable->name},")
            ->line("Tiket \"{$this->ticket->subject}\" melewati batas SLA.")
            ->line('Batas waktu: ' . $this->ticket->due_at->format('d M Y H:i'))
            ->action('Tangani Tiket', route('tickets.show', $this->ticket));
    }
}