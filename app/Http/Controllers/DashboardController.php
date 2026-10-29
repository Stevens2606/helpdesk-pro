<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $isCustomer = $user->role === 'customer';

        // Customer hanya melihat tiketnya sendiri, staff/admin melihat semua
        $scoped = fn () => Ticket::query()
            ->when($isCustomer, fn ($q) => $q->where('user_id', $user->id));

        $stats = [
            'total'      => $scoped()->count(),
            'open'       => $scoped()->where('status', TicketStatus::Open->value)->count(),
            'inProgress' => $scoped()->where('status', TicketStatus::InProgress->value)->count(),
            'resolved'   => $scoped()->whereIn('status', [
                TicketStatus::Resolved->value,
                TicketStatus::Closed->value,
            ])->count(),
            'overdue'    => $scoped()->overdue()->count(),
        ];

        $recentTickets = $scoped()
            ->with(['customer', 'category'])
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard', compact('stats', 'recentTickets', 'isCustomer'));
    }
}