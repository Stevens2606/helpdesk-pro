<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * @group Tiket
 * @authenticated
 */
class TicketController extends Controller
{
    /**
     * Daftar tiket
     *
     * Pelanggan hanya melihat tiket miliknya, staf dan admin melihat semuanya.
     *
     * @queryParam status string Filter: open, in_progress, resolved, closed. Example: open
     *
     * @response 200 scenario="Berhasil" {
     *   "data": [
     *     {
     *       "id": 1,
     *       "code": "TKT-261015-AB12",
     *       "subject": "Printer tidak bisa mencetak",
     *       "status": "open",
     *       "priority": "high",
     *       "category": "Teknis",
     *       "customer": "Customer",
     *       "assignee": null,
     *       "due_at": "2026-10-16T08:00:00+00:00"
     *     }
     *   ],
     *   "links": {},
     *   "meta": {"current_page": 1, "per_page": 15, "total": 1}
     * }
     */
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

    /**
     * Buat tiket
     *
     * Membuat tiket baru atas nama pengguna yang sedang login. Kode tiket dan batas SLA
     * terisi otomatis berdasarkan kategori.
     *
     * @bodyParam subject string required Judul singkat tiket. Example: Printer tidak bisa mencetak
     * @bodyParam description string required Penjelasan masalah. Example: Printer lantai 2 menampilkan error.
     * @bodyParam category_id integer required ID kategori. Example: 1
     * @bodyParam priority string required Salah satu dari: low, medium, high, urgent. Example: high
     *
     * @response 201 scenario="Tiket dibuat" {
     *   "data": {
     *     "id": 1,
     *     "code": "TKT-261015-AB12",
     *     "subject": "Printer tidak bisa mencetak",
     *     "status": "open",
     *     "priority": "high",
     *     "category": "Teknis",
     *     "customer": "Customer",
     *     "assignee": null,
     *     "due_at": "2026-10-16T08:00:00+00:00"
     *   }
     * }
     * @response 422 scenario="Validasi gagal" {
     *   "message": "The subject field is required.",
     *   "errors": {"subject": ["The subject field is required."]}
     * }
     */
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

    /**
     * Detail tiket
     *
     * Pelanggan tidak menerima komentar internal.
     *
     * @urlParam ticket string required ID tiket. Example: 1
     *
     * @response 200 scenario="Berhasil" {
     *   "data": {
     *     "id": 1,
     *     "code": "TKT-261015-AB12",
     *     "subject": "Printer tidak bisa mencetak",
     *     "status": "open",
     *     "priority": "high",
     *     "comments": []
     *   }
     * }
     * @response 403 scenario="Bukan tiket milik Anda" {"message": "This action is unauthorized."}
     */
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

    /**
     * Perbarui tiket
     *
     * Hanya staf dan admin. Mengubah status, prioritas, dan penugasan.
     *
     * @urlParam ticket integer required ID tiket. Example: 1
     */
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

    /**
     * Hapus tiket
     *
     * Hanya admin. Tiket dihapus secara soft delete.
     *
     * @urlParam ticket integer required ID tiket. Example: 1
     *
     * @response 204 scenario="Berhasil" {}
     */
    public function destroy(Ticket $ticket)
    {
        Gate::authorize('delete', $ticket);

        $ticket->delete();

        return response()->noContent();
    }
}