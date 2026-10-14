<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $tickets = Ticket::with(['customer:id,name', 'assignee:id,name', 'category'])
            ->when($user->role === 'customer', fn ($q) => $q->where('user_id', $user->id))
            ->when($request->query('status'), fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(15);

        return TicketResource::collection($tickets);
    }

    public function store(StoreTicketRequest $request)
    {
        Gate::authorize('create', Ticket::class);

        $ticket = Ticket::create(
            $request->safe()->except('attachments') + ['user_id' => $request->user()->id]
        );

        return (new TicketResource($ticket->load(['customer', 'assignee', 'category'])))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, Ticket $ticket)
    {
        Gate::authorize('view', $ticket);

        $isCustomer = $request->user()->role === 'customer';

        $ticket->load([
            'customer', 'assignee', 'category',
            'comments' => fn ($q) => $q
                ->when($isCustomer, fn ($q) => $q->where('is_internal', false))
                ->with('user:id,name')
                ->oldest(),
        ]);

        return new TicketResource($ticket);
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        Gate::authorize('update', $ticket);

        $data = $request->validated();
        $isDone = in_array($data['status'], ['resolved', 'closed']);

        if ($isDone && ! $ticket->resolved_at) {
            $data['resolved_at'] = now();
        } elseif (! $isDone) {
            $data['resolved_at'] = null;
        }

        $ticket->update($data);

        return new TicketResource($ticket->load(['customer', 'assignee', 'category']));
    }

    public function destroy(Ticket $ticket)
    {
        Gate::authorize('delete', $ticket);

        $ticket->delete();

        return response()->noContent();
    }
}