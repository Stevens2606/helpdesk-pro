<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketOverdueNotification;
use Illuminate\Console\Command;

class CheckOverdueTickets extends Command
{
    protected $signature = 'tickets:check-overdue';

    protected $description = 'Tandai tiket yang melewati batas SLA dan kirim notifikasi';

    public function handle(): int
    {
        $admins = User::where('role', 'admin')->get();
        $count = 0;

        Ticket::overdue()
            ->whereNull('sla_breached_at')
            ->with('assignee')
            ->chunkById(100, function ($tickets) use ($admins, &$count) {
                foreach ($tickets as $ticket) {
                    $ticket->forceFill(['sla_breached_at' => now()])->save();

                    $ticket->logs()->create([
                        'user_id'   => null,
                        'action'    => 'sla_breached',
                        'new_value' => $ticket->due_at->toDateTimeString(),
                    ]);

                    $admins->concat([$ticket->assignee])
                        ->filter()
                        ->unique('id')
                        ->each(fn ($user) => $user->notify(new TicketOverdueNotification($ticket)));

                    $count++;
                }
            });

        $this->info("{$count} tiket ditandai melewati SLA.");

        return self::SUCCESS;
    }
}