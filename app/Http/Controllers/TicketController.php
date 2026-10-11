<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

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

    public function store(StoreTicketRequest $request)
    {
        Gate::authorize('create', Ticket::class);

        $ticket = Ticket::create($request->validated() + [
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('tickets.show', $ticket)
            ->with('status', 'Tiket berhasil dibuat.');
    }

    public function show(Ticket $ticket)
    {
        Gate::authorize('view', $ticket);

        $ticket->load(['customer', 'assignee', 'category']);

        return view('tickets.show', [
            'ticket' => $ticket,
            'staff'  => User::whereIn('role', ['admin', 'staff'])->get(['id', 'name']),
        ]);
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        Gate::authorize('update', $ticket);

        $data = $request->validated();

        if (in_array($data['status'], ['resolved', 'closed']) && ! $ticket->resolved_at) {
            $data['resolved_at'] = now();
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