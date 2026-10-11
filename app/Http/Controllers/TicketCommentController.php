<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Ticket;
use App\Services\AttachmentService;
use Illuminate\Support\Facades\Gate;

class TicketCommentController extends Controller
{
    public function store(StoreCommentRequest $request, Ticket $ticket, AttachmentService $attachments)
    {
        Gate::authorize('view', $ticket);

        $user = auth()->user();

        $comment = $ticket->comments()->create([
            'user_id'     => $user->id,
            'body'        => $request->validated('body'),
            // pelanggan tidak boleh membuat catatan internal
            'is_internal' => $user->role !== 'customer' && $request->boolean('is_internal'),
        ]);

        $attachments->store($comment, $request->file('attachments', []));

        return back()->with('status', 'Komentar ditambahkan.');
    }
}