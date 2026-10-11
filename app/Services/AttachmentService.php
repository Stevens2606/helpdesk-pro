<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class AttachmentService
{
    /**
     * Simpan file dan kaitkan ke model (Ticket atau TicketComment).
     */
   public function store(StoreTicketRequest $request, AttachmentService $attachments)
{
    Gate::authorize('create', Ticket::class);

    $ticket = Ticket::create(
        $request->safe()->except('attachments') + ['user_id' => auth()->id()]
    );

    $attachments->store($ticket, $request->file('attachments', []));

    return redirect()->route('tickets.show', $ticket)
        ->with('status', 'Tiket berhasil dibuat.');
}
public function show(Ticket $ticket)
{
    Gate::authorize('view', $ticket);

    $isCustomer = auth()->user()->role === 'customer';

    $ticket->load([
        'customer', 'assignee', 'category', 'attachments',
        'comments' => fn ($q) => $q
            ->when($isCustomer, fn ($q) => $q->where('is_internal', false))
            ->with(['user:id,name', 'attachments'])
            ->oldest(),
    ]);

    return view('tickets.show', [
        'ticket' => $ticket,
        'staff'  => User::whereIn('role', ['admin', 'staff'])->get(['id', 'name']),
    ]);
}
}