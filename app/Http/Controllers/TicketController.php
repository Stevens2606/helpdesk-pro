<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketCreatedNotification;
use App\Services\AttachmentService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;

class TicketController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $tickets = Ticket::with(['customer:id,name', 'assignee:id,name', 'category'])
            ->when($user->role === 'customer', fn ($q) => $q->where('user_id', $user->id))
            ->latest()
            ->paginate(15);

        return view('tickets.index', compact('tickets'));
    }

    public function create()
    {
        Gate::authorize('create', Ticket::class);

        return view('tickets.create', [
            'categories' => Category::all(),
        ]);
    }

    public function store(StoreTicketRequest $request, AttachmentService $attachments)
    {
        Gate::authorize('create', Ticket::class);

        $ticket = Ticket::create(
            $request->safe()->except('attachments') + ['user_id' => auth()->id()]
        );

        $attachments->store($ticket, $request->file('attachments', []));

        // kirim ke staf/admin, kecuali pembuat tiketnya sendiri
        $staff = User::whereIn('role', ['admin', 'staff'])
            ->where('id', '!=', auth()->id())
            ->get();

        Notification::send($staff, new TicketCreatedNotification($ticket));

        return redirect()->route('tickets.show', $ticket)
            ->with('status', 'Tiket berhasil dibuat.');
    }

    public function show(Ticket $ticket)
    {
        Gate::authorize('view', $ticket);

        $isCustomer = auth()->user()->role === 'customer';

        $ticket->load([
            'customer', 'assignee', 'category', 'attachments', 'logs.user:id,name',
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

    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        Gate::authorize('update', $ticket);

        $data = $request->validated();

        $isDone = in_array($data['status'], ['resolved', 'closed']);

        if ($isDone && ! $ticket->resolved_at) {
            $data['resolved_at'] = now();
        } elseif (! $isDone) {
            // tiket dibuka kembali, hapus waktu selesai
            $data['resolved_at'] = null;
        }

        $ticket->update($data);

        return back()->with('status', 'Tiket diperbarui.');
    }

    public function destroy(Ticket $ticket)
    {
        Gate::authorize('delete', $ticket);

        $ticket->delete();

        return redirect()->route('tickets.index')
            ->with('status', 'Tiket dihapus.');
    }
}